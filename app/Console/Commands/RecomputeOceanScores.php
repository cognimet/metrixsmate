<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserResult;
use App\Models\QuizDomainValueAnswer;
use App\Http\Controllers\AssessmentController;

class RecomputeOceanScores extends Command
{
    protected $signature = 'ocean:recompute {--user_id= : Recompute for a specific user ID only}';
    protected $description = 'Recompute OCEAN facet and CCS scores for users whose data is stale (all-zero)';

    public function handle(): int
    {
        $query = User::where('is_ocean_assessment_completed', 1);

        if ($userId = $this->option('user_id')) {
            $query->where('id', $userId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return self::SUCCESS;
        }

        $oceanQuiz = Quiz::with(['domains.questions' => fn($q) => $q->orderBy('order')])->where('slug', 'ocean')->first();

        if (!$oceanQuiz) {
            $this->error('OCEAN quiz not found in database.');
            return self::FAILURE;
        }

        $controller = app(AssessmentController::class);
        $recompute  = \Closure::bind(function (Quiz $quiz, int $userId, $userAnswers) {
            $this->computeAdvancedOceanResults($quiz, $userId, $userAnswers);
        }, $controller, AssessmentController::class);

        $questionIds = $oceanQuiz->domains->flatMap->questions->pluck('id')->unique()->values()->all();

        $this->info("Recomputing OCEAN scores for {$users->count()} user(s)...");

        foreach ($users as $user) {
            $userAnswers = QuizDomainValueAnswer::where('user_id', $user->id)
                ->whereIn('quiz_domain_value_question_id', $questionIds)
                ->get()
                ->keyBy('quiz_domain_value_question_id');

            if ($userAnswers->isEmpty()) {
                $this->warn("  User {$user->id} ({$user->email}): no answers found, skipping.");
                continue;
            }

            $recompute($oceanQuiz, $user->id, $userAnswers);

            $facetPct = UserResult::where('user_id', $user->id)
                ->where('result_type', 'facet')
                ->avg('percentage');

            $this->info("  User {$user->id} ({$user->email}): recomputed. Avg facet %: " . round($facetPct, 1));
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
