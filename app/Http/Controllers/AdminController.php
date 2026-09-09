<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\Quiz;
use App\Models\UserResult;
use App\Models\SchoolRecommendation;
use App\Models\RecommendationFeedbackLog;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use App\Services\ResultService;
use App\Services\AdminReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct(
        protected ResultService $resultService,
        protected AdminReportService $adminReportService,
    ) {}

    /**
     * Admin Dashboard - Overview stats
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'total_certificates' => Certificate::count(),
            'assessments_taken' => UserResult::distinct()->count(DB::raw("CONCAT(user_id, '-', assessment_type)")),
            'ocean_completed' => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
            'riasec_completed' => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
            'cognitive_completed' => UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
        ];

        // Recent payments
        $recentPayments = Payment::with('user')
            ->where('status', 'completed')
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        // Recent assessment completions - one row per user showing which assessments they've done
        $recentUserIds = UserResult::select('user_id', DB::raw('MAX(created_at) as last_at'))
            ->groupBy('user_id')
            ->orderByDesc('last_at')
            ->limit(10)
            ->pluck('user_id');

        $recentAssessments = User::whereIn('id', $recentUserIds)
            ->get()
            ->sortBy(fn ($u) => array_search($u->id, $recentUserIds->toArray()))
            ->map(function ($user) {
                $completed = UserResult::where('user_id', $user->id)
                    ->select('assessment_type')
                    ->distinct()
                    ->pluck('assessment_type')
                    ->toArray();
                $user->ocean_done = in_array('ocean', $completed);
                $user->riasec_done = in_array('riasec', $completed);
                $user->cognitive_done = in_array('cognitive', $completed);
                $user->last_assessment_at = UserResult::where('user_id', $user->id)->max('created_at');
                return $user;
            });

        // User growth (last 7 days)
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue growth (last 7 days)
        $revenueGrowth = Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->where('status', 'completed')
            ->where('paid_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentPayments',
            'recentAssessments',
            'userGrowth',
            'revenueGrowth'
        ));
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search by email or name
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
        }

        // Filter by role
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->status) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $users = $query->orderByDesc('created_at')->paginate(5);

        return view('admin.users.index', compact('users'));
    }

    /**
     * View user details
     */
    public function userShow(User $user)
    {
        $user->load([
            'payments' => fn($q) => $q->orderByDesc('created_at'),
            'certificate',
        ]);

        $oceanResults     = $this->resultService->getOceanResults($user->id);
        $riasecResults    = $this->resultService->getRiasecResults($user->id);
        $cognitiveResults = $this->resultService->getCognitiveResults($user->id);

        // Representative UserResults needed for download-report route (admin.assessments.download)
        $latestOceanResult     = UserResult::where('user_id', $user->id)->where('assessment_type', 'ocean')->latest()->first();
        $latestRiasecResult    = UserResult::where('user_id', $user->id)->where('assessment_type', 'riasec')->latest()->first();
        $latestCognitiveResult = UserResult::where('user_id', $user->id)->where('assessment_type', 'cognitive')->latest()->first();

        $recommendations = SchoolRecommendation::where('user_id', $user->id)
            ->with('school')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Learning styles & stream recommendations
        $learningStyles = $oceanResults['domains']->isNotEmpty()
            ? $this->resultService->getLearningStyles($oceanResults['domains'])
            : [];
        $streamRecommendations = ($oceanResults['domains']->isNotEmpty() && $riasecResults['domains']->isNotEmpty() && $cognitiveResults['domains']->isNotEmpty())
            ? $this->resultService->getStreamRecommendations($oceanResults, $riasecResults, $cognitiveResults)
            : [];

        return view('admin.users.show', compact(
            'user', 'oceanResults', 'riasecResults', 'cognitiveResults',
            'latestOceanResult', 'latestRiasecResult', 'latestCognitiveResult',
            'recommendations', 'learningStyles', 'streamRecommendations'
        ));
    }

    /**
     * Edit user
     */
    public function userEdit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function userUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_no' => 'nullable|string|max:20',
            'role' => 'required|in:student,admin',
            'is_active' => 'nullable|boolean',
            'admin_notes' => 'nullable|string|max:1000',
            'free_ai_searches' => 'required|integer|min:0',
            'free_assessment_searches' => 'required|integer|min:0',
            'paid_search_tokens' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user and all related data.
     */
    public function userDelete(User $user)
    {
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $this->deleteUserData($user->id);
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User and all related data deleted successfully.');
    }

    /**
     * Bulk delete users and all related data.
     */
    public function userBulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No users selected.');
        }

        // Prevent deleting own account
        $ids = array_values(array_filter($ids, fn ($id) => (int) $id !== auth()->id()));

        foreach ($ids as $id) {
            $this->deleteUserData($id);
        }

        $count = User::whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} user(s) and all related data deleted successfully.");
    }

    /**
     * Delete all data associated with a user ID.
     */
    private function deleteUserData(int $userId): void
    {
        UserResult::where('user_id', $userId)->delete();
        SchoolRecommendation::where('user_id', $userId)->delete();
        RecommendationFeedbackLog::where('user_id', $userId)->delete();
        Certificate::where('user_id', $userId)->delete();
        Payment::where('user_id', $userId)->delete();
    }

    /**
     * School results detail page for a user.
     */
    public function userSchoolDetail(User $user)
    {
        $data = $this->adminReportService->buildIndividualSchoolReport($user);
        return view('admin.users.school', $data);
    }

    /**
     * University results detail page for a user.
     */
    public function userUniversityDetail(User $user)
    {
        $data = $this->adminReportService->buildIndividualUniversityReport($user);
        return view('admin.users.university', $data);
    }

    /**
     * Company results detail page for a user.
     */
    public function userCompanyDetail(User $user)
    {
        $data = $this->adminReportService->buildIndividualCompanyReport($user);
        return view('admin.users.company', $data);
    }

    /**
     * Payment Management
     */
    public function payments(Request $request)
    {
        $query = Payment::with('user');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search by user email
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('email', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%"));
        }

        // Date range
        if ($request->date) {
            if ($request->date === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($request->date === 'week') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($request->date === 'month') {
                $query->where('created_at', '>=', now()->subDays(30));
            }
        }

        $payments = $query->orderByDesc('created_at')->paginate(20);

        // Stats for the view
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'avg_amount' => Payment::avg('amount') ?? 0,
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * View payment details
     */
    public function paymentShow(Payment $payment)
    {
        $payment->load('user');
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Mark payment as verified
     */
    public function paymentVerify(Request $request, Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('warning', 'Payment is already verified.');
        }

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Payment verified successfully.');
    }

    /**
     * Coupon Management
     */
    public function coupons(Request $request)
    {
        $query = Coupon::query();

        // Search by code
        if ($request->search) {
            $query->where('code', 'like', "%{$request->search}%");
        }

        // Filter by status
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->status === 'expired') {
            $query->where('expires_at', '<', now());
        }

        $coupons = $query->orderByDesc('created_at')->paginate(20);

        // Stats for the view
        $stats = [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->count(),
            'expired_coupons' => Coupon::where('expires_at', '<', now())->count(),
            'total_discount' => Coupon::sum(DB::raw('discount_value')),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Create coupon form
     */
    public function couponCreate()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store coupon
     */
    public function couponStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons|max:50',
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'uses_per_user' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Edit coupon
     */
    public function couponEdit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function couponUpdate(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'uses_per_user' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Show coupon detail with usage history
     */
    public function couponShow(Coupon $coupon)
    {
        $usages = \App\Models\QuizAccess::where('coupon_id', $coupon->id)
            ->with(['user', 'quiz'])
            ->orderByDesc('granted_at')
            ->get();

        return view('admin.coupons.show', compact('coupon', 'usages'));
    }

    /**
     * Delete coupon
     */
    public function couponDelete(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Reports & Analytics
     */
    public function reports()
    {
        // Key metrics
        $stats = [
            'total_users'          => User::count(),
            'user_growth'          => User::where('created_at', '>=', now()->subDays(7))->count(),
            'total_revenue'        => Payment::where('status', 'completed')->sum('amount'),
            'total_payments'       => Payment::where('status', 'completed')->count(),
            'total_assessments'    => UserResult::distinct()->count(DB::raw("CONCAT(user_id, '-', assessment_type)")),
            'total_recommendations'=> SchoolRecommendation::count(),
            'ocean_assessments'    => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
            'riasec_assessments'   => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
            'cognitive_assessments'=> UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
        ];

        // User growth (last 7 days)
        $userGrowthData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $userGrowthData[$date->format('M d')] = $count;
        }
        $stats['user_growth_data'] = $userGrowthData;

        // Revenue growth (last 7 days)
        $revenueGrowthData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Payment::where('status', 'completed')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('amount');
            $revenueGrowthData[$date->format('M d')] = $total;
        }
        $stats['revenue_growth_data'] = $revenueGrowthData;

        // Top schools
        $topSchools = SchoolRecommendation::selectRaw('dynamic_school_id, dynamic_schools.name as name, COUNT(*) as recommendations')
            ->join('dynamic_schools', 'school_recommendations.dynamic_school_id', '=', 'dynamic_schools.id')
            ->groupBy('dynamic_school_id', 'dynamic_schools.name')
            ->orderByDesc('recommendations')
            ->limit(5)
            ->get()
            ->toArray();
        $stats['top_schools'] = $topSchools;

        // Top cities
        $topCities = User::selectRaw('cities.id, cities.name, COUNT(users.id) as users')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('users')
            ->limit(3)
            ->get()
            ->toArray();
        $stats['top_cities'] = $topCities;

        return view('admin.reports', compact('stats'));
    }

    /**
     * Results Management - View all assessments
     */
    public function results(Request $request)
    {
        $query = UserResult::query()->with('user');

        // Search by user email or name
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by quiz type
        if ($request->quiz_type && $request->quiz_type !== 'all') {
            $query->where('assessment_type', $request->quiz_type);
        }

        $results = $query->orderByDesc('created_at')->paginate(20);

        // Stats for results — counts distinct users who completed each assessment type
        $stats = [
            'total_assessments'    => UserResult::distinct()->count(DB::raw("CONCAT(user_id, '-', assessment_type)")),
            'ocean_assessments'    => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
            'riasec_assessments'   => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
            'cognitive_assessments'=> UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
            'avg_score'            => UserResult::avg('percentage') ?? 0,
            'coupon_uses'          => Coupon::sum('usage_count') ?? 0,
        ];

        return view('admin.results.index', compact('results', 'stats'));
    }

    /**
     * View single assessment result
     */
    public function resultShow(UserResult $result)
    {
        $result->load('user');
        return view('admin.results.show', compact('result'));
    }

    /**
     * View assessment details
     */
    public function assessmentShow(UserResult $userResult)
    {
        $userResult->load('user');

        $assessmentType = $userResult->assessment_type;

        $results = match($assessmentType) {
            'ocean'     => $this->resultService->getOceanResults($userResult->user_id),
            'riasec'    => $this->resultService->getRiasecResults($userResult->user_id),
            'cognitive' => $this->resultService->getCognitiveResults($userResult->user_id),
            default     => ['domains' => collect()],
        };

        $totalPercent = $results['domains']->isNotEmpty()
            ? round($results['domains']->avg('percentage'), 2)
            : 0;

        return view('admin.assessments.show', [
            'result'       => $userResult,
            'assessmentType' => $assessmentType,
            'results'      => $results,
            'totalPercent' => $totalPercent,
        ]);
    }

    /**
     * Download assessment report as PDF
     */
    public function downloadAssessmentReport(UserResult $userResult)
    {
        $userResult->load('user');

        $reportData = $this->resultService->buildReportData($userResult->user);

        $pdf = Pdf::loadView('results.pdf-report', $reportData)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        $fileName = 'Assessment-Report-' . str_replace(' ', '-', $userResult->user->name) . '-' . strtoupper($userResult->assessment_type) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Analytics Dashboard
     */
    public function analytics()
    {
        // User statistics
        $userStats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Assessment statistics — completed (distinct users per type)
        $assessmentStats = [
            'total_assessments'    => UserResult::distinct()->count(DB::raw("CONCAT(user_id, '-', assessment_type)")),
            'ocean_assessments'    => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
            'riasec_assessments'   => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
            'cognitive_assessments'=> UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
            'avg_score'            => UserResult::avg('percentage') ?? 0,
        ];

        // Payment statistics
        $paymentStats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'avg_payment' => Payment::avg('amount') ?? 0,
            'revenue_this_month' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // School recommendation statistics
        $schoolStats = [
            'total_recommendations' => SchoolRecommendation::count(),
            'positive_feedbacks' => RecommendationFeedbackLog::where('feedback_type', 'positive')->count(),
            'negative_feedbacks' => RecommendationFeedbackLog::where('feedback_type', 'negative')->count(),
            'neutral_feedbacks' => RecommendationFeedbackLog::where('feedback_type', 'neutral')->count(),
        ];

        // User growth data (last 30 days)
        $userGrowthData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $userGrowthData[$date->format('M d')] = $count;
        }

        // Revenue growth data (last 30 days)
        $revenueGrowthData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Payment::where('status', 'completed')
                ->whereDate('paid_at', $date->format('Y-m-d'))
                ->sum('amount');
            $revenueGrowthData[$date->format('M d')] = $total;
        }

        // Assessment completion data — distinct users who completed each type
        $assessmentTypeData = [
            'ocean'     => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
            'riasec'    => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
            'cognitive' => UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
        ];

        // Top schools recommended
        $topSchools = SchoolRecommendation::selectRaw('dynamic_school_id, dynamic_schools.name as name, COUNT(*) as recommendations')
            ->join('dynamic_schools', 'school_recommendations.dynamic_school_id', '=', 'dynamic_schools.id')
            ->groupBy('dynamic_school_id', 'dynamic_schools.name')
            ->orderByDesc('recommendations')
            ->limit(10)
            ->get();

        // Top cities
        $topCities = User::selectRaw('cities.name, COUNT(users.id) as user_count')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('user_count')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'userStats',
            'assessmentStats',
            'paymentStats',
            'schoolStats',
            'userGrowthData',
            'revenueGrowthData',
            'assessmentTypeData',
            'topSchools',
            'topCities'
        ));
    }
}
