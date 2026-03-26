<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeRecommendationFeedback implements ShouldQueue
{
    use Queueable;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        try {
            Log::info("Analyzing recommendation feedback for user {$this->user->id}");

            // Get recent feedback logs for this user
            $feedbackLogs = $this->user->recommendationFeebacks()
                ->recent(30)
                ->get()
                ->groupBy('feedback_type');

            $analysis = [
                'total_viewed' => $feedbackLogs->get('viewed', collect())->count(),
                'total_liked' => $feedbackLogs->get('liked', collect())->count(),
                'total_disliked' => $feedbackLogs->get('disliked', collect())->count(),
                'total_interested' => $feedbackLogs->get('interested', collect())->count(),
                'feedback_rate' => $this->calculateFeedbackRate(),
            ];

            Log::info("Feedback analysis complete for user {$this->user->id}", $analysis);

            // TODO: Use this analysis to improve future recommendations
            // - Adjust weights in AISchoolRankingService
            // - Identify patterns in user preferences
            // - Personalize school discovery

        } catch (\Exception $e) {
            Log::error("Failed to analyze feedback for user {$this->user->id}: {$e->getMessage()}");
            $this->fail($e);
        }
    }

    private function calculateFeedbackRate(): float
    {
        $totalRecommendations = $this->user->recommendations()->count();
        if ($totalRecommendations === 0) {
            return 0;
        }

        $feedbackCount = $this->user->recommendationFeebacks()
            ->recent(30)
            ->distinct('school_recommendation_id')
            ->count();

        return round(($feedbackCount / $totalRecommendations) * 100, 2);
    }
}
