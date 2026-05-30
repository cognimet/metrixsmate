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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;

Route::get('/', function () {
    return view('welcome');
});

// Public school search (no auth required) — powers the landing page School Finder widget
Route::get('/schools/public-search', [SchoolFinderController::class, 'publicSearch'])->name('schools.public-search');

// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Public certificate verification
Route::get('/verify-certificate', [CertificateController::class, 'verify'])->name('certificates.verify');

// Payment webhooks
Route::post('/webhooks/razorpay', [PaymentController::class, 'handleRazorpayWebhook'])->name('webhooks.razorpay');
Route::post('/webhooks/paypal', [PaymentController::class, 'handlePayPalWebhook'])->name('webhooks.paypal');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');

    // Assessment flow
    Route::get('/assessments/{quiz}/all', [AssessmentController::class, 'showAll'])->name('assessments.showAll');
    Route::post('/assessments/{quiz}/all/submit', [AssessmentController::class, 'submitAll'])->name('assessments.submitAll');

    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/export/json', [ResultController::class, 'exportJson'])->name('results.export');
    Route::get('/results/{quiz}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/results/{quiz}/download', [ResultController::class, 'downloadReport'])->name('results.download');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/payments/{payment}/razorpay/complete', [PaymentController::class, 'completeRazorpay'])->name('payments.razorpay.complete');
    Route::post('/payments/{payment}/paypal/capture', [PaymentController::class, 'capturePayPal'])->name('payments.paypal.capture');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');

    // Coupons
    Route::post('/coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
    Route::post('/coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');

    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // School Finder (assessment-based)
    Route::get('/school-finder', [SchoolFinderController::class, 'index'])->name('school-finder.index');
    Route::get('/school-finder/states/{country}', [SchoolFinderController::class, 'getStates'])->name('school-finder.states');
    Route::get('/school-finder/cities/{state}', [SchoolFinderController::class, 'getCities'])->name('school-finder.cities');
    Route::post('/school-finder/search', [SchoolFinderController::class, 'findSchools'])->name('school-finder.search');
    
    // Unified MetrixsMate AI School Finder (live, location-aware, no assessment required)
    Route::get('/school-finder/ai', [SchoolFinderController::class, 'showSearchForm'])->name('school-finder.ai.index');
    Route::post('/school-finder/ai/search', [SchoolFinderController::class, 'search'])->name('school-finder.ai.search');
    
    // School Recommendation APIs
    Route::get('/school-finder/recommendations', [SchoolFinderController::class, 'getRecommendations'])->name('school-finder.recommendations');
    Route::get('/school-finder/detail/{recommendation}', [SchoolFinderController::class, 'getDetail'])->name('school-finder.detail');
    Route::post('/school-finder/feedback/{recommendation}', [SchoolFinderController::class, 'recordFeedback'])->name('school-finder.feedback');
    Route::get('/school-finder/analytics', [SchoolFinderController::class, 'getAnalytics'])->name('school-finder.analytics');
    Route::get('/school-finder/search-dynamic', [SchoolFinderController::class, 'searchSchools'])->name('school-finder.search-dynamic');

    // Gemini AI School Finder (legacy - will be deprecated in favor of unified AI finder)
    Route::get('/school-finder/gemini', [SchoolFinderController::class, 'geminiIndex'])->name('school-finder.gemini');
    Route::post('/school-finder/gemini/search', [SchoolFinderController::class, 'geminiSearch'])->name('school-finder.gemini.search');
    Route::post('/school-finder/gemini/ajax', [SchoolFinderController::class, 'geminiAjaxSearch'])->name('school-finder.gemini.ajax');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Quiz start (legacy)
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
});

// Admin Routes
Route::middleware(['auth', 'admin', 'throttle:120,1'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'userShow'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'userEdit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'userUpdate'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'userDelete'])->name('users.delete');
    Route::delete('/users', [AdminController::class, 'userBulkDelete'])->name('users.bulk-delete');

    // Individual user report downloads
    Route::get('/users/{user}/report/school', [AdminReportController::class, 'userSchoolReport'])->name('users.report.school');
    Route::get('/users/{user}/report/university', [AdminReportController::class, 'userUniversityReport'])->name('users.report.university');
    Route::get('/users/{user}/report/company', [AdminReportController::class, 'userCompanyReport'])->name('users.report.company');

    // Individual user categorized detail pages
    Route::get('/users/{user}/school', [AdminController::class, 'userSchoolDetail'])->name('users.school');
    Route::get('/users/{user}/university', [AdminController::class, 'userUniversityDetail'])->name('users.university');
    Route::get('/users/{user}/company', [AdminController::class, 'userCompanyDetail'])->name('users.company');

    // Payment Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [AdminController::class, 'paymentShow'])->name('payments.show');
    Route::post('/payments/{payment}/verify', [AdminController::class, 'paymentVerify'])->name('payments.verify');

    // Coupon Management
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::get('/coupons/create', [AdminController::class, 'couponCreate'])->name('coupons.create');
    Route::post('/coupons', [AdminController::class, 'couponStore'])->name('coupons.store');
    Route::get('/coupons/{coupon}', [AdminController::class, 'couponShow'])->name('coupons.show');
    Route::get('/coupons/{coupon}/edit', [AdminController::class, 'couponEdit'])->name('coupons.edit');
    Route::patch('/coupons/{coupon}', [AdminController::class, 'couponUpdate'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminController::class, 'couponDelete'])->name('coupons.delete');

    // Reports & Analytics
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    
    // Results & Assessments
    Route::get('/results', [AdminController::class, 'results'])->name('results');
    Route::get('/results/{result}', [AdminController::class, 'resultShow'])->name('results.show');
    Route::get('/assessments/{userResult}', [AdminController::class, 'assessmentShow'])->name('assessments.show');
    Route::get('/assessments/{userResult}/download', [AdminController::class, 'downloadAssessmentReport'])->name('assessments.download');
    
    // Analytics
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

    // Downloadable Reports (School / University / Company)
    Route::get('/reports/generate', [AdminReportController::class, 'index'])->name('reports.generate');
    Route::post('/reports/school', [AdminReportController::class, 'schoolReport'])->name('reports.school');
    Route::post('/reports/university', [AdminReportController::class, 'universityReport'])->name('reports.university');
    Route::post('/reports/company', [AdminReportController::class, 'companyReport'])->name('reports.company');
});

require __DIR__.'/auth.php';