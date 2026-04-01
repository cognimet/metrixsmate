<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.brand'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'},
                    accent:  {50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',400:'#2dd4bf',500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59',900:'#134e4a'},
                    surface: '#F8FAFC',
                },
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                boxShadow: {
                    'card':       '0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04)',
                    'card-hover': '0 10px 25px -5px rgba(0,0,0,.08), 0 8px 10px -6px rgba(0,0,0,.04)',
                },
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .fade-in { animation: fadeIn .4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 3px; }
    </style>
</head>
<body class="min-h-screen bg-surface">

{{-- ═══  Navigation  ═══ --}}
<nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 shadow-sm" x-data="{ open: false, userMenu: false, langMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-lg font-bold text-gray-900 hidden sm:block">{{ __('app.brand') }}</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">{{ __('app.dashboard') }}</a>
                <a href="{{ route('results.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('results.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">{{ __('app.my_results') }}</a>
                <a href="{{ route('certificates.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('certificates.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">Certificate</a>
                <a href="{{ route('school-finder.ai.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('school-finder.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">School Finder</a>
                <a href="{{ route('payments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('payments.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">Payments</a>
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition">⚙️ Admin</a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                {{-- Language --}}
                <div class="relative" @click.outside="langMenu=false">
                    <button @click="langMenu=!langMenu" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200/60 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ app()->getLocale() === 'hi' ? 'हिं' : 'EN' }}
                    </button>
                    <div x-show="langMenu" x-transition class="absolute right-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="?lang=en" class="block px-4 py-2 text-sm {{ app()->getLocale()==='en' ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('app.english') }}</a>
                        <a href="?lang=hi" class="block px-4 py-2 text-sm {{ app()->getLocale()==='hi' ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('app.hindi') }}</a>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="relative" @click.outside="userMenu=false">
                    <button @click="userMenu=!userMenu" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="userMenu" x-transition class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">{{ __('app.profile') }}</a>
                        @if(Auth::user()->isAdmin())
                        <hr class="my-1 border-gray-100">
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium">⚙️ Admin Panel</a>
                        @endif
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">{{ __('app.logout') }}</button>
                        </form>
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="open=!open" class="md:hidden p-2 rounded-lg hover:bg-gray-50 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile nav --}}
    <div x-show="open" x-transition class="md:hidden border-t border-gray-100 bg-white pb-3">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-primary-700 bg-primary-50' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('app.dashboard') }}</a>
        <a href="{{ route('results.index') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('results.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('app.my_results') }}</a>
        <a href="{{ route('certificates.index') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('certificates.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-600 hover:bg-gray-50' }}">Certificate</a>
        <a href="{{ route('school-finder.ai.index') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('school-finder.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-600 hover:bg-gray-50' }}">School Finder</a>
        <a href="{{ route('payments.index') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('payments.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-600 hover:bg-gray-50' }}">Payments</a>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('admin.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">⚙️ Admin Panel</a>
        @endif
    </div>
</nav>

{{-- ═══  Flash Messages  ═══ --}}
@if(session('success'))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
        <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
</div>
@endif
@if(session('error'))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-medium">{{ session('error') }}</p>
        <button @click="show=false" class="ml-auto text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
</div>
@endif

{{-- ═══  Page Header  ═══ --}}
@hasSection('header')
<header class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">@yield('header')</div>
</header>
@endif

{{-- ═══  Content  ═══ --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 fade-in">
    @yield('content')
</main>

{{-- ═══  Footer  ═══ --}}
<footer class="border-t border-gray-100 bg-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-xs text-gray-400">
        {{ __('app.copyright', ['year' => date('Y')]) }}
    </div>
</footer>

@stack('scripts')
</body>
</html>
