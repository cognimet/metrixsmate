<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\DynamicSchool;
use App\Models\SchoolRecommendation;
use App\Models\UserResult;
use App\Services\AiSchoolFinderService;
use App\Services\SchoolRecommendationEngine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchoolFinderController extends Controller
{
    private $aiService;
    private $recommendationEngine;

    public function __construct(
        AiSchoolFinderService $aiService,
        SchoolRecommendationEngine $recommendationEngine
    ) {
        $this->aiService = $aiService;
        $this->recommendationEngine = $recommendationEngine;
    }

    /**
     * Show the school finder page
     */
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        $user = Auth::user();
        $allCompleted = $user->hasCompletedAllAssessments();

        return view('school-finder.index', compact('countries', 'allCompleted'));
    }

    /**
     * Get states for a country (AJAX)
     */
    public function getStates(Country $country)
    {
        return response()->json(
            $country->states()->orderBy('name')->get(['id', 'name'])
        );
    }

    /**
     * Get cities for a state (AJAX)
     */
    public function getCities(State $state)
    {
        return response()->json(
            $state->cities()->orderBy('name')->get(['id', 'name'])
        );
    }

    /**
     * Find recommended schools based on assessment results (AI-powered)
     */
    public function findSchools(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
        ]);

        $user = Auth::user();
        $city = City::with('state.country')->findOrFail($request->city_id);

        // Generate AI-powered recommendations using the new engine
        $result = $this->recommendationEngine->generateRecommendations($user, $city, limit: 10);

        if (!$result['success']) {
            return back()->withErrors($result['message']);
        }

        $recommendations = collect($result['recommendations']);

        // Build profile summary for display
        $riasecDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->get();

        $cognitiveDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'cognitive')
            ->where('result_type', 'domain')
            ->get();

        $oceanDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'ocean')
            ->where('result_type', 'domain')
            ->get();

        $profileSummary = $this->buildProfileSummary($riasecDomains, $cognitiveDomains, $oceanDomains);
        $recommendedTypes = $this->getRecommendedSchoolTypes($riasecDomains);

        $countries = Country::orderBy('name')->get();
        $allCompleted = $user->hasCompletedAllAssessments();
        $analytics = $this->recommendationEngine->getAnalytics($user);

        return view('school-finder.results', compact(
            'recommendations', 'city', 'profileSummary', 'recommendedTypes',
            'countries', 'allCompleted', 'analytics', 'result'
        ));
    }

    /**
     * Get recommendation details
     */
    public function getDetail(SchoolRecommendation $recommendation)
    {
        $user = Auth::user();

        // Ensure user can only view their own recommendations
        if ($recommendation->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $details = $this->recommendationEngine->getRecommendationDetail($recommendation);

        return view('school-finder.detail', compact('details', 'recommendation'));
    }

    /**
     * Record feedback on a recommendation (API endpoint)
     */
    public function recordFeedback(Request $request, SchoolRecommendation $recommendation)
    {
        $request->validate([
            'feedback_type' => 'required|in:like,dislike,interested,not_interested,interested_to_enroll',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Ensure user can only feedback on their own recommendations
        if ($recommendation->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $this->recommendationEngine->recordFeedback(
                $recommendation,
                $request->feedback_type,
                $request->comment
            );

            return response()->json([
                'success' => true,
                'message' => 'Feedback recorded successfully',
                'recommendation' => $this->formatRecommendationResponse($recommendation),
            ]);
        } catch (\Exception $e) {
            Log::error("Feedback recording failed: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to record feedback'], 500);
        }
    }

    /**
     * Format recommendation for response
     */
    private function formatRecommendationResponse(SchoolRecommendation $recommendation): array
    {
        return [
            'id' => $recommendation->id,
            'rank' => $recommendation->rank,
            'compatibility_score' => $recommendation->compatibility_score,
            'user_feedback' => $recommendation->user_feedback,
            'feedback_at' => $recommendation->user_feedback_at,
        ];
    }

    /**
     * Get all recommendations for a city
     */
    public function getRecommendations(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
        ]);

        $user = Auth::user();
        $city = City::findOrFail($request->city_id);

        $recommendations = $this->recommendationEngine->getRecommendations($user, $city);

        return response()->json([
            'success' => true,
            'count' => $recommendations->count(),
            'city' => $city->name,
            'recommendations' => $recommendations->toArray(),
        ]);
    }

    /**
     * Get analytics for user's recommendations
     */
    public function getAnalytics()
    {
        $user = Auth::user();
        $analytics = $this->recommendationEngine->getAnalytics($user);

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Search schools dynamically
     */
    public function searchSchools(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $city = null;
        if ($request->city_id) {
            $city = City::findOrFail($request->city_id);
        }

        $results = $this->recommendationEngine->searchSchools($request->query, $city);

        return response()->json([
            'success' => true,
            'count' => $results->count(),
            'results' => $results->toArray(),
        ]);
    }

    /**
     * Build user profile summary for display
     */
    private function buildProfileSummary($riasecDomains, $cognitiveDomains, $oceanDomains): array
    {
        $topRiasec = $riasecDomains->sortByDesc('percentage')->take(3);
        $topCognitive = $cognitiveDomains->sortByDesc('percentage')->first();
        $topOcean = $oceanDomains->sortByDesc('percentage')->first();

        $hollandCode = $topRiasec->map(fn($d) => strtoupper(substr($d->name, 0, 1)))->implode('');

        return [
            'holland_code' => $hollandCode,
            'top_interests' => $topRiasec->pluck('name')->toArray(),
            'top_cognitive' => $topCognitive->name ?? 'N/A',
            'top_personality' => $topOcean->name ?? 'N/A',
            'cognitive_score' => round($cognitiveDomains->avg('percentage') ?? 0, 1),
        ];
    }

    /**
     * Get recommended school types based on RIASEC profile
     */
    private function getRecommendedSchoolTypes($riasecDomains): array
    {
        $typeMapping = [
            'realistic' => 'technical',
            'investigative' => 'stem',
            'artistic' => 'arts',
            'social' => 'social',
            'enterprising' => 'business',
            'conventional' => 'mainstream',
        ];

        $topRiasec = $riasecDomains->sortByDesc('percentage')->take(2);
        $types = [];

        foreach ($topRiasec as $domain) {
            $key = strtolower($domain->name);
            if (isset($typeMapping[$key])) {
                $types[] = $typeMapping[$key];
            }
        }

        return array_unique($types) ?: ['mainstream'];
    }
}
