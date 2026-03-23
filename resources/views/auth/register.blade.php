@extends('layouts.auth')

@section('title', __('app.auth_register') . ' — ' . __('app.brand'))

@section('content')
{{-- ═══ Hero Banner — mirrors welcome page ═══ --}}
<section class="relative py-10 sm:py-16 overflow-hidden hero-pattern">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start">

            {{-- Left — Value Proposition --}}
            <div class="hidden lg:block lg:sticky lg:top-24">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-50 text-primary-700 text-sm font-semibold rounded-full mb-6 border border-primary-100">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ __('app.brand') }}
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ __('app.auth_create_heading') }}
                </h1>
                <p class="mt-4 text-gray-500 text-lg leading-relaxed">
                    {{ __('app.auth_register_subtitle') }}
                </p>

                {{-- How It Works — same step cards as welcome --}}
                <div class="mt-8 space-y-4">
                    @php $regSteps = [
                        ['num' => '01', 'title' => __('app.welcome_step1_title'), 'desc' => __('app.welcome_step1_desc'), 'gradient' => 'from-green-400 to-emerald-600'],
                        ['num' => '02', 'title' => __('app.welcome_step2_title'), 'desc' => __('app.welcome_step2_desc'), 'gradient' => 'from-blue-400 to-indigo-600'],
                        ['num' => '03', 'title' => __('app.welcome_step3_title'), 'desc' => __('app.welcome_step3_desc'), 'gradient' => 'from-purple-400 to-pink-600'],
                    ]; @endphp
                    @foreach($regSteps as $step)
                    <div class="flex items-start gap-4 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $step['gradient'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <span class="text-white text-xs font-black">{{ $step['num'] }}</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $step['title'] }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Testimonial snippet --}}
                <div class="mt-8 bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <svg class="w-6 h-6 text-primary-200 mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    <p class="text-gray-600 italic text-sm leading-relaxed">{{ __('app.welcome_testimonial_1') }}</p>
                    <p class="mt-3 font-bold text-gray-900 text-sm">— {{ __('app.welcome_testimonial_1_author') }}</p>
                </div>
            </div>

            {{-- Right — Register Form Card --}}
            <div class="w-full max-w-md mx-auto lg:mx-0">
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">

                    {{-- Mobile-only heading --}}
                    <div class="lg:hidden text-center mb-6">
                        <h1 class="text-2xl font-extrabold text-gray-900">{{ __('app.auth_create_heading') }}</h1>
                        <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_register_subtitle') }}</p>
                    </div>

                    {{-- Desktop heading --}}
                    <div class="hidden lg:block mb-6">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('app.auth_register') }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ __('app.auth_register_subtitle') }}</p>
                    </div>

                    <form action="{{ route('register') }}" method="POST" class="space-y-5" id="registerForm">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       class="w-full pl-11 pr-4 py-3 border @error('name') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                                       placeholder="{{ __('app.auth_name_placeholder') }}" required minlength="2" autofocus>
                            </div>
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="w-full pl-11 pr-4 py-3 border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                                       placeholder="{{ __('app.auth_email_placeholder') }}" required>
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
                                       placeholder="••••••••" required minlength="8">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            {{-- Strength indicator --}}
                            <div class="mt-2.5">
                                <div class="flex gap-1 mb-1.5">
                                    <div id="sb1" class="h-1 flex-1 bg-gray-200 rounded-full transition-colors"></div>
                                    <div id="sb2" class="h-1 flex-1 bg-gray-200 rounded-full transition-colors"></div>
                                    <div id="sb3" class="h-1 flex-1 bg-gray-200 rounded-full transition-colors"></div>
                                    <div id="sb4" class="h-1 flex-1 bg-gray-200 rounded-full transition-colors"></div>
                                </div>
                                <p id="stxt" class="text-xs text-gray-400">{{ __('app.auth_password_hint') }}</p>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth_confirm_password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                                       class="w-full pl-11 pr-11 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50/50 text-sm"
                                       placeholder="••••••••" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <p id="confirmErr" class="mt-1.5 text-sm text-red-600 hidden"></p>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 transform hover:-translate-y-0.5 transition-all duration-200 text-sm">
                            {{ __('app.auth_register_btn') }}
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">{{ __('app.auth_have_account') }}</span></div>
                    </div>

                    <a href="{{ route('login') }}" class="block w-full text-center py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all duration-200">
                        {{ __('app.auth_sign_in_instead') }}
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pw = document.getElementById('password');
    const pc = document.getElementById('password_confirmation');
    const bars = [document.getElementById('sb1'),document.getElementById('sb2'),document.getElementById('sb3'),document.getElementById('sb4')];
    const stxt = document.getElementById('stxt');
    const confirmErr = document.getElementById('confirmErr');

    pw.addEventListener('input', function() {
        let s = 0, v = this.value;
        if (v.length >= 8) s++;
        if (v.length >= 12) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^a-zA-Z0-9]/.test(v)) s++;

        bars.forEach(b => b.className = 'h-1 flex-1 bg-gray-200 rounded-full transition-colors');
        const colors = ['','bg-red-500','bg-yellow-500','bg-blue-500','bg-green-500'];
        const labels = ['{{ __("app.auth_password_hint") }}','{{ __("app.auth_pw_weak") }}','{{ __("app.auth_pw_medium") }}','{{ __("app.auth_pw_good") }}','{{ __("app.auth_pw_strong") }}'];
        const textColors = ['text-gray-400','text-red-500','text-yellow-600','text-blue-600','text-green-600'];
        let level = s === 0 ? 0 : (s <= 2 ? 1 : (s === 3 ? 2 : (s === 4 ? 3 : 4)));
        for (let i = 0; i < level; i++) bars[i].className = 'h-1 flex-1 ' + colors[level] + ' rounded-full transition-colors';
        stxt.textContent = labels[level];
        stxt.className = 'text-xs ' + textColors[level];

        if (pc.value.length > 0) checkMatch();
    });

    pc.addEventListener('input', checkMatch);

    function checkMatch() {
        if (pc.value.length === 0) { confirmErr.classList.add('hidden'); return; }
        if (pw.value !== pc.value) {
            confirmErr.textContent = '{{ __("app.auth_pw_mismatch") }}';
            confirmErr.classList.remove('hidden');
            pc.closest('.relative').querySelector('input').classList.add('border-red-400');
            pc.closest('.relative').querySelector('input').classList.remove('border-gray-200');
        } else {
            confirmErr.classList.add('hidden');
            pc.closest('.relative').querySelector('input').classList.remove('border-red-400');
            pc.closest('.relative').querySelector('input').classList.add('border-gray-200');
        }
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (pw.value !== pc.value) { e.preventDefault(); checkMatch(); pc.focus(); }
    });
});
</script>
@endsection
