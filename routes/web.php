<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SchoolFinderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Public certificate verification
Route::get('/verify-certificate', [CertificateController::class, 'verify'])->name('certificates.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');

    // Assessment flow
    Route::get('/assessments/{quiz}/all', [AssessmentController::class, 'showAll'])->name('assessments.showAll');
    Route::post('/assessments/{quiz}/all/submit', [AssessmentController::class, 'submitAll'])->name('assessments.submitAll');

    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{quiz}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/results/{quiz}/download', [ResultController::class, 'downloadReport'])->name('results.download');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');

    // Coupons
    Route::post('/coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
    Route::post('/coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');

    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // School Finder
    Route::get('/school-finder', [SchoolFinderController::class, 'index'])->name('school-finder.index');
    Route::get('/school-finder/states/{country}', [SchoolFinderController::class, 'getStates'])->name('school-finder.states');
    Route::get('/school-finder/cities/{state}', [SchoolFinderController::class, 'getCities'])->name('school-finder.cities');
    Route::post('/school-finder/search', [SchoolFinderController::class, 'findSchools'])->name('school-finder.search');
    
    // School Recommendation APIs
    Route::get('/school-finder/recommendations', [SchoolFinderController::class, 'getRecommendations'])->name('school-finder.recommendations');
    Route::get('/school-finder/detail/{recommendation}', [SchoolFinderController::class, 'getDetail'])->name('school-finder.detail');
    Route::post('/school-finder/feedback/{recommendation}', [SchoolFinderController::class, 'recordFeedback'])->name('school-finder.feedback');
    Route::get('/school-finder/analytics', [SchoolFinderController::class, 'getAnalytics'])->name('school-finder.analytics');
    Route::get('/school-finder/search-dynamic', [SchoolFinderController::class, 'searchSchools'])->name('school-finder.search-dynamic');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Quiz start (legacy)
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
});

require __DIR__.'/auth.php';