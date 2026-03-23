@extends('layouts.app')
@section('title', $quiz->title . ' ' . __('app.results_title') . ' - ' . __('app.brand'))

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }} — {{ __('app.results_title') }}</h1>
        <p class="text-sm text-gray-500">{{ __('app.results_show_sub') }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('app.results_back_dash') }}
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('app.results_domain_chart') }}</h2>
        <div class="relative" style="height:340px"><canvas id="domainChart"></canvas></div>
    </div>
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('app.results_summary') }}</h2>
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
            <p class="text-sm text-gray-500 mt-2">{{ __('app.results_overall') }}</p>
        </div>
        <hr class="border-gray-100 my-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.results_domain_details') }}</h3>
        <div class="space-y-3">
            @foreach($domains as $d)
            @php
                $pct = $d['percentage'];
                $color = $pct >= 85 ? 'emerald' : ($pct >= 70 ? 'blue' : ($pct >= 40 ? 'amber' : ($pct >= 20 ? 'orange' : 'red')));
            @endphp
            <div>
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">{{ $d['title'] }}</span>
                    <span class="font-semibold text-{{ $color }}-600">{{ $pct }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-{{ $color }}-500 h-2 rounded-full transition-all duration-700" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('results.download', $quiz) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('app.results_download') }}
            </a>
            <a href="{{ route('results.index') }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-semibold rounded-xl transition bg-white">
                {{ __('app.results_view_all') }}
            </a>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($domains as $d)
    @php
        $pct = $d['percentage'];
        $color = $pct >= 85 ? 'emerald' : ($pct >= 70 ? 'blue' : ($pct >= 40 ? 'amber' : ($pct >= 20 ? 'orange' : 'red')));
        $level = $pct >= 85 ? __('app.results_exceptional') : ($pct >= 70 ? __('app.results_high') : ($pct >= 40 ? __('app.results_avg') : ($pct >= 20 ? __('app.results_below_avg') : __('app.results_low'))));
    @endphp
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-5 hover:shadow-card-hover transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">{{ $d['title'] }}</h3>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">{{ $level }}</span>
        </div>
        <div class="flex items-center gap-3 mb-2">
            <div class="flex-1 bg-gray-100 rounded-full h-3">
                <div class="bg-gradient-to-r from-{{ $color }}-400 to-{{ $color }}-600 h-3 rounded-full transition-all duration-700" style="width:{{ $pct }}%"></div>
            </div>
            <span class="text-2xl font-bold text-{{ $color }}-600">{{ $pct }}%</span>
        </div>
        <p class="text-xs text-gray-400">{{ __('app.results_score') }}: {{ $d['score'] }} / {{ $d['max'] }}</p>
    </div>
    @endforeach
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const ctx = document.getElementById('domainChart').getContext('2d');
const chartType = '{{ $quiz->slug === "ocean" ? "radar" : "bar" }}';
const labels = @json($domains->pluck('title'));
const scores = @json($domains->pluck('percentage'));
const colors = scores.map(v => v >= 85 ? '#10b981' : (v >= 70 ? '#3b82f6' : (v >= 40 ? '#f59e0b' : (v >= 20 ? '#f97316' : '#ef4444'))));

new Chart(ctx, {
    type: chartType,
    data: {
        labels: labels,
        datasets: [{
            label: '{{ __("app.results_score") }} %',
            data: scores,
            backgroundColor: chartType === 'radar' ? 'rgba(99,102,241,0.15)' : colors.map(c => c + '25'),
            borderColor: chartType === 'radar' ? 'rgba(99,102,241,0.8)' : colors,
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
