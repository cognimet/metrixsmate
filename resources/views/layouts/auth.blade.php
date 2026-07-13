<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.analytics')

    @include('partials.seo-meta', [
        'seoTitle'       => trim($__env->yieldContent('title')) ?: __('app.brand'),
        'seoDescription' => trim($__env->yieldContent('meta_description')) ?: __('app.seo_default_description'),
        'seoRobots'      => trim($__env->yieldContent('meta_robots')) ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
<body class="bg-white text-gray-800 min-h-screen flex flex-col">

{{-- ═══ Header — identical to welcome page ═══ --}}
<header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-base sm:text-lg font-bold text-gray-900">{{ __('app.brand') }}</span>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="flex items-center bg-gray-100 rounded-lg p-0.5 text-sm font-medium">
                <a href="?lang=en" class="px-2 sm:px-2.5 py-1 rounded-md transition {{ app()->getLocale()==='en' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                <a href="?lang=hi" class="px-2 sm:px-2.5 py-1 rounded-md transition {{ app()->getLocale()==='hi' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">हिं</a>
            </div>
            @hasSection('isLoginPage')
            <a href="{{ route('register') }}" class="px-3 sm:px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 shadow-sm transition">{{ __('app.register') }}</a>
            @else
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-primary-600 transition">{{ __('app.login') }}</a>
            @endif
        </div>
    </div>
</header>

{{-- ═══ Page Content ═══ --}}
<main class="flex-1 pt-[60px]">
    @yield('content')
</main>

{{-- ═══ Footer — identical to welcome page ═══ --}}
<footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm">
    <p>{{ __('app.copyright', ['year' => date('Y')]) }}</p>
</footer>

</body>
</html>
