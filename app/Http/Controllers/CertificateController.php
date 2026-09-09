<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\UserResult;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Show certificate page (generate or view existing)
     */
    public function index()
    {
        $user = Auth::user();
        $certificate = Certificate::where('user_id', $user->id)->first();

        $allCompleted = $user->hasCompletedAllAssessments();

        return view('certificates.index', compact('certificate', 'allCompleted'));
    }

    /**
     * Generate certificate (one-time only)
     */
    public function generate(Request $request)
    {
        $user = Auth::user();

        // Check if certificate already exists
        if ($user->hasCertificate()) {
            return redirect()->route('certificates.index')
                ->with('error', 'Certificate has already been generated. Each user can only generate one certificate.');
        }

        // Check all assessments completed
        if (!$user->hasCompletedAllAssessments()) {
            return redirect()->route('certificates.index')
                ->with('error', 'Please complete all assessments before generating your certificate.');
        }

        $request->validate([
            'full_name' => 'required|string|min:2|max:100',
        ]);

        // Gather scores
        $oceanDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'domain')
            ->get();

        $riasecDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->get();

        $cognitiveDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'cognitive')
            ->where('result_type', 'domain')
            ->get();

        $oceanAvg = $oceanDomains->avg('percentage') ?? 0;
        $riasecAvg = $riasecDomains->avg('percentage') ?? 0;
        $cognitiveAvg = $cognitiveDomains->avg('percentage') ?? 0;

        // Generate Holland code
        $topRiasec = $riasecDomains->sortByDesc('percentage')->take(3);
        $hollandCode = $topRiasec->map(fn($d) => strtoupper(substr($d->name, 0, 1)))->implode('');

        // Top personality trait
        $topPersonality = $oceanDomains->sortByDesc('percentage')->first();
        $topCognitive = $cognitiveDomains->sortByDesc('percentage')->first();

        $certificateNumber = Certificate::generateCertificateNumber();

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'certificate_number' => $certificateNumber,
            'full_name' => $request->full_name,
            'ocean_score' => round($oceanAvg, 2),
            'riasec_score' => round($riasecAvg, 2),
            'cognitive_score' => round($cognitiveAvg, 2),
            'holland_code' => $hollandCode,
            'top_personality_trait' => $topPersonality->name ?? 'N/A',
            'top_cognitive_strength' => $topCognitive->name ?? 'N/A',
            'issued_at' => now(),
        ]);

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate generated successfully!');
    }

    /**
     * Download certificate as PDF
     */
    public function download(Certificate $certificate)
    {
        $user = Auth::user();

        if ($certificate->user_id !== $user->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = 'MetrixsMate-Certificate-' . str_replace(' ', '-', $certificate->full_name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Public certificate verification
     */
    public function verify(Request $request)
    {
        $certificate = null;
        $searched = false;

        if ($request->has('certificate_number') && $request->certificate_number) {
            $searched = true;
            $certificate = Certificate::where('certificate_number', $request->certificate_number)->first();
        }

        return view('certificates.verify', compact('certificate', 'searched'));
    }
}
