<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_no',
        'city_id',
        'state_id',
        'country_id',
        'google_id',
        'avatar',
        'is_ocean_assessment_completed',
        'is_riasec_assessment_completed',
        'is_cognitive_assessment_completed',
        'free_ai_searches',
        'free_assessment_searches',
        'paid_search_tokens',
        'role',
        'is_active',
        'admin_notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recommendations()
    {
        return $this->hasMany(SchoolRecommendation::class);
    }

    public function recommendationFeebacks()
    {
        return $this->hasMany(RecommendationFeedbackLog::class);
    }

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

    public function hasCertificate(): bool
    {
        return $this->certificate()->exists();
    }

    public function hasCompletedAllAssessments(): bool
    {
        return $this->is_ocean_assessment_completed
            && $this->is_riasec_assessment_completed
            && $this->is_cognitive_assessment_completed;
    }

    /**
     * Check if user can use AI-Powered search
     */
    public function canUseAiSearch(): bool
    {
        return $this->free_ai_searches > 0 || $this->paid_search_tokens > 0;
    }

    /**
     * Check if user can use Assessment-Based search
     */
    public function canUseAssessmentSearch(): bool
    {
        return $this->free_assessment_searches > 0 || $this->paid_search_tokens > 0;
    }

    /**
     * Get remaining free AI searches
     */
    public function getRemainingAiSearches(): int
    {
        return $this->free_ai_searches;
    }

    /**
     * Get remaining free assessment searches
     */
    public function getRemainingAssessmentSearches(): int
    {
        return $this->free_assessment_searches;
    }

    /**
     * Get total remaining searches (free + paid)
     */
    public function getTotalAvailableSearches(): int
    {
        return $this->free_ai_searches + $this->free_assessment_searches + $this->paid_search_tokens;
    }

    /**
     * Consume an AI search token
     */
    public function consumeAiSearch(): bool
    {
        if ($this->free_ai_searches > 0) {
            $this->decrement('free_ai_searches');
            return true;
        } elseif ($this->paid_search_tokens > 0) {
            $this->decrement('paid_search_tokens');
            return true;
        }
        return false;
    }

    /**
     * Consume an Assessment search token
     */
    public function consumeAssessmentSearch(): bool
    {
        if ($this->free_assessment_searches > 0) {
            $this->decrement('free_assessment_searches');
            return true;
        } elseif ($this->paid_search_tokens > 0) {
            $this->decrement('paid_search_tokens');
            return true;
        }
        return false;
    }

    /**
     * Add paid search tokens
     */
    public function addSearchTokens(int $count): void
    {
        $this->increment('paid_search_tokens', $count);
    }

    /**
     * Reset free searches (for new users or monthly reset)
     */
    public function resetFreeSearches(): void
    {
        $this->update([
            'free_ai_searches' => 3,
            'free_assessment_searches' => 3,
        ]);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->is_active === 1;
    }

    /**
     * Make user an admin
     */
    public function makeAdmin(): void
    {
        $this->update(['role' => 'admin']);
    }

    /**
     * Make user a regular student
     */
    public function makeStudent(): void
    {
        $this->update(['role' => 'student']);
    }

    /**
     * Deactivate account
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate account
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function userResults()
    {
        return $this->hasMany(UserResult::class);
    }

    public function schoolRecommendations()
    {
        return $this->hasMany(SchoolRecommendation::class);
    }
}
