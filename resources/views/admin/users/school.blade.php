@extends('layouts.admin')
@section('title', 'School Report - ' . $user->name)
@section('page-title', 'School Report')
@section('page-description', 'Student talent &amp; development insights for ' . $user->name)

@section('page-actions')
    <a href="{{ route('admin.users.report.school', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
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
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center">
                <span class="text-2xl font-bold text-emerald-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $overall_colors['hex_progress'] ?? '#10b981' }}" stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $overall_score / 100) }}" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center"><span class="text-lg font-bold text-gray-900">{{ $overall_score }}%</span></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Overall Score</p>
            </div>
        </div>
    </div>

    {{-- Score Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-gray-400">
            <p class="text-xs font-semibold text-gray-600 uppercase">Overall</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $overall_score }}%</p>
        </div>
    </div>

    {{-- OCEAN Personality Profile --}}
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

    {{-- Facets & CCS Skills --}}
    @if(count($facets) > 0 || count($ccs_skills) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if(count($facets) > 0)
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Personality Facets</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            @foreach($facets as $f)
            <div class="bg-gray-50 rounded-lg border p-3" style="border-color:{{ $f['colors']['hex_border'] }}">
                <p class="font-medium text-gray-900 text-xs">{{ $f['name'] }}</p>
                <p class="text-xl font-bold mt-1" style="color:{{ $f['colors']['hex_text'] }}">{{ $f['percentage'] }}%</p>
            </div>
            @endforeach
        </div>
        @endif
        @if(count($ccs_skills) > 0)
        <h3 class="text-lg font-semibold text-gray-900 mb-4">CCS Skills</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($ccs_skills as $s)
            <div class="bg-gray-50 rounded-lg border p-3" style="border-color:{{ $s['colors']['hex_border'] }}">
                <p class="font-medium text-gray-900 text-xs">{{ $s['name'] }}</p>
                <p class="text-xl font-bold mt-1" style="color:{{ $s['colors']['hex_text'] }}">{{ $s['percentage'] }}%</p>
            </div>
            @endforeach
        </div>
        @endif
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

    {{-- Learning Styles --}}
    @if(count($learning_styles) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Learning Styles</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($learning_styles as $idx => $style)
            @php $sc = [['bg'=>'bg-indigo-50','text'=>'text-indigo-700','border'=>'border-indigo-200','bar'=>'bg-indigo-500'],['bg'=>'bg-emerald-50','text'=>'text-emerald-700','border'=>'border-emerald-200','bar'=>'bg-emerald-500'],['bg'=>'bg-amber-50','text'=>'text-amber-700','border'=>'border-amber-200','bar'=>'bg-amber-500'],['bg'=>'bg-purple-50','text'=>'text-purple-700','border'=>'border-purple-200','bar'=>'bg-purple-500']][$idx] ?? ['bg'=>'bg-gray-50','text'=>'text-gray-700','border'=>'border-gray-200','bar'=>'bg-gray-500']; @endphp
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

    {{-- Academic Stream Recommendations --}}
    @if(count($stream_recommendations) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Stream Recommendations</h3>
        <div class="space-y-3">
            @foreach($stream_recommendations as $idx => $stream)
            @php $rc = ['strongly_recommended'=>['bg'=>'bg-green-50','border'=>'border-green-200','badge'=>'bg-green-100 text-green-800','bar'=>'bg-green-500'],'recommended'=>['bg'=>'bg-blue-50','border'=>'border-blue-200','badge'=>'bg-blue-100 text-blue-800','bar'=>'bg-blue-500'],'suitable'=>['bg'=>'bg-amber-50','border'=>'border-amber-200','badge'=>'bg-amber-100 text-amber-800','bar'=>'bg-amber-500'],'less_suitable'=>['bg'=>'bg-gray-50','border'=>'border-gray-200','badge'=>'bg-gray-100 text-gray-600','bar'=>'bg-gray-400']][$stream['recommendation']] ?? ['bg'=>'bg-gray-50','border'=>'border-gray-200','badge'=>'bg-gray-100 text-gray-600','bar'=>'bg-gray-400']; @endphp
            <div class="{{ $rc['bg'] }} rounded-xl border {{ $rc['border'] }} p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3"><span class="text-lg font-bold text-gray-400">#{{ $idx + 1 }}</span><div><h4 class="font-semibold text-gray-900">{{ $stream['name'] }}</h4><p class="text-xs text-gray-500">{{ $stream['description'] }}</p></div></div>
                    <div class="text-right"><span class="text-xl font-bold text-gray-900">{{ $stream['score'] }}%</span><span class="block text-xs font-medium px-2 py-0.5 rounded-full {{ $rc['badge'] }} mt-1">{{ $stream['recommendation_label'] }}</span></div>
                </div>
                <div class="w-full bg-white/50 rounded-full h-2 mb-2"><div class="h-2 rounded-full {{ $rc['bar'] }}" style="width:{{ $stream['score'] }}%"></div></div>
                <p class="text-xs text-gray-500">Careers: {{ implode(', ', $stream['careers']) }}</p>
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

    {{-- Teacher Action Plan --}}
    @if(count($teacher_actions) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Action Plan</h3>
        <div class="space-y-3">
            @foreach($teacher_actions as $action)
            @php $pColors = ['high'=>'bg-red-100 text-red-800','medium'=>'bg-amber-100 text-amber-800','low'=>'bg-green-100 text-green-800'][$action['priority']] ?? 'bg-gray-100 text-gray-800'; @endphp
            <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-4 border border-gray-100">
                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $pColors }} shrink-0 mt-0.5">{{ ucfirst($action['priority']) }}</span>
                <p class="text-sm text-gray-700">{{ $action['action'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Parent Guidance --}}
    @if(count($parent_guidance) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Parent Guidance</h3>
        <div class="space-y-3">
            @foreach($parent_guidance as $tip)
            <div class="flex items-start gap-3 bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-700">{!! str_replace(['**'], ['<strong>'], str_replace(['**'], ['</strong>'], $tip)) !!}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
