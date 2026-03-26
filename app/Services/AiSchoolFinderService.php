<?php

namespace App\Services;

use App\Models\School;
use App\Models\UserResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSchoolFinderService
{
    private $apiKey;
    private $apiEndpoint = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    /**
     * Get AI-powered school recommendations
     */
    public function getRecommendations($user, $city, $schools, $riasecDomains, $cognitiveDomains, $oceanDomains): array
    {
        // Build user profile context
        $userProfile = $this->buildUserProfile($riasecDomains, $cognitiveDomains, $oceanDomains);

        // Build schools context
        $schoolsContext = $this->buildSchoolsContext($schools);

        // Generate AI recommendations
        $recommendations = $this->callAiModel($userProfile, $schoolsContext, $schools);

        return $recommendations;
    }

    /**
     * Build comprehensive user profile from assessment results
     */
    private function buildUserProfile($riasecDomains, $cognitiveDomains, $oceanDomains): string
    {
        $topRiasec = $riasecDomains->sortByDesc('percentage')->take(3);
        $topCognitive = $cognitiveDomains->sortByDesc('percentage')->first();
        $topOcean = $oceanDomains->sortByDesc('percentage')->first();

        $profile = "User Assessment Profile:\n\n";

        // RIASEC Profile
        $profile .= "Career Interests (Holland Code - RIASEC):\n";
        foreach ($topRiasec as $domain) {
            $profile .= "- {$domain->name}: {$domain->percentage}%\n";
        }

        // Cognitive Profile
        $profile .= "\nLearning Style (Cognitive Abilities):\n";
        $profile .= "- Primary: {$topCognitive->name} ({$topCognitive->percentage}%)\n";

        // Personality Profile
        $profile .= "\nPersonality Traits (OCEAN):\n";
        $profile .= "- Dominant Trait: {$topOcean->name} ({$topOcean->percentage}%)\n";

        $profile .= "\nContext: This student should be matched with schools that align with their interests, learning style, and personality.";

        return $profile;
    }

    /**
     * Build schools data context
     */
    private function buildSchoolsContext($schools): string
    {
        $context = "Available Schools:\n\n";

        foreach ($schools as $school) {
            $context .= "School: {$school->name}\n";
            $context .= "- Type: {$school->type}\n";
            $context .= "- Board: {$school->board}\n";
            $context .= "- Rating: {$school->rating}/5\n";
            $context .= "- Strengths: " . implode(", ", $school->strengths ?? []) . "\n";
            $context .= "- Facilities: " . implode(", ", $school->facilities ?? []) . "\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Call Groq AI API for recommendations
     */
    private function callAiModel($userProfile, $schoolsContext, $schools)
    {
        $systemPrompt = "You are an expert educational counselor. Based on the student's assessment profile and available schools, provide personalized school recommendations. For each school, explain why it's suitable for the student. Be concise and focus on the best 3-5 matches.";

        $userPrompt = $userProfile . "\n\n" . $schoolsContext . 
                     "\n\nBased on this student's profile and the available schools, provide your top 3-5 school recommendations with brief explanations (2-3 sentences each) for why each school is suitable. Output as JSON array with structure: [{\"name\": \"School Name\", \"score\": 95, \"reason\": \"explanation\"}]";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->failed()) {
                Log::error('Groq API Error', ['response' => $response->json()]);
                return $this->fallbackRanking($schools);
            }

            $content = $response->json('choices.0.message.content');

            // Extract JSON from response
            $jsonMatch = preg_match('/\[.*\]/s', $content, $matches);
            if ($jsonMatch) {
                $recommendations = json_decode($matches[0], true);
                
                if ($recommendations && is_array($recommendations)) {
                    return $this->enrichRecommendations($recommendations, $schools);
                }
            }

            return $this->fallbackRanking($schools);
        } catch (\Exception $e) {
            Log::error('AiSchoolFinder Service Error', ['error' => $e->getMessage()]);
            return $this->fallbackRanking($schools);
        }
    }

    /**
     * Enrich AI recommendations with full school data
     */
    private function enrichRecommendations($recommendations, $schools): array
    {
        $enriched = [];

        foreach ($recommendations as $rec) {
            $school = $schools->firstWhere('name', $rec['name']);
            
            if ($school) {
                $enriched[] = [
                    'school' => $school,
                    'compatibility_score' => $rec['score'] ?? 90,
                    'ai_reason' => $rec['reason'] ?? 'Excellent match for your profile',
                    'match_reasons' => array_slice(explode('.', $rec['reason'] ?? 'Good match'), 0, 2),
                ];
            }
        }

        return $enriched ?: $this->fallbackRanking($schools);
    }

    /**
     * Fallback to basic ranking if AI fails
     */
    private function fallbackRanking($schools): array
    {
        return $schools->map(function ($school, $index) {
            return [
                'school' => $school,
                'compatibility_score' => 100 - ($index * 5),
                'ai_reason' => 'Highly-rated school in your area',
                'match_reasons' => ['Excellent academic record', 'Good facilities'],
            ];
        })->toArray();
    }
}
