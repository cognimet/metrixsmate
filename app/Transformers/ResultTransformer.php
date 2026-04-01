<?php

namespace App\Transformers;

use App\Models\User;

/**
 * Transforms assessment result data into a structured, API-ready array.
 * Used by ResultController::exportJson() to produce the JSON download.
 */
class ResultTransformer
{
    /**
     * @param  User   $user
     * @param  array  $oceanResults          Output of ResultService::getOceanResults()
     * @param  array  $riasecResults         Output of ResultService::getRiasecResults()
     * @param  array  $cognitiveResults      Output of ResultService::getCognitiveResults()
     * @param  array  $streamRecommendations Output of ResultService::getStreamRecommendations()
     * @return array
     */
    public function transform(
        User  $user,
        array $oceanResults,
        array $riasecResults,
        array $cognitiveResults,
        array $streamRecommendations
    ): array {
        return [
            'version'      => '1.0',
            'generated_at' => now()->toIso8601String(),

            'student' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],

            'summary' => [
                'personality_score' => round($oceanResults['domains']->avg('percentage') ?? 0, 1),
                'career_score'      => round($riasecResults['domains']->avg('percentage') ?? 0, 1),
                'cognitive_score'   => round($cognitiveResults['average_score'] ?? 0, 1),
                'holland_code'      => $riasecResults['holland_code'] ?? '',
                'top_stream'        => $streamRecommendations[0]['name'] ?? null,
            ],

            'personality' => $oceanResults['domains']->map(fn ($d) => [
                'name'              => $d->name_en,
                'percentage'        => round($d->percentage, 1),
                'performance_level' => $d->performance_level,
                'performance_text'  => $d->performance_text,
            ])->values()->all(),

            'career_interests' => $riasecResults['domains']->map(fn ($d) => [
                'name'              => $d->name_en,
                'percentage'        => round($d->percentage, 1),
                'performance_level' => $d->performance_level,
                'performance_text'  => $d->performance_text,
            ])->values()->all(),

            'cognitive_abilities' => [
                'domains'       => $cognitiveResults['domains']->map(fn ($d) => [
                    'name'              => $d->name_en,
                    'percentage'        => round($d->percentage, 1),
                    'performance_level' => $d->performance_level,
                    'performance_text'  => $d->performance_text,
                ])->values()->all(),
                'average_score' => round($cognitiveResults['average_score'] ?? 0, 1),
            ],

            'stream_recommendations' => array_map(fn ($s) => [
                'name'                 => $s['name'],
                'description'          => $s['description'],
                'score'                => $s['score'],
                'recommendation'       => $s['recommendation'],
                'recommendation_label' => $s['recommendation_label'],
                'careers'              => $s['careers'],
            ], $streamRecommendations),
        ];
    }
}
