@extends('layouts.app')
@section('title', 'User Details - ' . $user->name)

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <div class="flex items-center gap-3">
                @if($latestOceanResult)
                <a href="{{ route('admin.assessments.download', $latestOceanResult) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF Report
                </a>
                @endif
                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800">← Back to Users</a>
            </div>
        </div>

        {{-- User Info Card --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">Personal Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Email</label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Mobile</label>
                            <p class="text-gray-900">{{ $user->mobile_no ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Location</label>
                            <p class="text-gray-900">{{ $user->city->name ?? 'N/A' }}, {{ $user->state->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Member Since</label>
                            <p class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">Account Status</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Role</label>
                            <p class="text-gray-900">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Status</label>
                            <p class="text-gray-900">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->isActive() ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">AI Searches</label>
                            <p class="text-gray-900">{{ $user->free_ai_searches ?? 0 }} free</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Assessment Searches</label>
                            <p class="text-gray-900">{{ $user->free_assessment_searches ?? 0 }} free</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6">
                <label class="text-xs text-gray-500 uppercase block mb-2">Admin Notes</label>
                <p class="text-gray-900">{{ $user->admin_notes ?? 'No notes' }}</p>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Edit User</a>
                <button onclick="if(confirm('Delete this user?')) { document.getElementById('delete-form').submit(); }" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete User</button>
            </div>
        </div>

        <form id="delete-form" action="{{ route('admin.users.delete', $user) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        {{-- Assessment Summary Stats --}}
        @php
            $hasOcean    = $oceanResults['domains']->isNotEmpty();
            $hasRiasec   = $riasecResults['domains']->isNotEmpty();
            $hasCognitive = $cognitiveResults['domains']->isNotEmpty();
            $totalCompleted = ($hasOcean ? 1 : 0) + ($hasRiasec ? 1 : 0) + ($hasCognitive ? 1 : 0);
        @endphp

        @if($totalCompleted > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-xs font-semibold uppercase">Completed Assessments</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalCompleted }} / 3</p>
            </div>
            @if($hasOcean)
            <div class="bg-blue-50 rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-gray-600 text-xs font-semibold uppercase">OCEAN</p>
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ round($oceanResults['domains']->avg('percentage'), 1) }}%</p>
                @if($latestOceanResult)
                <p class="text-xs text-gray-600 mt-1">Latest: {{ $latestOceanResult->created_at->format('M d') }}</p>
                @endif
            </div>
            @endif
            @if($hasRiasec)
            <div class="bg-green-50 rounded-lg shadow p-4 border-l-4 border-green-500">
                <p class="text-gray-600 text-xs font-semibold uppercase">RIASEC</p>
                <p class="text-2xl font-bold text-green-600 mt-2">{{ round($riasecResults['domains']->avg('percentage'), 1) }}%</p>
                @if($latestRiasecResult)
                <p class="text-xs text-gray-600 mt-1">Latest: {{ $latestRiasecResult->created_at->format('M d') }}</p>
                @endif
            </div>
            @endif
            @if($hasCognitive)
            <div class="bg-purple-50 rounded-lg shadow p-4 border-l-4 border-purple-500">
                <p class="text-gray-600 text-xs font-semibold uppercase">Cognitive</p>
                <p class="text-2xl font-bold text-purple-600 mt-2">{{ round($cognitiveResults['average_score'], 1) }}%</p>
                @if($latestCognitiveResult)
                <p class="text-xs text-gray-600 mt-1">Latest: {{ $latestCognitiveResult->created_at->format('M d') }}</p>
                @endif
            </div>
            @endif
        </div>
        @endif

        {{-- Assessment Results --}}
        <div class="space-y-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Assessment Results</h2>

            @if($totalCompleted === 0)
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <p class="text-gray-500">No assessment results yet.</p>
            </div>
            @endif

            {{-- OCEAN --}}
            @if($hasOcean)
            @php $domains = $oceanResults['domains']; $oid = 'ocean'; @endphp
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">OCEAN — Personality</h3>
                    @if($latestOceanResult)<span class="text-sm text-gray-500">{{ $latestOceanResult->created_at->format('M d, Y') }}</span>@endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Performance</h4>
                        <div class="relative" style="height:300px"><canvas id="chart-{{ $oid }}"></canvas></div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            @php $avgPct = round($domains->avg('percentage'), 1); @endphp
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $oid }})" stroke-width="10"
                                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}"
                                            stroke-linecap="round"/>
                                    <defs><linearGradient id="grad-{{ $oid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#14b8a6"/></linearGradient></defs>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Domain Average</p>
                        </div>
                        <hr class="border-gray-100">
                        <div class="space-y-2.5">
                            @foreach($domains as $d)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-0.5">
                                    <span class="font-medium text-gray-600">{{ $d->name }}</span>
                                    <span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($domains as $d)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 transition" style="border-color:{{ $d->colors['hex_border'] }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 text-xs">{{ $d->name }}</h4>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                            </div>
                            <span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                @php $facets = $oceanResults['facets']; $ccsSkills = $oceanResults['ccs_skills']; $predictiveInsights = $oceanResults['predictive_insights']; @endphp

                @if($facets->count() > 0)
                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Facets</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($facets as $facet)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $facet->colors['hex_border'] }}">
                            <p class="font-medium text-gray-900 text-xs">{{ $facet->name }}</p>
                            <p class="text-xl font-bold mt-1" style="color:{{ $facet->colors['hex_text'] }}">{{ round($facet->percentage, 2) }}%</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($ccsSkills->count() > 0)
                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">CCS Skills</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($ccsSkills as $skill)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $skill->colors['hex_border'] }}">
                            <p class="font-medium text-gray-900 text-xs">{{ $skill->name }}</p>
                            <p class="text-xl font-bold mt-1" style="color:{{ $skill->colors['hex_text'] }}">{{ round($skill->percentage, 2) }}%</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($predictiveInsights->count() > 0)
                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Predictive Insights</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($predictiveInsights as $insight)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3" style="border-color:{{ $insight->colors['hex_border'] }}">
                            <p class="font-medium text-gray-900 text-xs">{{ str_replace('_', ' ', ucwords($insight->result_type, '_')) }}</p>
                            <p class="text-xl font-bold mt-1" style="color:{{ $insight->colors['hex_text'] }}">{{ round($insight->percentage, 2) }}%</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($latestOceanResult)
                <div class="mt-6">
                    <a href="{{ route('admin.assessments.show', $latestOceanResult) }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-gray-300 transition">View Details</a>
                </div>
                @endif
            </div>
            @push('scripts')
            <script>
            (function(){
                const ctx = document.getElementById('chart-{{ $oid }}').getContext('2d');
                const labels = @json($domains->pluck('name'));
                const scores = @json($domains->pluck('percentage'));
                const hexColors = @json($domains->pluck('colors')->pluck('hex_progress'));
                new Chart(ctx, { type: 'radar', data: { labels, datasets: [{ label: 'Score %', data: scores, backgroundColor: 'rgba(99,102,241,0.15)', borderColor: 'rgba(99,102,241,0.8)', borderWidth: 2, pointBackgroundColor: 'rgba(99,102,241,1)', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' }, pointLabels: { font: { size: 12, weight: '600' } } } }, plugins: { legend: { display: false } } } });
            })();
            </script>
            @endpush
            @endif

            {{-- RIASEC --}}
            @if($hasRiasec)
            @php $domains = $riasecResults['domains']; $rid = 'riasec'; @endphp
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">RIASEC — Career Interests</h3>
                    @if($latestRiasecResult)<span class="text-sm text-gray-500">{{ $latestRiasecResult->created_at->format('M d, Y') }}</span>@endif
                </div>

                @if($riasecResults['holland_code'])
                <p class="mb-4 text-sm text-gray-600">Holland Code: <span class="font-bold text-primary-600 text-lg tracking-widest">{{ $riasecResults['holland_code'] }}</span></p>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Performance</h4>
                        <div class="relative" style="height:300px"><canvas id="chart-{{ $rid }}"></canvas></div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            @php $avgPct = round($domains->avg('percentage'), 1); @endphp
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $rid }})" stroke-width="10"
                                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}"
                                            stroke-linecap="round"/>
                                    <defs><linearGradient id="grad-{{ $rid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#10b981"/><stop offset="1" stop-color="#3b82f6"/></linearGradient></defs>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Domain Average</p>
                        </div>
                        <hr class="border-gray-100">
                        <div class="space-y-2.5">
                            @foreach($domains as $d)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-0.5">
                                    <span class="font-medium text-gray-600">{{ $d->name }}</span>
                                    <span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($domains as $d)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 transition" style="border-color:{{ $d->colors['hex_border'] }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 text-xs">{{ $d->name }}</h4>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                            </div>
                            <span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($latestRiasecResult)
                <div class="mt-6">
                    <a href="{{ route('admin.assessments.show', $latestRiasecResult) }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-gray-300 transition">View Details</a>
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

            {{-- Cognitive --}}
            @if($hasCognitive)
            @php $domains = $cognitiveResults['domains']; $cid = 'cognitive'; @endphp
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Cognitive Abilities</h3>
                    @if($latestCognitiveResult)<span class="text-sm text-gray-500">{{ $latestCognitiveResult->created_at->format('M d, Y') }}</span>@endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Performance</h4>
                        <div class="relative" style="height:300px"><canvas id="chart-{{ $cid }}"></canvas></div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            @php $avgPct = round($cognitiveResults['average_score'], 1); @endphp
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="url(#grad-{{ $cid }})" stroke-width="10"
                                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $avgPct / 100) }}"
                                            stroke-linecap="round"/>
                                    <defs><linearGradient id="grad-{{ $cid }}" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#8b5cf6"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-900">{{ $avgPct }}%</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Average Score</p>
                        </div>
                        <hr class="border-gray-100">
                        <div class="space-y-2.5">
                            @foreach($domains as $d)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-0.5">
                                    <span class="font-medium text-gray-600">{{ $d->name }}</span>
                                    <span class="font-semibold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($domains as $d)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 transition" style="border-color:{{ $d->colors['hex_border'] }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 text-xs">{{ $d->name }}</h4>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $d->colors['badge'] }}">{{ $d->performance_text }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" style="width:{{ $d->percentage }}%;background-color:{{ $d->colors['hex_progress'] }}"></div>
                            </div>
                            <span class="text-sm font-bold" style="color:{{ $d->colors['hex_text'] }}">{{ $d->percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($latestCognitiveResult)
                <div class="mt-6">
                    <a href="{{ route('admin.assessments.show', $latestCognitiveResult) }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-gray-300 transition">View Details</a>
                </div>
                @endif
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
        </div>

        {{-- Recommendations --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">School Recommendations</h2>
            <div class="space-y-3">
                @if($recommendations->count() > 0)
                    <table class="w-full">
                        <thead class="border-b border-gray-200">
                            <tr>
                                <th class="text-left py-2 text-sm font-semibold text-gray-700">School</th>
                                <th class="text-left py-2 text-sm font-semibold text-gray-700">Score</th>
                                <th class="text-left py-2 text-sm font-semibold text-gray-700">Method</th>
                                <th class="text-left py-2 text-sm font-semibold text-gray-700">Date</th>
                            </tr>
                        </thead>
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
                @else
                    <p class="text-gray-500">No school recommendations yet.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
