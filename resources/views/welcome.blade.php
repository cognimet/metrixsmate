<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('partials.analytics')

    @include('partials.seo-meta', [
        'seoTitle'       => __('app.seo_home_title'),
        'seoDescription' => __('app.seo_home_description'),
        'seoCanonical'   => url('/'),
        'seoType'        => 'website',
        'seoAlternates'  => true,
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    ink: {
                        50: '#f5f8fb', 100: '#eaf0f7', 200: '#ccd9e8', 300: '#a9bfd8',
                        400: '#819fbe', 500: '#6382a5', 600: '#4c6888', 700: '#3d5470',
                        800: '#34475d', 900: '#2f3c4e', 950: '#111c2c'
                    },
                    ocean: {
                        50: '#ecf8fb', 100: '#d3eff7', 200: '#aee1ef', 300: '#77c9e2',
                        400: '#39aad0', 500: '#1d8fb8', 600: '#176f94', 700: '#175a77',
                        800: '#194a62', 900: '#193f53'
                    },
                    sand: {
                        50: '#fff9ed', 100: '#fff1d3', 200: '#ffe0a5', 300: '#ffc86d',
                        400: '#ffad34', 500: '#f58d0c', 600: '#d86c07', 700: '#b34b08',
                        800: '#93380d', 900: '#782f0e'
                    }
                },
                fontFamily: {
                    display: ['Sora', 'system-ui', 'sans-serif'],
                    sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                },
                boxShadow: {
                    soft: '0 10px 30px -18px rgba(25, 63, 83, 0.55)',
                    glow: '0 18px 50px -28px rgba(29, 143, 184, 0.65)',
                }
            }
        }
    }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background:
                radial-gradient(70rem 36rem at 88% -8%, rgba(29, 143, 184, 0.20), transparent 62%),
                radial-gradient(56rem 28rem at -8% 24%, rgba(23, 90, 119, 0.14), transparent 58%),
                linear-gradient(180deg, #f5f8fb 0%, #ffffff 62%);
        }

        .noise-overlay {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.86)),
                radial-gradient(rgba(17, 28, 44, 0.08) 0.6px, transparent 0.6px);
            background-size: auto, 12px 12px;
            background-position: center;
        }

        .glass {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
        }

        .section-grid {
            background-image:
                linear-gradient(to right, rgba(52, 71, 93, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(52, 71, 93, 0.05) 1px, transparent 1px);
            background-size: 34px 34px;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        [data-reveal] {
            opacity: 0;
            transform: translateY(18px);
        }

        [data-reveal].is-visible {
            animation: revealUp 700ms cubic-bezier(0.18, 0.84, 0.22, 1) forwards;
            animation-delay: var(--reveal-delay, 0ms);
        }

        .orb {
            animation: floatOrb 8s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            [data-reveal],
            [data-reveal].is-visible {
                opacity: 1;
                transform: none;
                animation: none;
            }
            .orb { animation: none; }
        }
    </style>

    {{-- ── Structured data (JSON-LD) ── --}}
    @include('partials.schema')
    @php
        $webPageSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebPage',
            'name'            => __('app.seo_home_title'),
            'description'     => __('app.seo_home_description'),
            'url'             => url('/'),
            'inLanguage'      => app()->getLocale(),
            'isPartOf'        => ['@type' => 'WebSite', 'name' => __('app.brand'), 'url' => url('/')],
            'primaryImageOfPage' => asset('favicon.ico'),
        ];

        $breadcrumbSchema = [
            '@context'       => 'https://schema.org',
            '@type'          => 'BreadcrumbList',
            'itemListElement' => [[
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => __('app.brand'),
                'item'     => url('/'),
            ]],
        ];

        $faqSchema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => collect([1, 2, 3])->map(fn ($i) => [
                '@type'          => 'Question',
                'name'           => __("app.welcome_faq_{$i}_q"),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => __("app.welcome_faq_{$i}_a"),
                ],
            ])->all(),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
</head>

<body class="text-ink-900 antialiased">
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-ink-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Skip to content
</a>

<header x-data="{ mobileOpen: false }" class="fixed inset-x-0 top-0 z-50 px-4 sm:px-6 py-3">
    <div class="mx-auto max-w-7xl glass rounded-2xl shadow-soft">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3.5">
            <a href="/" class="flex items-center gap-3">
                <span class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-ocean-500 to-ink-900 text-white shadow-glow">
                    <svg aria-hidden="true" focusable="false" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="orb pointer-events-none absolute -right-1.5 -top-1.5 h-3 w-3 rounded-full bg-sand-400"></span>
                </span>
                <span class="font-display text-base sm:text-lg font-semibold text-ink-950">{{ __('app.brand') }}</span>
            </a>

            <nav aria-label="Primary" class="hidden lg:flex items-center gap-7 text-sm font-semibold text-ink-700">
                <a href="#features" class="transition hover:text-ocean-600">{{ __('app.welcome_features_title') }}</a>
                <a href="#how-it-works" class="transition hover:text-ocean-600">{{ __('app.welcome_how_title') }}</a>
                <a href="#testimonials" class="transition hover:text-ocean-600">{{ __('app.welcome_testimonials_title') }}</a>
                <a href="#faq" class="transition hover:text-ocean-600">{{ __('app.welcome_faq_title') }}</a>
            </nav>

            <div class="hidden sm:flex items-center gap-3">
                <div class="rounded-xl bg-ink-100 p-1 text-sm font-semibold">
                    <a href="?lang=en" class="inline-flex px-3 py-1.5 rounded-lg transition {{ app()->getLocale()==='en' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-800' }}">EN</a>
                    <a href="?lang=hi" class="inline-flex px-3 py-1.5 rounded-lg transition {{ app()->getLocale()==='hi' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-800' }}">हिं</a>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-semibold text-ink-700 transition hover:text-ocean-600">{{ __('app.login') }}</a>
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-ink-950 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-ink-900">{{ __('app.register') }}</a>
            </div>

            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="inline-flex lg:hidden h-10 w-10 items-center justify-center rounded-xl border border-ink-200 bg-white text-ink-700"
                aria-label="Toggle navigation menu"
                aria-controls="mobile-menu"
                :aria-expanded="mobileOpen"
            >
                <svg aria-hidden="true" focusable="false" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="mobile-menu" x-show="mobileOpen" x-transition.duration.220ms x-cloak class="border-t border-ink-100 px-4 pb-4 pt-3 lg:hidden">
            <div class="grid gap-2 text-sm font-semibold text-ink-700">
                <a @click="mobileOpen = false" href="#features" class="rounded-lg px-3 py-2 hover:bg-ink-50">{{ __('app.welcome_features_title') }}</a>
                <a @click="mobileOpen = false" href="#how-it-works" class="rounded-lg px-3 py-2 hover:bg-ink-50">{{ __('app.welcome_how_title') }}</a>
                <a @click="mobileOpen = false" href="#testimonials" class="rounded-lg px-3 py-2 hover:bg-ink-50">{{ __('app.welcome_testimonials_title') }}</a>
                <a @click="mobileOpen = false" href="#faq" class="rounded-lg px-3 py-2 hover:bg-ink-50">{{ __('app.welcome_faq_title') }}</a>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('login') }}" class="flex-1 rounded-lg border border-ink-200 px-3 py-2 text-center text-sm font-semibold text-ink-700">{{ __('app.login') }}</a>
                <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-ink-950 px-3 py-2 text-center text-sm font-semibold text-white">{{ __('app.register') }}</a>
            </div>
            <div class="mt-3 rounded-lg bg-ink-100 p-1 text-xs font-semibold text-center">
                <a href="?lang=en" class="inline-flex px-3 py-1 rounded-md {{ app()->getLocale()==='en' ? 'bg-white text-ink-900' : 'text-ink-500' }}">EN</a>
                <a href="?lang=hi" class="inline-flex px-3 py-1 rounded-md {{ app()->getLocale()==='hi' ? 'bg-white text-ink-900' : 'text-ink-500' }}">हिं</a>
            </div>
        </div>
    </div>
</header>

<main id="main-content" class="pt-28 sm:pt-32">
    <section class="relative overflow-hidden px-4 sm:px-6 pb-14 sm:pb-20">
        <div class="pointer-events-none absolute left-[-6rem] top-28 h-52 w-52 rounded-full bg-ocean-200/60 blur-3xl"></div>
        <div class="pointer-events-none absolute right-[-4rem] top-10 h-44 w-44 rounded-full bg-sand-200/80 blur-3xl"></div>

        <div class="mx-auto max-w-7xl section-grid rounded-[2rem] border border-white/70 bg-white/70 p-6 sm:p-10 lg:p-14 shadow-soft">
            <div class="grid items-center gap-10 lg:grid-cols-[1.08fr_0.92fr]">
                <div data-reveal>
                    <div class="inline-flex items-center gap-2 rounded-full border border-ocean-200 bg-ocean-50 px-4 py-1.5 text-sm font-semibold text-ocean-700">
                        <span class="h-2 w-2 rounded-full bg-ocean-500"></span>
                        {{ __('app.brand') }}
                    </div>

                    <h1 class="mt-6 font-display text-3xl sm:text-5xl lg:text-6xl font-bold leading-tight text-ink-950">
                        {{ __('app.welcome_hero_title') }}
                    </h1>

                    <p class="mt-5 max-w-2xl text-base sm:text-lg leading-relaxed text-ink-600">
                        {{ __('app.welcome_hero_subtitle') }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-ink-950 px-7 py-3.5 text-base font-bold text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-ink-900">
                            {{ __('app.welcome_cta_start') }}
                        </a>
                        <a href="#features" class="inline-flex items-center justify-center rounded-xl border border-ink-200 bg-white px-7 py-3.5 text-base font-bold text-ink-700 transition hover:border-ocean-300 hover:text-ocean-700">
                            {{ __('app.welcome_cta_learn') }}
                        </a>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-ocean-100 bg-ocean-50/70 p-3.5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ocean-700">Signals</p>
                            <p class="mt-1 text-sm font-semibold text-ink-800">OCEAN + RIASEC + Cognitive</p>
                        </div>
                        <div class="rounded-xl border border-ink-100 bg-white p-3.5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Personalized</p>
                            <p class="mt-1 text-sm font-semibold text-ink-800">Adaptive pathways for each learner</p>
                        </div>
                        <div class="rounded-xl border border-sand-100 bg-sand-50/70 p-3.5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sand-700">Decision Ready</p>
                            <p class="mt-1 text-sm font-semibold text-ink-800">Recommendations you can act on</p>
                        </div>
                    </div>
                </div>

                <div class="relative" data-reveal>
                    <div class="absolute -right-4 -top-5 hidden sm:block rounded-full border border-ocean-200 bg-ocean-50 px-3 py-1 text-xs font-semibold text-ocean-700">Psychometrics Engine</div>

                    <div class="noise-overlay rounded-3xl border border-white/80 p-5 sm:p-6 shadow-glow">
                        <div class="rounded-2xl bg-white p-5 sm:p-6 border border-ink-100">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ink-500">Assessment Intelligence</p>
                                    <h2 class="mt-2 font-display text-xl sm:text-2xl font-semibold text-ink-950">Profile Synthesis</h2>
                                </div>
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Live</span>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div class="rounded-xl border border-ink-100 bg-ink-50/70 p-4">
                                    <div class="flex items-center justify-between text-sm font-semibold text-ink-700">
                                        <span>{{ __('app.welcome_feature_ocean_title') }}</span>
                                        <span class="text-ocean-700">94%</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-white">
                                        <div class="h-2 w-[94%] rounded-full bg-gradient-to-r from-ocean-400 to-ocean-600"></div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-ink-100 bg-ink-50/70 p-4">
                                    <div class="flex items-center justify-between text-sm font-semibold text-ink-700">
                                        <span>{{ __('app.welcome_feature_riasec_title') }}</span>
                                        <span class="text-ocean-700">91%</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-white">
                                        <div class="h-2 w-[91%] rounded-full bg-gradient-to-r from-ocean-400 to-ocean-600"></div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-ink-100 bg-ink-50/70 p-4">
                                    <div class="flex items-center justify-between text-sm font-semibold text-ink-700">
                                        <span>{{ __('app.welcome_feature_cognitive_title') }}</span>
                                        <span class="text-ocean-700">89%</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-white">
                                        <div class="h-2 w-[89%] rounded-full bg-gradient-to-r from-ocean-400 to-ocean-600"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="px-4 sm:px-6 py-14 sm:py-20">
        <div class="mx-auto max-w-7xl" data-reveal>
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ocean-700">Capabilities</p>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold text-ink-950">{{ __('app.welcome_features_title') }}</h2>
                <p class="mt-4 text-base sm:text-lg text-ink-600">{{ __('app.welcome_features_desc') }}</p>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                <article class="group rounded-3xl border border-ink-100 bg-white p-6 sm:p-8 shadow-soft transition hover:-translate-y-1" data-reveal>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg aria-hidden="true" focusable="false" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-6 font-display text-xl font-semibold text-ink-950">{{ __('app.welcome_feature_ocean_title') }}</h3>
                    <p class="mt-3 text-ink-600 leading-relaxed">{{ __('app.welcome_feature_ocean_desc') }}</p>
                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Openness</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Conscientiousness</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Extraversion</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Agreeableness</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Emotional Stability</span>
                    </div>
                </article>

                <article class="group rounded-3xl border border-ink-100 bg-white p-6 sm:p-8 shadow-soft transition hover:-translate-y-1" data-reveal>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-ocean-100 text-ocean-700">
                        <svg aria-hidden="true" focusable="false" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-6 font-display text-xl font-semibold text-ink-950">{{ __('app.welcome_feature_riasec_title') }}</h3>
                    <p class="mt-3 text-ink-600 leading-relaxed">{{ __('app.welcome_feature_riasec_desc') }}</p>
                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Realistic</span>
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Investigative</span>
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Artistic</span>
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Social</span>
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Enterprising</span>
                        <span class="rounded-full bg-ocean-50 px-3 py-1 text-ocean-700">Conventional</span>
                    </div>
                </article>

                <article class="group rounded-3xl border border-ink-100 bg-white p-6 sm:p-8 shadow-soft transition hover:-translate-y-1" data-reveal>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sand-100 text-sand-700">
                        <svg aria-hidden="true" focusable="false" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="mt-6 font-display text-xl font-semibold text-ink-950">{{ __('app.welcome_feature_cognitive_title') }}</h3>
                    <p class="mt-3 text-ink-600 leading-relaxed">{{ __('app.welcome_feature_cognitive_desc') }}</p>
                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-sand-50 px-3 py-1 text-sand-700">Verbal Reasoning</span>
                        <span class="rounded-full bg-sand-50 px-3 py-1 text-sand-700">Numerical Reasoning</span>
                        <span class="rounded-full bg-sand-50 px-3 py-1 text-sand-700">Logical Reasoning</span>
                        <span class="rounded-full bg-sand-50 px-3 py-1 text-sand-700">Working Memory</span>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="px-4 sm:px-6 py-14 sm:py-20 bg-ink-950 text-white">
        <div class="mx-auto max-w-7xl">
            <div class="max-w-2xl" data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ocean-300">Workflow</p>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold">{{ __('app.welcome_how_title') }}</h2>
            </div>

            @php
                $steps = [
                    ['num' => '01', 'title' => __('app.welcome_step1_title'), 'desc' => __('app.welcome_step1_desc')],
                    ['num' => '02', 'title' => __('app.welcome_step2_title'), 'desc' => __('app.welcome_step2_desc')],
                    ['num' => '03', 'title' => __('app.welcome_step3_title'), 'desc' => __('app.welcome_step3_desc')],
                ];
            @endphp

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach($steps as $index => $step)
                    <article class="relative rounded-3xl border border-white/15 bg-white/5 p-6 sm:p-8" data-reveal>
                        <div class="flex items-center justify-between">
                            <span class="font-display text-4xl sm:text-5xl font-semibold text-white/20">{{ $step['num'] }}</span>
                            <span class="h-2 w-2 rounded-full bg-ocean-300"></span>
                        </div>
                        <h3 class="mt-4 font-display text-xl font-semibold text-white">{{ $step['title'] }}</h3>
                        <p class="mt-3 text-white/70 leading-relaxed">{{ $step['desc'] }}</p>

                        @if($index < 2)
                            <div class="hidden lg:block pointer-events-none absolute right-[-20px] top-11 h-px w-10 bg-gradient-to-r from-ocean-300/70 to-transparent"></div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="testimonials" class="px-4 sm:px-6 py-14 sm:py-20 bg-white">
        <div class="mx-auto max-w-7xl">
            <div class="max-w-2xl" data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ocean-700">Outcomes</p>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold text-ink-950">{{ __('app.welcome_testimonials_title') }}</h2>
            </div>

            @php
                $testimonials = [
                    ['text' => __('app.welcome_testimonial_1'), 'author' => __('app.welcome_testimonial_1_author')],
                    ['text' => __('app.welcome_testimonial_2'), 'author' => __('app.welcome_testimonial_2_author')],
                    ['text' => __('app.welcome_testimonial_3'), 'author' => __('app.welcome_testimonial_3_author')],
                ];
            @endphp

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach($testimonials as $t)
                    <figure class="rounded-3xl border border-ink-100 bg-ink-50/60 p-6 sm:p-8 shadow-soft" data-reveal>
                        <svg aria-hidden="true" focusable="false" class="h-8 w-8 text-ocean-300" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <blockquote class="mt-4 text-ink-700 leading-relaxed">{{ $t['text'] }}</blockquote>
                        <figcaption class="mt-5 font-semibold text-ink-950">{{ $t['author'] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="px-4 sm:px-6 py-14 sm:py-20 bg-ink-50/70">
        <div class="mx-auto max-w-7xl grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <div data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ocean-700">Support</p>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold text-ink-950">{{ __('app.welcome_faq_title') }}</h2>
                <p class="mt-4 text-ink-600 leading-relaxed">
                    Find clear answers about the assessment process, timelines, and recommendations so every next step feels confident.
                </p>
            </div>

            @php
                $faqs = [
                    ['q' => __('app.welcome_faq_1_q'), 'a' => __('app.welcome_faq_1_a')],
                    ['q' => __('app.welcome_faq_2_q'), 'a' => __('app.welcome_faq_2_a')],
                    ['q' => __('app.welcome_faq_3_q'), 'a' => __('app.welcome_faq_3_a')],
                ];
            @endphp

            <div class="space-y-4" x-data="{ open: 0 }" data-reveal>
                @foreach($faqs as $i => $faq)
                    <article class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-soft">
                        <button
                            type="button"
                            id="faq-trigger-{{ $i }}"
                            class="flex w-full items-center justify-between gap-4 px-5 sm:px-6 py-4 text-left"
                            @click="open === {{ $i }} ? open = -1 : open = {{ $i }}"
                            :aria-expanded="open === {{ $i }}"
                            aria-controls="faq-panel-{{ $i }}"
                        >
                            <span class="text-sm sm:text-base font-semibold text-ink-900">{{ $faq['q'] }}</span>
                            <svg aria-hidden="true" focusable="false" class="h-5 w-5 transition-transform" :class="open === {{ $i }} ? 'rotate-180 text-ocean-600' : 'text-ink-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-{{ $i }}" role="region" aria-labelledby="faq-trigger-{{ $i }}" x-show="open === {{ $i }}" x-transition.duration.220ms x-cloak>
                            <div class="px-5 sm:px-6 pb-5 text-sm sm:text-base leading-relaxed text-ink-600">{{ $faq['a'] }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="px-4 sm:px-6 py-14 sm:py-20">
        <div class="mx-auto max-w-6xl rounded-[2rem] bg-gradient-to-br from-ink-950 via-ink-900 to-ocean-800 p-8 sm:p-12 text-white shadow-glow relative overflow-hidden" data-reveal>
            <div class="pointer-events-none absolute -right-16 -top-12 h-56 w-56 rounded-full bg-ocean-400/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-14 -bottom-16 h-52 w-52 rounded-full bg-sand-300/20 blur-3xl"></div>

            <div class="relative text-center max-w-3xl mx-auto">
                <h2 class="font-display text-3xl sm:text-5xl font-semibold leading-tight">{{ __('app.welcome_cta_bottom') }}</h2>
                <p class="mt-4 text-base sm:text-lg text-ink-200">{{ __('app.welcome_cta_bottom_desc') }}</p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-3.5 text-base font-bold text-ink-900 transition hover:-translate-y-0.5 hover:bg-ink-100">
                        {{ __('app.welcome_cta_bottom_btn') }}
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center rounded-xl border border-white/30 px-8 py-3.5 text-base font-bold text-white/90 transition hover:bg-white/10">
                        {{ __('app.welcome_cta_learn') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="border-t border-ink-100 bg-white px-4 sm:px-6 py-8 text-center text-sm text-ink-500">
    <p>{{ __('app.copyright', ['year' => date('Y')]) }}</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const revealElements = document.querySelectorAll('[data-reveal]');

        if (!('IntersectionObserver' in window)) {
            revealElements.forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }

        const observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -8% 0px'
        });

        revealElements.forEach(function (el, index) {
            const delay = Math.min(index * 70, 350);
            el.style.setProperty('--reveal-delay', delay + 'ms');
            observer.observe(el);
        });
    });
</script>
</body>
</html>