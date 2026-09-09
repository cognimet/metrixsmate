@extends('layouts.auth')

@section('title', __('app.auth_verify_title') . ' — ' . __('app.brand'))
@section('meta_robots', 'noindex, follow')

@section('content')
<section class="relative py-12 sm:py-20 overflow-hidden hero-pattern flex-1 flex items-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="w-full max-w-md mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-accent-500 to-accent-700 rounded-2xl mb-4 shadow-lg shadow-accent-500/25">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
            </div>

            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('app.auth_verify_title') }}</h1>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">{{ __('app.auth_verify_desc') }}</p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium text-green-700">{{ __('app.auth_verify_sent') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-500/25 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    {{ __('app.auth_verify_resend') }}
                </button>
            </form>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                {{ __('app.auth_logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </div>
</section>
@endsection
