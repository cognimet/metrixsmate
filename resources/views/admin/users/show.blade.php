@extends('layouts.admin')
@section('title', 'User Details - ' . $user->name)
@section('page-title', $user->name)
@section('page-description', 'User profile and complete assessment results')

@section('page-actions')
    @if($latestOceanResult)
    <a href="{{ route('admin.assessments.download', $latestOceanResult) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
    </a>
    @endif
    <a href="{{ route('admin.users') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@section('content')
<div class="space-y-6">

    @php
        $hasOcean    = $oceanResults['domains']->isNotEmpty();
        $hasRiasec   = $riasecResults['domains']->isNotEmpty();
        $hasCognitive = $cognitiveResults['domains']->isNotEmpty();
        $totalCompleted = ($hasOcean ? 1 : 0) + ($hasRiasec ? 1 : 0) + ($hasCognitive ? 1 : 0);
    @endphp

    {{-- Categorized Detail Views & PDF Downloads --}}
    @if($totalCompleted > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Categorized Detail Views</h2>
                <p class="text-xs text-gray-500 mt-0.5">View results tailored for different audiences</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.school', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg border border-emerald-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    School View
                </a>
                <a href="{{ route('admin.users.university', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-lg border border-indigo-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    University View
                </a>
                <a href="{{ route('admin.users.company', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold rounded-lg border border-rose-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    Company View
                </a>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-2">Download PDF Reports</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.report.school', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    School PDF
                </a>
                <a href="{{ route('admin.users.report.university', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    University PDF
                </a>
                <a href="{{ route('admin.users.report.company', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium rounded-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Company PDF
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- User Info Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Personal Information</h2>
                <div class="space-y-3">
                    <div><label class="text-xs text-gray-500 uppercase">Email</label><p class="text-gray-900">{{ $user->email }}</p></div>
                    <div><label class="text-xs text-gray-500 uppercase">Mobile</label><p class="text-gray-900">{{ $user->mobile_no ?? 'Not provided' }}</p></div>
                    <div><label class="text-xs text-gray-500 uppercase">Location</label><p class="text-gray-900">{{ $user->city->name ?? 'N/A' }}, {{ $user->state->name ?? 'N/A' }}</p></div>
                    <div><label class="text-xs text-gray-500 uppercase">Member Since</label><p class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</p></div>
                </div>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Account Status</h2>
                <div class="space-y-3">
                    <div><label class="text-xs text-gray-500 uppercase">Role</label><p><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">{{ ucfirst($user->role) }}</span></p></div>
                    <div><label class="text-xs text-gray-500 uppercase">Status</label><p><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $user->isActive() ? 'Active' : 'Inactive' }}</span></p></div>
                    <div><label class="text-xs text-gray-500 uppercase">AI Searches</label><p class="text-gray-900">{{ $user->free_ai_searches ?? 0 }} free</p></div>
                    <div><label class="text-xs text-gray-500 uppercase">Assessment Searches</label><p class="text-gray-900">{{ $user->free_assessment_searches ?? 0 }} free</p></div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 mt-6 pt-6"><label class="text-xs text-gray-500 uppercase block mb-2">Admin Notes</label><p class="text-gray-900">{{ $user->admin_notes ?? 'No notes' }}</p></div>
        <div class="flex gap-3 mt-6">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Edit User</a>
            <button onclick="if(confirm('Delete this user?')) { document.getElementById('delete-form').submit(); }" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Delete User</button>
        </div>
    </div>
    <form id="delete-form" action="{{ route('admin.users.delete', $user) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>

    {{-- Assessment Summary Stats --}}
    @if($totalCompleted > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-gray-600 text-xs font-semibold uppercase">Completed Assessments</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalCompleted }} / 3</p>
        </div>
        @if($hasOcean)
        <div class="bg-blue-50 rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-gray-600 text-xs font-semibold uppercase">OCEAN</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ round($oceanResults['domains']->avg('percentage'), 1) }}%</p>
            @if($latestOceanResult)<p class="text-xs text-gray-600 mt-1">{{ $latestOceanResult->created_at->format('M d, Y') }}</p>@endif
        </div>
        @endif
        @if($hasRiasec)
        <div class="bg-green-50 rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-gray-600 text-xs font-semibold uppercase">RIASEC</p>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ round($riasecResults['domains']->avg('percentage'), 1) }}%</p>
            @if($latestRiasecResult)<p class="text-xs text-gray-600 mt-1">{{ $latestRiasecResult->created_at->format('M d, Y') }}</p>@endif
        </div>
        @endif
        @if($hasCognitive)
        <div class="bg-purple-50 rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-gray-600 text-xs font-semibold uppercase">Cognitive</p>
            <p class="text-2xl font-bold text-purple-600 mt-2">{{ round($cognitiveResults['average_score'], 1) }}%</p>
            @if($latestCognitiveResult)<p class="text-xs text-gray-600 mt-1">{{ $latestCognitiveResult->created_at->format('M d, Y') }}</p>@endif
        </div>
        @endif
    </div>
    @endif

    @if($totalCompleted === 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"><p class="text-gray-500">No assessment results yet.</p></div>
    @endif

    {{-- OCEAN Personality --}}
    @if($hasOcean)
    @php $domains = $oceanResults['domains']; $oid = 'ocean'; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" id="section-ocean">
        <div class="flex items-center justify-between mb-6">
            <div><h3 class="text-xl font-semibold text-gray-900">OCEAN &mdash; Personality Assessment</h3><p class="text-sm text-gray-500 mt-1">Big Five personality traits analysis</p></div>
            @if($latestOceanResult)<span class="text-sm text-gray-500">{{ $latestOceanResult->created_at->format('M d, Y') }}</span>@endif
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Performance Radar</h4>
                <div class="relative" style="height:300px"><canvas id="chart-{{ $oid }}"></canvas></div>
            </div>
            <div class="space-y-4">
                <div class="flex flex-col items-center">
                    @php $avgPct = round($domains->avg('percentage'), 1); @endphp
                    <div class="relative w-24 h-24">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $oid }})" stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}" stroke-linecap="round"/>
                            <defs><linearGradient id="grad-{{ $oid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#14b8a6"/></linearGradient></defs>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center"><span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Domain Average</p>
                </div>
                <hr class="border-gray-100">
                <div class="space-y-2.5">
                    @foreach($domains as $d)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-0.5"><span class="font-medium text-gray-600">{{ $d->name }}</span><span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d->colors['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d->name }}</h4><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span></div>
                <div class="flex items-center gap-2 mb-3"><div class="flex-1 bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div><span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                @if($d->level_description)<p class="text-xs text-gray-600 mb-2">{{ $d->level_description }}</p>@endif
                @if($d->actionable_insights)<p class="text-xs text-gray-500 italic">{{ $d->actionable_insights }}</p>@endif
            </div>
            @endforeach
        </div>
        @php $facets = $oceanResults['facets']; @endphp
        @if($facets->count() > 0)
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="font-semibold text-gray-900 mb-3">Personality Facets</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($facets as $facet)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $facet->colors['hex_border'] }}">
                    <p class="font-medium text-gray-900 text-xs">{{ $facet->name }}</p>
                    <p class="text-xl font-bold mt-1" style="color:{{ $facet->colors['hex_text'] }}">{{ round($facet->percentage, 1) }}%</p>
                    <p class="text-xs {{ $facet->colors['badge'] }} inline-block px-1.5 py-0.5 rounded mt-1">{{ $facet->performance_text }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @php $ccsSkills = $oceanResults['ccs_skills']; @endphp
        @if($ccsSkills->count() > 0)
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="font-semibold text-gray-900 mb-3">CCS Skills (Competency)</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($ccsSkills as $skill)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $skill->colors['hex_border'] }}">
                    <p class="font-medium text-gray-900 text-xs">{{ $skill->name }}</p>
                    <p class="text-xl font-bold mt-1" style="color:{{ $skill->colors['hex_text'] }}">{{ round($skill->percentage, 1) }}%</p>
                    <p class="text-xs {{ $skill->colors['badge'] }} inline-block px-1.5 py-0.5 rounded mt-1">{{ $skill->performance_text }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @php $predictiveInsights = $oceanResults['predictive_insights']; @endphp
        @if($predictiveInsights->count() > 0)
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="font-semibold text-gray-900 mb-3">Predictive Insights</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($predictiveInsights as $insight)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $insight->colors['hex_border'] }}">
                    <p class="font-medium text-gray-900 text-xs">{{ str_replace('_', ' ', ucwords($insight->result_type, '_')) }}</p>
                    <p class="text-xl font-bold mt-1" style="color:{{ $insight->colors['hex_text'] }}">{{ round($insight->percentage, 1) }}%</p>
                    <p class="text-xs {{ $insight->colors['badge'] }} inline-block px-1.5 py-0.5 rounded mt-1">{{ $insight->performance_text }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @push('scripts')
    <script>
    (function(){
        const ctx = document.getElementById('chart-{{ $oid }}').getContext('2d');
        const labels = @json($domains->pluck('name'));
        const scores = @json($domains->pluck('percentage'));
        new Chart(ctx, { type: 'radar', data: { labels, datasets: [{ label: 'Score %', data: scores, backgroundColor: 'rgba(99,102,241,0.15)', borderColor: 'rgba(99,102,241,0.8)', borderWidth: 2, pointBackgroundColor: 'rgba(99,102,241,1)', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' }, pointLabels: { font: { size: 12, weight: '600' } } } }, plugins: { legend: { display: false } } } });
    })();
    </script>
    @endpush
    @endif

    {{-- RIASEC Career Interests --}}
    @if($hasRiasec)
    @php $domains = $riasecResults['domains']; $rid = 'riasec'; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" id="section-riasec">
        <div class="flex items-center justify-between mb-6">
            <div><h3 class="text-xl font-semibold text-gray-900">RIASEC &mdash; Career Interests</h3><p class="text-sm text-gray-500 mt-1">Holland career interest profiling</p></div>
            @if($latestRiasecResult)<span class="text-sm text-gray-500">{{ $latestRiasecResult->created_at->format('M d, Y') }}</span>@endif
        </div>
        @if($riasecResults['holland_code'])
        <div class="mb-4 bg-primary-50 rounded-lg px-4 py-3 border border-primary-100">
            <span class="text-sm text-gray-600">Holland Code:</span>
            <span class="font-bold text-primary-700 text-2xl tracking-widest ml-2">{{ $riasecResults['holland_code'] }}</span>
        </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Interest Profile</h4>
                <div class="relative" style="height:300px"><canvas id="chart-{{ $rid }}"></canvas></div>
            </div>
            <div class="space-y-4">
                <div class="flex flex-col items-center">
                    @php $avgPct = round($domains->avg('percentage'), 1); @endphp
                    <div class="relative w-24 h-24">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $rid }})" stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}" stroke-linecap="round"/>
                            <defs><linearGradient id="grad-{{ $rid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#10b981"/><stop offset="1" stop-color="#3b82f6"/></linearGradient></defs>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center"><span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Domain Average</p>
                </div>
                <hr class="border-gray-100">
                <div class="space-y-2.5">
                    @foreach($domains as $d)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-0.5"><span class="font-medium text-gray-600">{{ $d->name }}</span><span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d->colors['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d->name }}</h4><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span></div>
                <div class="flex items-center gap-2 mb-3"><div class="flex-1 bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div><span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                @if($d->level_description)<p class="text-xs text-gray-600 mb-2">{{ $d->level_description }}</p>@endif
                @if($d->actionable_insights)<p class="text-xs text-gray-500 italic">{{ $d->actionable_insights }}</p>@endif
            </div>
            @endforeach
        </div>
        @if($riasecResults['career_analysis'])
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="font-semibold text-gray-900 mb-3">Career Analysis</h4>
            <div class="bg-green-50 rounded-lg border border-green-100 p-4">
                <p class="text-sm text-gray-700">{{ $riasecResults['career_analysis']->description }}</p>
                @if($riasecResults['career_analysis']->actionable_insights)<p class="text-sm text-gray-600 mt-2 italic">{{ $riasecResults['career_analysis']->actionable_insights }}</p>@endif
            </div>
        </div>
        @endif
        @if($riasecResults['work_environment'])
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="font-semibold text-gray-900 mb-3">Ideal Work Environment</h4>
            <div class="bg-blue-50 rounded-lg border border-blue-100 p-4">
                <p class="text-sm text-gray-700">{{ $riasecResults['work_environment']->description }}</p>
                @if($riasecResults['work_environment']->actionable_insights)<p class="text-sm text-gray-600 mt-2 italic">{{ $riasecResults['work_environment']->actionable_insights }}</p>@endif
            </div>
        </div>
        @endif
    </div>
    @push('scripts')
    <script>
    (function(){
        const ctx = document.getElementById('chart-{{ $rid }}').getContext('2d');
        const labels = @json($domains->pluck('name'));
        const scores = @json($domains->pluck('percentage'));
        const hexColors = @json($domains->pluck('colors')->pluck('hex_progress'));
        new Chart(ctx, { type: 'bar', data: { labels, datasets: [{ label: 'Score %', data: scores, backgroundColor: hexColors.map(c => c + '40'), borderColor: hexColors, borderWidth: 2, borderRadius: 10, borderSkipped: false }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } } });
    })();
    </script>
    @endpush
    @endif

    {{-- Cognitive Abilities --}}
    @if($hasCognitive)
    @php $domains = $cognitiveResults['domains']; $cid = 'cognitive'; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" id="section-cognitive">
        <div class="flex items-center justify-between mb-6">
            <div><h3 class="text-xl font-semibold text-gray-900">Cognitive Abilities</h3><p class="text-sm text-gray-500 mt-1">Mental aptitude and reasoning skills</p></div>
            @if($latestCognitiveResult)<span class="text-sm text-gray-500">{{ $latestCognitiveResult->created_at->format('M d, Y') }}</span>@endif
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Cognitive Profile</h4>
                <div class="relative" style="height:300px"><canvas id="chart-{{ $cid }}"></canvas></div>
            </div>
            <div class="space-y-4">
                <div class="flex flex-col items-center">
                    @php $avgPct = round($cognitiveResults['average_score'], 1); @endphp
                    <div class="relative w-24 h-24">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $cid }})" stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}" stroke-linecap="round"/>
                            <defs><linearGradient id="grad-{{ $cid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#8b5cf6"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center"><span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Average Score</p>
                </div>
                <hr class="border-gray-100">
                <div class="space-y-2.5">
                    @foreach($domains as $d)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-0.5"><span class="font-medium text-gray-600">{{ $d->name }}</span><span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d->colors['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d->name }}</h4><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span></div>
                <div class="flex items-center gap-2 mb-3"><div class="flex-1 bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div></div><span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span></div>
                @if($d->level_description)<p class="text-xs text-gray-600 mb-2">{{ $d->level_description }}</p>@endif
                @if($d->actionable_insights)<p class="text-xs text-gray-500 italic">{{ $d->actionable_insights }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @push('scripts')
    <script>
    (function(){
        const ctx = document.getElementById('chart-{{ $cid }}').getContext('2d');
        const labels = @json($domains->pluck('name'));
        const scores = @json($domains->pluck('percentage'));
        const hexColors = @json($domains->pluck('colors')->pluck('hex_progress'));
        new Chart(ctx, { type: 'bar', data: { labels, datasets: [{ label: 'Score %', data: scores, backgroundColor: hexColors.map(c => c + '40'), borderColor: hexColors, borderWidth: 2, borderRadius: 10, borderSkipped: false }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,.04)' } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } } });
    })();
    </script>
    @endpush
    @endif

    {{-- Learning Styles --}}
    @if(count($learningStyles) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Learning Styles</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($learningStyles as $idx => $style)
            @php
                $sc = [['bg'=>'bg-indigo-50','text'=>'text-indigo-700','border'=>'border-indigo-200','bar'=>'bg-indigo-500'],['bg'=>'bg-emerald-50','text'=>'text-emerald-700','border'=>'border-emerald-200','bar'=>'bg-emerald-500'],['bg'=>'bg-amber-50','text'=>'text-amber-700','border'=>'border-amber-200','bar'=>'bg-amber-500'],['bg'=>'bg-purple-50','text'=>'text-purple-700','border'=>'border-purple-200','bar'=>'bg-purple-500']][$idx] ?? ['bg'=>'bg-gray-50','text'=>'text-gray-700','border'=>'border-gray-200','bar'=>'bg-gray-500'];
            @endphp
            <div class="{{ $sc['bg'] }} rounded-xl border {{ $sc['border'] }} p-4">
                <p class="text-sm font-semibold {{ $sc['text'] }}">{{ $style['name'] }}</p>
                <p class="text-2xl font-bold {{ $sc['text'] }} mt-1">{{ $style['score'] }}%</p>
                <div class="w-full bg-white/50 rounded-full h-1.5 mt-2"><div class="h-1.5 rounded-full {{ $sc['bar'] }}" style="width:{{ $style['score'] }}%"></div></div>
                @if($idx === 0)<span class="inline-block mt-2 text-xs font-medium px-2 py-0.5 rounded-full bg-white {{ $sc['text'] }}">Primary</span>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stream Recommendations --}}
    @if(count($streamRecommendations) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Stream Recommendations</h3>
        <div class="space-y-3">
            @foreach($streamRecommendations as $idx => $stream)
            @php
                $rc = ['strongly_recommended'=>['bg'=>'bg-green-50','border'=>'border-green-200','badge'=>'bg-green-100 text-green-800','bar'=>'bg-green-500'],'recommended'=>['bg'=>'bg-blue-50','border'=>'border-blue-200','badge'=>'bg-blue-100 text-blue-800','bar'=>'bg-blue-500'],'suitable'=>['bg'=>'bg-amber-50','border'=>'border-amber-200','badge'=>'bg-amber-100 text-amber-800','bar'=>'bg-amber-500'],'less_suitable'=>['bg'=>'bg-gray-50','border'=>'border-gray-200','badge'=>'bg-gray-100 text-gray-600','bar'=>'bg-gray-400']][$stream['recommendation']] ?? ['bg'=>'bg-gray-50','border'=>'border-gray-200','badge'=>'bg-gray-100 text-gray-600','bar'=>'bg-gray-400'];
            @endphp
            <div class="{{ $rc['bg'] }} rounded-xl border {{ $rc['border'] }} p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-gray-400">#{{ $idx + 1 }}</span>
                        <div><h4 class="font-semibold text-gray-900">{{ $stream['name'] }}</h4><p class="text-xs text-gray-500">{{ $stream['description'] }}</p></div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-gray-900">{{ $stream['score'] }}%</span>
                        <span class="block text-xs font-medium px-2 py-0.5 rounded-full {{ $rc['badge'] }} mt-1">{{ $stream['recommendation_label'] }}</span>
                    </div>
                </div>
                <div class="w-full bg-white/50 rounded-full h-2 mb-2"><div class="h-2 rounded-full {{ $rc['bar'] }}" style="width:{{ $stream['score'] }}%"></div></div>
                <p class="text-xs text-gray-500">Careers: {{ implode(', ', $stream['careers']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- School Recommendations --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">School Recommendations</h2>
        @if($recommendations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200"><tr><th class="text-left py-2 text-sm font-semibold text-gray-700">School</th><th class="text-left py-2 text-sm font-semibold text-gray-700">Score</th><th class="text-left py-2 text-sm font-semibold text-gray-700">Method</th><th class="text-left py-2 text-sm font-semibold text-gray-700">Date</th></tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recommendations as $rec)
                    <tr>
                        <td class="py-2 text-sm text-gray-900">{{ $rec->school->name ?? 'Unknown' }}</td>
                        <td class="py-2 text-sm text-gray-900">{{ round($rec->compatibility_score, 1) }}%</td>
                        <td class="py-2 text-sm text-gray-600">-</td>
                        <td class="py-2 text-sm text-gray-600">{{ $rec->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-sm">No school recommendations yet.</p>
        @endif
    </div>

</div>
@endsection
