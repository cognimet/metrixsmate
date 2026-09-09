<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Quiz extends Model
{
    protected $table = 'quizzes';
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

    /**
     * Get the locale-aware description.
     */
    public function getLocaleDescriptionAttribute(): ?string
    {
        if (App::getLocale() === 'hi' && !empty($this->description_hi)) {
            return $this->description_hi;
        }
        return $this->description;
    }

    public function domains(): HasMany
    {
        return $this->hasMany(QuizDomainValue::class, 'quiz_id');
    }
}
