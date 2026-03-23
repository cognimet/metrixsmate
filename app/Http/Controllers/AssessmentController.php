<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizDomainValue;
use App\Models\QuizDomainValueQuestion;
use App\Models\QuizDomainValueAnswer;
use App\Models\User;
use App\Models\UserResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    public function show(Quiz $quiz, $domainId)
    {
        $domain = QuizDomainValue::findOrFail($domainId);
        $questions = $domain->questions()->orderBy('order')->get();

        return view('assessments.show', compact('quiz','domain','questions'));
    }

    public function submit(Request $request, Quiz $quiz, $domainId)
    {
        $user = Auth::user();
        $domain = QuizDomainValue::findOrFail($domainId);
        $questions = $domain->questions()->get();

        // validation: ensure answers present
        $answers = $request->input('answers', []);
        DB::beginTransaction();
        try {
            $totalScore = 0;
            $maxScore = 0;
            foreach ($questions as $q) {
                $selectedOptionIndex = isset($answers[$q->id]) ? (int)$answers[$q->id] : null;
                $isCorrect = 0;

                // If question has a correct_answer integer, check
                if ($q->correct_answer !== null) {
                    $isCorrect = ($selectedOptionIndex === (int)$q->correct_answer) ? 1 : 0;
                } else {
                    // For OCEAN/RIASEC personality items, these are Likert scale (1..5)
                    // Store value as marks (selected index)
                    $isCorrect = 0;
                }

                $marks = 0;
                if ($q->min_points !== null && $q->max_points !== null) {
                    // if options are Likert 1..5, the selectedOptionIndex will be stored as marks
                    $marks = $selectedOptionIndex ?? 0;
                } else {
                    $marks = $isCorrect ? 1 : 0;
                }

                QuizDomainValueAnswer::create([
                    'user_id' => $user->id,
                    'quiz_domain_value_question_id' => $q->id,
                    'marks' => $marks,
                    'time_taken' => null,
                    'option_selected' => $selectedOptionIndex,
                    'is_correct' => $isCorrect,
                    'level_of_difficulty' => $q->level_of_difficulty,
                ]);

                $totalScore += $marks;
                $maxScore += ($q->max_points ?? 1);
            }

            // Save aggregated result into user_results table (per quiz)
            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

            // find descriptor_id = NULL for now - we will store generic
            $result = UserResult::create([
                'user_id' => $user->id,
                'descriptor_id' => 0,
                'assessment_type' => $quiz->slug,
                'result_type' => 'cognitive_assessment',
                'name' => $quiz->title . ' result',
                'slug' => $quiz->slug,
                'code' => null,
                'description' => null,
                'score' => $totalScore,
                'percentage' => $percentage,
                'level' => intval(round($percentage/20)), // 0..5
                'level_description' => null,
            ]);

            // mark user as completed for the quiz
            if ($quiz->slug === 'ocean') {
                $user->is_ocean_assessment_completed = 1;
            } elseif ($quiz->slug === 'riasec') {
                $user->is_riasec_assessment_completed = 1;
            } elseif ($quiz->slug === 'cognitive') {
                $user->is_cognitive_assessment_completed = 1;
            }
            $user->save();

            DB::commit();

            return redirect()->route('results.show', ['quiz' => $quiz->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Error saving your answers: '.$e->getMessage()]);
        }
    }

    public function showAll($quizId)
    {
        $quiz = Quiz::with('domains.questions')->findOrFail($quizId);

        // ═══ RETAKE BLOCKED — prevent users from retaking a completed assessment ═══
        $user = auth()->user();
        $slug = strtolower($quiz->slug);
        $completedField = match ($slug) {
            'ocean' => 'is_ocean_assessment_completed',
            'riasec' => 'is_riasec_assessment_completed',
            'cognitive', 'cognitive-abilities' => 'is_cognitive_assessment_completed',
            default => null,
        };
        if ($completedField && $user->{$completedField}) {
            return redirect()->route('dashboard')
                ->with('error', __('app.error_already_completed'));
        }
        // ═══ END RETAKE BLOCK ═══

        // Flatten all questions from all domains
        $questions = $quiz->domains->flatMap->questions;

        return view('assessments.all-questions', compact('quiz', 'questions'));
    }

    // public function submitAll(Request $request, $quizId)
    // {
    //     $quiz = Quiz::with('domains.questions')->findOrFail($quizId);
    //     $assessmentType = strtolower($quiz->slug);
    //     $userId = auth()->id();

    //     // Build validation rules
    //     $rules = [];
    //     foreach ($quiz->domains as $domain) {
    //         foreach ($domain->questions as $question) {
    //             $rules['answers.' . $question->id] = 'required';
    //         }
    //     }
    //     $validated = $request->validate($rules);

    //     // ------------------------------
    //     // Save raw answers
    //     // ------------------------------
    //     foreach ($quiz->domains as $domain) {
    //         foreach ($domain->questions as $question) {
    //             $answer = $validated['answers'][$question->id];

    //             $payload = [
    //                 'user_id'                       => $userId,
    //                 'quiz_domain_value_question_id' => $question->id,
    //                 'option_selected'               => $answer,
    //                 'time_taken'                    => null,
    //             ];

    //             if (strtolower($quiz->type) === 'cognitive') {
    //                 $payload['is_correct'] = ($answer == $question->correct_answer) ? 1 : 0;
    //                 $payload['level_of_difficulty'] = $question->level_of_difficulty ?? null;
    //                 $payload['marks'] = $payload['is_correct'] ? ($question->marks ?? 1) : 0;
    //             } else {
    //                 // assume Likert numeric 1..5
    //                 $payload['marks'] = is_numeric($answer) ? (int)$answer : 3;
    //             }

    //             QuizDomainValueAnswer::updateOrCreate(
    //                 [
    //                     'user_id'                       => $userId,
    //                     'quiz_domain_value_question_id' => $question->id,
    //                 ],
    //                 $payload
    //             );
    //         }
    //     }

    //     // ------------------------------
    //     // Update results & flags
    //     // ------------------------------
    //     $user = auth()->user();

    //     if ($assessmentType === 'ocean') {
    //         $user->is_ocean_assessment_completed = 1;

    //         // 1) Domain-level aggregation & save
    //         $domainPercentages = []; // ['Neuroticism' => pct, ...]
    //         foreach ($quiz->domains as $domain) {
    //             $questionIds = $domain->questions->pluck('id')->toArray();

    //             $sumMarks = QuizDomainValueAnswer::where('user_id', $userId)
    //                 ->whereIn('quiz_domain_value_question_id', $questionIds)
    //                 ->sum('marks');

    //             // compute max: if each question has marks field defined, sum them; otherwise assume 5 per question
    //             $maxMarks = $domain->questions->sum(function($q) {
    //                 return $q->marks ?? 5;
    //             });

    //             $percentage = $maxMarks > 0 ? round(($sumMarks / $maxMarks) * 100, 2) : 0;
    //             $domainTitle = $domain->title;

    //             $domainPercentages[$domainTitle] = $percentage;

    //             UserResult::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'assessment_type' => 'ocean',
    //                     'result_type' => 'domain',
    //                     'slug' => Str::slug($domainTitle),
    //                 ],
    //                 [
    //                     'name' => $domainTitle,
    //                     'slug' => Str::slug($domainTitle),
    //                     'description' => $this->getDomainDescriptor('ocean', $domainTitle, $percentage),
    //                     'score' => $sumMarks,
    //                     'percentage' => $percentage,
    //                     'level' => $this->mapLevel($percentage),
    //                     'level_description' => $this->describeLevel('domain', $percentage),
    //                 ]
    //             );
    //         }

    //         // 2) Facet-level (30 facets) — map question number -> facet
    //         // Reverse-scored question numbers (per your mapping): 23 (Modesty - reverse), 30 (Cautiousness - reverse)
    //         $reverseQuestions = [23, 30];

    //         $facetMapping = [
    //             1 => ['Neuroticism', 'Anxiety'],
    //             2 => ['Neuroticism', 'Anger / Irritability'],
    //             3 => ['Neuroticism', 'Depression / Negativity'],
    //             4 => ['Neuroticism', 'Self-Consciousness'],
    //             5 => ['Neuroticism', 'Immoderation (Impulsiveness)'],
    //             6 => ['Neuroticism', 'Vulnerability / Stress Response'],
    //             7 => ['Extraversion', 'Friendliness'],
    //             8 => ['Extraversion', 'Gregariousness'],
    //             9 => ['Extraversion', 'Assertiveness'],
    //             10 => ['Extraversion', 'Activity Level'],
    //             11 => ['Extraversion', 'Excitement-Seeking'],
    //             12 => ['Extraversion', 'Cheerfulness'],
    //             13 => ['Openness', 'Imagination'],
    //             14 => ['Openness', 'Artistic Interests'],
    //             15 => ['Openness', 'Emotional Awareness'],
    //             16 => ['Openness', 'Adventurousness'],
    //             17 => ['Openness', 'Intellect'],
    //             18 => ['Openness', 'Openness to Values'],
    //             19 => ['Agreeableness', 'Trust'],
    //             20 => ['Agreeableness', 'Morality / Honesty'],
    //             21 => ['Agreeableness', 'Altruism'],
    //             22 => ['Agreeableness', 'Cooperation / Diplomacy'],
    //             23 => ['Agreeableness', 'Modesty / Humility (reverse)'],
    //             24 => ['Agreeableness', 'Sympathy / Compassion'],
    //             25 => ['Conscientiousness', 'Self-Efficacy'],
    //             26 => ['Conscientiousness', 'Orderliness'],
    //             27 => ['Conscientiousness', 'Dutifulness'],
    //             28 => ['Conscientiousness', 'Achievement-Striving'],
    //             29 => ['Conscientiousness', 'Self-Discipline / Willpower'],
    //             30 => ['Conscientiousness', 'Cautiousness (reverse)'],
    //         ];

    //         foreach ($facetMapping as $qNo => [$domainName, $facetName]) {
    //             // fetch the answer marks for this question_number
    //             $raw = QuizDomainValueAnswer::where('user_id', $userId)
    //                 ->whereHas('question', function($q) use ($qNo) {
    //                     $q->where('id', $qNo);
    //                 })
    //                 ->value('marks');

    //             $raw = is_null($raw) ? 0 : (int)$raw;

    //             // reverse if needed
    //             if (in_array($qNo, $reverseQuestions, true) && $raw > 0) {
    //                 $raw = 6 - $raw; // invert 1..5 -> 5..1
    //             }

    //             $percentage = $raw > 0 ? round(($raw / 5) * 100, 2) : 0;

    //             UserResult::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'assessment_type' => 'ocean',
    //                     'result_type' => 'facet',
    //                     'slug' => Str::slug($facetName),
    //                 ],
    //                 [
    //                     'name' => $facetName,
    //                     'slug' => Str::slug($facetName),
    //                     'description' => "{$facetName} ({$domainName})",
    //                     'score' => $raw,
    //                     'percentage' => $percentage,
    //                     'level' => $this->mapLevel($percentage),
    //                     'level_description' => $this->describeLevel('facet', $percentage),
    //                 ]
    //             );
    //         }

    //         // 3) CCS Calculation (aggregate specified question numbers)
    //         $ccsMapping = [
    //             'Problem Solving' => [17, 25, 30],
    //             'Digital Fluency' => [26],
    //             'Influence' => [9, 20, 27],
    //             'Creative Thinking' => [11, 13, 14],
    //             'Communication' => [4, 7, 15],
    //             'Building Inclusivity' => [12, 18, 19, 22],
    //             'Sense Making' => [14, 25],
    //             'Collaboration' => [7, 8, 12, 15, 19, 21, 23],
    //             'Adaptability' => [6, 10, 11, 16],
    //             'Learning Agility' => [10, 16, 28],
    //             'Customer Orientation' => [20, 24, 27],
    //             'Decision Making' => [5, 9, 28, 29, 30],
    //             'Global Perspective' => [18, 22],
    //             'Developing People' => [21, 24],
    //             'Self Management' => [1, 2, 3, 4, 5, 6, 23, 29],
    //             'Transdisciplinary Thinking' => [13, 17],
    //         ];

    //         // For CCS, handle reverse-coded items when reading marks (same reverse list)
    //         foreach ($ccsMapping as $skill => $questionNums) {
    //             $total = 0;
    //             $count = count($questionNums);
    //             foreach ($questionNums as $qNo) {
    //                 $raw = QuizDomainValueAnswer::where('user_id', $userId)
    //                     ->whereHas('question', function($q) use ($qNo) {
    //                         $q->where('id', $qNo);
    //                     })
    //                     ->value('marks');

    //                 $raw = is_null($raw) ? 0 : (int)$raw;
    //                 if (in_array($qNo, $reverseQuestions, true) && $raw > 0) {
    //                     $raw = 6 - $raw;
    //                 }
    //                 $total += $raw;
    //             }

    //             $percentage = $count > 0 ? round(($total / ($count * 5)) * 100, 2) : 0;

    //             UserResult::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'assessment_type' => 'ocean',
    //                     'result_type' => 'ccs',
    //                     'slug' => Str::slug($skill),
    //                 ],
    //                 [
    //                     'name' => $skill,
    //                     'slug' => Str::slug($skill),
    //                     'description' => $this->getCcsDescription($skill),
    //                     'score' => $total,
    //                     'percentage' => $percentage,
    //                     'level' => $this->mapLevel($percentage),
    //                     'level_description' => $this->describeLevel('ccs', $percentage),
    //                 ]
    //             );
    //         }

    //         // 4) Derived OCEAN metrics
    //         $opennessPct = $domainPercentages['Openness'] ?? 0;
    //         $conscientiousPct = $domainPercentages['Conscientiousness'] ?? 0;
    //         $agreeablenessPct = $domainPercentages['Agreeableness'] ?? 0;
    //         $neuroticPct = $domainPercentages['Neuroticism'] ?? 0;

    //         $growthPotential = round(($opennessPct + $conscientiousPct) / 2, 2);
    //         $fitForecast = round(($agreeablenessPct + $conscientiousPct) / 2, 2);
    //         $flightRisk = round(100 - $neuroticPct, 2);
    //         $learningStyle = $opennessPct >= 50 ? 'Exploratory' : 'Structured';

    //         UserResult::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'assessment_type' => 'ocean',
    //                 'result_type' => 'growth_potential',
    //                 'slug' => 'growth-potential',
    //             ],
    //             [
    //                 'name' => 'Growth Potential',
    //                 'slug' => 'growth-potential',
    //                 'description' => 'Estimated capacity for learning, adaptation and stretch',
    //                 'score' => $growthPotential,
    //                 'percentage' => $growthPotential,
    //                 'level' => $this->mapLevel($growthPotential),
    //                 'level_description' => $this->describeLevel('growth', $growthPotential),
    //             ]
    //         );

    //         UserResult::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'assessment_type' => 'ocean',
    //                 'result_type' => 'organizational_fit_forecast',
    //                 'slug' => 'organizational-fit-forecast',
    //             ],
    //             [
    //                 'name' => 'Organizational Fit Forecast',
    //                 'slug' => 'organizational-fit-forecast',
    //                 'description' => 'Estimated alignment with team and organisational culture',
    //                 'score' => $fitForecast,
    //                 'percentage' => $fitForecast,
    //                 'level' => $this->mapLevel($fitForecast),
    //                 'level_description' => $this->describeLevel('fit', $fitForecast),
    //             ]
    //         );

    //         UserResult::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'assessment_type' => 'ocean',
    //                 'result_type' => 'flight_risk',
    //                 'slug' => 'flight-risk',
    //             ],
    //             [
    //                 'name' => 'Flight Risk',
    //                 'slug' => 'flight-risk',
    //                 'description' => 'Estimated propensity to leave an organisation (higher = higher risk)',
    //                 'score' => $flightRisk,
    //                 'percentage' => $flightRisk,
    //                 'level' => $this->mapLevel($flightRisk, true),
    //                 'level_description' => $this->describeLevel('flight', $flightRisk),
    //             ]
    //         );

    //         UserResult::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'assessment_type' => 'ocean',
    //                 'result_type' => 'learning_style',
    //                 'slug' => 'learning-style',
    //             ],
    //             [
    //                 'name' => 'Learning Style',
    //                 'slug' => 'learning-style',
    //                 'description' => 'Preferred learning orientation derived from Openness',
    //                 'score' => 0,
    //                 'percentage' => 0,
    //                 'level' => 0,
    //                 'level_description' => $learningStyle,
    //             ]
    //         );

    //     } // end ocean

    //     // ------------------------------
    //     // RIASEC
    //     // ------------------------------
    //     elseif ($assessmentType === 'riasec') {
    //         $user->is_riasec_assessment_completed = 1;

    //         $domainResults = [];
    //         foreach ($quiz->domains as $domain) {
    //             $questionIds = $domain->questions->pluck('id')->toArray();
    //             $sumMarks = QuizDomainValueAnswer::where('user_id', $userId)
    //                 ->whereIn('quiz_domain_value_question_id', $questionIds)
    //                 ->sum('marks');

    //             $maxMarks = $domain->questions->sum(function($q) {
    //                 return $q->marks ?? 5;
    //             });

    //             $percentage = $maxMarks > 0 ? round(($sumMarks / $maxMarks) * 100, 2) : 0;
    //             $domainTitle = $domain->title;

    //             $domainResults[] = ['domain' => $domainTitle, 'score' => $sumMarks, 'percentage' => $percentage];

    //             UserResult::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'assessment_type' => 'riasec',
    //                     'result_type' => 'domain',
    //                     'slug' => Str::slug($domainTitle),
    //                 ],
    //                 [
    //                     'name' => $domainTitle,
    //                     'slug' => Str::slug($domainTitle),
    //                     'description' => $this->getDomainDescriptor('riasec', $domainTitle, $percentage),
    //                     'score' => $sumMarks,
    //                     'percentage' => $percentage,
    //                     'level' => $this->mapLevel($percentage),
    //                     'level_description' => $this->describeLevel('domain', $percentage),
    //                 ]
    //             );
    //         }

    //         // sort and get top 3
    //         usort($domainResults, fn($a, $b) => $b['score'] <=> $a['score']);
    //         $top3 = array_slice($domainResults, 0, 3);

    //         // combine initials in correct order
    //         $top3String = collect($top3)
    //             ->map(fn($res) => strtoupper(substr($res['domain'], 0, 1)))
    //             ->implode('');

    //         // store in one row
    //         UserResult::updateOrCreate(
    //             [
    //                 'user_id' => $userId,
    //                 'assessment_type' => 'riasec',
    //                 'result_type' => 'top3',
    //             ],
    //             [
    //                 'name' => $top3String, // e.g., AIR
    //                 'slug' => Str::slug($top3String),
    //                 'description' => 'Top 3 RIASEC combination',
    //                 'score' => null, // optional
    //                 'percentage' => null, // optional
    //                 'level' => null, // optional
    //                 'level_description' => null, // optional
    //             ]
    //         );

    //     }

    //     // ------------------------------
    //     // COGNITIVE
    //     // ------------------------------
    //     elseif ($assessmentType === 'cognitive') {
    //         $user->is_cognitive_assessment_completed = 1;

    //         foreach ($quiz->domains as $domain) {
    //             $questionIds = $domain->questions->pluck('id')->toArray();
    //             $correct = QuizDomainValueAnswer::where('user_id', $userId)
    //                 ->whereIn('quiz_domain_value_question_id', $questionIds)
    //                 ->where('is_correct', 1)
    //                 ->sum('marks'); // marks stored as 0/1 or question marks

    //             $totalMarks = $domain->questions->sum(function($q) {
    //                 return $q->marks ?? 1;
    //             });

    //             $percentage = $totalMarks > 0 ? round(($correct / $totalMarks) * 100, 2) : 0;

    //             UserResult::updateOrCreate(
    //                 [
    //                     'user_id' => $userId,
    //                     'assessment_type' => 'cognitive',
    //                     'result_type' => 'domain',
    //                     'slug' => Str::slug($domain->title),
    //                 ],
    //                 [
    //                     'name' => $domain->title,
    //                     'slug' => Str::slug($domain->title),
    //                     'description' => $this->getDomainDescriptor('cognitive', $domain->title, $percentage),
    //                     'score' => $correct,
    //                     'percentage' => $percentage,
    //                     'level' => $this->mapLevel($percentage),
    //                     'level_description' => $this->describeLevel('cognitive', $percentage),
    //                 ]
    //             );
    //         }
    //     }

    //     // Save user flags
    //     $user->save();

    //     return redirect()
    //         ->route('results.show', $quizId)
    //         ->with('success', 'Your answers have been submitted!');
    // }

    // /**
    //  * Map percentage to level code or name
    //  * $inverse = true flips interpretation (for flight risk we treat higher percentage as higher risk)
    //  */
    // private function mapLevel(float $pct, bool $inverse = false)
    // {
    //     if ($inverse) {
    //         if ($pct >= 75) return 3;
    //         if ($pct >= 45) return 2;
    //         return 1;
    //     } else {
    //         if ($pct >= 75) return 3;
    //         if ($pct >= 45) return 2;
    //         return 1;
    //     }
    // }

    // /**
    //  * Provide human friendly descriptions per type/percentage
    //  */
    // private function describeLevel(string $type, float $pct)
    // {
    //     if ($type === 'domain') {
    //         if ($pct >= 75) return 'Very Strong';
    //         if ($pct >= 45) return 'Average';
    //         return 'Below Average';
    //     }
    //     if ($type === 'facet') {
    //         if ($pct >= 75) return 'High';
    //         if ($pct >= 45) return 'Moderate';
    //         return 'Low';
    //     }
    //     if ($type === 'ccs') {
    //         if ($pct >= 75) return 'Excellent';
    //         if ($pct >= 45) return 'Developing';
    //         return 'Needs Development';
    //     }
    //     if ($type === 'growth') {
    //         if ($pct >= 70) return 'High growth potential';
    //         if ($pct >= 45) return 'Moderate growth potential';
    //         return 'Low growth potential';
    //     }
    //     if ($type === 'fit') {
    //         if ($pct >= 70) return 'Excellent fit';
    //         if ($pct >= 45) return 'Reasonable fit';
    //         return 'Low fit';
    //     }
    //     if ($type === 'flight') {
    //         if ($pct >= 70) return 'High flight risk';
    //         if ($pct >= 45) return 'Moderate flight risk';
    //         return 'Low flight risk';
    //     }
    //     // default
    //     if ($pct >= 75) return 'High';
    //     if ($pct >= 45) return 'Medium';
    //     return 'Low';
    // }

    // /**
    //  * Get domain descriptor (simple defaults — extend with richer text)
    //  */
    // private function getDomainDescriptor($assessmentType, $domainTitle, $percentage)
    // {
    //     $assessmentType = strtolower($assessmentType);
    //     if ($assessmentType === 'ocean') {
    //         return "{$domainTitle} score derived from personality responses ({$percentage}%).";
    //     }
    //     if ($assessmentType === 'riasec') {
    //         return "{$domainTitle} interests strength ({$percentage}%).";
    //     }
    //     if ($assessmentType === 'cognitive') {
    //         return "{$domainTitle} cognitive performance ({$percentage}%).";
    //     }
    //     return null;
    // }

    // /**
    //  * RIASEC friendly description (example)
    //  */
    // private function getRiasecDescription($domain)
    // {
    //     $map = [
    //         'Realistic' => 'Hands-on, practical work preference.',
    //         'Investigative' => 'Analytical and research-focused.',
    //         'Artistic' => 'Creative and expressive tendencies.',
    //         'Social' => 'Helpful, caring and teaching orientation.',
    //         'Enterprising' => 'Persuasive and leadership-driven.',
    //         'Conventional' => 'Organized and detail oriented.',
    //     ];
    //     return $map[$domain] ?? null;
    // }

    // /**
    //  * Simple CCS description lookup (extend as needed)
    //  */
    // private function getCcsDescription($skill)
    // {
    //     $map = [
    //         'Problem Solving' => 'Ability to analyze complex problems and find solutions.',
    //         'Digital Fluency' => 'Comfort and competence with digital tools and processes.',
    //         'Influence' => 'Ability to persuade and lead others.',
    //         'Creative Thinking' => 'Capacity for novel ideas and creative approaches.',
    //         'Communication' => 'Effectiveness in sharing ideas and listening.',
    //         'Building Inclusivity' => 'Promotes inclusive behaviours and trust.',
    //         'Sense Making' => 'Makes sense of complex information.',
    //         'Collaboration' => 'Works effectively with others to achieve goals.',
    //         'Adaptability' => 'Responds well to change and uncertainty.',
    //         'Learning Agility' => 'Learns from experience and applies quickly.',
    //         'Customer Orientation' => 'Focus on customer needs and service.',
    //         'Decision Making' => 'Tendency to take appropriate and timely decisions.',
    //         'Global Perspective' => 'Openness to different viewpoints and cultures.',
    //         'Developing People' => 'Coaches and supports development of others.',
    //         'Self Management' => 'Emotional regulation and self-discipline.',
    //         'Transdisciplinary Thinking' => 'Integrates ideas across domains.',
    //     ];
    //     return $map[$skill] ?? null;
    // }

    public function submitAll(Request $request, $quizId)
    {
        $quiz = Quiz::with(['domains.questions' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($quizId);
        
        $assessmentType = strtolower($quiz->slug);
        $user = auth()->user();
        $userId = $user->id;

        // ═══ RETAKE BLOCKED — prevent resubmission of a completed assessment ═══
        $completedField = match ($assessmentType) {
            'ocean' => 'is_ocean_assessment_completed',
            'riasec' => 'is_riasec_assessment_completed',
            'cognitive', 'cognitive-abilities' => 'is_cognitive_assessment_completed',
            default => null,
        };
        if ($completedField && $user->{$completedField}) {
            return redirect()->route('dashboard')
                ->with('error', __('app.error_already_completed'));
        }
        // ═══ END RETAKE BLOCK ═══

        // Enhanced validation with custom error messages
        $rules = [];
        $messages = [];
        foreach ($quiz->domains as $domain) {
            foreach ($domain->questions as $question) {
                $rules['answers.' . $question->id] = 'required';
                $messages['answers.' . $question->id . '.required'] = 'Please answer question #' . $question->order;
            }
        }
        
        $validated = $request->validate($rules, $messages);
        $answers = $validated['answers'] ?? [];

        DB::beginTransaction();
        try {
            // Save answers with response time tracking
            $this->saveRawAnswers($quiz, $answers, $userId, $request->get('response_times', []));

            // Fetch user answers efficiently
            $questionIds = $quiz->domains->pluck('questions.*.id')->flatten()->unique()->values()->all();
            $userAnswers = QuizDomainValueAnswer::where('user_id', $userId)
                ->whereIn('quiz_domain_value_question_id', $questionIds)
                ->get()
                ->keyBy('quiz_domain_value_question_id');

            // Compute results with enhanced algorithms
            match($assessmentType) {
                'ocean' => $this->computeAdvancedOceanResults($quiz, $userId, $userAnswers),
                'riasec' => $this->computeAdvancedRiasecResults($quiz, $userId, $userAnswers),
                'cognitive' => $this->computeAdvancedCognitiveResults($quiz, $userId, $userAnswers),
                default => throw new \InvalidArgumentException("Unknown assessment type: {$assessmentType}")
            };

            // Update user completion status
            $user->update([
                "is_{$assessmentType}_assessment_completed" => 1,
                "{$assessmentType}_completion_date" => now()
            ]);

            DB::commit();

            return redirect()->route('results.show', $quizId)
                ->with('success', 'Assessment completed successfully! Your personalized insights are ready.');
                
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Assessment submission failed', [
                'user_id' => $userId, 
                'quiz_id' => $quizId, 
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors('We encountered an issue processing your assessment. Please try again or contact support if the problem persists.');
        }
    }

    private function saveRawAnswers(Quiz $quiz, array $answers, int $userId, array $responseTimes = []): void
    {
        $type = strtolower($quiz->type ?? '');
        $bulkData = [];

        foreach ($quiz->domains as $domain) {
            foreach ($domain->questions as $question) {
                $qid = $question->id;
                $answer = $answers[$qid] ?? null;
                $responseTime = $responseTimes[$qid] ?? null;

                $payload = [
                    'user_id' => $userId,
                    'quiz_domain_value_question_id' => $qid,
                    'option_selected' => $answer,
                    'time_taken' => $responseTime,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($type === 'cognitive') {
                    $isCorrect = ($answer == $question->correct_answer) ? 1 : 0;
                    $payload['is_correct'] = $isCorrect;
                    $payload['level_of_difficulty'] = $question->level_of_difficulty ?? 1;
                    $payload['marks'] = $isCorrect ? ($question->marks ?? 1) : 0;
                } else {
                    $payload['marks'] = is_numeric($answer) ? (int)$answer : 3;
                    $payload['is_correct'] = 0;
                    $payload['level_of_difficulty'] = null;
                }

                $bulkData[] = $payload;
            }
        }

        // Use upsert for better performance
        QuizDomainValueAnswer::upsert(
            $bulkData,
            ['user_id', 'quiz_domain_value_question_id'],
            array_keys($bulkData[0] ?? [])
        );
    }

    private function computeAdvancedOceanResults(Quiz $quiz, int $userId, $userAnswers): void
    {
        $reverseQuestions = [23, 30];
        $domainPercentages = [];
        $reliabilityScores = [];

        // 1. Enhanced Domain-level computation with reliability scoring
        foreach ($quiz->domains as $domain) {
            $questionIds = $domain->questions->pluck('id')->all();
            $responses = [];
            $sumMarks = 0;

            foreach ($questionIds as $qid) {
                $ans = $userAnswers->get($qid);
                $raw = $ans && is_numeric($ans->marks) ? (int)$ans->marks : 0;
                $responses[] = $raw;
                $sumMarks += $raw;
            }

            $maxMarks = count($questionIds) * 5;
            $percentage = $maxMarks > 0 ? round(($sumMarks / $maxMarks) * 100, 2) : 0;
            $domainTitle = $domain->title;
            $domainPercentages[$domainTitle] = $percentage;

            // Calculate internal consistency (simplified Cronbach's alpha)
            $reliability = $this->calculateReliability($responses);
            $reliabilityScores[$domainTitle] = $reliability;

            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'ocean',
                    'result_type' => 'domain',
                    'slug' => Str::slug($domainTitle),
                ],
                [
                    'name' => $domainTitle,
                    'slug' => Str::slug($domainTitle),
                    'description' => $this->getEnhancedDomainDescription('ocean', $domainTitle, $percentage),
                    'score' => $sumMarks,
                    'percentage' => $percentage,
                    'reliability_score' => $reliability,
                    'level' => $this->getIntuitiveLevelMapping($percentage),
                    'level_description' => $this->getPersonalizedLevelDescription('domain', $domainTitle, $percentage),
                    'actionable_insights' => $this->getActionableInsights('ocean', $domainTitle, $percentage),
                ]
            );
        }

        // 2. Enhanced Facet-level analysis
        $this->computeOceanFacets($userId, $userAnswers, $reverseQuestions);

        // 3. Advanced CCS Skills mapping with career relevance
        $this->computeAdvancedCcsSkills($userId, $userAnswers, $reverseQuestions);

        // 4. Predictive analytics and derived insights
        $this->computePredictiveInsights($userId, $domainPercentages, $reliabilityScores);
    }

    private function computeOceanFacets(int $userId, $userAnswers, array $reverseQuestions): void
    {
        // Enhanced facet mapping with more detailed descriptions
        $facetMapping = [
            1 => ['Neuroticism', 'Anxiety', 'Your tendency to experience worry and nervousness in challenging situations'],
            2 => ['Neuroticism', 'Anger / Irritability', 'How easily you become frustrated or annoyed when things don\'t go as planned'],
            3 => ['Neuroticism', 'Depression / Negativity', 'Your tendency toward negative emotions and pessimistic thinking'],
            4 => ['Neuroticism', 'Self-Consciousness', 'How aware and concerned you are about others\' opinions of you'],
            5 => ['Neuroticism', 'Immoderation (Impulsiveness)', 'Your ability to resist temptations and control immediate impulses'],
            6 => ['Neuroticism', 'Vulnerability / Stress Response', 'How well you cope under pressure and maintain composure during stress'],
            7 => ['Extraversion', 'Friendliness', 'Your warmth and approachability in social interactions'],
            8 => ['Extraversion', 'Gregariousness', 'How much you enjoy being around groups of people and social gatherings'],
            9 => ['Extraversion', 'Assertiveness', 'Your confidence in expressing opinions and taking charge in situations'],
            10 => ['Extraversion', 'Activity Level', 'Your energy level and preference for fast-paced, busy environments'],
            11 => ['Extraversion', 'Excitement-Seeking', 'Your desire for thrills, stimulation, and adventurous experiences'],
            12 => ['Extraversion', 'Cheerfulness', 'Your tendency to experience and express positive emotions and optimism'],
            13 => ['Openness', 'Imagination', 'Your appreciation for fantasy, creativity, and abstract thinking'],
            14 => ['Openness', 'Artistic Interests', 'Your sensitivity to and appreciation for art, beauty, and aesthetic experiences'],
            15 => ['Openness', 'Emotional Awareness', 'Your ability to recognize and understand your own and others\' emotions'],
            16 => ['Openness', 'Adventurousness', 'Your willingness to try new activities, travel, and experience different things'],
            17 => ['Openness', 'Intellect', 'Your love of learning, intellectual curiosity, and enjoyment of complex ideas'],
            18 => ['Openness', 'Openness to Values', 'Your willingness to re-examine social, political, and religious values'],
            19 => ['Agreeableness', 'Trust', 'Your tendency to believe others have good intentions and are generally honest'],
            20 => ['Agreeableness', 'Morality / Honesty', 'Your commitment to being straightforward, genuine, and ethical in dealings'],
            21 => ['Agreeableness', 'Altruism', 'Your genuine care for others\' welfare and willingness to help those in need'],
            22 => ['Agreeableness', 'Cooperation / Diplomacy', 'Your preference for harmony and avoiding conflict in relationships'],
            23 => ['Agreeableness', 'Modesty / Humility', 'Your tendency to be humble and avoid claiming superiority over others'],
            24 => ['Agreeableness', 'Sympathy / Compassion', 'Your ability to feel and respond to others\' emotional experiences'],
            25 => ['Conscientiousness', 'Self-Efficacy', 'Your confidence in your ability to accomplish tasks and achieve goals'],
            26 => ['Conscientiousness', 'Orderliness', 'Your preference for organization, cleanliness, and structured environments'],
            27 => ['Conscientiousness', 'Dutifulness', 'Your sense of moral obligation and commitment to fulfilling responsibilities'],
            28 => ['Conscientiousness', 'Achievement-Striving', 'Your ambition and drive to accomplish significant goals and excel'],
            29 => ['Conscientiousness', 'Self-Discipline / Willpower', 'Your ability to persist through difficulties and resist distractions'],
            30 => ['Conscientiousness', 'Cautiousness', 'Your tendency to think carefully before acting and avoid hasty decisions'],
        ];

        foreach ($facetMapping as $qNo => [$domainName, $facetName, $description]) {
            $ans = $userAnswers->get($qNo);
            $raw = $ans && is_numeric($ans->marks) ? (int)$ans->marks : 0;
            
            if (in_array($qNo, $reverseQuestions, true) && $raw > 0) {
                $raw = 6 - $raw; // reverse 1..5 <-> 5..1
            }
            
            $percentage = $raw > 0 ? round(($raw / 5) * 100, 2) : 0;

            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'ocean',
                    'result_type' => 'facet',
                    'slug' => Str::slug($facetName),
                ],
                [
                    'name' => $facetName,
                    'slug' => Str::slug($facetName),
                    'description' => $description,
                    'score' => $raw,
                    'percentage' => $percentage,
                    'level' => $this->getIntuitiveLevelMapping($percentage),
                    'level_description' => $this->getFacetLevelDescription($facetName, $percentage),
                    'actionable_insights' => $this->getFacetInsights($facetName, $percentage, $domainName),
                ]
            );
        }
    }

    private function computeAdvancedCcsSkills(int $userId, $userAnswers, array $reverseQuestions): void
    {
        // Enhanced CCS mapping with detailed skill descriptions
        $ccsMapping = [
            'Problem Solving' => [
                'questions' => [17,25,30],
                'description' => 'Your ability to analyze complex situations, identify root causes, and develop effective solutions',
                'workplace_application' => 'Critical for strategic planning, troubleshooting, and innovation projects'
            ],
            'Digital Fluency' => [
                'questions' => [26],
                'description' => 'Your comfort and competence with digital tools, platforms, and technological solutions',
                'workplace_application' => 'Essential for modern workplace efficiency and digital transformation initiatives'
            ],
            'Influence' => [
                'questions' => [9,20,27],
                'description' => 'Your ability to persuade others, build consensus, and guide decision-making processes',
                'workplace_application' => 'Key for leadership roles, sales, and change management'
            ],
            'Creative Thinking' => [
                'questions' => [11,13,14],
                'description' => 'Your capacity to generate novel ideas, think outside conventional boundaries, and innovate',
                'workplace_application' => 'Valuable in R&D, marketing, product development, and strategic planning'
            ],
            'Communication' => [
                'questions' => [4,7,15],
                'description' => 'Your effectiveness in sharing ideas clearly, listening actively, and facilitating understanding',
                'workplace_application' => 'Fundamental for all roles, especially management, training, and client relations'
            ],
            'Building Inclusivity' => [
                'questions' => [12,18,19,22],
                'description' => 'Your ability to create welcoming environments and promote diverse perspectives and backgrounds',
                'workplace_application' => 'Critical for team leadership, HR roles, and organizational culture development'
            ],
            'Sense Making' => [
                'questions' => [14,25],
                'description' => 'Your skill in interpreting complex information, identifying patterns, and extracting meaningful insights',
                'workplace_application' => 'Important for data analysis, strategic planning, and decision support roles'
            ],
            'Collaboration' => [
                'questions' => [7,8,12,15,19,21,23],
                'description' => 'Your ability to work effectively with others, build relationships, and achieve shared goals',
                'workplace_application' => 'Essential for teamwork, project management, and cross-functional initiatives'
            ],
            'Adaptability' => [
                'questions' => [6,10,11,16],
                'description' => 'Your flexibility in responding to change, uncertainty, and evolving circumstances',
                'workplace_application' => 'Crucial in dynamic environments, change management, and startup cultures'
            ],
            'Learning Agility' => [
                'questions' => [10,16,28],
                'description' => 'Your ability to learn from experience, apply new knowledge quickly, and continuously improve',
                'workplace_application' => 'Vital for career growth, skill development, and keeping pace with industry changes'
            ],
            'Customer Orientation' => [
                'questions' => [20,24,27],
                'description' => 'Your focus on understanding and meeting customer needs, expectations, and preferences',
                'workplace_application' => 'Critical for sales, customer service, product management, and client-facing roles'
            ],
            'Decision Making' => [
                'questions' => [5,9,28,29,30],
                'description' => 'Your ability to evaluate options, make timely decisions, and take responsibility for outcomes',
                'workplace_application' => 'Essential for management roles, strategic planning, and high-stakes situations'
            ],
            'Global Perspective' => [
                'questions' => [18,22],
                'description' => 'Your awareness of cultural differences, global trends, and international business considerations',
                'workplace_application' => 'Important for international roles, multicultural teams, and global market strategies'
            ],
            'Developing People' => [
                'questions' => [21,24],
                'description' => 'Your ability to mentor, coach, and support the growth and development of others',
                'workplace_application' => 'Key for management, training roles, and organizational development'
            ],
            'Self Management' => [
                'questions' => [1,2,3,4,5,6,23,29],
                'description' => 'Your ability to regulate emotions, manage stress, and maintain professional composure',
                'workplace_application' => 'Fundamental for all roles, especially high-pressure positions and leadership'
            ],
            'Transdisciplinary Thinking' => [
                'questions' => [13,17],
                'description' => 'Your ability to integrate knowledge across different fields and apply diverse perspectives',
                'workplace_application' => 'Valuable for innovation, consulting, and complex problem-solving roles'
            ],
        ];

        foreach ($ccsMapping as $skill => $skillData) {
            $questionNums = $skillData['questions'];
            $total = 0;
            $count = count($questionNums);
            
            foreach ($questionNums as $qNo) {
                $ans = $userAnswers->get($qNo);
                $raw = $ans && is_numeric($ans->marks) ? (int)$ans->marks : 0;
                if (in_array($qNo, $reverseQuestions, true) && $raw > 0) {
                    $raw = 6 - $raw;
                }
                $total += $raw;
            }

            $percentage = $count > 0 ? round(($total / ($count * 5)) * 100, 2) : 0;

            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'ocean',
                    'result_type' => 'ccs',
                    'slug' => Str::slug($skill),
                ],
                [
                    'name' => $skill,
                    'slug' => Str::slug($skill),
                    'description' => $skillData['description'],
                    'score' => $total,
                    'percentage' => $percentage,
                    'level' => $this->getIntuitiveLevelMapping($percentage),
                    'level_description' => $this->getCcsLevelDescription($skill, $percentage),
                    'actionable_insights' => $this->getCcsInsights($skill, $percentage, $skillData['workplace_application']),
                ]
            );
        }
    }

    private function computePredictiveInsights(int $userId, array $domainPercentages, array $reliabilityScores): void
    {
        $opennessPct = $domainPercentages['Openness'] ?? 0;
        $conscientiousPct = $domainPercentages['Conscientiousness'] ?? 0;
        $agreeablenessPct = $domainPercentages['Agreeableness'] ?? 0;
        $extraversionPct = $domainPercentages['Extraversion'] ?? 0;
        $neuroticPct = $domainPercentages['Neuroticism'] ?? 0;

        // Enhanced derived metrics with more sophisticated algorithms
        $growthPotential = round(($opennessPct * 0.6 + $conscientiousPct * 0.4), 2);
        $fitForecast = round(($agreeablenessPct * 0.4 + $conscientiousPct * 0.3 + $extraversionPct * 0.3), 2);
        $flightRisk = round((100 - $neuroticPct) * 0.5 + ($conscientiousPct * 0.3) + ($agreeablenessPct * 0.2), 2);
        $leadershipPotential = round(($extraversionPct * 0.4 + $conscientiousPct * 0.3 + (100 - $neuroticPct) * 0.3), 2);
        $innovationIndex = round(($opennessPct * 0.7 + $extraversionPct * 0.3), 2);
        
        // Determine learning style based on multiple factors
        $learningStyle = $this->determineLearningStyle($opennessPct, $conscientiousPct, $extraversionPct);
        $workStyle = $this->determineWorkStyle($extraversionPct, $conscientiousPct, $agreeablenessPct);

        $derived = [
            [
                'key' => 'growth_potential',
                'name' => 'Growth Potential',
                'score' => $growthPotential,
                'desc' => 'Your estimated capacity for learning, adaptation, and professional development based on openness to experience and conscientiousness',
                'type' => 'growth',
                'value' => $growthPotential
            ],
            [
                'key' => 'organizational_fit_forecast',
                'name' => 'Organizational Fit Forecast',
                'score' => $fitForecast,
                'desc' => 'Your estimated alignment with typical team dynamics and organizational cultures',
                'type' => 'fit',
                'value' => $fitForecast
            ],
            [
                'key' => 'retention_likelihood',
                'name' => 'Retention Likelihood',
                'score' => 100 - $flightRisk,
                'desc' => 'Your estimated likelihood to remain committed to an organization long-term',
                'type' => 'retention',
                'value' => 100 - $flightRisk
            ],
            [
                'key' => 'leadership_potential',
                'name' => 'Leadership Potential',
                'score' => $leadershipPotential,
                'desc' => 'Your natural inclination and potential effectiveness in leadership roles',
                'type' => 'leadership',
                'value' => $leadershipPotential
            ],
            [
                'key' => 'innovation_index',
                'name' => 'Innovation Index',
                'score' => $innovationIndex,
                'desc' => 'Your capacity for creative thinking and driving innovation initiatives',
                'type' => 'innovation',
                'value' => $innovationIndex
            ],
            [
                'key' => 'learning_style',
                'name' => 'Learning Style',
                'score' => 0,
                'desc' => 'Your preferred approach to acquiring new knowledge and skills',
                'type' => 'learning',
                'value' => $learningStyle
            ],
            [
                'key' => 'work_style',
                'name' => 'Work Style Preference',
                'score' => 0,
                'desc' => 'Your optimal work environment and collaboration preferences',
                'type' => 'work_style',
                'value' => $workStyle
            ]
        ];

        foreach ($derived as $d) {
            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'ocean',
                    'result_type' => $d['key'],
                    'slug' => Str::slug($d['key']),
                ],
                [
                    'name' => $d['name'],
                    'slug' => Str::slug($d['key']),
                    'description' => $d['desc'],
                    'score' => $d['score'],
                    'percentage' => is_numeric($d['score']) ? $d['score'] : null,
                    'level' => is_numeric($d['score']) ? $this->getIntuitiveLevelMapping($d['score']) : 0,
                    'level_description' => $this->getDerivedMetricDescription($d['type'], $d['value']),
                    'actionable_insights' => $this->getDerivedMetricInsights($d['type'], $d['value'], $d['score'] ?? 0),
                ]
            );
        }
    }

    private function computeAdvancedRiasecResults(Quiz $quiz, int $userId, $userAnswers): void
    {
        $domainResults = [];
        $consistencyScores = [];

        foreach ($quiz->domains as $domain) {
            $questionIds = $domain->questions->pluck('id')->all();
            $responses = [];
            $sumMarks = 0;

            foreach ($questionIds as $qid) {
                $ans = $userAnswers->get($qid);
                $mark = $ans && is_numeric($ans->marks) ? (int)$ans->marks : 0;
                $responses[] = $mark;
                $sumMarks += $mark;
            }

            $maxMarks = count($questionIds) * 5;
            $percentage = $maxMarks > 0 ? round(($sumMarks / $maxMarks) * 100, 2) : 0;
            $consistency = $this->calculateConsistency($responses);
            
            $domainResults[] = [
                'domain' => $domain->title,
                'score' => $sumMarks,
                'percentage' => $percentage,
                'consistency' => $consistency
            ];

            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'riasec',
                    'result_type' => 'domain',
                    'slug' => Str::slug($domain->title),
                ],
                [
                    'name' => $domain->title,
                    'slug' => Str::slug($domain->title),
                    'description' => $this->getEnhancedRiasecDescription($domain->title, $percentage),
                    'score' => $sumMarks,
                    'percentage' => $percentage,
                    'reliability_score' => $consistency,
                    'level' => $this->getIntuitiveLevelMapping($percentage),
                    'level_description' => $this->getCareerFocusedDescription($domain->title, $percentage),
                    'actionable_insights' => $this->getCareerInsights($domain->title, $percentage),
                ]
            );
        }

        // Enhanced career path analysis
        $this->computeCareerPathAnalysis($userId, $domainResults);
        $this->computeWorkEnvironmentPreferences($userId, $domainResults);
    }

    private function computeAdvancedCognitiveResults(Quiz $quiz, int $userId, $userAnswers): void
    {
        foreach ($quiz->domains as $domain) {
            $questionIds = $domain->questions->pluck('id')->all();
            $correct = 0;
            $totalMarks = 0;
            $difficultyScores = [];
            $responsePattern = [];

            foreach ($domain->questions as $question) {
                $ans = $userAnswers->get($question->id);
                $marks = $ans && is_numeric($ans->marks) ? (int)$ans->marks : 0;
                $correct += $marks;
                $totalMarks += $question->marks ?? 1;
                
                if ($marks > 0) {
                    $difficultyScores[] = $question->level_of_difficulty ?? 1;
                }
                $responsePattern[] = $marks > 0 ? 1 : 0;
            }

            $percentage = $totalMarks > 0 ? round(($correct / $totalMarks) * 100, 2) : 0;
            $averageDifficulty = !empty($difficultyScores) ? array_sum($difficultyScores) / count($difficultyScores) : 0;
            $abilityLevel = $this->calculateAbilityLevel($percentage, $averageDifficulty);
            $consistencyScore = $this->calculateCognitiveConsistency($responsePattern);

            UserResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'assessment_type' => 'cognitive',
                    'result_type' => 'domain',
                    'slug' => Str::slug($domain->title),
                ],
                [
                    'name' => $domain->title,
                    'slug' => Str::slug($domain->title),
                    'description' => $this->getEnhancedCognitiveDescription($domain->title, $percentage, $abilityLevel),
                    'score' => $correct,
                    'percentage' => $percentage,
                    // 'ability_level' => $abilityLevel,
                    'reliability_score' => $consistencyScore,
                    'level' => $this->getIntuitiveLevelMapping($percentage),
                    'level_description' => $this->getCognitiveStrengthDescription($domain->title, $percentage),
                    'actionable_insights' => $this->getCognitiveInsights($domain->title, $percentage, $abilityLevel),
                ]
            );
        }
    }

    // Helper methods remain the same but with proper typing...

    private function getIntuitiveLevelMapping(float $percentage): int
    {
        return match(true) {
            $percentage >= 85 => 5, // Exceptional
            $percentage >= 70 => 4, // High
            $percentage >= 40 => 3, // Average
            $percentage >= 20 => 2, // Below Average
            default => 1           // Low
        };
    }

    private function calculateReliability(array $responses): float
    {
        $n = count($responses);
        if ($n < 2) return 0.7; // Default reliability

        $mean = array_sum($responses) / $n;
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $responses)) / $n;
        
        // Simplified reliability estimate based on variance
        return min(1.0, max(0.5, 1 - ($variance / 10)));
    }

    private function calculateConsistency(array $responses): float
    {
        if (empty($responses)) return 0.7;
        
        $std = $this->standardDeviation($responses);
        $mean = array_sum($responses) / count($responses);
        
        // Lower coefficient of variation indicates higher consistency
        $cv = $mean > 0 ? $std / $mean : 1;
        return max(0.5, min(1.0, 1 - ($cv / 3)));
    }

    private function calculateCognitiveConsistency(array $pattern): float
    {
        if (empty($pattern)) return 0.7;
        
        // Look for patterns in correct/incorrect responses
        $streaks = [];
        $currentStreak = 1;
        
        for ($i = 1; $i < count($pattern); $i++) {
            if ($pattern[$i] === $pattern[$i-1]) {
                $currentStreak++;
            } else {
                $streaks[] = $currentStreak;
                $currentStreak = 1;
            }
        }
        $streaks[] = $currentStreak;
        
        $avgStreak = array_sum($streaks) / count($streaks);
        return min(1.0, max(0.5, $avgStreak / count($pattern)));
    }

    private function standardDeviation(array $values): float
    {
        if (count($values) < 2) return 0;
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / count($values);
        return sqrt($variance);
    }

    private function calculateAbilityLevel(float $percentage, float $averageDifficulty): string
    {
        if ($percentage >= 80 && $averageDifficulty >= 3) return 'Advanced';
        if ($percentage >= 70 && $averageDifficulty >= 2) return 'Proficient';
        if ($percentage >= 50) return 'Developing';
        return 'Foundational';
    }

    private function determineLearningStyle(float $openness, float $conscientiousness, float $extraversion): string
    {
        if ($openness >= 70 && $extraversion >= 60) return 'Collaborative Explorer';
        if ($openness >= 70) return 'Independent Explorer';
        if ($conscientiousness >= 70) return 'Structured Learner';
        if ($extraversion >= 70) return 'Social Learner';
        return 'Balanced Learner';
    }

    private function determineWorkStyle(float $extraversion, float $conscientiousness, float $agreeableness): string
    {
        if ($extraversion >= 70 && $agreeableness >= 70) return 'Team-Oriented Collaborator';
        if ($extraversion >= 70) return 'Dynamic Networker';
        if ($conscientiousness >= 70 && $agreeableness >= 70) return 'Supportive Organizer';
        if ($conscientiousness >= 70) return 'Independent Achiever';
        if ($agreeableness >= 70) return 'Harmonious Supporter';
        return 'Adaptable Professional';
    }

    private function getEnhancedDomainDescription(string $type, string $domain, float $percentage): string
    {
        $descriptions = [
            'ocean' => [
                'Openness' => "Your openness to experience score of {$percentage}% reflects your level of curiosity, creativity, and willingness to explore new ideas. This influences how you approach innovation, change, and learning opportunities.",
                'Conscientiousness' => "Your conscientiousness level of {$percentage}% indicates your organizational skills, reliability, and goal-directed behavior. This affects your work habits, time management, and achievement orientation.",
                'Extraversion' => "Your extraversion score of {$percentage}% shows your energy level in social situations and preference for external stimulation. This impacts your communication style and team collaboration approach.",
                'Agreeableness' => "Your agreeableness level of {$percentage}% reflects your cooperative nature, trust in others, and concern for harmonious relationships. This influences your teamwork and conflict resolution style.",
                'Neuroticism' => "Your emotional stability (100% - neuroticism) score of {$percentage}% indicates your resilience under stress and emotional regulation abilities. This affects how you handle pressure and uncertainty."
            ]
        ];

        return $descriptions[$type][$domain] ?? "Your {$domain} score is {$percentage}%";
    }

    private function getEnhancedRiasecDescription(string $domain, float $percentage): string
    {
        $descriptions = [
            'Realistic' => "Your {$percentage}% interest in Realistic activities indicates your preference for hands-on, practical work involving tools, machines, or outdoor activities. You likely enjoy concrete, tangible outcomes and working with your hands.",
            'Investigative' => "Your {$percentage}% interest in Investigative activities shows your preference for research, analysis, and intellectual problem-solving. You probably enjoy exploring ideas, conducting research, and understanding complex systems.",
            'Artistic' => "Your {$percentage}% interest in Artistic activities reflects your appreciation for creativity, self-expression, and aesthetic experiences. You likely value originality, imagination, and creative freedom in your work.",
            'Social' => "Your {$percentage}% interest in Social activities indicates your preference for helping, teaching, and working with people. You probably find fulfillment in making a positive impact on others' lives and facilitating growth.",
            'Enterprising' => "Your {$percentage}% interest in Enterprising activities shows your preference for leadership, persuasion, and business activities. You likely enjoy taking initiative, influencing others, and driving results.",
            'Conventional' => "Your {$percentage}% interest in Conventional activities reflects your preference for organized, systematic work with clear procedures. You probably value structure, accuracy, and attention to detail."
        ];

        return $descriptions[$domain] ?? "Your interest in {$domain} activities is {$percentage}%";
    }

    private function getEnhancedCognitiveDescription(string $domain, float $percentage, string $abilityLevel): string
    {
        return "Your performance in {$domain} is at {$percentage}%, indicating {$abilityLevel} level capabilities. This reflects your current proficiency in this cognitive area and your potential for tasks requiring these specific mental abilities.";
    }

    private function getActionableInsights(string $type, string $domain, float $percentage): string
    {
        $insights = [
            'ocean' => [
                'Openness' => match(true) {
                    $percentage >= 70 => "Leverage your creativity and curiosity by seeking roles in innovation, R&D, or strategic planning. Consider leading change initiatives and exploring new methodologies.",
                    $percentage >= 40 => "Balance your openness with practical application. Look for opportunities to contribute fresh perspectives while building on proven approaches.",
                    default => "Gradually expand your comfort zone by experimenting with new approaches in low-risk situations. Start with small changes and build confidence over time."
                },
                'Conscientiousness' => match(true) {
                    $percentage >= 70 => "Your strong organizational skills make you ideal for project management, quality assurance, and leadership roles. Share your planning expertise with others.",
                    $percentage >= 40 => "Continue developing systematic approaches to work. Implement planning tools and create structured workflows to enhance your effectiveness.",
                    default => "Focus on building better organizational habits. Start with daily planning, set clear priorities, and use tools to track progress and deadlines."
                },
                'Extraversion' => match(true) {
                    $percentage >= 70 => "Utilize your social energy in networking, team leadership, and client-facing roles. Consider mentoring others and facilitating group activities.",
                    $percentage >= 40 => "Balance social interaction with focused work time. Seek collaborative projects while ensuring you have space for individual contribution.",
                    default => "Build social skills gradually through structured interactions. Practice active listening and contribute to team discussions at your own pace."
                },
                'Agreeableness' => match(true) {
                    $percentage >= 70 => "Your collaborative nature is valuable in team environments, customer service, and conflict resolution. Consider roles in HR, counseling, or team facilitation.",
                    $percentage >= 40 => "Balance cooperation with assertiveness. Practice expressing your views while maintaining positive relationships.",
                    default => "Work on building trust with colleagues. Focus on finding common ground and developing your empathy and perspective-taking skills."
                },
                'Neuroticism' => match(true) {
                    $percentage <= 30 => "Your emotional stability is a significant strength. Consider high-pressure roles and help others develop stress management techniques.",
                    $percentage <= 60 => "Continue building resilience through stress management techniques, mindfulness, and maintaining work-life balance.",
                    default => "Focus on developing coping strategies for stress. Practice relaxation techniques, seek support when needed, and build emotional regulation skills."
                }
            ]
        ];

        return $insights[$type][$domain] ?? "Continue developing your {$domain} skills through targeted practice and reflection.";
    }

    // Continue with all other helper methods as they were, but ensure proper type hinting...
    // (The rest of the methods remain the same as in the previous code)

    private function getFacetInsights(string $facetName, float $percentage, string $domainName): string
    {
        return match(true) {
            $percentage >= 70 => "Your high level in {$facetName} is a significant strength. Consider how to leverage this in leadership opportunities and help others develop in this area.",
            $percentage >= 40 => "Your moderate level in {$facetName} provides good balance. Focus on applying this strength consistently while continuing to develop further.",
            default => "Your {$facetName} scores suggest room for growth. Consider specific strategies and practices to develop this aspect of your {$domainName}."
        };
    }

    private function getCcsInsights(string $skill, float $percentage, string $workplaceApplication): string
    {
        $developmentLevel = match(true) {
            $percentage >= 80 => "exceptional",
            $percentage >= 65 => "strong", 
            $percentage >= 45 => "developing",
            default => "emerging"
        };

        return "Your {$developmentLevel} {$skill} capability shows great potential. {$workplaceApplication}. " . 
               match($developmentLevel) {
                   "exceptional" => "Consider mentoring others and leading initiatives in this area.",
                   "strong" => "Look for opportunities to apply and showcase this strength in your role.",
                   "developing" => "Continue building this skill through practice and targeted development activities.",
                   "emerging" => "Focus on foundational skill-building through training, practice, and seeking feedback."
               };
    }

    private function getDerivedMetricDescription(string $type, $value): string
    {
        return match($type) {
            'growth' => is_numeric($value) ? "Growth potential: " . ($value >= 70 ? "High" : ($value >= 45 ? "Moderate" : "Developing")) : $value,
            'fit' => is_numeric($value) ? "Organizational fit: " . ($value >= 70 ? "Excellent" : ($value >= 45 ? "Good" : "Developing")) : $value,
            'retention' => is_numeric($value) ? "Retention likelihood: " . ($value >= 70 ? "High" : ($value >= 45 ? "Moderate" : "Variable")) : $value,
            'leadership' => is_numeric($value) ? "Leadership potential: " . ($value >= 70 ? "Strong" : ($value >= 45 ? "Developing" : "Emerging")) : $value,
            'innovation' => is_numeric($value) ? "Innovation capacity: " . ($value >= 70 ? "High" : ($value >= 45 ? "Moderate" : "Developing")) : $value,
            'learning' => "Learning style: {$value}",
            'work_style' => "Work style: {$value}",
            default => $value
        };
    }

    private function getDerivedMetricInsights(string $type, $value, float $score): string
    {
        return match($type) {
            'growth' => match(true) {
                $score >= 70 => "Your high growth potential suggests excellent capacity for learning and adaptation. Seek challenging assignments and continuous learning opportunities.",
                $score >= 45 => "Your moderate growth potential indicates good learning capacity. Focus on specific skill development areas for maximum impact.",
                default => "Focus on building foundational learning habits and gradually expanding your comfort zone with new challenges."
            },
            'leadership' => match(true) {
                $score >= 70 => "Your strong leadership potential suggests readiness for management roles. Consider formal leadership development and mentoring opportunities.",
                $score >= 45 => "Your developing leadership potential can be enhanced through team projects and gradual responsibility increases.",
                default => "Build leadership foundations through self-awareness, communication skills, and collaborative project experience."
            },
            'innovation' => match(true) {
                $score >= 70 => "Your high innovation index suggests strong creative thinking abilities. Seek roles in R&D, strategic planning, or change management.",
                $score >= 45 => "Your moderate innovation capability can be enhanced through creative problem-solving practice and diverse experiences.",
                default => "Develop innovation skills through brainstorming practice, diverse perspectives, and creative thinking techniques."
            },
            'learning' => "Your {$value} learning style suggests you learn best through " . match($value) {
                'Collaborative Explorer' => "interactive discovery with others, hands-on experimentation, and group learning activities.",
                'Independent Explorer' => "self-directed investigation, research-based learning, and exploring diverse resources.",
                'Structured Learner' => "organized curricula, step-by-step progression, and systematic skill-building.",
                'Social Learner' => "group discussions, peer learning, and collaborative knowledge construction.",
                default => "a flexible approach that combines different learning methods based on the situation."
            },
            'work_style' => "Your {$value} work style indicates you thrive in " . match($value) {
                'Team-Oriented Collaborator' => "collaborative environments with strong team dynamics and shared goals.",
                'Dynamic Networker' => "fast-paced, socially rich environments with varied interactions and networking opportunities.",
                'Supportive Organizer' => "structured, harmonious environments where you can help organize and support others.",
                'Independent Achiever' => "autonomous environments with clear goals and minimal supervision.",
                'Harmonious Supporter' => "cooperative, people-focused environments with emphasis on team harmony.",
                default => "flexible environments that allow you to adapt your style to different situations."
            },
            default => "Continue developing in this area through focused effort and practice."
        };
    }

    private function computeCareerPathAnalysis(int $userId, array $domainResults): void
    {
        usort($domainResults, fn($a, $b) => $b['score'] <=> $a['score']);
        $top3 = array_slice($domainResults, 0, 3);
        
        $hollandCode = collect($top3)
            ->map(fn($res) => strtoupper(substr($res['domain'], 0, 1)))
            ->implode('');

        $careerPaths = $this->getDetailedCareerPaths($hollandCode, $top3);
        $skillDevelopment = $this->getSkillDevelopmentRecommendations($domainResults);

        UserResult::updateOrCreate(
            [
                'user_id' => $userId,
                'assessment_type' => 'riasec',
                'result_type' => 'career_analysis',
            ],
            [
                'name' => "Career Path Analysis",
                'slug' => 'career-analysis',
                'description' => "Based on your interest profile ({$hollandCode}), here are personalized career insights and development recommendations.",
                'score' => 0,
                'percentage' => 0,
                'level_description' => implode('; ', $careerPaths),
                'actionable_insights' => implode(' | ', $skillDevelopment),
            ]
        );
    }

    private function getDetailedCareerPaths(string $code, array $top3): array
    {
        // Comprehensive career mapping covering all 20 unique RIASEC type-set combinations.
        // The lookup is permutation-aware, so any ordering of the same 3 letters finds the match.
        $careerMapping = [
            // R, I, A
            'RIA' => ['Software Engineer', 'Research Scientist', 'Product Developer', 'Technical Analyst'],
            // R, I, S
            'RIS' => ['Biomedical Technician', 'Environmental Scientist', 'Occupational Health Specialist', 'Veterinary Technologist'],
            // R, I, E
            'RIE' => ['Engineering Manager', 'Technical Project Manager', 'Operations Engineer', 'Manufacturing Supervisor'],
            // R, I, C
            'RIC' => ['Quality Assurance Specialist', 'Technical Inspector', 'Laboratory Technician', 'Process Engineer'],
            // R, A, S
            'RAS' => ['Occupational Therapist', 'Athletic Trainer', 'Environmental Educator', 'Landscape Architect'],
            // R, A, E
            'RAE' => ['Construction Manager', 'Landscape Contractor', 'Technical Sales', 'Interior Designer'],
            // R, A, C
            'RAC' => ['Drafter', 'Cartographer', 'Surveyor', 'Architectural Technician'],
            // R, S, E
            'RSE' => ['Fitness Center Manager', 'Emergency Services Coordinator', 'Safety Inspector', 'Military Officer'],
            // R, S, C
            'RSC' => ['Medical Lab Technician', 'Pharmacy Technician', 'Dental Hygienist', 'Dietetic Technician'],
            // R, E, C
            'REC' => ['Logistics Manager', 'Supply Chain Specialist', 'Facilities Manager', 'Transportation Coordinator'],
            // I, A, S
            'IAS' => ['Clinical Researcher', 'Educational Researcher', 'Psychologist', 'Anthropologist'],
            // I, A, E
            'IAE' => ['Management Consultant', 'Technology Analyst', 'Strategic Planner', 'Innovation Director'],
            // I, A, C
            'IAC' => ['Financial Analyst', 'Market Researcher', 'Statistician', 'Actuary'],
            // I, S, E
            'ISE' => ['Healthcare Manager', 'Research Program Director', 'Clinical Trial Manager', 'Public Health Administrator'],
            // I, S, C
            'ISC' => ['Medical Records Specialist', 'Epidemiologist', 'Health Informatics Specialist', 'Lab Manager'],
            // I, E, C
            'IEC' => ['Data Analyst', 'Business Intelligence Analyst', 'Quantitative Analyst', 'Risk Manager'],
            // A, S, E
            'ASE' => ['Communications Director', 'Advertising Manager', 'Media Producer', 'Public Relations Manager'],
            // A, S, C
            'ASC' => ['Library Curator', 'Archivist', 'Museum Curator', 'Heritage Conservation Officer'],
            // A, E, C
            'AEC' => ['Art Director', 'Advertising Executive', 'Fashion Merchandiser', 'Publishing Manager'],
            // S, E, C
            'SEC' => ['Office Manager', 'Executive Assistant', 'Community Services Manager', 'Non-profit Administrator'],
        ];

        // Permutation-aware lookup: try all orderings of the 3·letter code
        $careers = null;
        if (isset($careerMapping[$code])) {
            $careers = $careerMapping[$code];
        } else {
            $letters = str_split($code);
            $permutations = $this->getPermutations($letters);
            foreach ($permutations as $perm) {
                $key = implode('', $perm);
                if (isset($careerMapping[$key])) {
                    $careers = $careerMapping[$key];
                    break;
                }
            }
        }

        if (!$careers) {
            $careers = ['Explore careers combining ' . implode(', ', array_column($top3, 'domain'))];
        }

        return array_slice($careers, 0, 4); // Limit to top 4 suggestions
    }

    private function getPermutations(array $items): array
    {
        if (count($items) <= 1) {
            return [$items];
        }

        $result = [];
        foreach ($items as $key => $item) {
            $remaining = $items;
            unset($remaining[$key]);
            foreach ($this->getPermutations(array_values($remaining)) as $perm) {
                $result[] = array_merge([$item], $perm);
            }
        }
        return $result;
    }

    private function getSkillDevelopmentRecommendations(array $domainResults): array
    {
        $recommendations = [];
        
        foreach ($domainResults as $result) {
            if ($result['percentage'] < 60) {
                $domain = $result['domain'];
                $recommendations[] = match($domain) {
                    'Realistic' => 'Develop hands-on technical skills through workshops or apprenticeships',
                    'Investigative' => 'Enhance analytical skills through data analysis courses or research projects',
                    'Artistic' => 'Build creative skills through art classes, design workshops, or creative writing',
                    'Social' => 'Strengthen interpersonal skills through volunteering, mentoring, or communication training',
                    'Enterprising' => 'Develop leadership and business skills through management courses or leadership roles',
                    'Conventional' => 'Improve organizational skills through project management training or systems thinking',
                    default => "Develop {$domain} skills through targeted learning and practice"
                };
            }
        }

        return array_slice($recommendations, 0, 3); // Limit to top 3 recommendations
    }

    private function computeWorkEnvironmentPreferences(int $userId, array $domainResults): void
    {
        $topDomains = array_slice($domainResults, 0, 3);
        $environments = $this->getOptimalWorkEnvironments($topDomains);
        $teamStyles = $this->getPreferredTeamStyles($topDomains);

        UserResult::updateOrCreate(
            [
                'user_id' => $userId,
                'assessment_type' => 'riasec',
                'result_type' => 'work_environment',
            ],
            [
                'name' => "Work Environment Preferences",
                'slug' => 'work-environment',
                'description' => "Your ideal work environments and team dynamics based on your interest profile.",
                'score' => 0,
                'percentage' => 0,
                'level_description' => implode('; ', $environments),
                'actionable_insights' => implode(' | ', $teamStyles),
            ]
        );
    }

    private function getOptimalWorkEnvironments(array $topDomains): array
    {
        $environments = [];
        
        foreach ($topDomains as $domain) {
            $environments[] = match($domain['domain']) {
                'Realistic' => 'Hands-on workshops, technical facilities, outdoor settings',
                'Investigative' => 'Research laboratories, quiet analytical spaces, libraries',
                'Artistic' => 'Creative studios, flexible open spaces, inspiring environments',
                'Social' => 'Collaborative spaces, community centers, people-focused settings',
                'Enterprising' => 'Dynamic offices, meeting rooms, networking environments',
                'Conventional' => 'Organized offices, structured settings, systematic workflows'
            };
        }

        return array_unique($environments);
    }

    private function getPreferredTeamStyles(array $topDomains): array
    {
        $styles = [];
        
        foreach ($topDomains as $domain) {
            $styles[] = match($domain['domain']) {
                'Realistic' => 'Practical, task-focused teams with clear objectives',
                'Investigative' => 'Analytical teams that value expertise and thorough research',
                'Artistic' => 'Creative, innovative teams that encourage original thinking',
                'Social' => 'Supportive, collaborative teams focused on helping others',
                'Enterprising' => 'Results-driven teams with competitive energy and leadership',
                'Conventional' => 'Well-organized teams with clear processes and procedures'
            };
        }

        return array_unique($styles);
    }

    // Additional helper methods for UI descriptions...

    private function getPersonalizedLevelDescription(string $type, string $domain, float $percentage): string
    {
        $level = $this->getIntuitiveLevelMapping($percentage);
        
        return match($level) {
            5 => "Exceptional - You demonstrate outstanding {$domain} characteristics that set you apart",
            4 => "High - Your {$domain} is a notable strength that serves you well",
            3 => "Average - Your {$domain} level is well-balanced and typical",
            2 => "Below Average - Your {$domain} has room for development and growth",
            1 => "Low - Consider this an opportunity area for focused development in {$domain}"
        };
    }

    private function getCareerFocusedDescription(string $domain, float $percentage): string
    {
        return match(true) {
            $percentage >= 70 => "Strong interest - This is likely a core motivator in your career choices and satisfaction",
            $percentage >= 50 => "Moderate interest - This area may provide some career satisfaction but isn't a primary driver",
            $percentage >= 30 => "Limited interest - This area is less likely to provide long-term career satisfaction",
            default => "Minimal interest - Career paths in this area may not align well with your natural preferences"
        };
    }

    private function getCognitiveStrengthDescription(string $domain, float $percentage): string
    {
        return match(true) {
            $percentage >= 80 => "Exceptional cognitive strength - You excel in {$domain} tasks and can handle complex challenges",
            $percentage >= 65 => "Strong cognitive ability - You perform well in {$domain} and can tackle most related challenges",
            $percentage >= 45 => "Average cognitive ability - You have solid foundational skills in {$domain}",
            $percentage >= 25 => "Developing cognitive ability - With practice, you can improve your {$domain} performance",
            default => "Emerging cognitive ability - Focus on building fundamental {$domain} skills"
        };
    }

    private function getFacetLevelDescription(string $facetName, float $percentage): string
    {
        return match(true) {
            $percentage >= 75 => "Very High - Your {$facetName} is exceptionally well-developed",
            $percentage >= 60 => "High - Your {$facetName} is a clear strength",
            $percentage >= 40 => "Moderate - Your {$facetName} is reasonably well-developed",
            $percentage >= 25 => "Below Average - Your {$facetName} could benefit from development",
            default => "Low - Consider this an important growth area for {$facetName}"
        };
    }

    private function getCcsLevelDescription(string $skill, float $percentage): string
    {
        return match(true) {
            $percentage >= 80 => "Mastery Level - You demonstrate exceptional {$skill} capabilities",
            $percentage >= 65 => "Proficient Level - You show strong {$skill} abilities", 
            $percentage >= 45 => "Developing Level - Your {$skill} is progressing well",
            $percentage >= 25 => "Foundational Level - You're building basic {$skill} capabilities",
            default => "Emerging Level - Focus on developing fundamental {$skill} skills"
        };
    }

    private function getCareerInsights(string $domain, float $percentage): string
    {
        $insights = [
            'Realistic' => match(true) {
                $percentage >= 70 => "Pursue hands-on careers in engineering, skilled trades, or technical fields. You'll thrive in roles with tangible outcomes.",
                $percentage >= 40 => "Consider careers that blend practical work with other interests. Technical consulting or field supervision might appeal to you.",
                default => "While hands-on work isn't your primary interest, developing some technical skills can complement your other strengths."
            },
            'Investigative' => match(true) {
                $percentage >= 70 => "Excel in research, analysis, or scientific careers. You're naturally suited for roles requiring deep thinking and problem-solving.",
                $percentage >= 40 => "Your analytical nature can enhance many career paths. Consider roles with research components or data analysis aspects.",
                default => "While pure research may not appeal to you, developing analytical thinking can strengthen your other career interests."
            },
            'Artistic' => match(true) {
                $percentage >= 70 => "Pursue creative careers in arts, design, writing, or innovation. Your creativity is a valuable asset in any field.",
                $percentage >= 40 => "Integrate creative elements into your work whenever possible. Look for roles that value innovation and original thinking.",
                default => "While creativity isn't your main drive, developing creative problem-solving skills can enhance your effectiveness."
            },
            'Social' => match(true) {
                $percentage >= 70 => "Thrive in people-focused careers like education, healthcare, counseling, or human resources. Your desire to help others is a core strength.",
                $percentage >= 40 => "Seek roles with meaningful people interaction. Even in technical fields, look for opportunities to mentor or support others.",
                default => "While people-focus isn't primary for you, developing interpersonal skills will enhance your career effectiveness."
            },
            'Enterprising' => match(true) {
                $percentage >= 70 => "Excel in leadership, sales, business, or entrepreneurial roles. Your drive to influence and lead is a significant career asset.",
                $percentage >= 40 => "Look for advancement opportunities and roles with leadership potential. Your enterprising nature can drive career growth.",
                default => "While leadership may not be your main interest, developing influence skills can support your other career goals."
            },
            'Conventional' => match(true) {
                $percentage >= 70 => "Thrive in organized, structured environments like finance, administration, or operations. Your attention to detail is highly valuable.",
                $percentage >= 40 => "Appreciate structure and systems in your work. You can bring valuable organizational skills to any career path.",
                default => "While structure isn't your main preference, developing organizational skills can improve your overall effectiveness."
            }
        ];

        return $insights[$domain] ?? "Continue exploring how {$domain} interests can enhance your career path.";
    }

    private function getCognitiveInsights(string $domain, float $percentage, string $abilityLevel): string
    {
        return match($abilityLevel) {
            'Advanced' => "Your {$abilityLevel} {$domain} abilities position you well for complex, challenging roles requiring high cognitive demands.",
            'Proficient' => "Your {$abilityLevel} {$domain} skills provide a solid foundation for most career requirements in related areas.",
            'Developing' => "Your {$abilityLevel} {$domain} abilities show good potential. Continue building these skills through practice and learning.",
            'Foundational' => "Focus on strengthening your {$domain} fundamentals through targeted learning and skill-building activities."
        };
    }
}


