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
        'is_cognitive_assessment_completed'
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
}
