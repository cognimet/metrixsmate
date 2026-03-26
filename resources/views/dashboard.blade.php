@extends('layouts.app')

@section('title', __('app.dashboard') . ' — ' . __('app.brand'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('app.dash_welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="mt-1 text-gray-500">{{ __('app.dash_subtitle') }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8">

    {{-- ═══ Access Control Section (Global) ═══ --}}
    @php
        $user = auth()->user();
        $quizzes = isset($quizzes) ? $quizzes : \App\Models\Quiz::all();
        $hasFullAccess = $quizzes->every(fn($q) => \App\Models\QuizAccess::hasAccess($user, $q));
    @endphp

    @if(!$hasFullAccess)
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-amber-900 mb-2">Unlock All Assessments</h3>
                <p class="text-amber-800 mb-4">Get full access to all assessments by making a payment or using a coupon code.</p>
                <div class="flex gap-3 flex-wrap">
                    <button onclick="showCouponModal()"
                        class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m-4 11H9m6 0h.01M9 17h.01M9 4h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        Have a Coupon?
                    </button>
                    <a href="{{ route('payments.index') }}"
                        class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white font-semibold rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pay ₹999 to Unlock
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-green-900">All Assessments Unlocked</h3>
                <p class="text-green-800 text-sm">You have full access to all assessments.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ Progress Overview ═══ --}}
    @php
        $totalAssessments = 3;
        $completedCount = 0;
        if (auth()->user()->is_ocean_assessment_completed) $completedCount++;
        if (auth()->user()->is_riasec_assessment_completed) $completedCount++;
        if (auth()->user()->is_cognitive_assessment_completed) $completedCount++;
        $pct = round(($completedCount / $totalAssessments) * 100);
    @endphp
    <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">{{ __('app.dash_progress') }}</h2>
            <span class="text-sm font-semibold text-primary-600">{{ __('app.dash_complete_count', ['done' => $completedCount, 'total' => $totalAssessments]) }}</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-500 to-accent-500 h-3 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
        </div>
        <p class="mt-3 text-sm text-gray-500">
            @if($completedCount === 0)
                {{ __('app.dash_start_msg') }}
            @elseif($completedCount < $totalAssessments)
                {{ __('app.dash_progress_msg', ['remaining' => $totalAssessments - $completedCount]) }}
            @else
                {{ __('app.dash_complete_msg') }}
            @endif
        </p>
    </div>

    {{-- ═══ Assessment Cards ═══ --}}
    @if(isset($quizzes) && $quizzes->count())
    <div>
        <h2 class="text-lg font-bold text-gray-900 mb-5">{{ __('app.dash_available') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($quizzes as $quiz)
                @php
                    $user = auth()->user();
                    $completed = false;
                    $icon = '';
                    $gradient = '';
                    $slug = strtolower($quiz->title);

                    if ($slug === 'ocean') {
                        $completed = $user->is_ocean_assessment_completed;
                        $icon = 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                        $gradient = 'from-green-400 to-emerald-600';
                    } elseif ($slug === 'riasec') {
                        $completed = $user->is_riasec_assessment_completed;
                        $icon = 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z';
                        $gradient = 'from-blue-400 to-indigo-600';
                    } elseif ($slug === 'cognitive') {
                        $completed = $user->is_cognitive_assessment_completed;
                        $icon = 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z';
                        $gradient = 'from-purple-400 to-pink-600';
                    }
                @endphp

                <div class="bg-white rounded-2xl shadow-card hover:shadow-card-hover transition-all transform hover:-translate-y-1 overflow-hidden border border-gray-100">
                    {{-- Card Header --}}
                    <div class="bg-gradient-to-br {{ $gradient }} p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                            </div>
                            @if($completed)
                            <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                                <span class="text-white text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    {{ __('app.dash_completed_badge') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-white">{{ $quiz->locale_title }}</h3>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6">
                        <p class="text-gray-500 text-sm mb-6 leading-relaxed">{{ $quiz->locale_description }}</p>

                        @if($completed)
                            <a href="{{ route('results.show', $quiz->id) }}"
                               class="block text-center px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 shadow-sm transition">
                                {{ __('app.dash_view_results') }}
                            </a>
                        @elseif($hasFullAccess)
                            <a href="{{ route('assessments.showAll', $quiz->id) }}"
                               class="block text-center px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 shadow-sm transition">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ __('app.dash_start_assessment') }}
                                </span>
                            </a>
                        @else
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 font-medium">Unlock to start</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ All-Complete Banner ═══ --}}
    @php
        $allCompleted = auth()->user()->is_ocean_assessment_completed &&
                        auth()->user()->is_riasec_assessment_completed &&
                        auth()->user()->is_cognitive_assessment_completed;
    @endphp

    @if($allCompleted)
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-green-500 rounded-full mb-4">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('app.dash_all_complete_title') }}</h3>
        <p class="text-gray-600 mb-6">{{ __('app.dash_all_complete_desc') }}</p>
        <a href="{{ route('results.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary-600 text-white rounded-xl font-bold shadow-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            {{ __('app.dash_view_complete') }}
        </a>
    </div>
    @endif

    @else
    <div class="bg-white rounded-2xl shadow-card p-12 text-center border border-gray-100">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full mb-4">
            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('app.dash_no_assessments') }}</h3>
        <p class="text-gray-500 text-sm">{{ __('app.dash_no_assessments_msg') }}</p>
    </div>
    @endif
</div>

{{-- Include Coupon Modal Component --}}
@include('components.coupon-modal')

@endsection
