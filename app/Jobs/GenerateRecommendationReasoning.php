<?php

namespace App\Jobs;

use App\Models\SchoolRecommendation;
use App\Services\AISchoolRankingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateRecommendationReasoning implements ShouldQueue
{
    use Queueable;

    private $recommendation;

    public function __construct(SchoolRecommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    public function handle(AISchoolRankingService $rankingService)
    {
        try {
            Log::info("Generating AI reasoning for recommendation {$this->recommendation->id}");

            $reasoning = $rankingService->generateRecommendationReasoning(
                $this->recommendation,
                $this->recommendation->user
            );

            $this->recommendation->update(['ai_reasoning' => $reasoning]);

            Log::info("Successfully generated reasoning for recommendation {$this->recommendation->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate reasoning: {$e->getMessage()}");
            $this->fail($e);
        }
    }
}
