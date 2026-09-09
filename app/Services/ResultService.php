<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserResult;
use Carbon\Carbon;

/**
 * Single source of truth for all assessment result processing.
 * Used by ResultController, AdminController, and PDF generation.
 */
class ResultService
{
    // ─────────────────────────────────────────────────────────────────────
    // Performance helpers
    // ─────────────────────────────────────────────────────────────────────

    public function getPerformanceLevel(float $percentage): string
    {
        if ($percentage >= 85) return 'exceptional';
        if ($percentage >= 70) return 'high';
        if ($percentage >= 40) return 'average';
        if ($percentage >= 20) return 'below-average';
        return 'low';
    }

    /** Returns both Tailwind class names and hex values for DomPDF. */
    public function getPerformanceColors(string $level): array
    {
        $colors = [
            'exceptional' => [
                'text'         => 'text-green-700',
                'bg'           => 'bg-green-50',
                'border'       => 'border-green-200',
                'badge'        => 'bg-green-100 text-green-800',
                'progress'     => 'bg-green-500',
                'hex_text'     => '#15803d',
                'hex_bg'       => '#f0fdf4',
                'hex_border'   => '#bbf7d0',
                'hex_progress' => '#10b981',
            ],
            'high' => [
                'text'         => 'text-blue-700',
                'bg'           => 'bg-blue-50',
                'border'       => 'border-blue-200',
                'badge'        => 'bg-blue-100 text-blue-800',
                'progress'     => 'bg-blue-500',
                'hex_text'     => '#1d4ed8',
                'hex_bg'       => '#eff6ff',
                'hex_border'   => '#bfdbfe',
                'hex_progress' => '#3b82f6',
            ],
            'average' => [
                'text'         => 'text-amber-700',
                'bg'           => 'bg-amber-50',
                'border'       => 'border-amber-200',
                'badge'        => 'bg-amber-100 text-amber-800',
                'progress'     => 'bg-amber-500',
                'hex_text'     => '#d97706',
                'hex_bg'       => '#fffbeb',
                'hex_border'   => '#fde68a',
                'hex_progress' => '#f59e0b',
            ],
            'below-average' => [
                'text'         => 'text-orange-700',
                'bg'           => 'bg-orange-50',
                'border'       => 'border-orange-200',
                'badge'        => 'bg-orange-100 text-orange-800',
                'progress'     => 'bg-orange-500',
                'hex_text'     => '#ea580c',
                'hex_bg'       => '#fff7ed',
                'hex_border'   => '#fed7aa',
                'hex_progress' => '#f97316',
            ],
            'low' => [
                'text'         => 'text-red-700',
                'bg'           => 'bg-red-50',
                'border'       => 'border-red-200',
                'badge'        => 'bg-red-100 text-red-800',
                'progress'     => 'bg-red-500',
                'hex_text'     => '#dc2626',
                'hex_bg'       => '#fef2f2',
                'hex_border'   => '#fecaca',
                'hex_progress' => '#ef4444',
            ],
        ];

        return $colors[$level] ?? $colors['average'];
    }

    public function getPerformanceLevelText(float $percentage): string
    {
        $text = match (true) {
            $percentage >= 85 => 'Exceptional',
            $percentage >= 70 => 'High',
            $percentage >= 40 => 'Average',
            $percentage >= 20 => 'Below Average',
            default           => 'Low',
        };

        return function_exists('transContent') ? transContent($text) : $text;
    }

    /** Attaches performance_level, performance_text, and colors to a UserResult model in-place. */
    public function decorateResult(UserResult $result): UserResult
    {
        $level = $this->getPerformanceLevel($result->percentage);
        $result->performance_level = $level;
        $result->performance_text  = $this->getPerformanceLevelText($result->percentage);
        $result->colors            = $this->getPerformanceColors($level);
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────
    // OCEAN
    // ─────────────────────────────────────────────────────────────────────

    public function getOceanResults(int $userId): array
    {
        $translate = function_exists('transContent');

        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'domain')
            ->get()
            ->map(function (UserResult $result) use ($translate) {
                $level  = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);

                return (object) [
                    'name'                => $translate ? transContent($result->name) : $result->name,
                    'name_en'             => $result->name,
                    'description'         => $result->description,
                    'percentage'          => $result->percentage,
                    'level_description'   => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level'               => $result->level,
                    'performance_level'   => $level,
                    'performance_text'    => $this->getPerformanceLevelText($result->percentage),
                    'colors'              => $colors,
                ];
            });

        $predictiveInsights = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->whereIn('result_type', [
                'growth_potential',
                'organizational_fit_forecast',
                'leadership_potential',
                'innovation_index',
            ])
            ->get()
            ->mapWithKeys(function (UserResult $result) {
                return [$result->result_type => $this->decorateResult($result)];
            });

        $facets = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'facet')
            ->get()
            ->map(fn (UserResult $r) => $this->decorateResult($r));

        $ccsSkills = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'ccs')
            ->get()
            ->map(fn (UserResult $r) => $this->decorateResult($r));

        return [
            'domains'             => $domains,
            'predictive_insights' => $predictiveInsights,
            'facets'              => $facets,
            'ccs_skills'          => $ccsSkills,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // RIASEC
    // ─────────────────────────────────────────────────────────────────────

    public function getRiasecResults(int $userId): array
    {
        $translate = function_exists('transContent');

        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->get()
            ->map(function (UserResult $result) use ($translate) {
                $level  = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);

                return (object) [
                    'name'                => $translate ? transContent($result->name) : $result->name,
                    'name_en'             => $result->name,
                    'description'         => $result->description,
                    'percentage'          => $result->percentage,
                    'level_description'   => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level'               => $result->level,
                    'performance_level'   => $level,
                    'performance_text'    => $this->getPerformanceLevelText($result->percentage),
                    'colors'              => $colors,
                ];
            });

        $careerAnalysis  = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'career_analysis')
            ->first();

        $workEnvironment = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'work_environment')
            ->first();

        $sortedDomains = $domains->sortByDesc('percentage')->take(3);
        $hollandCode   = $sortedDomains
            ->map(fn ($d) => strtoupper(substr($d->name_en, 0, 1)))
            ->implode('');

        return [
            'domains'          => $domains,
            'career_analysis'  => $careerAnalysis,
            'work_environment' => $workEnvironment,
            'holland_code'     => $hollandCode,
            'top_domains'      => $sortedDomains,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Cognitive
    // ─────────────────────────────────────────────────────────────────────

    public function getCognitiveResults(int $userId): array
    {
        $translate = function_exists('transContent');

        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'cognitive')
            ->where('result_type', 'domain')
            ->get()
            ->map(function (UserResult $result) use ($translate) {
                $level  = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);

                return (object) [
                    'name'                => $translate ? transContent($result->name) : $result->name,
                    'name_en'             => $result->name,
                    'description'         => $result->description,
                    'percentage'          => $result->percentage,
                    'level_description'   => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level'               => $result->level,
                    'performance_level'   => $level,
                    'performance_text'    => $this->getPerformanceLevelText($result->percentage),
                    'colors'              => $colors,
                ];
            });

        return [
            'domains'       => $domains,
            'average_score' => $domains->avg('percentage') ?? 0,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Learning styles (derived from OCEAN domain percentages)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @param  \Illuminate\Support\Collection  $oceanDomains  Output of getOceanResults()['domains']
     * @return array  Sorted descending by score
     */
    public function getLearningStyles($oceanDomains): array
    {
        $byName = $oceanDomains->keyBy('name_en');

        $conscientiousness = (float) ($byName->get('Conscientiousness')->percentage ?? 0);
        $openness          = (float) ($byName->get('Openness')->percentage          ?? 0);
        $extraversion      = (float) ($byName->get('Extraversion')->percentage      ?? 0);
        $agreeableness     = (float) ($byName->get('Agreeableness')->percentage     ?? 0);

        $styles = [
            ['name' => 'Reading/Writing', 'score' => round(($conscientiousness + $openness) / 2)],
            ['name' => 'Verbal',          'score' => round($extraversion)],
            ['name' => 'Kinesthetic',     'score' => round(($agreeableness + $extraversion) / 2)],
            ['name' => 'Visual',          'score' => round($openness)],
        ];

        usort($styles, fn ($a, $b) => $b['score'] - $a['score']);

        return $styles;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Stream recommendations (derived from RIASEC + OCEAN + Cognitive)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Returns an array of academic stream recommendations sorted by fit score.
     * Each element contains: name, description, color, score, careers,
     * recommendation ('strongly_recommended'|'recommended'|'suitable'|'less_suitable'),
     * recommendation_label.
     */
    public function getStreamRecommendations(array $oceanResults, array $riasecResults, array $cognitiveResults): array
    {
        $riasecByName = $riasecResults['domains']->keyBy('name_en');
        $oceanByName  = $oceanResults['domains']->keyBy('name_en');

        $R  = (float) ($riasecByName->get('Realistic')?->percentage      ?? 0);
        $I  = (float) ($riasecByName->get('Investigative')?->percentage  ?? 0);
        $A  = (float) ($riasecByName->get('Artistic')?->percentage       ?? 0);
        $S  = (float) ($riasecByName->get('Social')?->percentage         ?? 0);
        $E  = (float) ($riasecByName->get('Enterprising')?->percentage   ?? 0);
        $C  = (float) ($riasecByName->get('Conventional')?->percentage   ?? 0);

        $O  = (float) ($oceanByName->get('Openness')?->percentage         ?? 0);
        $Co = (float) ($oceanByName->get('Conscientiousness')?->percentage ?? 0);
        $Ex = (float) ($oceanByName->get('Extraversion')?->percentage     ?? 0);
        $Ag = (float) ($oceanByName->get('Agreeableness')?->percentage    ?? 0);

        $cognitive = (float) ($cognitiveResults['average_score'] ?? 50);

        $streams = [
            [
                'name'        => 'Science (PCM)',
                'description' => 'Physics, Chemistry, Mathematics — ideal for engineering, technology, and research careers.',
                'color'       => 'blue',
                'score'       => round(($I * 0.35) + ($R * 0.20) + ($O * 0.20) + ($Co * 0.15) + ($cognitive * 0.10)),
                'careers'     => ['Engineering', 'Architecture', 'Data Science', 'Research Scientist', 'Computer Science'],
            ],
            [
                'name'        => 'Science (PCB)',
                'description' => 'Physics, Chemistry, Biology — ideal for medicine, healthcare, and life sciences.',
                'color'       => 'green',
                'score'       => round(($I * 0.30) + ($S * 0.25) + ($Ag * 0.20) + ($Co * 0.15) + ($cognitive * 0.10)),
                'careers'     => ['Medicine (MBBS)', 'Pharmacy', 'Nursing', 'Biotechnology', 'Veterinary Science'],
            ],
            [
                'name'        => 'Commerce',
                'description' => 'Accounting, Business Studies, Economics — ideal for finance, management, and trade.',
                'color'       => 'amber',
                'score'       => round(($E * 0.30) + ($C * 0.25) + ($Co * 0.20) + ($Ex * 0.15) + ($cognitive * 0.10)),
                'careers'     => ['Chartered Accountant', 'Business Management', 'Banking', 'Finance Analyst', 'Entrepreneur'],
            ],
            [
                'name'        => 'Arts & Humanities',
                'description' => 'Literature, History, Psychology — ideal for education, social work, and creative careers.',
                'color'       => 'purple',
                'score'       => round(($A * 0.30) + ($S * 0.25) + ($O * 0.20) + ($Ag * 0.15) + ($Ex * 0.10)),
                'careers'     => ['Teaching', 'Journalism', 'Law', 'Psychology', 'Social Work', 'Creative Arts'],
            ],
            [
                'name'        => 'Vocational & Technical',
                'description' => 'Skill-based programs in trades, technology, and applied sciences.',
                'color'       => 'orange',
                'score'       => round(($R * 0.40) + ($Co * 0.25) + ($C * 0.20) + ($cognitive * 0.15)),
                'careers'     => ['Electrician', 'IT Technician', 'Auto Mechanic', 'Graphic Design', 'Hospitality'],
            ],
        ];

        usort($streams, fn ($a, $b) => $b['score'] - $a['score']);

        $topScore = $streams[0]['score'];
        foreach ($streams as &$stream) {
            $stream['recommendation'] = match (true) {
                $stream['score'] >= $topScore        => 'strongly_recommended',
                $stream['score'] >= $topScore * 0.85 => 'recommended',
                $stream['score'] >= $topScore * 0.70 => 'suitable',
                default                              => 'less_suitable',
            };
            $stream['recommendation_label'] = match ($stream['recommendation']) {
                'strongly_recommended' => 'Strongly Recommended',
                'recommended'          => 'Recommended',
                'suitable'             => 'Suitable',
                default                => 'Less Suitable',
            };
        }
        unset($stream);

        return $streams;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Full report data (shared by user PDF and admin PDF)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Returns the complete data array needed to render results.pdf-report.
     * Use this in both ResultController::downloadReport() and
     * AdminController::downloadAssessmentReport().
     */
    public function buildReportData(User $user): array
    {
        $oceanResults     = $this->getOceanResults($user->id);
        $riasecResults    = $this->getRiasecResults($user->id);
        $cognitiveResults = $this->getCognitiveResults($user->id);

        $learningStyles        = $this->getLearningStyles($oceanResults['domains']);
        $streamRecommendations = $this->getStreamRecommendations($oceanResults, $riasecResults, $cognitiveResults);

        return [
            'user'                   => $user,
            'oceanResults'           => $oceanResults,
            'riasecResults'          => $riasecResults,
            'cognitiveResults'       => $cognitiveResults,
            'learningStyles'         => $learningStyles,
            'streamRecommendations'  => $streamRecommendations,
            'generatedDate'          => Carbon::now()->format('F j, Y'),
            'overallScore'           => [
                'personality' => round($oceanResults['domains']->avg('percentage') ?? 0, 1),
                'career'      => round($riasecResults['domains']->avg('percentage') ?? 0, 1),
                'cognitive'   => round($cognitiveResults['average_score'], 1),
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Completeness check
    // ─────────────────────────────────────────────────────────────────────

    public function hasCompletedAllAssessments(int $userId): bool
    {
        return UserResult::where('user_id', $userId)
            ->where('result_type', 'domain')
            ->whereIn('assessment_type', ['ocean', 'riasec', 'cognitive'])
            ->distinct()
            ->count('assessment_type') === 3;
    }
}
