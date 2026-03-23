<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ResultController;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });
Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');

    // start quiz (select domain set / start)
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');

    // assessment flow
    // Route::get('/assessments/{quiz}/{domainId}', [AssessmentController::class, 'show'])->name('assessments.show');
    // Route::post('/assessments/{quiz}/{domainId}/submit', [AssessmentController::class, 'submit'])->name('assessments.submit');

    // result
    Route::get('/results/{quiz}', [ResultController::class, 'show'])->name('results.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/assessments/{quiz}/all', [AssessmentController::class, 'showAll'])->name('assessments.showAll');
    Route::post('/assessments/{quiz}/all/submit', [AssessmentController::class, 'submitAll'])->name('assessments.submitAll');

    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{quiz}/download', [ResultController::class, 'downloadReport'])->name('results.download');
});

require __DIR__.'/auth.php';