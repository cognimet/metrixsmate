<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class QuizDomainValueQuestion extends Model
{
    protected $table = 'quiz_domain_value_questions';
    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'options_hi' => 'array',
    ];

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
     * Get the locale-aware options.
     */
    public function getLocaleOptionsAttribute(): ?array
    {
        if (App::getLocale() === 'hi' && !empty($this->options_hi)) {
            return $this->options_hi;
        }
        return $this->options;
    }

    public function domain()
    {
        return $this->belongsTo(QuizDomainValue::class, 'quiz_domain_value_id');
    }
}
