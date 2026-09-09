<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserResult;
use App\Models\Payment;
use App\Models\Certificate;
use Illuminate\Support\Collection;

class AdminReportService
{
    public function __construct(protected ResultService $resultService) {}

    /**
     * Build full data for a group of users. Used by all three report types.
     */
    public function buildGroupData(Collection $users): array
    {
        $userIds = $users->pluck('id');

        // Aggregate assessment stats
        $totalAssessments = UserResult::whereIn('user_id', $userIds)->where('result_type', 'domain')->count();
        $completedAll     = $users->filter(fn (User $u) => $this->resultService->hasCompletedAllAssessments($u->id))->count();

        // Compute per-user report data (only for users who completed all 3)
        $profiles = [];
        foreach ($users as $user) {
            if (! $this->resultService->hasCompletedAllAssessments($user->id)) {
                continue;
            }
            $profiles[] = $this->resultService->buildReportData($user);
        }

        // Aggregate OCEAN domain averages across all completed users
        $oceanAgg   = $this->aggregateDomains($profiles, 'oceanResults');
        $riasecAgg  = $this->aggregateDomains($profiles, 'riasecResults');
        $cogAgg     = $this->aggregateDomains($profiles, 'cognitiveResults');

        // Overall score averages
        $avgPersonality = count($profiles) > 0
            ? round(collect($profiles)->avg(fn ($p) => $p['overallScore']['personality']), 1) : 0;
        $avgCareer = count($profiles) > 0
            ? round(collect($profiles)->avg(fn ($p) => $p['overallScore']['career']), 1) : 0;
        $avgCognitive = count($profiles) > 0
            ? round(collect($profiles)->avg(fn ($p) => $p['overallScore']['cognitive']), 1) : 0;

        // Performance distribution
        $perfDist = $this->buildPerformanceDistribution($profiles);

        // Holland code distribution
        $hollandDist = collect($profiles)
            ->map(fn ($p) => $p['riasecResults']['holland_code'] ?? '')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->toArray();

        // Stream recommendation aggregation
        $streamAgg = $this->aggregateStreams($profiles);

        // Learning style aggregation
        $learningAgg = $this->aggregateLearningStyles($profiles);

        // Strength / weakness analysis
        $strengths  = $this->identifyGroupStrengths($oceanAgg, $riasecAgg, $cogAgg);
        $weaknesses = $this->identifyGroupWeaknesses($oceanAgg, $riasecAgg, $cogAgg);

        // Top / bottom performers
        $ranked = collect($profiles)->map(function ($p) {
            $overall = round(($p['overallScore']['personality'] + $p['overallScore']['career'] + $p['overallScore']['cognitive']) / 3, 1);
            return ['user' => $p['user'], 'overall' => $overall, 'data' => $p];
        })->sortByDesc('overall')->values();

        return [
            'total_users'       => $users->count(),
            'completed_users'   => count($profiles),
            'total_assessments' => $totalAssessments,
            'profiles'          => $profiles,
            'ranked'            => $ranked,
            'top_performers'    => $ranked->take(5),
            'needs_attention'   => $ranked->reverse()->take(5)->values(),
            'avg_scores'        => [
                'personality' => $avgPersonality,
                'career'      => $avgCareer,
                'cognitive'   => $avgCognitive,
                'overall'     => round(($avgPersonality + $avgCareer + $avgCognitive) / 3, 1),
            ],
            'ocean_agg'       => $oceanAgg,
            'riasec_agg'      => $riasecAgg,
            'cognitive_agg'   => $cogAgg,
            'perf_distribution' => $perfDist,
            'holland_dist'    => $hollandDist,
            'stream_agg'      => $streamAgg,
            'learning_agg'    => $learningAgg,
            'strengths'       => $strengths,
            'weaknesses'      => $weaknesses,
        ];
    }

    /**
     * Build school-specific recommendations and insights.
     */
    public function buildSchoolReport(Collection $users, string $institutionName): array
    {
        $group = $this->buildGroupData($users);

        // School-specific: early talent identification
        $talentPool = collect($group['profiles'])->filter(function ($p) {
            return ($p['overallScore']['personality'] + $p['overallScore']['career'] + $p['overallScore']['cognitive']) / 3 >= 70;
        })->count();

        // Intervention candidates (students scoring below average)
        $interventionCandidates = collect($group['profiles'])->filter(function ($p) {
            return ($p['overallScore']['personality'] + $p['overallScore']['career'] + $p['overallScore']['cognitive']) / 3 < 40;
        })->count();

        // Teacher action items
        $teacherActions = $this->generateSchoolActions($group);

        // Parent guidance points
        $parentGuidance = $this->generateParentGuidance($group);

        return array_merge($group, [
            'report_type'       => 'school',
            'institution_name'  => $institutionName,
            'talent_pool_count' => $talentPool,
            'intervention_count' => $interventionCandidates,
            'teacher_actions'   => $teacherActions,
            'parent_guidance'   => $parentGuidance,
        ]);
    }

    /**
     * Build university-specific career readiness insights.
     */
    public function buildUniversityReport(Collection $users, string $institutionName): array
    {
        $group = $this->buildGroupData($users);

        // Employability index: weighted avg of cognitive + riasec
        $employabilityIndex = round(($group['avg_scores']['cognitive'] * 0.4 + $group['avg_scores']['career'] * 0.6), 1);

        // Career-ready percentage (students with ≥60 overall)
        $careerReady = collect($group['profiles'])->filter(function ($p) {
            return ($p['overallScore']['career'] + $p['overallScore']['cognitive']) / 2 >= 60;
        })->count();

        // Specialization alignment
        $specAlignment = $this->analyzeSpecializationAlignment($group);

        // Industry readiness
        $industryReadiness = $this->analyzeIndustryReadiness($group);

        return array_merge($group, [
            'report_type'          => 'university',
            'institution_name'     => $institutionName,
            'employability_index'  => $employabilityIndex,
            'career_ready_count'   => $careerReady,
            'career_ready_pct'     => $group['completed_users'] > 0 ? round($careerReady / $group['completed_users'] * 100) : 0,
            'spec_alignment'       => $specAlignment,
            'industry_readiness'   => $industryReadiness,
        ]);
    }

    /**
     * Build company/corporate report with workforce planning insights.
     */
    public function buildCompanyReport(Collection $users, string $companyName): array
    {
        $group = $this->buildGroupData($users);

        // Role fitment analysis
        $roleFitment = $this->analyzeRoleFitment($group);

        // Team composition analysis
        $teamComposition = $this->analyzeTeamComposition($group);

        // Upskilling priorities
        $upskillingPriorities = $this->identifyUpskillingPriorities($group);

        // Leadership pipeline
        $leadershipPipeline = collect($group['profiles'])->filter(function ($p) {
            $pi = $p['oceanResults']['predictive_insights'] ?? collect();
            $lp = $pi->get('leadership_potential');
            return $lp && $lp->percentage >= 70;
        })->count();

        // Innovation potential
        $innovationPotential = collect($group['profiles'])->filter(function ($p) {
            $pi = $p['oceanResults']['predictive_insights'] ?? collect();
            $ii = $pi->get('innovation_index');
            return $ii && $ii->percentage >= 70;
        })->count();

        return array_merge($group, [
            'report_type'          => 'company',
            'institution_name'     => $companyName,
            'role_fitment'         => $roleFitment,
            'team_composition'     => $teamComposition,
            'upskilling_priorities' => $upskillingPriorities,
            'leadership_pipeline'  => $leadershipPipeline,
            'innovation_potential' => $innovationPotential,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Individual (single-user) report builders
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Build a rich individual profile from ResultService data.
     */
    public function buildIndividualData(User $user): array
    {
        $profile = $this->resultService->buildReportData($user);

        $overall = round(($profile['overallScore']['personality'] + $profile['overallScore']['career'] + $profile['overallScore']['cognitive']) / 3, 1);
        $overallLevel = $this->resultService->getPerformanceLevel($overall);

        // Transform domain collections into arrays with level/colors for PDF
        $oceanDomains = $profile['oceanResults']['domains']->map(fn ($d) => [
            'name'       => $d->name,
            'percentage' => round($d->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($d->percentage),
            'colors'     => $d->colors,
            'description' => $d->description ?? '',
        ])->toArray();

        $riasecDomains = $profile['riasecResults']['domains']->map(fn ($d) => [
            'name'       => $d->name,
            'percentage' => round($d->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($d->percentage),
            'colors'     => $d->colors,
            'description' => $d->description ?? '',
        ])->toArray();

        $cognitiveDomains = $profile['cognitiveResults']['domains']->map(fn ($d) => [
            'name'       => $d->name,
            'percentage' => round($d->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($d->percentage),
            'colors'     => $d->colors,
        ])->toArray();

        // Facets
        $facets = ($profile['oceanResults']['facets'] ?? collect())->map(fn ($f) => [
            'name'       => $f->name,
            'percentage' => round($f->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($f->percentage),
            'colors'     => $f->colors,
        ])->toArray();

        // CCS Skills
        $ccsSkills = ($profile['oceanResults']['ccs_skills'] ?? collect())->map(fn ($s) => [
            'name'       => $s->name,
            'percentage' => round($s->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($s->percentage),
            'colors'     => $s->colors,
        ])->toArray();

        // Predictive insights
        $predictiveInsights = ($profile['oceanResults']['predictive_insights'] ?? collect())->map(fn ($pi) => [
            'name'       => str_replace('_', ' ', ucwords($pi->result_type, '_')),
            'percentage' => round($pi->percentage, 1),
            'level'      => $this->resultService->getPerformanceLevel($pi->percentage),
            'colors'     => $pi->colors,
        ])->toArray();

        // Determine top strengths and areas for growth
        $allDomains = collect(array_merge(
            array_map(fn ($d) => array_merge($d, ['category' => 'Personality']), $oceanDomains),
            array_map(fn ($d) => array_merge($d, ['category' => 'Career Interest']), $riasecDomains),
            array_map(fn ($d) => array_merge($d, ['category' => 'Cognitive']), $cognitiveDomains)
        ));
        $strengths = $allDomains->filter(fn ($d) => $d['percentage'] >= 65)->sortByDesc('percentage')->take(5)->values()->toArray();
        $weaknesses = $allDomains->filter(fn ($d) => $d['percentage'] < 45)->sortBy('percentage')->take(5)->values()->toArray();

        return [
            'user'              => $user,
            'profile'           => $profile,
            'overall_score'     => $overall,
            'overall_level'     => $overallLevel,
            'overall_colors'    => $this->resultService->getPerformanceColors($overallLevel),
            'scores'            => $profile['overallScore'],
            'ocean_domains'     => $oceanDomains,
            'riasec_domains'    => $riasecDomains,
            'cognitive_domains' => $cognitiveDomains,
            'facets'            => $facets,
            'ccs_skills'        => $ccsSkills,
            'predictive_insights' => $predictiveInsights,
            'holland_code'      => $profile['riasecResults']['holland_code'] ?? '',
            'learning_styles'   => $profile['learningStyles'] ?? [],
            'stream_recommendations' => $profile['streamRecommendations'] ?? [],
            'strengths'         => $strengths,
            'weaknesses'        => $weaknesses,
        ];
    }

    /**
     * Build individual School report for a single user.
     */
    public function buildIndividualSchoolReport(User $user): array
    {
        $data = $this->buildIndividualData($user);

        // School-specific: identify best streams and academic guidance
        $topStreams = array_slice($data['stream_recommendations'], 0, 3);
        $actions = $this->generateIndividualSchoolActions($data);
        $parentTips = $this->generateIndividualParentGuidance($data);

        return array_merge($data, [
            'report_type'    => 'school',
            'top_streams'    => $topStreams,
            'teacher_actions' => $actions,
            'parent_guidance' => $parentTips,
        ]);
    }

    /**
     * Build individual University report for a single user.
     */
    public function buildIndividualUniversityReport(User $user): array
    {
        $data = $this->buildIndividualData($user);

        // Employability index
        $employabilityIndex = round(($data['scores']['cognitive'] * 0.4 + $data['scores']['career'] * 0.6), 1);
        $careerReady = (($data['scores']['career'] + $data['scores']['cognitive']) / 2) >= 60;

        // Industry readiness for this individual
        $industryReadiness = $this->calculateIndividualIndustryReadiness($data['profile']);

        // Top career paths based on stream recommendations
        $careerPaths = [];
        foreach ($data['stream_recommendations'] as $s) {
            if (($s['score'] ?? 0) >= 50) {
                $careerPaths[] = [
                    'stream'  => $s['name'],
                    'score'   => $s['score'],
                    'careers' => $s['careers'] ?? [],
                    'color'   => $s['color'] ?? 'gray',
                ];
            }
        }

        return array_merge($data, [
            'report_type'          => 'university',
            'employability_index'  => $employabilityIndex,
            'career_ready'         => $careerReady,
            'industry_readiness'   => $industryReadiness,
            'career_paths'         => $careerPaths,
        ]);
    }

    /**
     * Build individual Company report for a single user.
     */
    public function buildIndividualCompanyReport(User $user): array
    {
        $data = $this->buildIndividualData($user);

        // Role fitment for individual
        $roleFitment = $this->calculateIndividualRoleFitment($data['profile']);

        // Leadership & innovation from predictive insights
        $leadership = collect($data['predictive_insights'])->firstWhere('name', 'Leadership Potential');
        $innovation = collect($data['predictive_insights'])->firstWhere('name', 'Innovation Index');

        // Work style from top RIASEC domain
        $topRiasec = collect($data['riasec_domains'])->sortByDesc('percentage')->first();
        $hollandLabels = [
            'R' => 'Realistic (Doer)', 'I' => 'Investigative (Thinker)', 'A' => 'Artistic (Creator)',
            'S' => 'Social (Helper)', 'E' => 'Enterprising (Persuader)', 'C' => 'Conventional (Organizer)',
        ];
        $workStyle = $topRiasec ? ($hollandLabels[strtoupper(substr($topRiasec['name'], 0, 1))] ?? $topRiasec['name']) : 'Not assessed';

        // Development priorities: weakest domains
        $devPriorities = [];
        foreach ($data['weaknesses'] as $w) {
            $devPriorities[] = [
                'domain'   => $w['name'],
                'score'    => $w['percentage'],
                'gap'      => round(65 - $w['percentage'], 1),
                'category' => $w['category'],
            ];
        }

        return array_merge($data, [
            'report_type'       => 'company',
            'role_fitment'      => $roleFitment,
            'leadership'        => $leadership,
            'innovation'        => $innovation,
            'work_style'        => $workStyle,
            'dev_priorities'    => $devPriorities,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Individual report helpers
    // ─────────────────────────────────────────────────────────────────────

    private function generateIndividualSchoolActions(array $data): array
    {
        $actions = [];
        $name = $data['user']->name;

        if ($data['scores']['cognitive'] < 50) {
            $actions[] = ['priority' => 'high', 'action' => "Provide $name with additional cognitive skill exercises — focus on logical reasoning and problem-solving practice."];
        }
        if ($data['scores']['personality'] < 45) {
            $actions[] = ['priority' => 'high', 'action' => "Encourage $name to participate in personality development activities — focus on confidence, responsibility, and openness to new experiences."];
        }

        foreach (array_slice($data['weaknesses'], 0, 3) as $w) {
            $actions[] = ['priority' => 'medium', 'action' => "Schedule focused practice sessions for {$w['name']} (current: {$w['percentage']}%) — targeted exercises and encouragement can make a significant difference."];
        }

        $topStream = $data['stream_recommendations'][0] ?? null;
        if ($topStream) {
            $actions[] = ['priority' => 'low', 'action' => "Consider guiding $name towards {$topStream['name']} stream — strongest aptitude alignment at {$topStream['score']}%."];
        }

        if (empty($actions)) {
            $actions[] = ['priority' => 'low', 'action' => "$name is performing well across all dimensions — continue to encourage exploration and growth."];
        }

        return $actions;
    }

    private function generateIndividualParentGuidance(array $data): array
    {
        $guidance = [];
        $name = $data['user']->name;

        $topStyle = $data['learning_styles'][0] ?? null;
        if ($topStyle) {
            $guidance[] = "$name learns best through **{$topStyle['name']}** methods — encourage activities that match this style at home (score: {$topStyle['score']}%).";
        }

        $topStream = $data['stream_recommendations'][0] ?? null;
        if ($topStream) {
            $careers = implode(', ', array_slice($topStream['careers'] ?? [], 0, 3));
            $guidance[] = "Strongest career stream fit: **{$topStream['name']}** ({$topStream['score']}%) — explore related hobbies and career conversations" . ($careers ? " in areas like $careers." : '.');
        }

        if (count($data['strengths']) > 0) {
            $sNames = implode(', ', array_column(array_slice($data['strengths'], 0, 3), 'name'));
            $guidance[] = "Key strengths to nurture: **$sNames** — positive reinforcement in these areas will build confidence.";
        }

        if (count($data['weaknesses']) > 0) {
            $wNames = implode(', ', array_column(array_slice($data['weaknesses'], 0, 2), 'name'));
            $guidance[] = "Areas that need support: **$wNames** — patience and consistent practice will help improve these over time.";
        }

        $guidance[] = 'Encourage a balanced approach — both academic and personality development contribute to long-term success.';

        return $guidance;
    }

    private function calculateIndividualIndustryReadiness(array $profile): array
    {
        $sectors = [
            'Technology & Engineering' => fn () => ($profile['overallScore']['cognitive'] * 0.5 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.5),
            'Healthcare & Sciences' => fn () => (($profile['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.4 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.4 + $profile['overallScore']['cognitive'] * 0.2),
            'Business & Finance' => fn () => (($profile['riasecResults']['domains']->firstWhere('name_en', 'Enterprising')?->percentage ?? 0) * 0.4 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Conventional')?->percentage ?? 0) * 0.3 + $profile['overallScore']['personality'] * 0.3),
            'Creative & Arts' => fn () => (($profile['riasecResults']['domains']->firstWhere('name_en', 'Artistic')?->percentage ?? 0) * 0.5 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Openness')?->percentage ?? 0) * 0.5),
            'Education & Social Work' => fn () => (($profile['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.5 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Agreeableness')?->percentage ?? 0) * 0.3 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.2),
        ];

        $result = [];
        foreach ($sectors as $sector => $scorer) {
            $score = round($scorer(), 1);
            $result[] = [
                'sector' => $sector,
                'score'  => $score,
                'level'  => $this->resultService->getPerformanceLevel($score),
                'ready'  => $score >= 60,
            ];
        }

        usort($result, fn ($a, $b) => $b['score'] <=> $a['score']);
        return $result;
    }

    private function calculateIndividualRoleFitment(array $profile): array
    {
        $roles = [
            'Leadership / Management' => fn () => (($profile['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.3 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.3 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Enterprising')?->percentage ?? 0) * 0.4),
            'Technical / Analytical' => fn () => ($profile['overallScore']['cognitive'] * 0.4 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.3 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.3),
            'Creative / Innovation' => fn () => (($profile['riasecResults']['domains']->firstWhere('name_en', 'Artistic')?->percentage ?? 0) * 0.4 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Openness')?->percentage ?? 0) * 0.4 + $profile['overallScore']['cognitive'] * 0.2),
            'Client-Facing / Sales' => fn () => (($profile['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.3 + ($profile['oceanResults']['domains']->firstWhere('name_en', 'Agreeableness')?->percentage ?? 0) * 0.3 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.4),
            'Operations / Process' => fn () => (($profile['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.4 + ($profile['riasecResults']['domains']->firstWhere('name_en', 'Conventional')?->percentage ?? 0) * 0.4 + $profile['overallScore']['cognitive'] * 0.2),
        ];

        $result = [];
        foreach ($roles as $role => $scorer) {
            $score = round($scorer(), 1);
            $result[] = [
                'role'  => $role,
                'score' => $score,
                'level' => $this->resultService->getPerformanceLevel($score),
                'fit'   => $score >= 60,
            ];
        }

        usort($result, fn ($a, $b) => $b['score'] <=> $a['score']);
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private aggregation helpers (group reports)
    // ─────────────────────────────────────────────────────────────────────

    private function aggregateDomains(array $profiles, string $key): array
    {
        if (empty($profiles)) return [];

        $domainMap = [];
        foreach ($profiles as $p) {
            foreach ($p[$key]['domains'] as $domain) {
                $name = $domain->name_en ?? $domain->name;
                if (! isset($domainMap[$name])) {
                    $domainMap[$name] = ['scores' => [], 'name' => $domain->name];
                }
                $domainMap[$name]['scores'][] = $domain->percentage;
            }
        }

        $agg = [];
        foreach ($domainMap as $nameEn => $data) {
            $scores = collect($data['scores']);
            $avg    = round($scores->avg(), 1);
            $level  = $this->resultService->getPerformanceLevel($avg);
            $agg[] = [
                'name'       => $data['name'],
                'name_en'    => $nameEn,
                'avg'        => $avg,
                'min'        => round($scores->min(), 1),
                'max'        => round($scores->max(), 1),
                'std_dev'    => round($this->stddev($scores->toArray()), 1),
                'level'      => $level,
                'colors'     => $this->resultService->getPerformanceColors($level),
                'count'      => $scores->count(),
            ];
        }

        usort($agg, fn ($a, $b) => $b['avg'] <=> $a['avg']);
        return $agg;
    }

    private function aggregateStreams(array $profiles): array
    {
        if (empty($profiles)) return [];

        $streamMap = [];
        foreach ($profiles as $p) {
            foreach ($p['streamRecommendations'] ?? [] as $s) {
                $name = $s['name'];
                if (! isset($streamMap[$name])) {
                    $streamMap[$name] = [
                        'scores' => [],
                        'name'   => $name,
                        'color'  => $s['color'] ?? 'gray',
                        'careers' => $s['careers'] ?? [],
                    ];
                }
                $streamMap[$name]['scores'][] = $s['score'];
            }
        }

        $agg = [];
        foreach ($streamMap as $name => $data) {
            $scores = collect($data['scores']);
            $agg[] = [
                'name'    => $name,
                'avg'     => round($scores->avg(), 1),
                'color'   => $data['color'],
                'careers' => $data['careers'],
                'count'   => $scores->filter(fn ($s) => $s >= 60)->count(), // students scoring ≥60
            ];
        }

        usort($agg, fn ($a, $b) => $b['avg'] <=> $a['avg']);
        return $agg;
    }

    private function aggregateLearningStyles(array $profiles): array
    {
        if (empty($profiles)) return [];

        $styleMap = [];
        foreach ($profiles as $p) {
            foreach ($p['learningStyles'] ?? [] as $ls) {
                $name = $ls['name'];
                if (! isset($styleMap[$name])) $styleMap[$name] = [];
                $styleMap[$name][] = $ls['score'];
            }
        }

        $agg = [];
        foreach ($styleMap as $name => $scores) {
            $agg[] = [
                'name' => $name,
                'avg'  => round(collect($scores)->avg(), 1),
            ];
        }

        usort($agg, fn ($a, $b) => $b['avg'] <=> $a['avg']);
        return $agg;
    }

    private function buildPerformanceDistribution(array $profiles): array
    {
        $dist = ['exceptional' => 0, 'high' => 0, 'average' => 0, 'below-average' => 0, 'low' => 0];

        foreach ($profiles as $p) {
            $overall = ($p['overallScore']['personality'] + $p['overallScore']['career'] + $p['overallScore']['cognitive']) / 3;
            $level = $this->resultService->getPerformanceLevel($overall);
            $dist[$level]++;
        }

        return $dist;
    }

    private function identifyGroupStrengths(array $oceanAgg, array $riasecAgg, array $cogAgg): array
    {
        $all = collect(array_merge($oceanAgg, $riasecAgg, $cogAgg));
        return $all->filter(fn ($d) => $d['avg'] >= 65)->sortByDesc('avg')->take(5)->values()->toArray();
    }

    private function identifyGroupWeaknesses(array $oceanAgg, array $riasecAgg, array $cogAgg): array
    {
        $all = collect(array_merge($oceanAgg, $riasecAgg, $cogAgg));
        return $all->filter(fn ($d) => $d['avg'] < 45)->sortBy('avg')->take(5)->values()->toArray();
    }

    private function generateSchoolActions(array $group): array
    {
        $actions = [];

        if (($group['avg_scores']['cognitive'] ?? 0) < 50) {
            $actions[] = ['priority' => 'high', 'action' => 'Implement targeted cognitive skills workshops — logical reasoning and problem-solving need improvement.'];
        }
        if (($group['avg_scores']['personality'] ?? 0) < 45) {
            $actions[] = ['priority' => 'high', 'action' => 'Introduce personality development programs — focus on conscientiousness and openness.'];
        }

        $weakDomains = collect($group['weaknesses'] ?? []);
        foreach ($weakDomains->take(3) as $w) {
            $actions[] = ['priority' => 'medium', 'action' => "Schedule focused training sessions for {$w['name']} (group average: {$w['avg']}%)."];
        }

        if (($group['perf_distribution']['low'] ?? 0) > 0) {
            $actions[] = ['priority' => 'high', 'action' => "Identify and provide one-on-one mentoring for {$group['perf_distribution']['low']} students in the 'Low' performance band."];
        }

        $topStream = $group['stream_agg'][0]['name'] ?? null;
        if ($topStream) {
            $actions[] = ['priority' => 'medium', 'action' => "Consider strengthening $topStream curriculum — highest aptitude observed across the student body."];
        }

        if (empty($actions)) {
            $actions[] = ['priority' => 'low', 'action' => 'Continue existing programs — student performance is on track across all domains.'];
        }

        return $actions;
    }

    private function generateParentGuidance(array $group): array
    {
        $guidance = [];

        $topStyle = $group['learning_agg'][0]['name'] ?? null;
        if ($topStyle) {
            $guidance[] = "Most students learn best via **{$topStyle}** methods — encourage activities that match this style at home.";
        }

        $topStream = $group['stream_agg'][0] ?? null;
        if ($topStream) {
            $guidance[] = "The strongest academic stream fit is **{$topStream['name']}** — explore related extracurriculars and career exposure.";
        }

        $guidance[] = 'Review your child\'s individual score card for personalized strengths and areas for growth.';
        $guidance[] = 'Encourage a balanced approach — both academic and personality development contribute to long-term success.';

        return $guidance;
    }

    private function analyzeSpecializationAlignment(array $group): array
    {
        $streams = $group['stream_agg'] ?? [];
        $result  = [];
        foreach ($streams as $s) {
            $total = $group['completed_users'] > 0 ? $group['completed_users'] : 1;
            $result[] = [
                'stream'  => $s['name'],
                'avg'     => $s['avg'],
                'suited'  => $s['count'],
                'pct'     => round($s['count'] / $total * 100),
                'careers' => $s['careers'],
            ];
        }
        return $result;
    }

    private function analyzeIndustryReadiness(array $group): array
    {
        $sectors = [
            'Technology & Engineering' => fn ($p) => ($p['overallScore']['cognitive'] * 0.5 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.5),
            'Healthcare & Sciences'    => fn ($p) => (($p['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.4 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.4 + $p['overallScore']['cognitive'] * 0.2),
            'Business & Finance'       => fn ($p) => (($p['riasecResults']['domains']->firstWhere('name_en', 'Enterprising')?->percentage ?? 0) * 0.4 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Conventional')?->percentage ?? 0) * 0.3 + $p['overallScore']['personality'] * 0.3),
            'Creative & Arts'          => fn ($p) => (($p['riasecResults']['domains']->firstWhere('name_en', 'Artistic')?->percentage ?? 0) * 0.5 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Openness')?->percentage ?? 0) * 0.5),
            'Education & Social Work'  => fn ($p) => (($p['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.5 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Agreeableness')?->percentage ?? 0) * 0.3 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.2),
        ];

        $result = [];
        foreach ($sectors as $sector => $scorer) {
            $scores = collect($group['profiles'])->map(fn ($p) => $scorer($p));
            $result[] = [
                'sector'    => $sector,
                'avg'       => round($scores->avg(), 1),
                'ready'     => $scores->filter(fn ($s) => $s >= 60)->count(),
                'ready_pct' => $group['completed_users'] > 0 ? round($scores->filter(fn ($s) => $s >= 60)->count() / $group['completed_users'] * 100) : 0,
            ];
        }

        usort($result, fn ($a, $b) => $b['avg'] <=> $a['avg']);
        return $result;
    }

    private function analyzeRoleFitment(array $group): array
    {
        $roles = [
            'Leadership / Management' => fn ($p) => (($p['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.3 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.3 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Enterprising')?->percentage ?? 0) * 0.4),
            'Technical / Analytical' => fn ($p) => ($p['overallScore']['cognitive'] * 0.4 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Investigative')?->percentage ?? 0) * 0.3 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.3),
            'Creative / Innovation' => fn ($p) => (($p['riasecResults']['domains']->firstWhere('name_en', 'Artistic')?->percentage ?? 0) * 0.4 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Openness')?->percentage ?? 0) * 0.4 + $p['overallScore']['cognitive'] * 0.2),
            'Client-Facing / Sales' => fn ($p) => (($p['oceanResults']['domains']->firstWhere('name_en', 'Extraversion')?->percentage ?? 0) * 0.3 + ($p['oceanResults']['domains']->firstWhere('name_en', 'Agreeableness')?->percentage ?? 0) * 0.3 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Social')?->percentage ?? 0) * 0.4),
            'Operations / Process' => fn ($p) => (($p['oceanResults']['domains']->firstWhere('name_en', 'Conscientiousness')?->percentage ?? 0) * 0.4 + ($p['riasecResults']['domains']->firstWhere('name_en', 'Conventional')?->percentage ?? 0) * 0.4 + $p['overallScore']['cognitive'] * 0.2),
        ];

        $result = [];
        foreach ($roles as $role => $scorer) {
            $scores = collect($group['profiles'])->map(fn ($p) => $scorer($p));
            $result[] = [
                'role'     => $role,
                'avg'      => round($scores->avg(), 1),
                'fit_count' => $scores->filter(fn ($s) => $s >= 60)->count(),
                'fit_pct'  => $group['completed_users'] > 0 ? round($scores->filter(fn ($s) => $s >= 60)->count() / $group['completed_users'] * 100) : 0,
            ];
        }

        usort($result, fn ($a, $b) => $b['avg'] <=> $a['avg']);
        return $result;
    }

    private function analyzeTeamComposition(array $group): array
    {
        $hollandLabels = [
            'R' => 'Realistic (Doers)',
            'I' => 'Investigative (Thinkers)',
            'A' => 'Artistic (Creators)',
            'S' => 'Social (Helpers)',
            'E' => 'Enterprising (Persuaders)',
            'C' => 'Conventional (Organizers)',
        ];

        $dist = [];
        foreach ($group['profiles'] as $p) {
            $topDomain = $p['riasecResults']['domains']->sortByDesc('percentage')->first();
            if ($topDomain) {
                $key = strtoupper(substr($topDomain->name_en, 0, 1));
                $label = $hollandLabels[$key] ?? $topDomain->name_en;
                if (! isset($dist[$label])) $dist[$label] = 0;
                $dist[$label]++;
            }
        }

        arsort($dist);
        $total = $group['completed_users'] > 0 ? $group['completed_users'] : 1;

        $result = [];
        foreach ($dist as $type => $count) {
            $result[] = [
                'type'  => $type,
                'count' => $count,
                'pct'   => round($count / $total * 100),
            ];
        }

        return $result;
    }

    private function identifyUpskillingPriorities(array $group): array
    {
        $priorities = [];

        foreach ($group['weaknesses'] as $w) {
            $priorities[] = [
                'domain'   => $w['name'],
                'avg'      => $w['avg'],
                'gap'      => round(65 - $w['avg'], 1),
                'priority' => $w['avg'] < 30 ? 'Critical' : ($w['avg'] < 45 ? 'High' : 'Medium'),
            ];
        }

        return $priorities;
    }

    private function stddev(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0;
        $mean = array_sum($values) / $n;
        $sum  = 0;
        foreach ($values as $v) {
            $sum += ($v - $mean) ** 2;
        }
        return sqrt($sum / ($n - 1));
    }
}
