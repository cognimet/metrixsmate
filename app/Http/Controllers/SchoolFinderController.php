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
use App\Services\GeminiSchoolFinderService;
use App\Services\SchoolRecommendationEngine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchoolFinderController extends Controller
{
    private $aiService;
    private $recommendationEngine;
    private $geminiService;

    public function __construct(
        AiSchoolFinderService $aiService,
        SchoolRecommendationEngine $recommendationEngine,
        GeminiSchoolFinderService $geminiService
    ) {
        $this->aiService           = $aiService;
        $this->recommendationEngine = $recommendationEngine;
        $this->geminiService       = $geminiService;
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
     * Find recommended schools based on assessment results (AI-powered via Gemini)
     */
    public function findSchools(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has completed all assessments
        if (!$user->hasCompletedAllAssessments()) {
            return back()
                ->withInput()
                ->withErrors(['search' => 'Please complete all assessments (RIASEC, Cognitive, and OCEAN) to use Assessment-Based search.'])
                ->with('assessmentRequired', true);
        }
        
        // Check if user has assessment search tokens
        if (!$user->canUseAssessmentSearch()) {
            return back()
                ->withInput()
                ->withErrors(['search' => 'You have used all your free Assessment-Based searches. Purchase tokens to continue.'])
                ->with('showTokenPurchaseModal', true)
                ->with('tokenType', 'assessment');
        }
        
        // Support both city_id and city name-based inputs
        $rules = ['city_id' => 'nullable|exists:cities,id'];
        
        if ($request->input('city_id')) {
            $rules['city_id'] = 'required|exists:cities,id';
        } else {
            // Validate city name inputs
            $rules = array_merge($rules, [
                'country' => 'required|string|max:100',
                'state'   => 'required|string|max:100',
                'city'    => 'required|string|max:100',
            ]);
        }

        $request->validate($rules);
        
        // Find city by name if city_id not provided
        if (!$request->input('city_id')) {
            $stateName = $request->state;
            $cityName = $request->city;
            
            $city = City::whereHas('state', function ($q) use ($stateName) {
                $q->where('name', $stateName);
            })
            ->where('name', $cityName)
            ->with('state.country')
            ->first();

            if (!$city) {
                return back()
                    ->withInput()
                    ->withErrors(['city' => "City '{$cityName}' not found in {$stateName}. Please check the spelling."]);
            }
        } else {
            $city = City::with('state.country')->findOrFail($request->city_id);
        }

        // Use Gemini service to search for schools based on assessment profile
        // Build assessment-based filters
        $filters = [
            'country' => $city->state->country->name,
            'state' => $city->state->name,
            'city' => $city->name,
            'school_level' => 'primary', // Default: can be customized based on user age
            'class_range' => 'class-4-to-7', // Default
            'limit' => 10,
        ];

        // Get user profile for assessment context
        $riasecDomains = UserResult::where('user_id', $user->id)
            ->where('assessment_type', 'riasec')
            ->where('result_type', 'domain')
            ->orderByDesc('percentage')
            ->get();

        $topRiasec = $riasecDomains->take(2)->pluck('name')->implode(', ');
        
        // Add assessment context to prompt (Gemini will understand)
        $filters['assessment_context'] = "User's top interests: {$topRiasec}";

        $result = $this->geminiService->searchSchools($filters);

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['search' => $result['error']]);
        }

        // Add assessment-specific metadata
        $result['meta']['search_type'] = 'assessment';
        $result['meta']['profile_context'] = "Based on user assessment profile (RIASEC: {$topRiasec})";
        
        // Consume token after successful search
        $user->consumeAssessmentSearch();

        return view('school-finder.results', [
            'schools'  => $result['schools'],
            'meta'     => $result['meta'],
            'filters'  => $request->only(['country', 'state', 'city']),
            'fromCache'=> $result['from_cache'] ?? false,
            'searchType' => 'assessment',
            'remainingSearches' => $user->getTotalAvailableSearches() - 1,
        ]);
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

    // ─── Gemini School Finder ─────────────────────────────────────────────────

    /**
     * Show Gemini-powered school finder form.
     */
    public function geminiIndex()
    {
        $countries = Country::orderBy('name')->get();

        return view('school-finder.gemini', compact('countries'));
    }

    // ─── Unified MetrixsMate AI School Finder ────────────────────────────────────

    /**
     * Show unified MetrixsMate AI School Finder form.
     */
    public function showSearchForm()
    {
        $user = Auth::user();
        $countries = Country::orderBy('name')->get();
        $assessmentsCompleted = $user->hasCompletedAllAssessments();
        
        // Get token status for the view
        $aiSearchesRemaining = $user->getRemainingAiSearches();
        $assessmentSearchesRemaining = $user->getRemainingAssessmentSearches();
        $paidTokensRemaining = $user->paid_search_tokens;

        return view('school-finder.search', compact(
            'countries',
            'assessmentsCompleted',
            'aiSearchesRemaining',
            'assessmentSearchesRemaining',
            'paidTokensRemaining'
        ));
    }

    /**
     * Handle unified MetrixsMate AI school search submission.
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has AI search tokens
        if (!$user->canUseAiSearch()) {
            return back()
                ->withInput()
                ->withErrors(['search' => 'You have used all your free AI searches. Purchase tokens to continue.'])
                ->with('showTokenPurchaseModal', true)
                ->with('tokenType', 'ai');
        }
        
        $validated = $request->validate([
            'country'        => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'school_level'   => 'required|string|in:pre-primary,primary,secondary,senior,matric',
            'class_range'    => 'required|string|in:nursery-to-3,class-4-to-7,class-8-to-10,class-10-plus',
            'stream'         => 'nullable|string|in:PCM,PCB,PCMB,Commerce,Arts,Vocational',
            'board'          => 'nullable|string|in:CBSE,RBSE,ICSE,IB,Cambridge,State',
            'fees_min'       => 'nullable|integer|min:0',
            'fees_max'       => 'nullable|integer|min:0',
            'academic_level' => 'nullable|string|in:below_average,average,above_average,excellent',
            'limit'          => 'nullable|integer|min:5|max:20',
        ]);

        $result = $this->geminiService->searchSchools($validated);

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['search' => $result['error']]);
        }
        
        // Consume token after successful search
        $user->consumeAiSearch();

        return view('school-finder.results', [
            'schools'  => $result['schools'],
            'meta'     => $result['meta'],
            'filters'  => $validated,
            'fromCache'=> $result['from_cache'] ?? false,
            'searchType' => 'ai',
            'remainingSearches' => $user->getTotalAvailableSearches() - 1,
        ]);
    }

    /**
     * Handle Gemini school search submission.
     */
    public function geminiSearch(Request $request)
    {
        $validated = $request->validate([
            'country'        => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'stream'         => 'required|string|in:PCM,PCB,PCMB,Commerce,Arts,Vocational,Any',
            'board'          => 'required|string|in:CBSE,RBSE,ICSE,IB,Cambridge,State,Any',
            'fees_min'       => 'nullable|integer|min:0',
            'fees_max'       => 'nullable|integer|min:0',
            'academic_level' => 'nullable|string|in:below_average,average,above_average,excellent',
            'limit'          => 'nullable|integer|min:5|max:20',
        ]);

        // Normalise "Any" to null so the prompt isn't artificially constrained
        if (($validated['stream'] ?? '') === 'Any')  $validated['stream']  = null;
        if (($validated['board']  ?? '') === 'Any')  $validated['board']   = null;

        $result = $this->geminiService->searchSchools($validated);

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['gemini' => $result['error']]);
        }

        return view('school-finder.results', [
            'schools'  => $result['schools'],
            'meta'     => $result['meta'],
            'filters'  => $validated,
            'fromCache'=> $result['from_cache'] ?? false,
        ]);
    }

    /**
     * AJAX: re-rank or re-fetch with updated filters (returns JSON).
     */
    public function geminiAjaxSearch(Request $request)
    {
        $validated = $request->validate([
            'country'        => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'stream'         => 'nullable|string|max:50',
            'board'          => 'nullable|string|max:50',
            'fees_min'       => 'nullable|integer|min:0',
            'fees_max'       => 'nullable|integer|min:0',
            'academic_level' => 'nullable|string',
            'limit'          => 'nullable|integer|min:5|max:20',
        ]);

        $result = $this->geminiService->searchSchools($validated);

        return response()->json($result);
    }
}
