<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolRecommendation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'match_factors' => 'array',
        'strengths_alignment' => 'array',
        'personality_alignment' => 'array',
        'compatibility_score' => 'decimal:2',
        'riasec_match_score' => 'decimal:2',
        'cognitive_match_score' => 'decimal:2',
        'ocean_match_score' => 'decimal:2',
        'academic_performance_score' => 'decimal:2',
        'location_proximity_score' => 'decimal:2',
        'viewed_at' => 'datetime',
        'user_feedback_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dynamicSchool()
    {
        return $this->belongsTo(DynamicSchool::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function feedbackLogs()
    {
        return $this->hasMany(RecommendationFeedbackLog::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeTopRanked($query, $count = 5)
    {
        return $query->orderBy('rank')->limit($count);
    }

    public function scopeByScore($query)
    {
        return $query->orderByDesc('compatibility_score');
    }

    public function scopeWithPositiveFeedback($query)
    {
        return $query->whereIn('user_feedback', [1, 2]);
    }

    public function scopeWithoutFeedback($query)
    {
        return $query->whereNull('user_feedback');
    }

    // Methods
    public function logFeedback($feedbackType, $context = null)
    {
        return $this->feedbackLogs()->create([
            'feedback_type' => $feedbackType,
            'feedback_context' => $context,
            'user_assessment_snapshot' => $this->captureUserAssessmentSnapshot(),
            'school_data_snapshot' => $this->dynamicSchool->toArray(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function recordView()
    {
        $this->increment('view_count');
        $this->update(['viewed_at' => now()]);
        $this->logFeedback('viewed');
    }

    public function recordFeedback($feedback, $comment = null)
    {
        $mappings = [
            'like' => 1,
            'dislike' => -1,
            'interested' => 1,
            'not_interested' => -1,
            'interested_to_enroll' => 2,
        ];

        $feedbackValue = $mappings[$feedback] ?? 0;
        
        $this->update([
            'user_feedback' => $feedbackValue,
            'user_feedback_comment' => $comment,
            'user_feedback_at' => now(),
        ]);

        $this->logFeedback($feedback, $comment);

        return $this;
    }

    public function getScoreBreakdown()
    {
        return [
            'overall' => $this->compatibility_score,
            'riasec_match' => $this->riasec_match_score,
            'cognitive_match' => $this->cognitive_match_score,
            'ocean_match' => $this->ocean_match_score,
            'academic_performance' => $this->academic_performance_score,
            'location_proximity' => $this->location_proximity_score,
        ];
    }

    public function getRecommendationSummary()
    {
        return [
            'school' => $this->dynamicSchool,
            'rank' => $this->rank,
            'score' => $this->compatibility_score,
            'reasoning' => $this->ai_reasoning,
            'match_factors' => $this->match_factors,
            'highlights' => $this->dynamicSchool->getHighlights(),
            'feedback' => $this->user_feedback,
        ];
    }

    protected function captureUserAssessmentSnapshot()
    {
        $user = $this->user;
        
        return [
            'user_id' => $user->id,
            'timestamp' => now(),
            'riasec_results' => UserResult::where('user_id', $user->id)
                ->where('assessment_type', 'riasec')
                ->get()
                ->keyBy('name')
                ->map(fn($r) => $r->percentage)
                ->toArray(),
            'cognitive_results' => UserResult::where('user_id', $user->id)
                ->where('assessment_type', 'cognitive')
                ->get()
                ->keyBy('name')
                ->map(fn($r) => $r->percentage)
                ->toArray(),
            'ocean_results' => UserResult::where('user_id', $user->id)
                ->where('assessment_type', 'ocean')
                ->get()
                ->keyBy('name')
                ->map(fn($r) => $r->percentage)
                ->toArray(),
        ];
    }
}
