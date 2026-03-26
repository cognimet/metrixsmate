<?php

namespace App\Services;

use App\Models\DynamicSchool;
use App\Models\SchoolRecommendation;
use App\Models\UserResult;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AISchoolRankingService
{
    private $groqApiKey;
    private const RIASEC_TYPES = ['realistic', 'investigative', 'artistic', 'social', 'enterprising', 'conventional'];
    private const OCEAN_TRAITS = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];

    public function __construct()
    {
        $this->groqApiKey = config('services.groq.api_key');
    }

    /**
     * Rank schools based on user assessment results
     */
    public function rankSchools(User $user, Collection $schools, $city): Collection
    {
        // Get user assessment results
        $riasecResults = $this->getUserRiasecResults($user);
        $cognitiveResults = $this->getUserCognitiveResults($user);
        $oceanResults = $this->getUserOceanResults($user);

        $ranked = collect();

        foreach ($schools as $school) {
            $scores = $this->calculateSchoolScores(
                $school,
                $riasecResults,
                $cognitiveResults,
                $oceanResults,
                $user,
                $city
            );

            // Calculate overall compatibility score
            $overallScore = $this->calculateOverallScore($scores);

            $recommendation = new SchoolRecommendation([
                'user_id' => $user->id,
                'dynamic_school_id' => $school->id,
                'city_id' => $city->id,
                'riasec_match_score' => $scores['riasec'],
                'cognitive_match_score' => $scores['cognitive'],
                'ocean_match_score' => $scores['ocean'],
                'academic_performance_score' => $scores['academic'],
                'location_proximity_score' => $scores['proximity'],
                'compatibility_score' => $overallScore,
                'match_factors' => $scores['factors'],
                'strengths_alignment' => $scores['strengths_alignment'],
                'personality_alignment' => $scores['personality_alignment'],
            ]);

            $ranked->push([
                'school' => $school,
                'recommendation' => $recommendation,
                'scores' => $scores,
                'overall_score' => $overallScore,
            ]);
        }

        // Sort by overall score
        $ranked = $ranked->sortByDesc('overall_score')
                         ->values()
                         ->map(function ($item, $index) {
                             $item['recommendation']['rank'] = $index + 1;
                             return $item;
                         });

        return $ranked;
    }

    /**
     * Calculate individual scores for a school
     */
    private function calculateSchoolScores(
        DynamicSchool $school,
        array $riasecResults,
        array $cognitiveResults,
        array $oceanResults,
        User $user,
        $city
    ): array {
        return [
            'riasec' => $this->calculateRiasecMatch($school, $riasecResults),
            'cognitive' => $this->calculateCognitiveMatch($school, $cognitiveResults),
            'ocean' => $this->calculateOceanMatch($school, $oceanResults),
            'academic' => $this->calculateAcademicPerformance($school),
            'proximity' => $this->calculateLocationProximity($school, $user, $city),
            'factors' => [],
            'strengths_alignment' => [],
            'personality_alignment' => [],
        ];
    }

    /**
     * Get user's RIASEC results
     */
    private function getUserRiasecResults(User $user): array
    {
        $results = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->get();

        $riasec = [];
        foreach (self::RIASEC_TYPES as $type) {
            $result = $results->firstWhere('name', ucfirst($type));
            $riasec[$type] = $result->percentage ?? 0;
        }

        return $riasec;
    }

    /**
     * Get user's Cognitive results
     */
    private function getUserCognitiveResults(User $user): array
    {
        $results = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'cognitive')
            ->where('result_type', 'domain')
            ->get()
            ->pluck('percentage', 'name')
            ->toArray();

        return $results;
    }

    /**
     * Get user's OCEAN results
     */
    private function getUserOceanResults(User $user): array
    {
        $results = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'domain')
            ->get();

        $ocean = [];
        foreach (self::OCEAN_TRAITS as $trait) {
            $result = $results->firstWhere('name', ucfirst($trait));
            $ocean[$trait] = $result->percentage ?? 0;
        }

        return $ocean;
    }

    /**
     * Calculate RIASEC match score
     */
    private function calculateRiasecMatch(DynamicSchool $school, array $riasecResults): float
    {
        $typeMapping = [
            'mainstream' => ['conventional' => 0.6, 'social' => 0.5, 'investigative' => 0.4],
            'stem' => ['investigative' => 0.9, 'realistic' => 0.7, 'conventional' => 0.5],
            'arts' => ['artistic' => 0.9, 'social' => 0.6, 'enterprising' => 0.5],
            'technical' => ['realistic' => 0.9, 'investigative' => 0.7, 'conventional' => 0.6],
            'business' => ['enterprising' => 0.9, 'conventional' => 0.7, 'social' => 0.6],
            'social' => ['social' => 0.9, 'artistic' => 0.6, 'enterprising' => 0.5],
        ];

        $schoolType = $school->type ?? 'mainstream';
        $weights = $typeMapping[$schoolType] ?? $typeMapping['mainstream'];

        $score = 0;
        $totalWeight = 0;

        foreach ($weights as $type => $weight) {
            $score += ($riasecResults[$type] ?? 0) * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round(($score / $totalWeight), 2) : 0;
    }

    /**
     * Calculate Cognitive match score
     */
    private function calculateCognitiveMatch(DynamicSchool $school, array $cognitiveResults): float
    {
        // Schools with specializations match specific cognitive profiles
        $avgCognitive = count($cognitiveResults) > 0 ? array_sum($cognitiveResults) / count($cognitiveResults) : 50;

        // Base score from average cognitive level
        $score = min(100, $avgCognitive * 1.2);

        // Bonus if school specialization aligns with cognitive strengths
        if (!empty($school->specializations)) {
            foreach ($school->specializations as $spec) {
                if (stripos($spec, 'advanced') !== false || stripos($spec, 'ib') !== false) {
                    $score = min(100, $score + 10);
                }
            }
        }

        return round($score, 2);
    }

    /**
     * Calculate OCEAN match score
     */
    private function calculateOceanMatch(DynamicSchool $school, array $oceanResults): float
    {
        $score = 50; // Baseline

        // High conscientiousness: schools with strong academics
        if (($oceanResults['conscientiousness'] ?? 0) > 70) {
            if ($school->rating > 4.0) {
                $score += 15;
            }
        }

        // High openness: schools with diverse programs
        if (($oceanResults['openness'] ?? 0) > 70) {
            if (!empty($school->programs) && count($school->programs) > 2) {
                $score += 15;
            }
        }

        // High extraversion: schools with social programs
        if (($oceanResults['extraversion'] ?? 0) > 70) {
            if (!empty($school->facilities) && collect($school->facilities)->contains(function ($f) {
                return stripos($f, 'club') !== false || stripos($f, 'activity') !== false;
            })) {
                $score += 15;
            }
        }

        return round(min(100, $score), 2);
    }

    /**
     * Calculate academic performance score
     */
    private function calculateAcademicPerformance(DynamicSchool $school): float
    {
        $score = 0;

        // Rating is most important for academic performance
        $score += ($school->rating / 5) * 60;

        // Verification increases confidence
        if ($school->is_verified) {
            $score += 15;
        }

        // Strong facilities indicate better academics
        if (!empty($school->facilities) && count($school->facilities) >= 3) {
            $score += 15;
        }

        // Recent data indicates current performance
        if ($school->last_refreshed_at && $school->last_refreshed_at->diffInDays(now()) < 30) {
            $score += 10;
        }

        return round(min(100, $score), 2);
    }

    /**
     * Calculate location proximity score
     */
    private function calculateLocationProximity(DynamicSchool $school, User $user, $city): float
    {
        // Same city
        if ($school->city_id === $city->id) {
            $score = 90;

            // If coordinates available, calculate distance
            if ($school->latitude && $school->longitude && $user->latitude && $user->longitude) {
                $distance = $this->haversineDistance(
                    $user->latitude,
                    $user->longitude,
                    $school->latitude,
                    $school->longitude
                );

                // Reduce score based on distance (5km is ideal)
                if ($distance < 5) {
                    $score = 100;
                } elseif ($distance < 10) {
                    $score = 95;
                } elseif ($distance < 15) {
                    $score = 85;
                } else {
                    $score = max(50, 100 - ($distance / 100));
                }
            }

            return round($score, 2);
        }

        // Same state
        if ($school->state_id === $city->state_id) {
            return 60.0;
        }

        // Same country
        if ($school->country_id === $city->state->country_id) {
            return 40.0;
        }

        return 20.0;
    }

    /**
     * Haversine distance formula
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earth_radius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earth_radius * $c;

        return $distance;
    }

    /**
     * Calculate overall compatibility score
     */
    private function calculateOverallScore(array $scores): float
    {
        // Weighted average of all scores
        $weights = [
            'riasec' => 0.35,      // Career interests are most important
            'cognitive' => 0.20,   // Learning style
            'ocean' => 0.15,       // Personality
            'academic' => 0.20,    // School quality
            'proximity' => 0.10,   // Location
        ];

        $totalScore = 0;
        foreach ($weights as $key => $weight) {
            $totalScore += ($scores[$key] ?? 0) * $weight;
        }

        return round($totalScore, 2);
    }

    /**
     * Generate AI-powered reasoning for recommendations
     */
    public function generateRecommendationReasoning(
        SchoolRecommendation $recommendation,
        User $user
    ): string {
        try {
            $userProfile = $this->buildUserProfileForAI($user);
            $schoolProfile = $this->buildSchoolProfileForAI($recommendation->dynamicSchool);
            $scores = $recommendation->getScoreBreakdown();

            $prompt = $this->buildReasoningPrompt($userProfile, $schoolProfile, $scores);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->groqApiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an educational counselor. Provide concise, personalized recommendations.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.6,
                'max_tokens' => 300,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Exception $e) {
            Log::warning("AI reasoning generation failed: {$e->getMessage()}");
        }

        return $this->generateFallbackReasoning($recommendation);
    }

    /**
     * Build user profile for AI reasoning
     */
    private function buildUserProfileForAI(User $user): string
    {
        $riasec = $this->getUserRiasecResults($user);
        $topRiasec = array_keys(array_slice(array_filter($riasec), 0, 2, true));

        $cognitive = $this->getUserCognitiveResults($user);
        $topCognitive = array_keys($cognitive)[0] ?? 'Unknown';

        $ocean = $this->getUserOceanResults($user);
        $topOcean = array_keys(array_slice(array_filter($ocean), 0, 1, true))[0] ?? 'Unknown';

        return "Student Profile: Top interests are " . implode(' and ', $topRiasec) . 
               ". Learning style: $topCognitive. Personality: $topOcean.";
    }

    /**
     * Build school profile for AI reasoning
     */
    private function buildSchoolProfileForAI(DynamicSchool $school): string
    {
        $highlights = [];

        if ($school->rating >= 4.5) {
            $highlights[] = "Highly rated ({$school->rating}/5)";
        }

        if (!empty($school->specializations)) {
            $highlights[] = "Specializes in " . implode(', ', array_slice($school->specializations, 0, 2));
        }

        if (!empty($school->strengths)) {
            $highlights[] = "Known for " . implode(', ', array_slice($school->strengths, 0, 2));
        }

        return "School: {$school->name} ({$school->type}). " . implode(". ", $highlights) . ".";
    }

    /**
     * Build reasoning prompt
     */
    private function buildReasoningPrompt(string $userProfile, string $schoolProfile, array $scores): string
    {
        return <<<PROMPT
$userProfile

$schoolProfile

Match scores: RIASEC alignment {$scores['riasec']}%, Cognitive alignment {$scores['cognitive']}%, 
Personality alignment {$scores['ocean']}%, Academic quality {$scores['academic_performance']}%, 
Location proximity {$scores['location_proximity']}%.

Provide 2-3 sentences explaining why this school is a good fit for this student. 
Be specific about how the school matches their interests and learning style.
PROMPT;
    }

    /**
     * Generate fallback reasoning if AI fails
     */
    private function generateFallbackReasoning(SchoolRecommendation $recommendation): string
    {
        $school = $recommendation->dynamicSchool;
        $scores = $recommendation->getScoreBreakdown();

        $reasons = [];

        if ($scores['riasec_match'] > 75) {
            $reasons[] = "Strong alignment with your career interests";
        }

        if ($scores['academic_performance'] > 80) {
            $reasons[] = "Excellent academic reputation and facilities";
        }

        if ($scores['location_proximity'] > 80) {
            $reasons[] = "Conveniently located";
        }

        if ($scores['cognitive_match'] > 75) {
            $reasons[] = "Well-suited to your learning style";
        }

        if (empty($reasons)) {
            $reasons[] = "Good overall match for your profile";
        }

        return implode(". ", $reasons) . ". " . $school->name . " offers programs and facilities that align well with your background.";
    }

    /**
     * Get match factors for display
     */
    public function getMatchFactors(SchoolRecommendation $recommendation): array
    {
        $factors = [];
        $scores = $recommendation->getScoreBreakdown();

        if ($scores['riasec_match'] > 70) {
            $factors[] = ["type" => "interests", "value" => "Aligns with your career interests"];
        }

        if ($scores['cognitive_match'] > 70) {
            $factors[] = ["type" => "learning", "value" => "Matches your learning style"];
        }

        if ($scores['ocean_match'] > 70) {
            $factors[] = ["type" => "personality", "value" => "Suits your personality traits"];
        }

        if ($scores['academic_performance'] > 70) {
            $factors[] = ["type" => "quality", "value" => "High academic standards"];
        }

        return $factors;
    }
}
