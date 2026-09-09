@extends('layouts.admin')
@section('title', 'University Report - ' . $user->name)
@section('page-title', 'University Report')
@section('page-description', 'Career readiness &amp; potential insights for ' . $user->name)

@section('page-actions')
    <a href="{{ route('admin.users.report.university', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        Download PDF
    </a>
    <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">&larr; Back to Profile</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Student Overview --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-2xl font-bold text-indigo-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }} &middot; {{ $user->city->name ?? 'N/A' }}, {{ $user->state->name ?? 'N/A' }}</p>
                @if($holland_code)
                <p class="text-sm text-gray-600 mt-1">Holland Code: <span class="font-bold text-primary-700 tracking-widest">{{ $holland_code }}</span></p>
                @endif
            </div>
            <div class="text-center">
                <div class="relative w-20 h-20 mx-auto">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#6366f1" stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $employability_index / 100) }}" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center"><span class="text-lg font-bold text-gray-900">{{ $employability_index }}%</span></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Employability Index</p>
            </div>
        </div>
    </div>

    {{-- Executive Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-500">
            <p class="text-xs font-semibold text-gray-600 uppercase">Personality</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $scores['personality'] }}%</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-500">
            <p class="text-xs font-semibold text-gray-600 uppercase">Career Aptitude</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $scores['career'] }}%</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-xs font-semibold text-gray-600 uppercase">Cognitive</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $scores['cognitive'] }}%</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 border-l-4 border-indigo-500">
            <p class="text-xs font-semibold text-gray-600 uppercase">Employability</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $employability_index }}%</p>
        </div>
        <div class="rounded-xl p-4 border-l-4 {{ $career_ready ? 'bg-green-50 border-green-500' : 'bg-amber-50 border-amber-500' }}">
            <p class="text-xs font-semibold text-gray-600 uppercase">Career Ready</p>
            <p class="text-2xl font-bold {{ $career_ready ? 'text-green-600' : 'text-amber-600' }} mt-1">{{ $career_ready ? 'Yes' : 'Not Yet' }}</p>
        </div>
    </div>

    {{-- Industry Sector Readiness --}}
    @if(count($industry_readiness) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Industry Sector Readiness</h3>
        <div class="space-y-3">
            @foreach($industry_readiness as $sector)
            <div class="flex items-center gap-4 bg-gray-50 rounded-lg p-4 border border-gray-100">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $sector['sector'] }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900">{{ $sector['score'] }}%</span>
                            @if($sector['ready'])
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-800">Ready</span>
                            @else
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Developing</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $sector['ready'] ? 'bg-green-500' : 'bg-amber-500' }}" style="width:{{ $sector['score'] }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- RIASEC Career Interests --}}
    @if(count($riasec_domains) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Career Interests (RIASEC)</h3>
        @if($holland_code)
        <div class="mb-4 bg-primary-50 rounded-lg px-4 py-3 border border-primary-100">
            <span class="text-sm text-gray-600">Holland Code:</span>
            <span class="font-bold text-primary-700 text-xl tracking-widest ml-2">{{ $holland_code }}</span>
        </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($riasec_domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d['colors']['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d['name'] }}</h4><span class="text-sm font-bold" style="color:{{ $d['colors']['hex_text'] }}">{{ $d['percentage'] }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2"><div class="h-2 rounded-full" style="width:{{ $d['percentage'] }}%;background-color:{{ $d['colors']['hex_progress'] }}"></div></div>
                @if(!empty($d['description']))<p class="text-xs text-gray-600">{{ $d['description'] }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Career Paths --}}
    @if(count($career_paths) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommended Career Paths</h3>
        <div class="space-y-3">
            @foreach($career_paths as $idx => $path)
            @php $pathColors = ['blue'=>'bg-blue-50 border-blue-200','green'=>'bg-green-50 border-green-200','amber'=>'bg-amber-50 border-amber-200','purple'=>'bg-purple-50 border-purple-200','orange'=>'bg-orange-50 border-orange-200'][$path['color']] ?? 'bg-gray-50 border-gray-200'; @endphp
            <div class="{{ $pathColors }} rounded-xl border p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3"><span class="text-lg font-bold text-gray-400">#{{ $idx + 1 }}</span><h4 class="font-semibold text-gray-900">{{ $path['stream'] }}</h4></div>
                    <span class="text-xl font-bold text-gray-900">{{ $path['score'] }}%</span>
                </div>
                <div class="w-full bg-white/50 rounded-full h-2 mb-2"><div class="h-2 rounded-full bg-indigo-500" style="width:{{ $path['score'] }}%"></div></div>
                <p class="text-xs text-gray-500">Careers: {{ implode(', ', $path['careers']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- OCEAN Personality --}}
    @if(count($ocean_domains) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Personality Profile (OCEAN)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ocean_domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d['colors']['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d['name'] }}</h4><span class="text-sm font-bold" style="color:{{ $d['colors']['hex_text'] }}">{{ $d['percentage'] }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2"><div class="h-2 rounded-full" style="width:{{ $d['percentage'] }}%;background-color:{{ $d['colors']['hex_progress'] }}"></div></div>
                @if(!empty($d['description']))<p class="text-xs text-gray-600">{{ $d['description'] }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Cognitive Abilities --}}
    @if(count($cognitive_domains) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cognitive Abilities</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cognitive_domains as $d)
            <div class="bg-gray-50 rounded-xl border p-4" style="border-color:{{ $d['colors']['hex_border'] }}">
                <div class="flex items-center justify-between mb-2"><h4 class="font-semibold text-gray-900 text-sm">{{ $d['name'] }}</h4><span class="text-sm font-bold" style="color:{{ $d['colors']['hex_text'] }}">{{ $d['percentage'] }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full" style="width:{{ $d['percentage'] }}%;background-color:{{ $d['colors']['hex_progress'] }}"></div></div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Predictive Insights --}}
    @if(count($predictive_insights) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Predictive Insights</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($predictive_insights as $pi)
            <div class="bg-gray-50 rounded-lg border p-4" style="border-color:{{ $pi['colors']['hex_border'] }}">
                <p class="font-medium text-gray-900 text-xs">{{ $pi['name'] }}</p>
                <p class="text-2xl font-bold mt-1" style="color:{{ $pi['colors']['hex_text'] }}">{{ $pi['percentage'] }}%</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Strengths & Areas for Growth --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if(count($strengths) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-green-700 mb-4">Key Strengths</h3>
            <div class="space-y-3">
                @foreach($strengths as $s)
                <div class="flex items-center justify-between bg-green-50 rounded-lg p-3 border border-green-100">
                    <div><p class="text-sm font-medium text-gray-900">{{ $s['name'] }}</p><p class="text-xs text-gray-500">{{ $s['category'] }}</p></div>
                    <span class="text-lg font-bold text-green-700">{{ $s['percentage'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if(count($weaknesses) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-amber-700 mb-4">Areas for Growth</h3>
            <div class="space-y-3">
                @foreach($weaknesses as $w)
                <div class="flex items-center justify-between bg-amber-50 rounded-lg p-3 border border-amber-100">
                    <div><p class="text-sm font-medium text-gray-900">{{ $w['name'] }}</p><p class="text-xs text-gray-500">{{ $w['category'] }}</p></div>
                    <span class="text-lg font-bold text-amber-700">{{ $w['percentage'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
