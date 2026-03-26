<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicSchool extends Model
{
    use SoftDeletes;

    protected $table = 'dynamic_schools';
    protected $guarded = [];

    protected $casts = [
        'strengths' => 'array',
        'facilities' => 'array',
        'programs' => 'array',
        'specializations' => 'array',
        'fees_range' => 'array',
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'last_verified_at' => 'datetime',
        'last_refreshed_at' => 'datetime',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function recommendations()
    {
        return $this->hasMany(SchoolRecommendation::class);
    }

    public function feedbackLogs()
    {
        return $this->through('recommendations')->hasManyThrough(
            RecommendationFeedbackLog::class,
            SchoolRecommendation::class,
            'dynamic_school_id',
            'school_recommendation_id'
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        // Include all non-deleted schools (both verified and AI-discovered)
        // The soft delete already handles filtering out invalid schools
        return $query->whereNotNull('name')->whereNotNull('city_id');
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeInState($query, $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighestRated($query, $limit = 10)
    {
        return $query->orderByDesc('rating')->limit($limit);
    }

    public function scopeNearby($query, $latitude, $longitude, $radiusKm = 15)
    {
        // Haversine formula for distance calculation
        $query->selectRaw(
            "*, (6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) " .
            "* cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) " .
            "* sin(radians(latitude)))) AS distance"
        )
        ->whereRaw(
            "ABS(latitude - $latitude) < " . ($radiusKm / 111) . " AND " .
            "ABS(longitude - $longitude) < " . ($radiusKm / 111)
        )
        ->having('distance', '<=', $radiusKm)
        ->orderByRaw('distance');

        return $query;
    }

    public function scopeRecentlyUpdated($query, $days = 30)
    {
        return $query->where('last_refreshed_at', '>=', now()->subDays($days));
    }

    public function scopeByDataSource($query, $source)
    {
        return $query->where('data_source', $source);
    }

    // Methods
    public function getRelevanceScore()
    {
        $score = 0;

        // Rating weight
        $score += ($this->rating / 5) * 20;

        // Verification weight
        $score += $this->is_verified ? 15 : 0;

        // Recency weight
        if ($this->last_refreshed_at) {
            $daysOld = now()->diffInDays($this->last_refreshed_at);
            $score += max(0, 20 - ($daysOld / 2));
        }

        // Data completeness weight
        $completeness = collect([
            $this->description,
            $this->address,
            $this->phone,
            $this->email,
            $this->website,
            $this->academic_performance,
        ])->filter()->count() / 6;
        $score += $completeness * 25;

        return min(100, round($score, 2));
    }

    public function getHighlights()
    {
        $highlights = [];

        if (!empty($this->strengths)) {
            $highlights['strengths'] = array_slice($this->strengths, 0, 3);
        }

        if (!empty($this->facilities)) {
            $highlights['facilities'] = array_slice($this->facilities, 0, 3);
        }

        if (!empty($this->specializations)) {
            $highlights['specializations'] = array_slice($this->specializations, 0, 2);
        }

        if ($this->rating > 4.5) {
            $highlights['rating'] = "Highly Rated ({$this->rating}/5)";
        }

        return $highlights;
    }

    public function shouldRefresh()
    {
        // Refresh if never refreshed or older than 30 days
        if (!$this->last_refreshed_at) {
            return true;
        }

        return now()->diffInDays($this->last_refreshed_at) >= 30;
    }
}
