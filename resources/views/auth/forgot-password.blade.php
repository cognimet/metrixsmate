@extends('layouts.auth')

@section('title', __('app.auth_forgot_title') . ' — ' . __('app.brand'))
@section('meta_robots', 'noindex, follow')
@section('isLoginPage', true)

@section('content')
<section class="relative py-12 sm:py-20 overflow-hidden hero-pattern flex-1 flex items-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="w-full max-w-md mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl mb-4 shadow-lg shadow-primary-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">{{ __('app.auth_forgot_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_forgot_desc') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium text-green-700">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full pl-11 pr-4 py-3 border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                               placeholder="{{ __('app.auth_email_placeholder') }}" required autofocus>
                    </div>
                    @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-500/25 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    {{ __('app.auth_reset_btn') }}
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('app.auth_back_login') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
