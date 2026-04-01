<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeminiSchoolFinderService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.gemini.api_key', '');
        $this->model   = config('services.gemini.model', 'gemini-2.0-flash');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    /**
     * Main entry: search schools via MetrixsMate AI with full filter context.
     * Results are cached for 6 hours for the same search parameters.
     *
     * @param array $filters {country, state, city, school_level, class_range, stream, board, fees_min, fees_max, academic_level, limit}
     * @return array {success, schools, meta, error?}
     */
    public function searchSchools(array $filters): array
    {
        // Extend PHP execution time for this request — API can take 30–60 s
        set_time_limit(180);

        $cacheKey = 'metrixsmate_school_' . md5(json_encode($filters));

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $cached['from_cache'] = true;
            return $cached;
        }

        if (empty($this->apiKey)) {
            return $this->errorResponse('Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.');
        }

        $prompt = $this->buildPrompt($filters);

        $raw = $this->callGemini($prompt);

        if (!$raw['success']) {
            return $this->errorResponse($raw['error']);
        }

        $parsed = $this->parseResponse($raw['text']);

        if (!$parsed['success']) {
            return $this->errorResponse($parsed['error'], $raw['text']);
        }

        $schools = $this->rankAndEnrich($parsed['schools'], $filters);

        $result = [
            'success'    => true,
            'schools'    => $schools,
            'from_cache' => false,
            'meta'       => [
                'total'            => count($schools),
                'city'             => $filters['city']  ?? null,
                'location'         => trim("{$filters['city']}, {$filters['state']}, {$filters['country']}"),
                'stream'           => $filters['stream'] ?? null,
                'board'            => $filters['board']  ?? null,
                'fees_range'       => $this->formatFeesRange($filters),
                'academic_level'   => $filters['academic_level'] ?? null,
                'model'            => $this->model,
                'generated_at'     => now()->toISOString(),
                'search_summary'   => $parsed['search_summary']    ?? '',
                'location_insights'=> $parsed['location_insights'] ?? '',
                'grounding_sources'=> $raw['sources'] ?? [],
            ],
        ];

        Cache::put($cacheKey, $result, now()->addHours(6));

        return $result;
    }

    /**
     * Build the Gemini prompt with full context and structured output requirements.
     */
    private function buildPrompt(array $f): string
    {
        $location     = trim("{$f['city']}, {$f['state']}, {$f['country']}");
        $schoolLevel  = $f['school_level'] ?? 'any level';
        $classRange   = $f['class_range']  ?? 'any class';
        $stream       = $f['stream']        ?? 'any stream';
        $board        = $f['board']         ?? 'any board';
        $feesMin      = !empty($f['fees_min']) ? '₹' . number_format($f['fees_min']) : null;
        $feesMax      = !empty($f['fees_max']) ? '₹' . number_format($f['fees_max']) : null;
        $feesRange    = ($feesMin && $feesMax) ? "{$feesMin} to {$feesMax}" : ($feesMax ? "up to {$feesMax}" : ($feesMin ? "above {$feesMin}" : 'any range'));
        $academic     = $f['academic_level'] ?? 'any level';
        $limit        = min((int)($f['limit'] ?? 10), 20);

        $schoolLevelContext = $this->getSchoolLevelContext($schoolLevel);
        $classRangeContext  = $this->getClassRangeContext($classRange);
        $streamContext      = $this->getStreamContext($stream);
        $boardContext       = $this->getBoardContext($board);

        return <<<PROMPT
You are India's most authoritative educational consultant with deep knowledge of every reputed school across all states and cities.

TASK: List the TOP {$limit} best-known, most reputed schools in {$location} matching the criteria below. These must be schools that appear in "Best Schools" rankings, have strong pass rates, and are well-known among parents.

CRITERIA:
- Location: {$location} (within 15 km of city centre)
- School Level: {$schoolLevel} {$schoolLevelContext}
- Class/Grade Range: {$classRange} {$classRangeContext}
- Stream: {$stream} {$streamContext}
- Board: {$board} {$boardContext}
- Fees budget: {$feesRange}
- Student academic level: {$academic}

RULES:
1. Only REAL, verifiable schools. No hallucinated names.
2. Rank 1 = single best school; ranking_score 85-100 for top, differentiate clearly.
3. highlights = specific achievements, not generic phrases.
4. If fewer than {$limit} real schools exist for these filters, return what genuinely exists.

Respond with ONLY a valid JSON object — no markdown, no preamble, no explanation outside the JSON.

{
  "schools": [
    {
      "rank": 1,
      "name": "Full official school name",
      "type": "private|government|aided|international",
      "board": "CBSE|RBSE|ICSE|IB|Cambridge|State Board",
      "streams_offered": ["PCM", "PCB", "Commerce", "Arts"],
      "location": "Locality or area name",
      "fees_annual_inr": 85000,
      "fees_is_estimate": false,
      "facilities": ["Smart Classrooms", "Labs", "Sports", "Library"],
      "highlights": ["Specific achievement 1", "Specific achievement 2", "Specific achievement 3"],
      "ranking_score": 92,
      "ranking_reason": "One clear sentence explaining why this school ranks here for these filters.",
      "data_confidence": "high|medium|low"
    }
  ],
  "search_summary": "1-2 sentence summary of results.",
  "location_insights": "1-2 sentence insight about schooling in this city for this stream/board."
}
PROMPT;
    }

    /**
     * Call the Gemini API using HTTP client.
     * Uses Google Search grounding so results are retrieved from the live web,
     * matching the quality of Google AI Overview answers.
     */
    private function callGemini(string $prompt): array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'tools' => [
                        // Google Search grounding: Gemini searches the web in real-time
                        // before answering — same mechanism used by Google AI Overview.
                        ['googleSearch' => (object)[]],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.1,
                        'topP'            => 0.8,
                        'maxOutputTokens' => 16384,
                    ],
                ]);

            if ($response->failed()) {
                $body = $response->json();
                $msg  = $body['error']['message'] ?? 'Gemini API request failed (HTTP ' . $response->status() . ')';
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $body]);
                return ['success' => false, 'error' => $msg];
            }

            $body  = $response->json();
            $parts = $body['candidates'][0]['content']['parts'] ?? [];

            // gemini-2.5-flash is a thinking model — it emits a `thought` part
            // followed by the real response part. Collect all non-thought text.
            $allText = '';
            foreach ($parts as $part) {
                if (empty($part['thought']) && !empty($part['text'])) {
                    $allText .= $part['text'];
                }
            }

            // Fallback: if every part had thought=true, take the last part's text
            if (!$allText && !empty($parts)) {
                $allText = end($parts)['text'] ?? '';
            }

            if (!$allText) {
                Log::error('Gemini empty content', ['body' => $body]);
                return ['success' => false, 'error' => 'Gemini returned empty content.'];
            }

            // Log grounding sources for debugging
            $sources = $body['candidates'][0]['groundingMetadata']['groundingChunks'] ?? [];
            if (!empty($sources)) {
                Log::info('Gemini grounding sources', ['count' => count($sources), 'sources' => array_column(array_column($sources, 'web'), 'uri')]);
            }

            // Extract clean source list for display
            $sourceList = [];
            foreach ($sources as $chunk) {
                $web = $chunk['web'] ?? null;
                if ($web && !empty($web['uri'])) {
                    $sourceList[] = [
                        'uri'   => $web['uri'],
                        'title' => $web['title'] ?? parse_url($web['uri'], PHP_URL_HOST),
                    ];
                }
            }

            return ['success' => true, 'text' => $allText, 'sources' => $sourceList];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini connection failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Could not connect to Gemini API. Please check your internet connection.'];
        } catch (\Exception $e) {
            Log::error('Gemini unexpected error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Parse and validate the JSON response from Gemini.
     */
    private function parseResponse(string $text): array
    {
        // 1. Strip markdown fences (```json ... ```)
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $clean = preg_replace('/\s*```\s*$/m', '', $clean);
        $clean = trim($clean);

        // 2. First attempt: decode as-is
        $data = json_decode($clean, true);

        // 3. If decode failed or returned non-array, find outermost { ... } block.
        //    This handles models that prepend/append natural-language text.
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $start = strpos($clean, '{');
            $end   = strrpos($clean, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $data = json_decode(substr($clean, $start, $end - $start + 1), true);
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            Log::warning('Gemini JSON parse failed', [
                'error' => json_last_error_msg(),
                'raw'   => substr($text, 0, 1000),
            ]);
            return ['success' => false, 'error' => 'Failed to parse Gemini response as JSON: ' . json_last_error_msg()];
        }

        $schools = $data['schools'] ?? [];

        if (empty($schools)) {
            return ['success' => false, 'error' => 'Gemini returned no school data for this location/filters.'];
        }

        return [
            'success'          => true,
            'schools'          => $schools,
            'search_summary'   => $data['search_summary'] ?? '',
            'location_insights'=> $data['location_insights'] ?? '',
        ];
    }

    /**
     * Apply additional ranking and normalization after Gemini response.
     */
    private function rankAndEnrich(array $schools, array $filters): array
    {
        // Ensure consistent structure for every school entry
        foreach ($schools as &$school) {
            $school['rank']              = $school['rank'] ?? 0;
            $school['name']              = $school['name'] ?? 'Unknown School';
            $school['ranking_score']     = min(100, max(0, (int)($school['ranking_score'] ?? 50)));
            $school['fees_annual_inr']   = !empty($school['fees_annual_inr']) ? (int)$school['fees_annual_inr'] : null;
            $school['fees_is_estimate']  = $school['fees_is_estimate'] ?? true;
            $school['facilities']        = $school['facilities']  ?? [];
            $school['highlights']        = $school['highlights']  ?? [];
            $school['streams_offered']   = $school['streams_offered'] ?? [];
            $school['data_confidence']   = $school['data_confidence'] ?? 'medium';

            // Compute a fee-fit badge
            $school['fees_badge'] = $this->feesBadge($school['fees_annual_inr'], $filters);
        }

        // Sort by ranking_score descending and re-index rank
        usort($schools, fn($a, $b) => $b['ranking_score'] <=> $a['ranking_score']);

        foreach ($schools as $i => &$s) {
            $s['rank'] = $i + 1;
        }

        return $schools;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getStreamContext(string $stream): string
    {
        $ctx = [
            'PCM'        => 'Physics, Chemistry, Mathematics — suitable for engineering/JEE aspirants.',
            'PCB'        => 'Physics, Chemistry, Biology — suitable for NEET/medical aspirants.',
            'PCMB'       => 'All four science subjects — for students keeping engineering and medical options open.',
            'Commerce'   => 'Accountancy, Business Studies, Economics — suitable for CA/MBA aspirants.',
            'Arts'       => 'History, Geography, Political Science, Sociology — humanities stream.',
            'Vocational' => 'Vocational/skill-based subjects alongside academics.',
        ];
        return $ctx[$stream] ?? '';
    }

    private function getSchoolLevelContext(string $level): string
    {
        $ctx = [
            'pre-primary'   => '(Kindergarten, LKG, UKG) — ages 3–5, foundational learning.',
            'primary'       => '(Classes 1–5) — ages 6–10, core academic skills.',
            'secondary'     => '(Classes 6–10) — ages 11–15, board exams at Class 10.',
            'senior'        => '(Classes 11–12) — ages 16–17, specialized stream selection, board exams.',
            'matric'        => '(Classes 1–10) — complete school without separate senior section.',
        ];
        return $ctx[$level] ?? '';
    }

    private function getClassRangeContext(string $range): string
    {
        $ctx = [
            'nursery-to-3'  => '(Nursery, LKG, UKG, Class 1–3) — foundational learning phase.',
            'class-4-to-7'  => '(Classes 4–7) — elementary to middle school transition.',
            'class-8-to-10' => '(Classes 8–10) — upper middle school, pre-board preparation.',
            'class-10-plus' => '(Classes 10+) — secondary & senior (Class 11–12) with board exams.',
        ];
        return $ctx[$range] ?? '';
    }

    private function getBoardContext(string $board): string
    {
        $ctx = [
            'CBSE'     => 'Central Board of Secondary Education — national curriculum, preferred for JEE/NEET.',
            'RBSE'     => 'Rajasthan Board of Secondary Education — state board curriculum.',
            'ICSE'     => 'Indian Certificate of Secondary Education — detailed English curriculum.',
            'IB'       => 'International Baccalaureate — internationally recognized curriculum.',
            'Cambridge'=> 'Cambridge IGCSE/A-Levels — UK-based international curriculum.',
            'State'    => 'State Board curriculum of the respective state.',
            'CBSE/RBSE' => 'Both CBSE and RBSE affiliated schools.',
        ];
        return $ctx[$board] ?? '';
    }

    private function feesBadge(?int $fees, array $filters): string
    {
        if ($fees === null) return 'unknown';
        $max = !empty($filters['fees_max']) ? (int)$filters['fees_max'] : null;
        $min = !empty($filters['fees_min']) ? (int)$filters['fees_min'] : null;
        if ($max && $fees <= $max * 0.7)  return 'budget-friendly';
        if ($max && $fees <= $max)         return 'within-budget';
        if ($max && $fees <= $max * 1.1)  return 'slightly-over';
        if ($max && $fees > $max * 1.1)   return 'over-budget';
        return 'available';
    }

    private function formatFeesRange(array $filters): string
    {
        $min = !empty($filters['fees_min']) ? '₹' . number_format($filters['fees_min']) : null;
        $max = !empty($filters['fees_max']) ? '₹' . number_format($filters['fees_max']) : null;
        if ($min && $max)  return "{$min} – {$max}";
        if ($max)          return "Up to {$max}";
        if ($min)          return "Above {$min}";
        return 'Not specified';
    }

    private function errorResponse(string $message, ?string $rawText = null): array
    {
        return [
            'success' => false,
            'error'   => $message,
            'schools' => [],
            'raw'     => $rawText,
        ];
    }
}
