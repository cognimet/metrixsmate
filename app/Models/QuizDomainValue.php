<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class QuizDomainValue extends Model
{
    protected $table = 'quiz_domain_values';
    protected $guarded = [];

    /**
     * Get the locale-aware title.
     */
    public function getLocaleTitleAttribute(): string
    {
        if (App::getLocale() === 'hi' && !empty($this->title_hi)) {
            return $this->title_hi;
        }
        return $this->title;
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizDomainValueQuestion::class, 'quiz_domain_value_id');
    }
}
