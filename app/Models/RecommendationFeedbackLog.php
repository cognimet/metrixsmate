<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationFeedbackLog extends Model
{
    protected $table = 'recommendation_feedback_logs';
    protected $guarded = [];

    protected $casts = [
        'user_assessment_snapshot' => 'array',
        'school_data_snapshot' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolRecommendation()
    {
        return $this->belongsTo(SchoolRecommendation::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByFeedbackType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function isPositiveFeedback()
    {
        return in_array($this->feedback_type, ['liked', 'interested', 'enrolled']);
    }

    public function isNegativeFeedback()
    {
        return in_array($this->feedback_type, ['disliked']);
    }

    public function isEngagement()
    {
        return in_array($this->feedback_type, ['viewed', 'clicked']);
    }
}
