<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizDomainValue;
use App\Models\UserResult;
use App\Models\QuizDomainValueAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class ResultController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Fetch OCEAN assessment results
        $oceanResults = $this->getOceanResults($user->id);
        
        // Fetch RIASEC assessment results
        $riasecResults = $this->getRiasecResults($user->id);
        
        // Fetch Cognitive assessment results
        $cognitiveResults = $this->getCognitiveResults($user->id);

        // Check if all assessments are completed
        $hasCompletedAll = $oceanResults['domains']->isNotEmpty() && 
                          $riasecResults['domains']->isNotEmpty() && 
                          $cognitiveResults['domains']->isNotEmpty();

        if (!$hasCompletedAll) {
            return redirect()->route('dashboard')
                ->with('error', 'Please complete all assessments to view results.');
        }

        return view('results.index', compact('oceanResults', 'riasecResults', 'cognitiveResults'));
    }

    public function downloadReport($quizId)
    {
        $user = Auth::user();

        // Fetch all results (same as index method)
        $oceanResults = $this->getOceanResults($user->id);
        $riasecResults = $this->getRiasecResults($user->id);
        $cognitiveResults = $this->getCognitiveResults($user->id);

        // Check if all assessments are completed
        $hasCompletedAll = $oceanResults['domains']->isNotEmpty() && 
                          $riasecResults['domains']->isNotEmpty() && 
                          $cognitiveResults['domains']->isNotEmpty();

        if (!$hasCompletedAll) {
            return redirect()->route('dashboard')
                ->with('error', 'Please complete all assessments before downloading the report.');
        }

        // Calculate learning styles from OCEAN scores
        $scores = $oceanResults['domains']->keyBy('name');
        $conscientiousness = $scores['Conscientiousness']->percentage ?? 0;
        $openness = $scores['Openness']->percentage ?? 0;
        $extraversion = $scores['Extraversion']->percentage ?? 0;
        $agreeableness = $scores['Agreeableness']->percentage ?? 0;

        $learningStyles = [
            ['name' => 'Reading/Writing', 'score' => round(($conscientiousness + $openness) / 2)],
            ['name' => 'Verbal', 'score' => round($extraversion)],
            ['name' => 'Kinesthetic', 'score' => round(($agreeableness + $extraversion) / 2)],
            ['name' => 'Visual', 'score' => round($openness)],
        ];
        
        usort($learningStyles, function($a, $b) { return $b['score'] - $a['score']; });

        // Prepare additional data for PDF
        $reportData = [
            'user' => $user,
            'oceanResults' => $oceanResults,
            'riasecResults' => $riasecResults,
            'cognitiveResults' => $cognitiveResults,
            'learningStyles' => $learningStyles,
            'generatedDate' => Carbon::now()->format('F j, Y'),
            'overallScore' => [
                'personality' => round($oceanResults['domains']->avg('percentage'), 1),
                'career' => round($riasecResults['domains']->avg('percentage'), 1),
                'cognitive' => round($cognitiveResults['average_score'], 1)
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('results.pdf-report', $reportData)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        // Download with user's name and date
        $fileName = 'Psychometric-Report-' . str_replace(' ', '-', $user->name) . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }

    private function getPerformanceLevel($percentage)
    {
        if ($percentage >= 85) return 'exceptional';
        if ($percentage >= 70) return 'high';
        if ($percentage >= 40) return 'average';
        if ($percentage >= 20) return 'below-average';
        return 'low';
    }

    private function getPerformanceColors($level)
    {
        $colors = [
            'exceptional' => [
                'text' => 'text-green-700',
                'bg' => 'bg-green-50',
                'border' => 'border-green-200',
                'badge' => 'bg-green-100 text-green-800',
                'progress' => 'bg-green-500',
                'hex_text' => '#15803d',
                'hex_bg' => '#f0fdf4',
                'hex_border' => '#bbf7d0',
                'hex_progress' => '#10b981'
            ],
            'high' => [
                'text' => 'text-blue-700',
                'bg' => 'bg-blue-50',
                'border' => 'border-blue-200',
                'badge' => 'bg-blue-100 text-blue-800',
                'progress' => 'bg-blue-500',
                'hex_text' => '#1d4ed8',
                'hex_bg' => '#eff6ff',
                'hex_border' => '#bfdbfe',
                'hex_progress' => '#3b82f6'
            ],
            'average' => [
                'text' => 'text-amber-700',
                'bg' => 'bg-amber-50',
                'border' => 'border-amber-200',
                'badge' => 'bg-amber-100 text-amber-800',
                'progress' => 'bg-amber-500',
                'hex_text' => '#d97706',
                'hex_bg' => '#fffbeb',
                'hex_border' => '#fde68a',
                'hex_progress' => '#f59e0b'
            ],
            'below-average' => [
                'text' => 'text-orange-700',
                'bg' => 'bg-orange-50',
                'border' => 'border-orange-200',
                'badge' => 'bg-orange-100 text-orange-800',
                'progress' => 'bg-orange-500',
                'hex_text' => '#ea580c',
                'hex_bg' => '#fff7ed',
                'hex_border' => '#fed7aa',
                'hex_progress' => '#f97316'
            ],
            'low' => [
                'text' => 'text-red-700',
                'bg' => 'bg-red-50',
                'border' => 'border-red-200',
                'badge' => 'bg-red-100 text-red-800',
                'progress' => 'bg-red-500',
                'hex_text' => '#dc2626',
                'hex_bg' => '#fef2f2',
                'hex_border' => '#fecaca',
                'hex_progress' => '#ef4444'
            ]
        ];

        return $colors[$level] ?? $colors['average'];
    }

    private function getPerformanceLevelText($percentage)
    {
        $text = match(true) {
            $percentage >= 85 => 'Exceptional',
            $percentage >= 70 => 'High',
            $percentage >= 40 => 'Average',
            $percentage >= 20 => 'Below Average',
            default => 'Low',
        };
        return transContent($text);
    }

    private function getOceanResults($userId)
    {
        // Get OCEAN domain results
        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'domain')
            ->get()
            ->map(function ($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                return (object)[
                    'name' => transContent($result->name),
                    'name_en' => $result->name,
                    'description' => $result->description,
                    'percentage' => $result->percentage,
                    'level_description' => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level' => $result->level,
                    'performance_level' => $level,
                    'performance_text' => $this->getPerformanceLevelText($result->percentage),
                    'colors' => $colors
                ];
            });

        // Get predictive insights
        $predictiveInsights = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->whereIn('result_type', [
                'growth_potential', 
                'organizational_fit_forecast', 
                'leadership_potential', 
                'innovation_index'
            ])
            ->get()
            ->mapWithKeys(function($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                $result->performance_level = $level;
                $result->performance_text = $this->getPerformanceLevelText($result->percentage);
                $result->colors = $colors;
                
                return [$result->result_type => $result];
            });

        // Get facets
        $facets = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'facet')
            ->get()
            ->map(function ($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                $result->performance_level = $level;
                $result->performance_text = $this->getPerformanceLevelText($result->percentage);
                $result->colors = $colors;
                
                return $result;
            });

        // Get CCS skills
        $ccsSkills = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'ccs')
            ->get()
            ->map(function ($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                $result->performance_level = $level;
                $result->performance_text = $this->getPerformanceLevelText($result->percentage);
                $result->colors = $colors;
                
                return $result;
            });

        return [
            'domains' => $domains,
            'predictive_insights' => $predictiveInsights,
            'facets' => $facets,
            'ccs_skills' => $ccsSkills
        ];
    }

    private function getRiasecResults($userId)
    {
        // Get RIASEC domain results
        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->get()
            ->map(function ($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                return (object)[
                    'name' => transContent($result->name),
                    'name_en' => $result->name,
                    'description' => $result->description,
                    'percentage' => $result->percentage,
                    'level_description' => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level' => $result->level,
                    'performance_level' => $level,
                    'performance_text' => $this->getPerformanceLevelText($result->percentage),
                    'colors' => $colors
                ];
            });

        // Get career analysis
        $careerAnalysis = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'career_analysis')
            ->first();

        // Get work environment preferences
        $workEnvironment = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'work_environment')
            ->first();

        // Generate Holland code from top 3 domains
        $sortedDomains = $domains->sortByDesc('percentage')->take(3);
        $hollandCode = $sortedDomains->map(function($domain) {
            return strtoupper(substr($domain->name_en, 0, 1));
        })->implode('');

        return [
            'domains' => $domains,
            'career_analysis' => $careerAnalysis,
            'work_environment' => $workEnvironment,
            'holland_code' => $hollandCode,
            'top_domains' => $sortedDomains
        ];
    }

    private function getCognitiveResults($userId)
    {
        // Get cognitive domain results
        $domains = UserResult::where('user_id', $userId)
            ->where('assessment_type', 'cognitive')
            ->where('result_type', 'domain')
            ->get()
            ->map(function ($result) {
                $level = $this->getPerformanceLevel($result->percentage);
                $colors = $this->getPerformanceColors($level);
                
                return (object)[
                    'name' => transContent($result->name),
                    'name_en' => $result->name,
                    'description' => $result->description,
                    'percentage' => $result->percentage,
                    'level_description' => $result->level_description,
                    'actionable_insights' => $result->actionable_insights,
                    'level' => $result->level,
                    'performance_level' => $level,
                    'performance_text' => $this->getPerformanceLevelText($result->percentage),
                    'colors' => $colors
                ];
            });

        return [
            'domains' => $domains,
            'average_score' => $domains->avg('percentage')
        ];
    }

    private function levelDescription($score)
    {
        if ($score >= 85) return 'Exceptional';
        if ($score >= 70) return 'High';
        if ($score >= 40) return 'Average';
        if ($score >= 20) return 'Below Average';
        return 'Low';
    }

    // Additional helper methods for enhanced functionality

    private function calculateDomainStrengths($domains)
    {
        return $domains->sortByDesc('percentage')->take(3);
    }

    private function calculateDevelopmentAreas($domains)
    {
        return $domains->sortBy('percentage')->take(3);
    }

    private function generatePersonalityInsights($domains)
    {
        $insights = [];
        
        foreach ($domains as $domain) {
            if ($domain->percentage >= 75) {
                $insights[] = "{$domain->name} is a significant strength";
            } elseif ($domain->percentage <= 25) {
                $insights[] = "{$domain->name} presents growth opportunities";
            }
        }
        
        return $insights;
    }

    private function generateCareerRecommendations($hollandCode)
    {
        $careerMappings = [
            // R, I, A
            'RIA' => [
                'primary' => ['Software Engineer', 'Research Scientist', 'Product Developer'],
                'secondary' => ['Technical Analyst', 'Systems Designer', 'Innovation Consultant'],
                'work_environments' => ['Tech companies', 'Research labs', 'Innovation hubs']
            ],
            // R, I, S
            'RIS' => [
                'primary' => ['Biomedical Technician', 'Environmental Scientist', 'Occupational Health Specialist'],
                'secondary' => ['Veterinary Technologist', 'Safety Engineer', 'Ergonomics Consultant'],
                'work_environments' => ['Healthcare facilities', 'Environmental agencies', 'Safety organizations']
            ],
            // R, I, E
            'RIE' => [
                'primary' => ['Engineering Manager', 'Technical Project Manager', 'Operations Engineer'],
                'secondary' => ['Manufacturing Supervisor', 'Production Director', 'Technical Sales Engineer'],
                'work_environments' => ['Engineering firms', 'Manufacturing plants', 'Technical startups']
            ],
            // R, I, C
            'RIC' => [
                'primary' => ['Quality Assurance Specialist', 'Technical Inspector', 'Laboratory Technician'],
                'secondary' => ['Process Engineer', 'Technical Writer', 'Compliance Officer'],
                'work_environments' => ['Manufacturing', 'Testing facilities', 'Regulatory bodies']
            ],
            // R, A, S
            'RAS' => [
                'primary' => ['Occupational Therapist', 'Athletic Trainer', 'Environmental Educator'],
                'secondary' => ['Landscape Architect', 'Art Teacher', 'Recreation Therapist'],
                'work_environments' => ['Therapy centers', 'Educational institutions', 'Community organizations']
            ],
            // R, A, E
            'RAE' => [
                'primary' => ['Construction Manager', 'Landscape Contractor', 'Technical Sales'],
                'secondary' => ['Interior Designer', 'Set Designer', 'Exhibition Designer'],
                'work_environments' => ['Construction sites', 'Design firms', 'Event companies']
            ],
            // R, A, C
            'RAC' => [
                'primary' => ['Drafter', 'Cartographer', 'Surveyor'],
                'secondary' => ['Architectural Technician', 'CAD Specialist', 'Technical Illustrator'],
                'work_environments' => ['Architecture firms', 'Engineering offices', 'Government agencies']
            ],
            // R, S, E
            'RSE' => [
                'primary' => ['Fitness Center Manager', 'Emergency Services Coordinator', 'Safety Inspector'],
                'secondary' => ['Military Officer', 'Sports Coach', 'Park Ranger'],
                'work_environments' => ['Sports facilities', 'Emergency services', 'Outdoor recreation']
            ],
            // R, S, C
            'RSC' => [
                'primary' => ['Medical Lab Technician', 'Pharmacy Technician', 'Dental Hygienist'],
                'secondary' => ['Dietetic Technician', 'Radiologic Technologist', 'Physical Therapy Assistant'],
                'work_environments' => ['Hospitals', 'Clinics', 'Pharmacies']
            ],
            // R, E, C
            'REC' => [
                'primary' => ['Logistics Manager', 'Supply Chain Specialist', 'Facilities Manager'],
                'secondary' => ['Transportation Coordinator', 'Warehouse Manager', 'Fleet Manager'],
                'work_environments' => ['Logistics companies', 'Warehouses', 'Distribution centers']
            ],
            // I, A, S
            'IAS' => [
                'primary' => ['Clinical Researcher', 'Educational Researcher', 'Psychologist'],
                'secondary' => ['Anthropologist', 'Sociologist', 'Academic Counselor'],
                'work_environments' => ['Universities', 'Research centers', 'Clinical settings']
            ],
            // I, A, E
            'IAE' => [
                'primary' => ['Management Consultant', 'Technology Analyst', 'Strategic Planner'],
                'secondary' => ['Innovation Director', 'R&D Manager', 'Venture Analyst'],
                'work_environments' => ['Consulting firms', 'Technology companies', 'Innovation labs']
            ],
            // I, A, C
            'IAC' => [
                'primary' => ['Financial Analyst', 'Market Researcher', 'Statistician'],
                'secondary' => ['Actuary', 'Data Architect', 'Research Analyst'],
                'work_environments' => ['Financial institutions', 'Research firms', 'Analytics companies']
            ],
            // I, S, E
            'ISE' => [
                'primary' => ['Healthcare Manager', 'Research Program Director', 'Clinical Trial Manager'],
                'secondary' => ['Public Health Administrator', 'Health Educator', 'Medical Science Liaison'],
                'work_environments' => ['Hospitals', 'Public health agencies', 'Pharmaceutical companies']
            ],
            // I, S, C
            'ISC' => [
                'primary' => ['Medical Records Specialist', 'Epidemiologist', 'Health Informatics Specialist'],
                'secondary' => ['Lab Manager', 'Clinical Data Manager', 'Health Policy Analyst'],
                'work_environments' => ['Healthcare systems', 'Government health agencies', 'Research institutions']
            ],
            // I, E, C
            'IEC' => [
                'primary' => ['Data Analyst', 'Business Intelligence Analyst', 'Quantitative Analyst'],
                'secondary' => ['Risk Manager', 'Investment Analyst', 'Operations Research Analyst'],
                'work_environments' => ['Financial firms', 'Tech companies', 'Consulting agencies']
            ],
            // A, S, E
            'ASE' => [
                'primary' => ['Communications Director', 'Advertising Manager', 'Media Producer'],
                'secondary' => ['Public Relations Manager', 'Event Planner', 'Talent Agent'],
                'work_environments' => ['Media agencies', 'Entertainment companies', 'PR firms']
            ],
            // A, S, C
            'ASC' => [
                'primary' => ['Library Curator', 'Archivist', 'Museum Curator'],
                'secondary' => ['Heritage Conservation Officer', 'Gallery Director', 'Cultural Programs Manager'],
                'work_environments' => ['Museums', 'Libraries', 'Cultural institutions']
            ],
            // A, E, C
            'AEC' => [
                'primary' => ['Art Director', 'Advertising Executive', 'Fashion Merchandiser'],
                'secondary' => ['Publishing Manager', 'Creative Agency Owner', 'Production Coordinator'],
                'work_environments' => ['Advertising agencies', 'Fashion houses', 'Publishing companies']
            ],
            // S, E, C
            'SEC' => [
                'primary' => ['Office Manager', 'Executive Assistant', 'Community Services Manager'],
                'secondary' => ['Non-profit Administrator', 'Hotel Manager', 'Real Estate Agent'],
                'work_environments' => ['Corporate offices', 'Non-profit organizations', 'Service industries']
            ],
        ];

        // Permutation-aware lookup: try all orderings of the 3-letter code
        if (isset($careerMappings[$hollandCode])) {
            return $careerMappings[$hollandCode];
        }

        $letters = str_split($hollandCode);
        $permutations = $this->getPermutationsOf($letters);
        foreach ($permutations as $perm) {
            $key = implode('', $perm);
            if (isset($careerMappings[$key])) {
                return $careerMappings[$key];
            }
        }

        return [
            'primary' => ['Explore careers combining your top interests'],
            'secondary' => ['Consider interdisciplinary roles'],
            'work_environments' => ['Diverse professional settings']
        ];
    }

    private function getPermutationsOf(array $items): array
    {
        if (count($items) <= 1) {
            return [$items];
        }

        $result = [];
        foreach ($items as $key => $item) {
            $remaining = $items;
            unset($remaining[$key]);
            foreach ($this->getPermutationsOf(array_values($remaining)) as $perm) {
                $result[] = array_merge([$item], $perm);
            }
        }
        return $result;
    }

    private function calculateOverallPerformance($allResults)
    {
        $totalPercentage = 0;
        $totalItems = 0;

        foreach ($allResults as $resultSet) {
            if (isset($resultSet['domains'])) {
                foreach ($resultSet['domains'] as $domain) {
                    $totalPercentage += $domain->percentage;
                    $totalItems++;
                }
            }
        }

        return $totalItems > 0 ? round($totalPercentage / $totalItems, 2) : 0;
    }

    private function generateDevelopmentPlan($oceanResults, $riasecResults, $cognitiveResults)
    {
        $plan = [
            'immediate_focus' => [],
            'growth_areas' => [],
            'career_opportunities' => []
        ];

        // Identify areas needing immediate attention (below 40%)
        foreach ($oceanResults['domains'] as $domain) {
            if ($domain->percentage < 40) {
                $plan['immediate_focus'][] = "Develop {$domain->name} skills";
            }
        }

        // Identify growth opportunities (40-70%)
        foreach ($oceanResults['domains'] as $domain) {
            if ($domain->percentage >= 40 && $domain->percentage < 70) {
                $plan['growth_areas'][] = "Enhance {$domain->name} capabilities";
            }
        }

        // Career opportunities based on strengths
        $topRiasec = $riasecResults['domains']->sortByDesc('percentage')->take(2);
        foreach ($topRiasec as $interest) {
            $plan['career_opportunities'][] = "Explore {$interest->name}-related roles";
        }

        return $plan;
    }

    private function generateExecutiveSummary($oceanResults, $riasecResults, $cognitiveResults)
    {
        $personalityAvg = $oceanResults['domains']->avg('percentage');
        $careerAvg = $riasecResults['domains']->avg('percentage');
        $cognitiveAvg = $cognitiveResults['average_score'];

        $topPersonality = $oceanResults['domains']->sortByDesc('percentage')->first();
        $topCareer = $riasecResults['domains']->sortByDesc('percentage')->first();
        $topCognitive = $cognitiveResults['domains']->sortByDesc('percentage')->first();

        return [
            'overall_performance' => round(($personalityAvg + $careerAvg + $cognitiveAvg) / 3, 1),
            'personality_strength' => $topPersonality->name ?? 'N/A',
            'career_preference' => $topCareer->name ?? 'N/A',
            'cognitive_strength' => $topCognitive->name ?? 'N/A',
            'holland_code' => $riasecResults['holland_code'],
            'key_insights' => $this->generateKeyInsights($oceanResults, $riasecResults, $cognitiveResults)
        ];
    }

    private function generateKeyInsights($oceanResults, $riasecResults, $cognitiveResults)
    {
        $insights = [];

        // Personality insights
        $strongPersonality = $oceanResults['domains']->where('percentage', '>=', 75)->pluck('name')->toArray();
        if (!empty($strongPersonality)) {
            $insights[] = "Strong personality traits: " . implode(', ', $strongPersonality);
        }

        // Career insights
        $strongCareer = $riasecResults['domains']->where('percentage', '>=', 75)->pluck('name')->toArray();
        if (!empty($strongCareer)) {
            $insights[] = "Primary career interests: " . implode(', ', $strongCareer);
        }

        // Cognitive insights
        $strongCognitive = $cognitiveResults['domains']->where('percentage', '>=', 85)->pluck('name')->toArray();
        if (!empty($strongCognitive)) {
            $insights[] = "Exceptional cognitive abilities: " . implode(', ', $strongCognitive);
        }

        return $insights;
    }


    public function show(Quiz $quiz)
    {
        $user = Auth::user();

        // Aggregate by domain for the user and quiz
        $domains = $quiz->domains()->orderBy('order')->get()->map(function($d) use ($user) {
            // get questions of this domain
            $questionIds = $d->questions()->pluck('id')->toArray();

            $answers = QuizDomainValueAnswer::whereIn('quiz_domain_value_question_id', $questionIds)
                ->where('user_id', $user->id)
                ->get();

            $score = $answers->sum('marks');
            $max = $d->questions()->sum('max_points') ?: ($d->questions()->count());
            $percentage = $max ? ($score / $max) * 100 : 0;

            return [
                'id' => $d->id,
                'title' => $d->title,
                'score' => $score,
                'max' => $max,
                'percentage' => round($percentage,2),
            ];
        });

        // total summary
        $totalScore = $domains->sum('score');
        $totalMax = $domains->sum('max');
        $totalPercent = $totalMax ? round(($totalScore / $totalMax) * 100,2) : 0;

        return view('results.show', compact('quiz','domains','totalScore','totalMax','totalPercent'));
    }

}
