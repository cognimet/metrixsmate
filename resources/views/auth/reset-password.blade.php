@extends('layouts.auth')

@section('title', __('app.auth_reset_title') . ' — ' . __('app.brand'))
@section('meta_robots', 'noindex, follow')
@section('isLoginPage', true)

@section('content')
<section class="relative py-12 sm:py-20 overflow-hidden hero-pattern flex-1 flex items-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="w-full max-w-md mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl mb-4 shadow-lg shadow-primary-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">{{ __('app.auth_reset_title') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_reset_desc') }}</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_email') }}</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-sm">{{ old('email', $request->email) }}</div>
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_new_password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" id="password"
                               class="w-full pl-11 pr-11 py-3 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                               placeholder="••••••••" required autofocus minlength="8">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_confirm_password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                               placeholder="••••••••" required>
                    </div>
                    @error('password_confirmation')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-500/25 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    {{ __('app.auth_reset_btn_submit') }}
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
