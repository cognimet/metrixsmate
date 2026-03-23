<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        // load quizzes and completion flags from users table
        $quizzes = Quiz::with('domains')->get()->map(function($quiz) use ($user) {
            $quiz->is_completed = false;
            if ($quiz->slug === 'ocean' && $user->is_ocean_assessment_completed) $quiz->is_completed = true;
            if ($quiz->slug === 'riasec' && $user->is_riasec_assessment_completed) $quiz->is_completed = true;
            if ($quiz->slug === 'cognitive' && $user->is_cognitive_assessment_completed) $quiz->is_completed = true;
            return $quiz;
        });

        return view('dashboard', compact('quizzes'));
    }

    public function start(Quiz $quiz)
    {
        // show domain selection / start screen
        $domains = $quiz->domains()->orderBy('order')->get();
        return view('quizzes.start', compact('quiz', 'domains'));
    }
}
