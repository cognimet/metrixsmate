<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct(protected AdminReportService $reportService) {}

    /**
     * Report generation dashboard.
     */
    public function index()
    {
        $totalStudents   = User::where('role', 'student')->count();
        $activeStudents  = User::where('role', 'student')->where('is_active', true)->count();

        return view('admin.reports.generate', compact('totalStudents', 'activeStudents'));
    }

    /**
     * Generate and download a School Report PDF.
     */
    public function schoolReport(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'user_ids'         => 'nullable|string',
        ]);

        $users = $this->resolveUsers($request);
        $data  = $this->reportService->buildSchoolReport($users, $request->institution_name);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.school', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $fileName = 'School-Report-' . str_replace(' ', '-', $request->institution_name) . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Generate and download a University Report PDF.
     */
    public function universityReport(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'user_ids'         => 'nullable|string',
        ]);

        $users = $this->resolveUsers($request);
        $data  = $this->reportService->buildUniversityReport($users, $request->institution_name);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.university', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $fileName = 'University-Report-' . str_replace(' ', '-', $request->institution_name) . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Generate and download a Company/Corporate Report PDF.
     */
    public function companyReport(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'user_ids'         => 'nullable|string',
        ]);

        $users = $this->resolveUsers($request);
        $data  = $this->reportService->buildCompanyReport($users, $request->institution_name);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.company', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $fileName = 'Company-Report-' . str_replace(' ', '-', $request->institution_name) . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Resolve user collection from request.
     * If user_ids is provided (comma-separated), fetch those. Otherwise all active students.
     */
    private function resolveUsers(Request $request)
    {
        if ($request->filled('user_ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->user_ids)));
            return User::whereIn('id', $ids)->get();
        }

        return User::where('role', 'student')->where('is_active', true)->get();
    }

    // ─────────────────────────────────────────────────────────────────────
    // Individual (single-user) report downloads
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Download individual School Report for a single user.
     */
    public function userSchoolReport(User $user)
    {
        $data = $this->reportService->buildIndividualSchoolReport($user);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.individual.school', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('School-Report-' . str_replace(' ', '-', $user->name) . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Download individual University Report for a single user.
     */
    public function userUniversityReport(User $user)
    {
        $data = $this->reportService->buildIndividualUniversityReport($user);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.individual.university', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('University-Report-' . str_replace(' ', '-', $user->name) . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Download individual Company Report for a single user.
     */
    public function userCompanyReport(User $user)
    {
        $data = $this->reportService->buildIndividualCompanyReport($user);
        $data['generatedDate'] = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('admin.reports.pdf.individual.company', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('Company-Report-' . str_replace(' ', '-', $user->name) . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
