<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\UserResult;
use App\Models\Certificate;
use App\Models\SchoolRecommendation;
use App\Models\RecommendationFeedbackLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Dashboard overview stats (cached).
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('admin.dashboard.stats', self::CACHE_TTL, function () {
            return [
                'total_users'        => User::count(),
                'total_students'     => User::where('role', 'student')->count(),
                'total_admins'       => User::where('role', 'admin')->count(),
                'active_users'       => User::where('is_active', true)->count(),
                'total_revenue'      => Payment::where('status', 'completed')->sum('amount'),
                'total_payments'     => Payment::where('status', 'completed')->count(),
                'pending_payments'   => Payment::where('status', 'pending')->count(),
                'total_coupons'      => Coupon::count(),
                'active_coupons'     => Coupon::where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                    ->count(),
                'total_certificates' => Certificate::count(),
                'assessments_taken'  => UserResult::select('user_id', 'assessment_type')->distinct('user_id')->count('user_id'),
                'ocean_completed'    => UserResult::where('assessment_type', 'ocean')->distinct('user_id')->count('user_id'),
                'riasec_completed'   => UserResult::where('assessment_type', 'riasec')->distinct('user_id')->count('user_id'),
                'cognitive_completed' => UserResult::where('assessment_type', 'cognitive')->distinct('user_id')->count('user_id'),
            ];
        });
    }

    /**
     * User growth data for a given number of days.
     */
    public function getUserGrowth(int $days = 7): array
    {
        return Cache::remember("admin.user_growth.{$days}", self::CACHE_TTL, function () use ($days) {
            $results = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($days))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();

            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $data[$date] = $results[$date] ?? 0;
            }
            return $data;
        });
    }

    /**
     * Revenue growth data for a given number of days.
     */
    public function getRevenueGrowth(int $days = 7): array
    {
        return Cache::remember("admin.revenue_growth.{$days}", self::CACHE_TTL, function () use ($days) {
            $results = Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total')
                ->where('status', 'completed')
                ->where('paid_at', '>=', now()->subDays($days))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $data[$date] = (float) ($results[$date] ?? 0);
            }
            return $data;
        });
    }

    /**
     * Comprehensive analytics stats (cached).
     */
    public function getAnalyticsData(): array
    {
        return Cache::remember('admin.analytics', self::CACHE_TTL, function () {
            $totalUsers = User::count();
            $totalPayments = Payment::count();
            $totalAssessments = UserResult::count();
            $totalRecommendations = SchoolRecommendation::count();

            $usersThisMonth = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();
            $usersLastMonth = User::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)->count();
            $userGrowthRate = $usersLastMonth > 0
                ? round(($usersThisMonth - $usersLastMonth) / $usersLastMonth * 100, 1)
                : 0;

            return [
                'user_stats' => [
                    'total_users'      => $totalUsers,
                    'total_students'   => User::where('role', 'student')->count(),
                    'total_admins'     => User::where('role', 'admin')->count(),
                    'active_users'     => User::where('is_active', true)->count(),
                    'users_this_month' => $usersThisMonth,
                    'growth_rate'      => $userGrowthRate,
                ],
                'assessment_stats' => [
                    'total'     => $totalAssessments,
                    'ocean'     => UserResult::where('assessment_type', 'ocean')->count(),
                    'riasec'    => UserResult::where('assessment_type', 'riasec')->count(),
                    'cognitive' => UserResult::where('assessment_type', 'cognitive')->count(),
                    'avg_score' => round((float) UserResult::avg('percentage'), 1),
                ],
                'payment_stats' => [
                    'total_revenue'      => Payment::where('status', 'completed')->sum('amount'),
                    'total_payments'     => $totalPayments,
                    'completed_payments' => Payment::where('status', 'completed')->count(),
                    'pending_payments'   => Payment::where('status', 'pending')->count(),
                    'avg_payment'        => round((float) Payment::where('status', 'completed')->avg('amount'), 2),
                    'revenue_this_month' => Payment::where('status', 'completed')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('amount'),
                ],
                'school_stats' => [
                    'total_recommendations' => $totalRecommendations,
                    'positive_feedbacks'    => RecommendationFeedbackLog::where('feedback_type', 'positive')->count(),
                    'negative_feedbacks'    => RecommendationFeedbackLog::where('feedback_type', 'negative')->count(),
                    'neutral_feedbacks'     => RecommendationFeedbackLog::where('feedback_type', 'neutral')->count(),
                ],
                'assessment_by_type' => [
                    'ocean'     => UserResult::where('assessment_type', 'ocean')->count(),
                    'riasec'    => UserResult::where('assessment_type', 'riasec')->count(),
                    'cognitive' => UserResult::where('assessment_type', 'cognitive')->count(),
                ],
            ];
        });
    }

    /**
     * Top recommended schools.
     */
    public function getTopSchools(int $limit = 10): \Illuminate\Support\Collection
    {
        return Cache::remember("admin.top_schools.{$limit}", self::CACHE_TTL, function () use ($limit) {
            return SchoolRecommendation::selectRaw('dynamic_school_id, dynamic_schools.name, COUNT(*) as recommendations')
                ->join('dynamic_schools', 'school_recommendations.dynamic_school_id', '=', 'dynamic_schools.id')
                ->groupBy('dynamic_school_id', 'dynamic_schools.name')
                ->orderByDesc('recommendations')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Top user cities.
     */
    public function getTopCities(int $limit = 10): \Illuminate\Support\Collection
    {
        return Cache::remember("admin.top_cities.{$limit}", self::CACHE_TTL, function () use ($limit) {
            return User::selectRaw('cities.name, COUNT(users.id) as user_count')
                ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
                ->whereNotNull('cities.name')
                ->groupBy('cities.id', 'cities.name')
                ->orderByDesc('user_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Payment stats for payment index page.
     */
    public function getPaymentStats(): array
    {
        return [
            'total_revenue'   => Payment::where('status', 'completed')->sum('amount'),
            'total_payments'  => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'avg_amount'      => round((float) Payment::where('status', 'completed')->avg('amount'), 2),
        ];
    }

    /**
     * Clear admin caches (call after data-modifying operations).
     */
    public function clearCache(): void
    {
        $keys = [
            'admin.dashboard.stats',
            'admin.analytics',
            'admin.user_growth.7',
            'admin.user_growth.30',
            'admin.revenue_growth.7',
            'admin.revenue_growth.30',
            'admin.top_schools.5',
            'admin.top_schools.10',
            'admin.top_cities.10',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
