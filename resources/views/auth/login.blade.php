@extends('layouts.auth')

@section('title', __('app.auth_login') . ' — ' . __('app.brand'))
@section('isLoginPage', true)

@section('content')
{{-- ═══ Hero Banner — mirrors welcome page ═══ --}}
<section class="relative py-10 sm:py-16 overflow-hidden hero-pattern">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- Left — Value Proposition --}}
            <div class="hidden lg:block">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-50 text-primary-700 text-sm font-semibold rounded-full mb-6 border border-primary-100">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ __('app.brand') }}
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ __('app.auth_welcome_back') }}
                </h1>
                <p class="mt-4 text-gray-500 text-lg leading-relaxed">
                    {{ __('app.auth_login_subtitle') }}
                </p>

                {{-- Trust indicators — same card style as features --}}
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-4 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ __('app.welcome_feature_ocean_title') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('app.welcome_feature_ocean_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ __('app.welcome_feature_riasec_title') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('app.welcome_feature_riasec_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ __('app.welcome_feature_cognitive_title') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('app.welcome_feature_cognitive_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right — Login Form Card --}}
            <div class="w-full max-w-md mx-auto lg:mx-0">
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">

                    {{-- Mobile-only heading --}}
                    <div class="lg:hidden text-center mb-6">
                        <h1 class="text-2xl font-extrabold text-gray-900">{{ __('app.auth_welcome_back') }}</h1>
                        <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_login_subtitle') }}</p>
                    </div>

                    {{-- Desktop heading --}}
                    <div class="hidden lg:block mb-6">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('app.auth_sign_in') }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_login_subtitle') }}</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="w-full pl-11 pr-4 py-3 border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                                       placeholder="{{ __('app.auth_email_placeholder') }}" required autofocus>
                            </div>
                            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Password --}}
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input :type="show ? 'text' : 'password'" name="password" id="password"
                                       class="w-full pl-11 pr-11 py-3 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                                       placeholder="••••••••" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <span>{{ __('app.auth_remember') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition">{{ __('app.auth_forgot') }}</a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 transform hover:-translate-y-0.5 transition-all duration-200 text-sm">
                            {{ __('app.auth_sign_in') }}
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">{{ __('app.auth_or') }}</span></div>
                    </div>

                    {{-- Google Sign In --}}
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200 mb-4">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Continue with Google
                    </a>

                    {{-- Divider --}}
                    <div class="relative my-4">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">{{ __('app.auth_new_user') }}</span></div>
                    </div>

                    <a href="{{ route('register') }}" class="block w-full text-center py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all duration-200">
                        {{ __('app.auth_create_account') }}
                    </a>
                </div>

                {{-- Back to home --}}
                <div class="text-center mt-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('app.auth_back_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
