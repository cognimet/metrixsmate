<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizDomainValueAnswer extends Model
{
    protected $table = 'quiz_domain_value_answers';
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(QuizDomainValueQuestion::class, 'quiz_domain_value_question_id');
    }
}
