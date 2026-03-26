<?php

namespace App\Services;

use App\Models\DynamicSchool;
use App\Models\SchoolRecommendation;
use App\Models\RecommendationFeedbackLog;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SchoolRecommendationEngine
{
    private $dataFetcher;
    private $rankingService;

    public function __construct(
        SchoolDataFetcherService $dataFetcher,
        AISchoolRankingService $rankingService
    ) {
        $this->dataFetcher = $dataFetcher;
        $this->rankingService = $rankingService;
    }

    /**
     * Generate personalized school recommendations for a user in a location
     */
    public function generateRecommendations(User $user, City $city, $limit = 10): array
    {
        try {
            Log::info("Generating recommendations for user {$user->id} in city {$city->name}");

            // Step 1: Check if user has completed assessments
            if (!$user->hasCompletedAllAssessments()) {
                return [
                    'success' => false,
                    'message' => 'Please complete all assessments first',
                    'recommendations' => [],
                ];
            }

            // Step 2: Fetch schools dynamically for the location
            $schools = $this->fetchSchoolsForLocation($city);

            if ($schools->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No schools found in this location',
                    'recommendations' => [],
                ];
            }

            Log::info("Found {$schools->count()} schools in {$city->name}");

            // Step 3: Rank schools based on user profile
            $ranked = $this->rankingService->rankSchools($user, $schools, $city);

            // Step 4: Save recommendations to database
            $saved = $this->saveRecommendations($user, $ranked, $limit);

            // Step 5: Generate AI reasoning for each recommendation
            $recommendations = $saved->map(function ($recommendation) use ($user) {
                $reasoning = $this->rankingService->generateRecommendationReasoning(
                    $recommendation,
                    $user
                );

                $recommendation->update(['ai_reasoning' => $reasoning]);

                // Get match factors
                $matchFactors = $this->rankingService->getMatchFactors($recommendation);
                $recommendation->update(['match_factors' => $matchFactors]);

                return $this->formatRecommendation($recommendation);
            });

            return [
                'success' => true,
                'message' => "Found {$recommendations->count()} personalized recommendations",
                'recommendations' => $recommendations->values()->toArray(),
                'city' => $city->name,
                'total_schools_analyzed' => $schools->count(),
            ];

        } catch (\Exception $e) {
            Log::error("Error generating recommendations: {$e->getMessage()}", [
                'user_id' => $user->id,
                'city_id' => $city->id,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Error generating recommendations. Please try again.',
                'recommendations' => [],
            ];
        }
    }

    /**
     * Fetch schools for a location, using cache when appropriate
     */
    private function fetchSchoolsForLocation(City $city): Collection
    {
        // Check cache first
        $cacheKey = "schools_city_{$city->id}";
        $cacheDuration = 24 * 60; // 24 hours

        $schools = Cache::remember($cacheKey, $cacheDuration, function () use ($city) {
            // Get existing schools from database
            $dbSchools = DynamicSchool::whereCityId($city->id)
                ->active()
                ->get();

            // If we have enough schools and they're recent, use them
            if ($dbSchools->count() >= 5 && $dbSchools->every(function ($s) {
                return $s->last_refreshed_at && $s->last_refreshed_at->diffInDays(now()) < 30;
            })) {
                return $dbSchools;
            }

            // Otherwise, fetch fresh data
            Log::info("Fetching fresh school data for city {$city->id}");
            $result = $this->dataFetcher->fetchSchoolsForLocation($city);

            // Return the newly fetched schools
            return DynamicSchool::whereCityId($city->id)->active()->get();
        });

        return $schools;
    }

    /**
     * Save recommendations to database
     */
    private function saveRecommendations(User $user, Collection $ranked, $limit): Collection
    {
        // Delete previous recommendations for this user and city
        // (assuming the ranked list is for a single city)
        if ($ranked->isNotEmpty()) {
            $cityId = $ranked->first()['recommendation']->city_id;
            SchoolRecommendation::where('user_id', $user->id)
                ->where('city_id', $cityId)
                ->forceDelete();
        }

        $saved = collect();

        foreach ($ranked->take($limit) as $item) {
            $recommendation = $item['recommendation'];
            $recommendation->user_id = $user->id;
            $recommendation->ai_reasoning = 'Analyzing...'; // Placeholder, will be updated later
            $recommendation->save();

            $saved->push($recommendation);
        }

        Log::info("Saved {$saved->count()} recommendations for user {$user->id}");

        return $saved;
    }

    /**
     * Format recommendation for API response
     */
    private function formatRecommendation(SchoolRecommendation $recommendation): array
    {
        $school = $recommendation->dynamicSchool;

        return [
            'id' => $recommendation->id,
            'rank' => $recommendation->rank,
            'school' => (object)[
                'id' => $school->id,
                'name' => $school->name,
                'type' => $school->type,
                'board' => $school->board,
                'rating' => $school->rating,
                'address' => $school->address,
                'phone' => $school->phone,
                'website' => $school->website,
                'email' => $school->email,
                'facilities' => $school->facilities ?? [],
            ],
            'compatibility_score' => round($recommendation->compatibility_score, 2),
            'score_breakdown' => [
                'riasec' => round($recommendation->riasec_match_score, 2),
                'cognitive' => round($recommendation->cognitive_match_score, 2),
                'personality' => round($recommendation->ocean_match_score, 2),
                'academic' => round($recommendation->academic_performance_score, 2),
                'location' => round($recommendation->location_proximity_score, 2),
            ],
            'highlights' => $school->getHighlights(),
            'strengths' => array_slice($school->strengths ?? [], 0, 3),
            'match_reasons' => $recommendation->match_factors ?? [],
            'programs' => array_slice($school->programs ?? [], 0, 3),
            'ai_reason' => $recommendation->ai_reasoning,
            'user_feedback' => $recommendation->user_feedback,
        ];
    }

    /**
     * Record user feedback on a recommendation
     */
    public function recordFeedback(SchoolRecommendation $recommendation, $feedbackType, $comment = null): SchoolRecommendation
    {
        try {
            Log::info("Recording feedback: user_id={$recommendation->user_id}, type=$feedbackType");

            $recommendation->recordFeedback($feedbackType, $comment);

            // Log for learning
            RecommendationFeedbackLog::create([
                'user_id' => $recommendation->user_id,
                'school_recommendation_id' => $recommendation->id,
                'feedback_type' => $feedbackType,
                'feedback_context' => $comment,
                'user_assessment_snapshot' => $recommendation->captureUserAssessmentSnapshot(),
                'school_data_snapshot' => $recommendation->dynamicSchool->toArray(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
            ]);

            return $recommendation;
        } catch (\Exception $e) {
            Log::error("Error recording feedback: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Get recommendations for a user
     */
    public function getRecommendations(User $user, City $city): Collection
    {
        return SchoolRecommendation::where('user_id', $user->id)
            ->where('city_id', $city->id)
            ->with('dynamicSchool')
            ->orderBy('rank')
            ->get()
            ->map(fn($rec) => $this->formatRecommendation($rec));
    }

    /**
     * Get recommendation details
     */
    public function getRecommendationDetail(SchoolRecommendation $recommendation): array
    {
        $recommendation->recordView();

        return [
            'recommendation' => $this->formatRecommendation($recommendation),
            'similar_schools' => $this->getSimilarSchools($recommendation),
            'user_feedback_context' => [
                'has_feedback' => $recommendation->user_feedback !== null,
                'feedback_type' => $recommendation->user_feedback,
                'feedback_comment' => $recommendation->user_feedback_comment,
                'feedback_date' => $recommendation->user_feedback_at,
            ],
        ];
    }

    /**
     * Get similar schools to a recommended school
     */
    private function getSimilarSchools(SchoolRecommendation $recommendation, $count = 3): array
    {
        $school = $recommendation->dynamicSchool;

        $similar = DynamicSchool::where('city_id', $school->city_id)
            ->where('type', $school->type)
            ->where('id', '!=', $school->id)
            ->where('rating', '>=', $school->rating - 0.5)
            ->active()
            ->orderByDesc('rating')
            ->limit($count)
            ->get();

        return $similar->map(fn($s) => [
            'name' => $s->name,
            'rating' => $s->rating,
            'type' => $s->type,
            'highlights' => $s->getHighlights(),
        ])->toArray();
    }

    /**
     * Get analytics for recommendations
     */
    public function getAnalytics(User $user): array
    {
        $recommendations = SchoolRecommendation::where('user_id', $user->id)->get();

        $positiveFeedback = $recommendations->filter(function ($r) {
            return $r->user_feedback && $r->user_feedback > 0;
        })->count();

        $negativeFeedback = $recommendations->filter(function ($r) {
            return $r->user_feedback && $r->user_feedback < 0;
        })->count();

        $totalViews = $recommendations->sum('view_count');

        $feedbackLogs = RecommendationFeedbackLog::where('user_id', $user->id)->get();

        $topInteraction = $recommendations
            ->sortByDesc('view_count')
            ->first();

        return [
            'total_recommendations_received' => $recommendations->count(),
            'recommendations_with_feedback' => $recommendations->whereNotNull('user_feedback')->count(),
            'positive_feedback_count' => $positiveFeedback,
            'negative_feedback_count' => $negativeFeedback,
            'total_views' => $totalViews,
            'total_interactions' => $feedbackLogs->count(),
            'interaction_breakdown' => [
                'viewed' => $feedbackLogs->where('feedback_type', 'viewed')->count(),
                'liked' => $feedbackLogs->where('feedback_type', 'liked')->count(),
                'disliked' => $feedbackLogs->where('feedback_type', 'disliked')->count(),
                'interested' => $feedbackLogs->where('feedback_type', 'interested')->count(),
            ],
            'most_viewed_school' => $topInteraction ? [
                'name' => $topInteraction->dynamicSchool->name,
                'views' => $topInteraction->view_count,
            ] : null,
            'engagement_rate' => $recommendations->count() > 0 
                ? round(($feedbackLogs->count() / $recommendations->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Search for schools dynamically
     */
    public function searchSchools($query, City $city = null): Collection
    {
        $q = DynamicSchool::active();

        if ($city) {
            $q->where('city_id', $city->id);
        }

        $results = $q->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%$query%")
              ->orWhere('description', 'LIKE', "%$query%")
              ->orWhere('address', 'LIKE', "%$query%");
        })
        ->orderByDesc('rating')
        ->limit(20)
        ->get();

        return $results->map(fn($school) => [
            'id' => $school->id,
            'name' => $school->name,
            'type' => $school->type,
            'rating' => $school->rating,
            'location' => $school->address,
            'highlights' => $school->getHighlights(),
            'url' => route('school-finder.detail', $school->id),
        ]);
    }
}
