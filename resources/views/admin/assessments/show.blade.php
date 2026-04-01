@extends('layouts.app')
@section('title', strtoupper($assessmentType) . ' Results - ' . $result->user->name)

@section('header')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ strtoupper($assessmentType) }} — Assessment Results</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $result->user->name }} • {{ $result->created_at->format('M d, Y') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.assessments.download', $result) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Report
        </a>
        <a href="{{ route('admin.users.show', $result->user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-semibold rounded-xl transition-colors bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to User
        </a>
    </div>
</div>
@endsection

@section('content')
@php $domains = $results['domains']; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Performance</h2>
        <div class="relative" style="height:340px"><canvas id="domainChart"></canvas></div>
    </div>
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
        <div class="flex flex-col items-center mb-6">
            <div class="relative w-28 h-28">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                    <circle cx="60" cy="60" r="52" fill="none" stroke="url(#ringGrad)" stroke-width="10"
                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $totalPercent / 100) }}"
                            stroke-linecap="round"/>
                    <defs><linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#14b8a6"/></linearGradient></defs>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-900">{{ $totalPercent }}%</span>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Overall Score</p>
        </div>
        <hr class="border-gray-100 my-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Domain Details</h3>
        <div class="space-y-3">
            @foreach($domains as $d)
            <div>
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">{{ $d->name }}</span>
                    <span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-700" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Domain Cards --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    @foreach($domains as $d)
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-5 hover:shadow-card-hover transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">{{ $d->name }}</h3>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $d->colors['badge'] }} border" style="border-color:{{ $d->colors['hex_border'] }}">{{ $d->performance_text }}</span>
        </div>
        <div class="flex items-center gap-3 mb-2">
            <div class="flex-1 bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
            </div>
            <span class="text-2xl font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
        </div>
        @if($d->level_description)
        <p class="text-xs text-gray-500 mt-1">{{ $d->level_description }}</p>
        @endif
        @if($d->actionable_insights)
        <div class="mt-3 p-3 rounded-lg border-l-4" style="background-color:{{ $d->colors['hex_bg'] }};border-color:{{ $d->colors['hex_border'] }}">
            <p class="text-xs font-semibold" style="color:{{ $d->colors['hex_text'] }}">Actionable Insight</p>
            <p class="text-xs mt-1" style="color:{{ $d->colors['hex_text'] }}">{{ $d->actionable_insights }}</p>
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- RIASEC: Holland Code + Career/Work --}}
@if($assessmentType === 'riasec')
@php
    $careerAnalysis  = $results['career_analysis'] ?? null;
    $workEnvironment = $results['work_environment'] ?? null;
    $hollandCode     = $results['holland_code'] ?? '';
@endphp
@if($hollandCode)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Holland Code</h2>
    <p class="text-3xl font-bold text-primary-600 tracking-widest">{{ $hollandCode }}</p>
</div>
@endif
@if($careerAnalysis)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Career Analysis</h2>
    <p class="text-sm text-gray-700">{{ $careerAnalysis->description }}</p>
    @if($careerAnalysis->level_description)
    <div class="mt-3 space-y-1">
        @foreach(explode(';', $careerAnalysis->level_description) as $career)
        @if(trim($career))
        <span class="inline-block text-xs bg-primary-50 text-primary-700 border border-primary-200 rounded-full px-3 py-0.5 mr-1 mb-1">{{ trim($career) }}</span>
        @endif
        @endforeach
    </div>
    @endif
</div>
@endif
@if($workEnvironment)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Work Environment Preferences</h2>
    <p class="text-sm text-gray-700">{{ $workEnvironment->description }}</p>
</div>
@endif
@endif

{{-- OCEAN: Facets, CCS, Predictive --}}
@if($assessmentType === 'ocean')
@php
    $facets          = $results['facets'] ?? collect();
    $ccsSkills       = $results['ccs_skills'] ?? collect();
    $predictiveInsights = $results['predictive_insights'] ?? collect();
@endphp

@if($facets->count() > 0)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Facets Analysis</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach($facets as $facet)
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 text-center transition hover:shadow-md" style="border-color:{{ $facet->colors['hex_border'] }}">
            <p class="font-medium text-gray-900 text-xs mb-2">{{ $facet->name }}</p>
            <p class="text-lg font-bold" style="color:{{ $facet->colors['hex_text'] }}">{{ round($facet->percentage, 2) }}%</p>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                <div class="h-1 rounded-full" style="width:{{ $facet->percentage }}%;background-color:{{ $facet->colors['hex_progress'] }}"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($ccsSkills->count() > 0)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">CCS Skills</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach($ccsSkills as $skill)
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 text-center transition hover:shadow-md" style="border-color:{{ $skill->colors['hex_border'] }}">
            <p class="font-medium text-gray-900 text-xs mb-2">{{ $skill->name }}</p>
            <p class="text-lg font-bold" style="color:{{ $skill->colors['hex_text'] }}">{{ round($skill->percentage, 2) }}%</p>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                <div class="h-1 rounded-full" style="width:{{ $skill->percentage }}%;background-color:{{ $skill->colors['hex_progress'] }}"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($predictiveInsights->count() > 0)
<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Predictive Insights</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($predictiveInsights as $insight)
        <div class="rounded-xl border p-4" style="background-color:{{ $insight->colors['hex_bg'] }};border-color:{{ $insight->colors['hex_border'] }}">
            <div class="flex items-center justify-between mb-3">
                <p class="font-semibold text-gray-900">{{ str_replace('_', ' ', ucwords($insight->result_type, '_')) }}</p>
                <span class="text-sm font-bold" style="color:{{ $insight->colors['hex_text'] }}">{{ round($insight->percentage, 2) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all" style="width:{{ $insight->percentage }}%;background-color:{{ $insight->colors['hex_progress'] }}"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const ctx = document.getElementById('domainChart').getContext('2d');
const chartType = '{{ $assessmentType === "ocean" ? "radar" : "bar" }}';
const labels = @json($domains->pluck('name'));
const scores = @json($domains->pluck('percentage'));
const hexColors = @json($domains->pluck('colors')->pluck('hex_progress'));

new Chart(ctx, {
    type: chartType,
    data: {
        labels: labels,
        datasets: [{
            label: 'Score %',
            data: scores,
            backgroundColor: chartType === 'radar' ? 'rgba(99,102,241,0.15)' : hexColors.map(c => c + '40'),
            borderColor: chartType === 'radar' ? 'rgba(99,102,241,0.8)' : hexColors,
            borderWidth: 2,
            borderRadius: chartType === 'bar' ? 10 : 0,
            borderSkipped: false,
            pointBackgroundColor: 'rgba(99,102,241,1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: chartType === 'radar'
            ? { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' }, pointLabels: { font: { size: 13, weight: '600' } } } }
            : { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection
