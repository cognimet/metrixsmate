@extends('layouts.app')
@section('title', __('app.results_title') . ' - ' . __('app.brand'))

@section('header')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.results_title') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('app.results_subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('results.download', ['quiz' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ __('app.results_download') }}
        </a>
        {{-- <button onclick="document.getElementById('shareModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-semibold rounded-xl transition-colors bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            {{ __('app.results_share') }}
        </button> --}}
    </div>
</div>
@endsection

@section('content')

{{-- Share Modal --}}
<div id="shareModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">{{ __('app.results_share_title') }}</h3>
            <button onclick="document.getElementById('shareModal').classList.add('hidden')" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="text-sm text-gray-500 mb-4">{{ __('app.results_share_desc') }}</p>
        <div class="flex items-center gap-2 mb-4">
            <input type="text" id="shareUrl" value="{{ url('/results') }}" readonly class="flex-1 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-600">
            <button onclick="navigator.clipboard.writeText(document.getElementById('shareUrl').value);this.textContent='{{ __('app.results_link_copied') }}';setTimeout(()=>this.textContent='{{ __('app.results_copy_link') }}',2000)" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition whitespace-nowrap">
                {{ __('app.results_copy_link') }}
            </button>
        </div>
        <div class="grid grid-cols-4 gap-2">
            <a href="https://wa.me/?text={{ urlencode(__('app.results_share_text') . ' ' . url('/results')) }}" target="_blank" class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-green-50 hover:bg-green-100 transition">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span class="text-xs font-medium text-green-700">WhatsApp</span>
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode(__('app.results_share_text')) }}&url={{ urlencode(url('/results')) }}" target="_blank" class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-sky-50 hover:bg-sky-100 transition">
                <svg class="w-6 h-6 text-sky-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                <span class="text-xs font-medium text-sky-700">Twitter</span>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/results')) }}" target="_blank" class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
                <svg class="w-6 h-6 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                <span class="text-xs font-medium text-blue-700">LinkedIn</span>
            </a>
            <a href="mailto:?subject={{ urlencode(__('app.results_share_text')) }}&body={{ urlencode(__('app.results_share_text') . "\n" . url('/results')) }}" class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="text-xs font-medium text-gray-600">{{ __('app.results_share_email') }}</span>
            </a>
        </div>
    </div>
</div>

{{-- Overview Cards --}}
@php
    $overallPersonality = $oceanResults['domains']->avg('percentage');
    $personalityLevel = $overallPersonality >= 85 ? 'exceptional' : ($overallPersonality >= 70 ? 'high' : ($overallPersonality >= 40 ? 'average' : ($overallPersonality >= 20 ? 'below-average' : 'low')));
    $overallCognitive = $cognitiveResults['average_score'];
    $cognitiveLevel = $overallCognitive >= 85 ? 'exceptional' : ($overallCognitive >= 70 ? 'high' : ($overallCognitive >= 40 ? 'average' : ($overallCognitive >= 20 ? 'below-average' : 'low')));
    $levelColors = [
        'exceptional' => 'text-emerald-600', 'high' => 'text-blue-600', 'average' => 'text-amber-600',
        'below-average' => 'text-orange-600', 'low' => 'text-red-600'
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <div><h3 class="font-semibold text-gray-900">{{ __('app.results_personality_title') }}</h3><p class="text-xs text-gray-500">{{ __('app.results_personality_desc') }}</p></div>
        </div>
        <div class="text-3xl font-bold {{ $levelColors[$personalityLevel] }}">{{ round($overallPersonality, 0) }}%</div>
        <div class="text-xs text-gray-400 mt-1">{{ __('app.results_avg_domains') }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <div><h3 class="font-semibold text-gray-900">{{ __('app.results_career_title') }}</h3><p class="text-xs text-gray-500">{{ __('app.results_career_desc') }}</p></div>
        </div>
        <div class="text-3xl font-bold text-green-600">{{ $riasecResults['holland_code'] }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ __('app.results_career_code') }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
            <div><h3 class="font-semibold text-gray-900">{{ __('app.results_cognitive_title') }}</h3><p class="text-xs text-gray-500">{{ __('app.results_cognitive_desc') }}</p></div>
        </div>
        <div class="text-3xl font-bold {{ $levelColors[$cognitiveLevel] }}">{{ round($overallCognitive, 0) }}%</div>
        <div class="text-xs text-gray-400 mt-1">{{ __('app.results_cognitive_avg') }}</div>
    </div>
</div>

{{-- Career Opportunities + Learning Styles --}}
@php
    $_careersList = [];
    if ($riasecResults['career_analysis']) {
        $_careersList = array_slice(explode(';', $riasecResults['career_analysis']->level_description), 0, 4);
    }
    $_lsScores = $oceanResults['domains']->keyBy('name');
    $_conscientiousness = $_lsScores['Conscientiousness']->percentage ?? 0;
    $_openness = $_lsScores['Openness']->percentage ?? 0;
    $_extraversion = $_lsScores['Extraversion']->percentage ?? 0;
    $_agreeableness = $_lsScores['Agreeableness']->percentage ?? 0;
    $_learningStyles = [
        ['name' => 'Reading/Writing', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'blue', 'score' => round(($_conscientiousness + $_openness) / 2)],
        ['name' => 'Verbal', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'green', 'score' => round($_extraversion)],
        ['name' => 'Kinesthetic', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple', 'score' => round(($_agreeableness + $_extraversion) / 2)],
        ['name' => 'Visual', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'amber', 'score' => round($_openness)],
    ];
    usort($_learningStyles, function($a, $b) { return $b['score'] - $a['score']; });
    $_preferredStyle = $_learningStyles[0];
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
    {{-- Career Opportunities --}}
    <div class="rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 text-white shadow-card relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-xl"></div>
        <div class="p-6 relative">
            <h2 class="text-xl font-bold mb-1">{{ __('app.results_career_opportunities') }}</h2>
            <p class="text-sm text-primary-200 mb-4">{{ __('app.results_career_based_on', ['code' => $riasecResults['holland_code']]) }}</p>
            @if($_careersList)
            <div class="space-y-2">
                @foreach($_careersList as $_career)
                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-primary-200 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium text-primary-50">{{ transContent(trim($_career)) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Learning Styles --}}
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Learning Styles</h2>
        <p class="text-sm text-gray-500 mb-4">Your preferred way of acquiring and processing information</p>
        <div class="mb-4 p-4 rounded-xl bg-gradient-to-br from-{{ $_preferredStyle['color'] }}-50 to-{{ $_preferredStyle['color'] }}-100 border border-{{ $_preferredStyle['color'] }}-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-{{ $_preferredStyle['color'] }}-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $_preferredStyle['color'] }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $_preferredStyle['icon'] }}"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-{{ $_preferredStyle['color'] }}-900">Preferred Style</h4>
                    <p class="text-sm font-semibold text-{{ $_preferredStyle['color'] }}-800">{{ $_preferredStyle['name'] }}</p>
                </div>
            </div>
            <p class="text-xs text-{{ $_preferredStyle['color'] }}-700">You learn best through {{ strtolower($_preferredStyle['name']) }} methods. Focus on leveraging this style in your development journey.</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @foreach($_learningStyles as $_style)
            <div class="rounded-xl bg-gray-50 border border-gray-200 p-3 text-center {{ $_style['name'] === $_preferredStyle['name'] ? 'ring-2 ring-'.$_style['color'].'-500 ring-offset-1' : '' }}">
                <div class="w-8 h-8 rounded-lg bg-{{ $_style['color'] }}-50 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-{{ $_style['color'] }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $_style['icon'] }}"/></svg>
                </div>
                <h4 class="text-xs font-bold text-gray-900 mb-1">{{ $_style['name'] }}</h4>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-{{ $_style['color'] }}-500 h-1.5 rounded-full" style="width:{{ $_style['score'] }}%"></div>
                </div>
                <p class="text-xs text-gray-600 mt-1 font-semibold">{{ $_style['score'] }}%</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- OCEAN Personality --}}
<div class="bg-white rounded-2xl shadow-card border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">{{ __('app.results_ocean_full') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('app.results_ocean_full_desc') }}</p>
    </div>
    <div class="p-6 flex justify-center">
        <div class="w-full max-w-md"><canvas id="oceanRadarChart" height="300"></canvas></div>
    </div>
    <div class="p-6 pt-0 space-y-4">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">{{ __('app.results_core_domains') }}</h3>
        @foreach($oceanResults['domains'] as $domain)
        <div class="rounded-xl border {{ $domain->colors['border'] }} {{ $domain->colors['bg'] }} p-5 hover:shadow-card-hover transition-shadow">
            <div class="flex items-center justify-between mb-2.5">
                <h4 class="font-semibold text-gray-900">{{ $domain->name }}</h4>
                <div class="flex items-center gap-2">
                    <span class="font-bold {{ $domain->colors['text'] }}">{{ round($domain->percentage, 0) }}%</span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $domain->colors['badge'] }}">{{ $domain->performance_text }}</span>
                </div>
            </div>
            <div class="w-full bg-gray-200/60 rounded-full h-2 mb-3"><div class="{{ $domain->colors['progress'] }} h-2 rounded-full transition-all duration-700" style="width:{{ $domain->percentage }}%"></div></div>
            <p class="text-sm text-gray-600 mb-2">{{ $domain->description }}</p>
            @if($domain->actionable_insights)
            <div class="rounded-lg {{ $domain->colors['bg'] }} border-l-4 {{ $domain->colors['border'] }} p-3">
                <p class="text-xs font-semibold {{ $domain->colors['text'] }}">{{ __('app.results_actionable') }}</p>
                <p class="text-sm text-gray-700 mt-0.5">{{ $domain->actionable_insights }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Predictive Insights --}}
    @if(isset($oceanResults['predictive_insights']) && count($oceanResults['predictive_insights']) > 0)
    <div class="p-6 border-t border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">{{ __('app.results_predictive') }}</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['key'=>'growth_potential', 'label'=>__('app.results_growth')],
                ['key'=>'organizational_fit_forecast', 'label'=>__('app.results_org_fit')],
                ['key'=>'leadership_potential', 'label'=>__('app.results_leadership')],
                ['key'=>'innovation_index', 'label'=>__('app.results_innovation')],
            ] as $pi)
                @if(isset($oceanResults['predictive_insights'][$pi['key']]))
                @php $insight = $oceanResults['predictive_insights'][$pi['key']]; @endphp
                <div class="rounded-xl {{ $insight->colors['bg'] }} border {{ $insight->colors['border'] }} p-4 text-center">
                    <div class="text-2xl font-bold {{ $insight->colors['text'] }}">{{ round($insight->percentage, 0) }}%</div>
                    <div class="text-sm font-medium text-gray-700 mt-1">{{ $pi['label'] }}</div>
                    <div class="text-xs {{ $insight->colors['text'] }} mt-0.5">{{ $insight->performance_text }}</div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Facets --}}
    @if(isset($oceanResults['facets']) && $oceanResults['facets']->count() > 0)
    <div class="p-6 border-t border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Personality Facets</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($oceanResults['facets'] as $facet)
            <div class="rounded-xl border {{ $facet->colors['border'] }} {{ $facet->colors['bg'] }} p-3 text-center">
                <p class="text-xs font-medium text-gray-700 mb-1">{{ $facet->name }}</p>
                <p class="text-lg font-bold {{ $facet->colors['text'] }}">{{ round($facet->percentage) }}%</p>
                <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2">
                    <div class="{{ $facet->colors['progress'] }} h-1.5 rounded-full" style="width:{{ $facet->percentage }}%"></div>
                </div>
                <span class="text-xs {{ $facet->colors['text'] }} mt-1 inline-block">{{ $facet->performance_text }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- CCS Skills --}}
    @if(isset($oceanResults['ccs_skills']) && $oceanResults['ccs_skills']->count() > 0)
    <div class="p-6 border-t border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Core Character Strengths</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($oceanResults['ccs_skills'] as $skill)
            <div class="rounded-xl border {{ $skill->colors['border'] }} {{ $skill->colors['bg'] }} p-3 text-center">
                <p class="text-xs font-medium text-gray-700 mb-1">{{ $skill->name }}</p>
                <p class="text-lg font-bold {{ $skill->colors['text'] }}">{{ round($skill->percentage) }}%</p>
                <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2">
                    <div class="{{ $skill->colors['progress'] }} h-1.5 rounded-full" style="width:{{ $skill->percentage }}%"></div>
                </div>
                <span class="text-xs {{ $skill->colors['text'] }} mt-1 inline-block">{{ $skill->performance_text }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- RIASEC --}}
<div class="bg-white rounded-2xl shadow-card border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">{{ __('app.results_riasec_full') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('app.results_riasec_full_desc') }}</p>
    </div>
    <div class="p-6 flex justify-center">
        <div class="w-full max-w-lg"><canvas id="riasecBarChart" height="250"></canvas></div>
    </div>
    <div class="p-6 pt-0">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            @php
            $riasecLetterColors = ['R'=>'bg-red-50 text-red-700 border-red-200','I'=>'bg-blue-50 text-blue-700 border-blue-200','A'=>'bg-purple-50 text-purple-700 border-purple-200','S'=>'bg-green-50 text-green-700 border-green-200','E'=>'bg-amber-50 text-amber-700 border-amber-200','C'=>'bg-slate-50 text-slate-700 border-slate-200'];
            @endphp
            @foreach($riasecResults['domains'] as $domain)
            @php $letter = strtoupper(substr($domain->name_en ?? $domain->name, 0, 1)); $lc = $riasecLetterColors[$letter] ?? 'bg-gray-50 text-gray-700 border-gray-200'; @endphp
            <div class="rounded-xl border {{ $domain->colors['border'] }} {{ $domain->colors['bg'] }} p-4 text-center">
                <div class="w-10 h-10 {{ $lc }} rounded-full flex items-center justify-center mx-auto mb-2 text-lg font-bold border">{{ $letter }}</div>
                <div class="text-xs font-medium text-gray-600">{{ $domain->name }}</div>
                <div class="text-xl font-bold {{ $domain->colors['text'] }} mt-1">{{ round($domain->percentage, 0) }}%</div>
                <div class="w-full bg-gray-200/60 rounded-full h-1.5 mt-2"><div class="{{ $domain->colors['progress'] }} h-1.5 rounded-full" style="width:{{ $domain->percentage }}%"></div></div>
            </div>
            @endforeach
        </div>

        @if($riasecResults['career_analysis'])
        <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-4">
            <h3 class="font-semibold text-green-800 mb-2">{{ __('app.results_career_paths') }}</h3>
            <p class="text-sm text-green-700 mb-3">{{ __('app.results_career_based_on', ['code' => $riasecResults['holland_code']]) }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach(explode(';', $riasecResults['career_analysis']->level_description) as $career)
                <span class="bg-white border border-green-300 text-green-800 px-3 py-1 rounded-full text-xs font-medium">{{ transContent(trim($career)) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($riasecResults['work_environment'])
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
            <h3 class="font-semibold text-emerald-800 mb-2">{{ __('app.results_work_env') }}</h3>
            <ul class="text-sm text-emerald-700 space-y-1">
                @foreach(explode(';', $riasecResults['work_environment']->level_description) as $env)
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>{{ transContent(trim($env)) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

{{-- Stream Recommendations --}}
@if(!empty($streamRecommendations))
<div class="bg-white rounded-2xl shadow-card border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Recommended Academic Streams</h2>
                <p class="text-sm text-gray-500 mt-0.5">Based on your personality, interests, and cognitive profile</p>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($streamRecommendations as $stream)
            @php
                $badgeClasses = match($stream['recommendation']) {
                    'strongly_recommended' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                    'recommended'          => 'bg-blue-100 text-blue-800 border border-blue-200',
                    'suitable'             => 'bg-amber-100 text-amber-800 border border-amber-200',
                    default                => 'bg-gray-100 text-gray-600 border border-gray-200',
                };
                $ringClass = $stream['recommendation'] === 'strongly_recommended'
                    ? 'ring-2 ring-emerald-400 ring-offset-2'
                    : '';
                $colorMap = [
                    'blue'   => ['border' => 'border-blue-200',   'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'badge_bg' => 'bg-blue-100',  'progress' => 'bg-blue-500'],
                    'green'  => ['border' => 'border-green-200',  'bg' => 'bg-green-50',  'text' => 'text-green-700',  'badge_bg' => 'bg-green-100', 'progress' => 'bg-green-500'],
                    'amber'  => ['border' => 'border-amber-200',  'bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'badge_bg' => 'bg-amber-100', 'progress' => 'bg-amber-500'],
                    'purple' => ['border' => 'border-purple-200', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'badge_bg' => 'bg-purple-100','progress' => 'bg-purple-500'],
                    'orange' => ['border' => 'border-orange-200', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'badge_bg' => 'bg-orange-100','progress' => 'bg-orange-500'],
                ];
                $c = $colorMap[$stream['color']] ?? $colorMap['blue'];
            @endphp
            <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} p-5 {{ $ringClass }} relative">
                @if($stream['recommendation'] === 'strongly_recommended')
                <div class="absolute -top-2 -right-2">
                    <span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Top Match
                    </span>
                </div>
                @endif
                <div class="flex items-start justify-between mb-3 pr-2">
                    <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $stream['name'] }}</h3>
                    <span class="text-lg font-bold {{ $c['text'] }} ml-2 flex-shrink-0">{{ $stream['score'] }}%</span>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">{{ $stream['description'] }}</p>
                <div class="w-full bg-gray-200/60 rounded-full h-1.5 mb-3">
                    <div class="{{ $c['progress'] }} h-1.5 rounded-full" style="width:{{ $stream['score'] }}%"></div>
                </div>
                <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeClasses }} mb-3">
                    {{ $stream['recommendation_label'] }}
                </span>
                <div class="flex flex-wrap gap-1.5 mt-1">
                    @foreach($stream['careers'] as $career)
                    <span class="text-xs {{ $c['badge_bg'] }} {{ $c['text'] }} px-2 py-0.5 rounded-full border {{ $c['border'] }} font-medium">{{ $career }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-4 text-center">Stream fit scores are computed from your RIASEC career interests, OCEAN personality traits, and cognitive abilities.</p>
    </div>
</div>
@endif

{{-- Cognitive --}}
<div class="bg-white rounded-2xl shadow-card border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">{{ __('app.results_cognitive_full') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('app.results_cognitive_full_desc') }}</p>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($cognitiveResults['domains'] as $cog)
            <div class="rounded-xl border {{ $cog->colors['border'] }} {{ $cog->colors['bg'] }} p-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">{{ $cog->name }}</h4>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $cog->colors['badge'] }}">{{ $cog->performance_text }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200/60 rounded-full h-2.5"><div class="{{ $cog->colors['progress'] }} h-2.5 rounded-full transition-all duration-700" style="width:{{ $cog->percentage }}%"></div></div>
                    <span class="text-xl font-bold {{ $cog->colors['text'] }}">{{ round($cog->percentage, 0) }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6 bg-purple-50 border border-purple-200 rounded-xl p-5">
            <h3 class="font-semibold text-purple-800 mb-2">{{ __('app.results_cog_strength_title') }}</h3>
            @php
                $cogLevel = $cognitiveResults['domains']->where('percentage', '>=', 85)->count() > 0 ? __('app.results_cog_exceptional') : ($cognitiveResults['domains']->where('percentage', '>=', 70)->count() > 0 ? __('app.results_cog_strong') : __('app.results_cog_good'));
            @endphp
            <p class="text-sm text-purple-700 mb-1">{{ __('app.results_cog_profile', ['level' => $cogLevel]) }}</p>
            <p class="text-sm text-purple-700"><strong>{{ __('app.results_cog_strengths') }}</strong> {{ $cognitiveResults['domains']->sortByDesc('percentage')->take(2)->pluck('name')->implode(', ') }}</p>
        </div>
    </div>
</div>

{{-- Development Plan --}}
@php
    // ── Compute personalised data for L&D plan ──
    $topOcean   = $oceanResults['domains']->sortByDesc('percentage')->first();
    $weakOcean  = $oceanResults['domains']->sortBy('percentage')->first();
    $topRiasec  = $riasecResults['domains']->sortByDesc('percentage')->first();
    $weakRiasec = $riasecResults['domains']->sortBy('percentage')->first();
    $topCog     = $cognitiveResults['domains']->sortByDesc('percentage')->first();
    $weakCog    = $cognitiveResults['domains']->sortBy('percentage')->first();

    $strengths = collect([
        ['name' => $topOcean->name, 'pct' => $topOcean->percentage, 'label' => __('app.ld_personality_insight'), 'color' => 'blue'],
        ['name' => $topRiasec->name, 'pct' => $topRiasec->percentage, 'label' => __('app.ld_career_insight'), 'color' => 'green'],
        ['name' => $topCog->name, 'pct' => $topCog->percentage, 'label' => __('app.ld_cognitive_insight'), 'color' => 'purple'],
    ]);

    $devAreas = collect([
        ['name' => $weakOcean->name, 'pct' => $weakOcean->percentage, 'color' => 'amber'],
        ['name' => $weakRiasec->name, 'pct' => $weakRiasec->percentage, 'color' => 'orange'],
        ['name' => $weakCog->name, 'pct' => $weakCog->percentage, 'color' => 'red'],
    ])->filter(fn($d) => $d['pct'] < 70);

    $careersList = [];
    if ($riasecResults['career_analysis']) {
        $careersList = array_slice(explode(';', $riasecResults['career_analysis']->level_description), 0, 4);
    }
@endphp

<div class="bg-white rounded-2xl shadow-card border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('app.ld_plan_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('app.ld_plan_subtitle') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Key Strengths ── --}}
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-1">{{ __('app.ld_strengths_title') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('app.ld_strengths_desc') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($strengths as $s)
            <div class="rounded-xl border border-{{ $s['color'] }}-200 bg-{{ $s['color'] }}-50/50 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-{{ $s['color'] }}-600 uppercase">{{ $s['label'] }}</span>
                    <span class="text-lg font-bold text-{{ $s['color'] }}-700">{{ round($s['pct']) }}%</span>
                </div>
                <div class="text-base font-semibold text-gray-900 mb-2">{{ $s['name'] }}</div>
                <div class="w-full bg-{{ $s['color'] }}-100 rounded-full h-1.5">
                    <div class="bg-{{ $s['color'] }}-500 h-1.5 rounded-full" style="width:{{ $s['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Development Areas ── --}}
    @if($devAreas->count() > 0)
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-1">{{ __('app.ld_development_title') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('app.ld_development_desc') }}</p>
        <div class="space-y-3">
            @foreach($devAreas as $d)
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 border border-gray-200">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-900">{{ $d['name'] }}</span>
                        <span class="text-sm font-bold text-{{ $d['color'] }}-600">{{ round($d['pct']) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-{{ $d['color'] }}-500 h-1.5 rounded-full" style="width:{{ $d['pct'] }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Long-Term Vision ── --}}
    <div class="p-6 border-b border-gray-100">
        <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 text-sm">{{ __('app.ld_long_term') }}</h4>
                        <p class="text-xs text-gray-500">{{ __('app.ld_long_term_desc') }}</p>
                    </div>
                </div>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ __('app.ld_top_strength') }}: <strong>{{ $topOcean->name }}</strong> ({{ round($topOcean->percentage) }}%)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ __('app.ld_primary_interest') }}: <strong>{{ $topRiasec->name }}</strong> ({{ round($topRiasec->percentage) }}%)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ __('app.ld_best_ability') }}: <strong>{{ $topCog->name }}</strong> ({{ round($topCog->percentage) }}%)</span>
                    </li>
                </ul>
            </div>
    </div>

    {{-- ── Recommended Resources ── --}}
    <div class="p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-1">{{ __('app.ld_resources_title') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('app.ld_resources_desc') }}</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 text-center group hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-semibold text-blue-700">{{ __('app.ld_books') }}</span>
            </div>
            <div class="rounded-xl bg-green-50 border border-green-100 p-4 text-center group hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-green-700">{{ __('app.ld_courses') }}</span>
            </div>
            <div class="rounded-xl bg-purple-50 border border-purple-100 p-4 text-center group hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <span class="text-xs font-semibold text-purple-700">{{ __('app.ld_practice') }}</span>
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-100 p-4 text-center group hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-amber-700">{{ __('app.ld_mentoring') }}</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const oceanCtx = document.getElementById('oceanRadarChart').getContext('2d');
new Chart(oceanCtx, {
    type: 'radar',
    data: {
        labels: @json($oceanResults['domains']->pluck('name')),
        datasets: [{
            label: 'Score %',
            data: @json($oceanResults['domains']->pluck('percentage')),
            backgroundColor: 'rgba(99, 102, 241, 0.15)',
            borderColor: 'rgba(99, 102, 241, 0.8)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(99, 102, 241, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' }, pointLabels: { font: { size: 13, weight: '600' } } } },
        plugins: { legend: { display: false } }
    }
});

const riasecCtx = document.getElementById('riasecBarChart').getContext('2d');
const riasecColors = ['#ef4444','#3b82f6','#8b5cf6','#22c55e','#f59e0b','#64748b'];
new Chart(riasecCtx, {
    type: 'bar',
    data: {
        labels: @json($riasecResults['domains']->pluck('name')),
        datasets: [{
            label: 'Score %',
            data: @json($riasecResults['domains']->pluck('percentage')),
            backgroundColor: riasecColors.map(c => c + '20'),
            borderColor: riasecColors,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } },
        plugins: { legend: { display: false } }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[style*="width:"]').forEach(bar => {
        const w = bar.style.width; bar.style.width = '0%';
        setTimeout(() => { bar.style.width = w; }, 150);
    });
});
</script>
@endpush
@endsection
