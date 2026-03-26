<?php

namespace App\Services;

use App\Models\DynamicSchool;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SchoolDataFetcherService
{
    private $googlePlacesApiKey;
    private $groqApiKey;

    public function __construct()
    {
        $this->googlePlacesApiKey = config('services.google.places_api_key');
        $this->groqApiKey = config('services.groq.api_key');
    }

    /**
     * Fetch schools dynamically for a city
     * Combines multiple data sources for comprehensive results
     */
    public function fetchSchoolsForLocation(City $city, $force = false)
    {
        $schools = collect();

        try {
            // Fetch from Google Places API
            $googleSchools = $this->fetchFromGooglePlaces($city);
            $schools = $schools->merge($googleSchools);
            Log::info("Fetched {$googleSchools->count()} schools from Google Places for {$city->name}");
        } catch (\Exception $e) {
            Log::warning("Google Places fetch failed for {$city->name}: {$e->getMessage()}");
        }

        try {
            // Enhance with AI-discovered schools
            $aiSchools = $this->discoverWithAI($city, $schools);
            $schools = $schools->merge($aiSchools);
            Log::info("Discovered {$aiSchools->count()} additional schools with AI for {$city->name}");
        } catch (\Exception $e) {
            Log::warning("AI discovery failed for {$city->name}: {$e->getMessage()}");
        }

        try {
            // Enhance existing data with additional details
            $schools = $this->enrichSchoolData($schools, $city);
        } catch (\Exception $e) {
            Log::warning("School enrichment failed for {$city->name}: {$e->getMessage()}");
        }

        // Save schools to database
        $savedCount = $this->saveSchoolsToDB($schools, $city);

        return [
            'schools' => $schools,
            'count' => $schools->count(),
            'saved' => $savedCount,
            'city' => $city->name,
        ];
    }

    /**
     * Fetch schools from Google Places API
     */
    private function fetchFromGooglePlaces(City $city): Collection
    {
        if (!$this->googlePlacesApiKey) {
            return collect();
        }

        $schools = collect();

        try {
            // Search for schools in the city
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $this->googlePlacesApiKey,
            ])->get('https://places.googleapis.com/v1/places:searchText', [
                'textQuery' => "schools in {$city->name}, {$city->state->name}, {$city->state->country->name}",
                'languageCode' => 'en',
                'locationBias' => [
                    'circle' => [
                        'center' => [
                            'latitude' => $city->latitude ?? 0,
                            'longitude' => $city->longitude ?? 0,
                        ],
                        'radius' => 30000, // 30km radius
                    ]
                ],
                'pageSize' => 20,
            ]);

            if ($response->successful() && !empty($response->json('places'))) {
                foreach ($response->json('places') as $place) {
                    $schools->push($this->parseGooglePlace($place, $city));
                }
            }
        } catch (\Exception $e) {
            Log::warning("Google Places API error: {$e->getMessage()}");
        }

        return $schools->filter()->unique('external_id');
    }

    /**
     * Parse Google Place data into our school format
     */
    private function parseGooglePlace($place, City $city): array
    {
        $address = $place['formattedAddress'] ?? 'City address unknown';
        
        return [
            'name' => $place['displayName']['text'] ?? 'Unknown School',
            'description' => $place['editorialSummary']['text'] ?? null,
            'external_id' => $place['id'] ?? null,
            'data_source' => 'google_places',
            'address' => $address,
            'phone' => $place['internationalPhoneNumber'] ?? null,
            'email' => null, // Google Places doesn't provide email
            'website' => $place['websiteUri'] ?? null,
            'latitude' => $place['location']['latitude'] ?? null,
            'longitude' => $place['location']['longitude'] ?? null,
            'rating' => $place['rating'] ?? 0,
            'rating_count' => $place['userRatingCount'] ?? 0,
            'city_id' => $city->id,
            'state_id' => $city->state_id,
            'country_id' => $city->state->country_id,
            'type' => 'mainstream',
            'is_verified' => false,
            'last_refreshed_at' => now(),
        ];
    }

    /**
     * Use AI to discover schools not in Google Places
     */
    private function discoverWithAI(City $city, Collection $existingSchools): Collection
    {
        $schools = collect();

        // Skip if no Groq API key configured
        if (!$this->groqApiKey) {
            Log::info("Groq API key not configured, skipping AI discovery");
            return $schools;
        }

        try {
            $prompt = $this->buildAIDiscoveryPrompt($city, $existingSchools);

            Log::info("Starting AI school discovery for {$city->name}");

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->groqApiKey}",
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an education expert. Identify notable schools in the given location that might not be in Google Places.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);

            if ($response->failed()) {
                Log::error("Groq API failed for {$city->name}: Status {$response->status()}", [
                    'response' => $response->json(),
                    'body' => $response->body(),
                ]);
                return $schools;
            }

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                
                if ($content) {
                    Log::info("AI response received, parsing schools for {$city->name}");
                    $schools = $this->parseAIDiscoveredSchools($content, $city);
                    Log::info("Parsed {$schools->count()} schools from AI for {$city->name}");
                } else {
                    Log::warning("Empty response from Groq API for {$city->name}");
                }
            }
        } catch (\Exception $e) {
            Log::error("AI discovery error for {$city->name}: {$e->getMessage()}", [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $schools;
    }

    /**
     * Build prompt for AI school discovery
     */
    private function buildAIDiscoveryPrompt(City $city, Collection $existingSchools): string
    {
        $existingNames = $existingSchools->pluck('name')->implode(', ');

        return <<<PROMPT
You are helping discover schools in {$city->name}, {$city->state->name}, {$city->state->country->name}.

These schools are already known: $existingNames

Identify 5-10 notable schools (private, government, international, specialty schools like STEM, arts, sports) that are:
1. Actually located in this specific city
2. NOT in the list above
3. Well-established and reputable
4. Relevant for high school or secondary education

Format your response as a JSON array with this structure:
[
  {
    "name": "School Name",
    "type": "mainstream|stem|arts|technical|business|social",
    "description": "Brief description",
    "specialization": "if any (e.g., STEM, Arts, Sports)"
  }
]

Only return valid JSON, nothing else.
PROMPT;
    }

    /**
     * Parse AI-discovered schools
     */
    private function parseAIDiscoveredSchools($response, City $city): Collection
    {
        $schools = collect();

        if (!$response) {
            Log::warning("Empty response provided to parseAIDiscoveredSchools");
            return $schools;
        }

        try {
            Log::debug("Parsing AI response, first 200 chars: " . substr($response, 0, 200));

            // Extract JSON from response
            if (preg_match('/\[.*\]/s', $response, $matches)) {
                $jsonString = $matches[0];
                Log::debug("Extracted JSON: " . substr($jsonString, 0, 200));
                
                $data = json_decode($jsonString, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("JSON decode error: " . json_last_error_msg(), [
                        'json_string' => substr($jsonString, 0, 500),
                    ]);
                    return $schools;
                }

                if (is_array($data) && !empty($data)) {
                    Log::info("Successfully decoded {$city->name} schools, count: " . count($data));
                    
                    foreach ($data as $item) {
                        if (!isset($item['name'])) {
                            Log::warning("School item missing 'name' field: " . json_encode($item));
                            continue;
                        }
                        
                        $schools->push([
                            'name' => $item['name'],
                            'description' => $item['description'] ?? null,
                            'external_id' => 'ai_' . slugify($item['name']) . '_' . $city->id,
                            'data_source' => 'ai_discovered',
                            'address' => $city->name . ', ' . $city->state->name,
                            'city_id' => $city->id,
                            'state_id' => $city->state_id,
                            'country_id' => $city->state->country_id,
                            'type' => strtolower($item['type'] ?? 'mainstream'),
                            'specializations' => !empty($item['specialization']) ? [$item['specialization']] : null,
                            'is_verified' => false,
                            'last_refreshed_at' => now(),
                        ]);
                    }
                } else {
                    Log::warning("AI response is not array or empty: " . json_encode($data));
                }
            } else {
                Log::warning("Could not extract JSON array from AI response: " . substr($response, 0, 300));
            }
        } catch (\Exception $e) {
            Log::error("Error parsing AI-discovered schools: {$e->getMessage()}", [
                'exception' => $e,
                'response_preview' => substr($response, 0, 300),
            ]);
        }

        return $schools;
    }

    /**
     * Enrich school data with additional information
     */
    private function enrichSchoolData(Collection $schools, City $city): Collection
    {
        foreach ($schools as $key => $school) {
            try {
                // Fetch additional details if we have a website
                if (!empty($school['website'])) {
                    $enrichment = $this->scrapeSchoolWebsite($school['website']);
                    $schools[$key] = array_merge($school, $enrichment);
                }

                // Use AI to infer strengths and facilities
                if (!isset($school['strengths'])) {
                    $inference = $this->inferSchoolCharacteristics($school);
                    $schools[$key] = array_merge($school, $inference);
                }
            } catch (\Exception $e) {
                Log::warning("Error enriching school {$school['name']}: {$e->getMessage()}");
            }
        }

        return $schools;
    }

    /**
     * Scrape website for additional school information
     */
    private function scrapeSchoolWebsite($url): array
    {
        $enrichment = [];

        try {
            // In production, use a headless browser or web scraping library
            // For now, we'll use a simplified approach
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $content = $response->body();

                // Extract common phrases for facilities and programs
                $enrichment['facilities'] = $this->extractFacilities($content);
                $enrichment['programs'] = $this->extractPrograms($content);
            }
        } catch (\Exception $e) {
            Log::debug("Web scraping failed for $url: {$e->getMessage()}");
        }

        return $enrichment;
    }

    /**
     * Extract facilities from website content
     */
    private function extractFacilities($content): ?array
    {
        $keywords = [
            'laboratory', 'library', 'playground', 'sports', 'cafeteria',
            'computer', 'science', 'auditorium', 'art studio', 'music',
            'basketball', 'swimming', 'gymnasium', 'wifi', 'technology',
            'transportation', 'medical', 'counseling', 'smart classes'
        ];

        $found = [];
        foreach ($keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $found[] = ucfirst($keyword);
            }
        }

        return !empty($found) ? array_slice(array_unique($found), 0, 5) : null;
    }

    /**
     * Extract programs from website content
     */
    private function extractPrograms($content): ?array
    {
        $keywords = [
            'stem', 'iit', 'jee', 'neet', 'cbse', 'icse', 'state board',
            'montessori', 'arts', 'commerce', 'science', 'engineering',
            'international', 'igcse', 'ib', 'advanced placement'
        ];

        $found = [];
        foreach ($keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $found[] = strtoupper($keyword);
            }
        }

        return !empty($found) ? array_slice(array_unique($found), 0, 5) : null;
    }

    /**
     * Use AI to infer school characteristics from name and data
     */
    private function inferSchoolCharacteristics($school): array
    {
        $inference = [
            'strengths' => [],
            'facilities' => [],
            'programs' => [],
        ];

        try {
            $description = $school['description'] ?? 'N/A';
            $prompt = "Based on the school name '{$school['name']}' and description '$description', " .
                     "what are likely strengths ('strengths'), facilities ('facilities'), and programs? " .
                     "Return as JSON with these keys. Keep arrays concise (max 3 items each).";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->groqApiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'mixtral-8x7b-32768',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an education expert. Infer school characteristics.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $data = json_decode($matches[0], true);
                    if (is_array($data)) {
                        $inference['strengths'] = array_slice($data['strengths'] ?? [], 0, 3);
                        $inference['facilities'] = array_slice($data['facilities'] ?? [], 0, 3);
                        $inference['programs'] = array_slice($data['programs'] ?? [], 0, 3);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::debug("AI inference failed: {$e->getMessage()}");
        }

        return $inference;
    }

    /**
     * Save schools to database, avoiding duplicates
     */
    private function saveSchoolsToDB(Collection $schools, City $city): int
    {
        $saved = 0;

        foreach ($schools as $schoolData) {
            try {
                $schoolData['relevance_score'] = $this->calculateRelevanceScore($schoolData);

                // Try to find existing school by external_id or name
                $existingSchool = DynamicSchool::where('city_id', $city->id)
                    ->where(function ($query) use ($schoolData) {
                        if (!empty($schoolData['external_id'])) {
                            $query->orWhere('external_id', $schoolData['external_id']);
                        }
                        $query->orWhere('name', $schoolData['name']);
                    })
                    ->first();

                if ($existingSchool) {
                    // Update existing
                    $existingSchool->update(array_merge($schoolData, [
                        'last_refreshed_at' => now(),
                    ]));
                } else {
                    // Create new
                    DynamicSchool::create($schoolData);
                }

                $saved++;
            } catch (\Exception $e) {
                Log::warning("Failed to save school '{$schoolData['name']}': {$e->getMessage()}");
            }
        }

        return $saved;
    }

    /**
     * Calculate relevance score for a school
     */
    private function calculateRelevanceScore($schoolData): int
    {
        $score = 0;

        // Rating weight (40%)
        $score += (($schoolData['rating'] ?? 0) / 5) * 40;

        // Data completeness (30%)
        $completeness = collect([
            $schoolData['description'] ?? null,
            $schoolData['address'] ?? null,
            $schoolData['phone'] ?? null,
            $schoolData['website'] ?? null,
            $schoolData['facilities'] ?? null,
        ])->filter()->count() / 5;
        $score += $completeness * 30;

        // Data source weight (30%)
        $sourceWeights = [
            'google_places' => 20,
            'ai_discovered' => 15,
            'web_scrape' => 15,
        ];
        $score += $sourceWeights[$schoolData['data_source']] ?? 10;

        return min(100, intval($score));
    }

    /**
     * Get schools needing refresh
     */
    public function getSchoolsNeedingRefresh($limit = 50)
    {
        return DynamicSchool::where(function ($query) {
            $query->whereNull('last_refreshed_at')
                  ->orWhere('last_refreshed_at', '<', now()->subDays(30));
        })
        ->orderBy('last_refreshed_at')
        ->limit($limit)
        ->get();
    }
}
