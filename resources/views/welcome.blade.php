<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.brand') }} — {{ __('app.welcome_hero_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'},
                    accent:  {50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',400:'#2dd4bf',500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59',900:'#134e4a'},
                },
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-pattern { background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,.15) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(20,184,166,.1) 0%, transparent 50%); }
    </style>
</head>
<body class="bg-white text-gray-800">

{{-- ═══ Header ═══ --}}
<header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-base sm:text-lg font-bold text-gray-900">{{ __('app.brand') }}</span>
        </a>

        <nav class="hidden md:flex items-center gap-6">
            <a href="#features" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">{{ __('app.welcome_features_title') }}</a>
            <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">{{ __('app.welcome_how_title') }}</a>
            <a href="#testimonials" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">{{ __('app.welcome_testimonials_title') }}</a>
            <a href="#faq" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">{{ __('app.welcome_faq_title') }}</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="flex items-center bg-gray-100 rounded-lg p-0.5 text-sm font-medium">
                <a href="?lang=en" class="px-2 sm:px-2.5 py-1 rounded-md transition {{ app()->getLocale()==='en' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                <a href="?lang=hi" class="px-2 sm:px-2.5 py-1 rounded-md transition {{ app()->getLocale()==='hi' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">हिं</a>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-primary-600 transition hidden sm:inline">{{ __('app.login') }}</a>
            <a href="{{ route('register') }}" class="px-3 sm:px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 shadow-sm transition">{{ __('app.register') }}</a>
        </div>
    </div>
</header>

{{-- ═══ Hero ═══ --}}
<section class="relative pt-24 sm:pt-32 pb-16 sm:pb-24 overflow-hidden hero-pattern">
    {{-- Decorative gradients --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-100/40 to-accent-100/30 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-100/30 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-50 text-primary-700 text-sm font-semibold rounded-full mb-6 sm:mb-8 border border-primary-100">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            {{ __('app.brand') }}
        </div>
        <h1 class="text-3xl sm:text-5xl md:text-7xl font-extrabold text-gray-900 leading-tight tracking-tight">
            {{ __('app.welcome_hero_title') }}
        </h1>
        <p class="mt-4 sm:mt-6 text-base sm:text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
            {{ __('app.welcome_hero_subtitle') }}
        </p>
        <div class="mt-8 sm:mt-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
            <a href="{{ route('register') }}" class="px-6 sm:px-8 py-3.5 sm:py-4 bg-primary-600 text-white font-bold rounded-xl shadow-lg hover:bg-primary-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all text-base sm:text-lg">
                {{ __('app.welcome_cta_start') }}
            </a>
            <a href="#features" class="px-6 sm:px-8 py-3.5 sm:py-4 bg-white text-gray-700 font-bold rounded-xl shadow border border-gray-200 hover:border-primary-300 hover:text-primary-600 transition text-base sm:text-lg">
                {{ __('app.welcome_cta_learn') }}
            </a>
        </div>
    </div>
</section>

{{-- ═══ Features ═══ --}}
<section id="features" class="py-16 sm:py-24 bg-gray-50/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900">{{ __('app.welcome_features_title') }}</h2>
        <p class="mt-4 text-gray-500 max-w-xl mx-auto text-base sm:text-lg">{{ __('app.welcome_features_desc') }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 mt-12 sm:mt-16">
            {{-- OCEAN --}}
            <div class="relative bg-white rounded-2xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-gray-100 transition-all group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-emerald-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-emerald-600 rounded-2xl sm:rounded-3xl flex items-center justify-center mb-5 sm:mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-green-500/25 mx-auto">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">{{ __('app.welcome_feature_ocean_title') }}</h3>
                <p class="text-gray-500 leading-relaxed text-sm sm:text-base">{{ __('app.welcome_feature_ocean_desc') }}</p>
                <div class="mt-4 sm:mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-100">Openness</span>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-100">Conscientiousness</span>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-100">Extraversion</span>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-100">Agreeableness</span>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-100">Emotional Stability</span>
                </div>
            </div>
            {{-- RIASEC --}}
            <div class="relative bg-white rounded-2xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-gray-100 transition-all group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl sm:rounded-3xl flex items-center justify-center mb-5 sm:mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-blue-500/25 mx-auto">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">{{ __('app.welcome_feature_riasec_title') }}</h3>
                <p class="text-gray-500 leading-relaxed text-sm sm:text-base">{{ __('app.welcome_feature_riasec_desc') }}</p>
                <div class="mt-4 sm:mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Realistic</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Investigative</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Artistic</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Social</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Enterprising</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-100">Conventional</span>
                </div>
            </div>
            {{-- Cognitive --}}
            <div class="relative bg-white rounded-2xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-gray-100 transition-all group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-400 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-400 to-pink-600 rounded-2xl sm:rounded-3xl flex items-center justify-center mb-5 sm:mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-purple-500/25 mx-auto">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">{{ __('app.welcome_feature_cognitive_title') }}</h3>
                <p class="text-gray-500 leading-relaxed text-sm sm:text-base">{{ __('app.welcome_feature_cognitive_desc') }}</p>
                <div class="mt-4 sm:mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-100">Verbal Reasoning</span>
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-100">Numerical Reasoning</span>
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-100">Logical Reasoning</span>
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-100">Working Memory</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ How It Works ═══ --}}
<section id="how-it-works" class="py-16 sm:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900">{{ __('app.welcome_how_title') }}</h2>
        <div class="mt-10 sm:mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-10">
            @php $steps = [
                ['num' => '01', 'title' => __('app.welcome_step1_title'), 'desc' => __('app.welcome_step1_desc'), 'color' => 'primary'],
                ['num' => '02', 'title' => __('app.welcome_step2_title'), 'desc' => __('app.welcome_step2_desc'), 'color' => 'accent'],
                ['num' => '03', 'title' => __('app.welcome_step3_title'), 'desc' => __('app.welcome_step3_desc'), 'color' => 'primary'],
            ]; @endphp
            @foreach($steps as $step)
            <div class="relative text-left bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <span class="text-4xl sm:text-5xl font-black text-{{ $step['color'] }}-100">{{ $step['num'] }}</span>
                <h3 class="mt-2 text-xl font-bold text-gray-900">{{ $step['title'] }}</h3>
                <p class="mt-2 text-gray-500">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ Testimonials ═══ --}}
<section id="testimonials" class="py-16 sm:py-24 bg-gradient-to-br from-primary-50/60 to-accent-50/40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900">{{ __('app.welcome_testimonials_title') }}</h2>
        <div class="mt-10 sm:mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
            @php $testimonials = [
                ['text' => __('app.welcome_testimonial_1'), 'author' => __('app.welcome_testimonial_1_author')],
                ['text' => __('app.welcome_testimonial_2'), 'author' => __('app.welcome_testimonial_2_author')],
                ['text' => __('app.welcome_testimonial_3'), 'author' => __('app.welcome_testimonial_3_author')],
            ]; @endphp
            @foreach($testimonials as $t)
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 text-left">
                <svg class="w-8 h-8 text-primary-200 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                <p class="text-gray-600 italic leading-relaxed">{{ $t['text'] }}</p>
                <p class="mt-4 font-bold text-gray-900">— {{ $t['author'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ FAQ ═══ --}}
<section id="faq" class="py-16 sm:py-24 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 text-center">{{ __('app.welcome_faq_title') }}</h2>
        <div class="mt-12 space-y-4" x-data="{ open: null }">
            @php $faqs = [
                ['q' => __('app.welcome_faq_1_q'), 'a' => __('app.welcome_faq_1_a')],
                ['q' => __('app.welcome_faq_2_q'), 'a' => __('app.welcome_faq_2_a')],
                ['q' => __('app.welcome_faq_3_q'), 'a' => __('app.welcome_faq_3_a')],
            ]; @endphp
            @foreach($faqs as $i => $faq)
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between px-4 sm:px-6 py-4 text-left hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900 text-sm sm:text-base pr-2">{{ $faq['q'] }}</span>
                    <svg :class="open === {{ $i }} ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse>
                    <div class="px-4 sm:px-6 pb-4 text-gray-500 leading-relaxed text-sm sm:text-base">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section class="py-16 sm:py-24 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 text-white text-center relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 30% 50%, #fff 1px, transparent 1px); background-size: 30px 30px;"></div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl sm:text-3xl md:text-5xl font-extrabold">{{ __('app.welcome_cta_bottom') }}</h2>
        <p class="mt-4 text-lg text-primary-200">{{ __('app.welcome_cta_bottom_desc') }}</p>
        <a href="{{ route('register') }}" class="mt-6 sm:mt-8 inline-block px-8 sm:px-10 py-3.5 sm:py-4 bg-white text-primary-700 font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all text-base sm:text-lg">
            {{ __('app.welcome_cta_bottom_btn') }}
        </a>
    </div>
</section>

{{-- ═══ Footer ═══ --}}
<footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm">
    <p>{{ __('app.copyright', ['year' => date('Y')]) }}</p>
</footer>

</body>
</html>
