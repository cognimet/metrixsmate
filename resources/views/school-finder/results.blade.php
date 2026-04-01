@extends('layouts.app')
@section('title', 'School Recommendations — ' . ($meta['city'] ?? '') . ' · MetrixsMate AI Finder')

@section('header')
<div class="flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
            <a href="{{ route('school-finder.ai.index') }}" class="hover:text-primary-600 transition">School Finder</a>
            <span>/</span>
            <span class="text-gray-700 font-medium">Results</span>
            @if(($meta['search_type'] ?? 'ai') === 'assessment')
            <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full ml-2">Assessment-Based</span>
            @else
            <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full ml-2">AI-Powered</span>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            Schools in {{ $meta['city'] ?? 'Your City' }}
            @if(!empty($fromCache) && $fromCache)
            <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Cached</span>
            @elseif(($meta['search_type'] ?? 'ai') === 'ai')
            <span class="text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Live</span>
            @endif
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $meta['total'] }} school{{ $meta['total'] != 1 ? 's' : '' }} found
            @if(!empty($meta['stream'])) · {{ $meta['stream'] }} @endif
            @if(!empty($meta['board'])) · {{ $meta['board'] }} @endif
            @if(!empty($meta['fees_range'])) · {{ $meta['fees_range'] }} @endif
            @if(($meta['search_type'] ?? 'ai') === 'assessment')
            · Based on your assessment profile
            @endif
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('school-finder.ai.index') }}"
           class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            New Search
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-5"

    {{-- Filters Summary Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 space-y-3">
        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-xs text-gray-500 font-medium mr-1">Active filters:</span>

            @if(!empty($filters['city']))
            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-medium px-3 py-1 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                {{ $filters['city'] }}, {{ $filters['state'] }}
            </span>
            @endif

            @if(!empty($filters['stream']) && $filters['stream'] !== 'Any')
            <span class="inline-flex items-center bg-purple-50 text-purple-700 text-xs font-medium px-3 py-1 rounded-full">
                Stream: {{ $filters['stream'] }}
            </span>
            @endif

            @if(!empty($filters['board']) && $filters['board'] !== 'Any')
            <span class="inline-flex items-center bg-indigo-50 text-indigo-700 text-xs font-medium px-3 py-1 rounded-full">
                {{ $filters['board'] }}
            </span>
            @endif

            @if(!empty($filters['fees_min']) || !empty($filters['fees_max']))
            <span class="inline-flex items-center bg-green-50 text-green-700 text-xs font-medium px-3 py-1 rounded-full">
                ₹{{ number_format($filters['fees_min'] ?? 0) }} – ₹{{ number_format($filters['fees_max'] ?? 999999) }}/yr
            </span>
            @endif

            @if(!empty($filters['academic_level']))
            <span class="inline-flex items-center bg-amber-50 text-amber-700 text-xs font-medium px-3 py-1 rounded-full">
                Level: {{ ucfirst(str_replace('_', ' ', $filters['academic_level'])) }}
            </span>
            @endif

            <span class="ml-auto text-xs text-gray-400">
                @if(!empty($meta['generated_at']))
                Generated {{ \Carbon\Carbon::parse($meta['generated_at'])->diffForHumans() }}
                @endif
            </span>
        </div>

        {{-- AI Search Summary --}}
        @if(!empty($meta['search_summary']))
        <div class="flex items-start gap-2 border-t border-gray-50 pt-3">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
            <p class="text-sm text-gray-700 leading-relaxed">
                <span class="font-medium text-blue-700">Gemini says:</span> {{ $meta['search_summary'] }}
                @if(!empty($meta['location_insights']))
                {{ $meta['location_insights'] }}
                @endif
            </p>
        </div>
        @endif
    </div>

    {{-- No results --}}
    @if(empty($schools))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Schools Found</h3>
        <p class="text-gray-500 text-sm max-w-sm mx-auto mb-6">Gemini couldn't find verified schools for these exact filters. Try broadening your location or changing the stream/board.</p>
        <a href="{{ route('school-finder.gemini') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition">
            Try a Different Search
        </a>
    </div>
    @else

    {{-- School Cards --}}
    <div class="space-y-4">
        @foreach($schools as $i => $school)
        @php
            $rank     = $school['rank']          ?? ($i + 1);
            $score    = $school['ranking_score']  ?? 0;
            $badge    = $school['fees_badge']     ?? 'within-budget';
            $conf     = $school['data_confidence'] ?? 'medium';
            $feesFmt  = isset($school['fees_annual_inr']) && $school['fees_annual_inr'] > 0
                            ? '₹' . number_format($school['fees_annual_inr'])
                            : 'Not disclosed';
            $scoreColor = $score >= 80 ? 'bg-green-100 text-green-800 ring-green-200'
                        : ($score >= 60 ? 'bg-blue-100 text-blue-800 ring-blue-200'
                        : 'bg-gray-100 text-gray-700 ring-gray-200');
            $badgeStyle = match($badge) {
                'budget-friendly' => 'bg-green-50 text-green-700 border-green-200',
                'over-budget'     => 'bg-red-50 text-red-600 border-red-200',
                default           => 'bg-blue-50 text-blue-700 border-blue-200',
            };
            $badgeLabel = match($badge) {
                'budget-friendly' => 'Budget-Friendly',
                'over-budget'     => 'Over Budget',
                default           => 'Within Budget',
            };
            $confIcon = match($conf) {
                'high'   => ['text-green-600',  '●●●', 'High confidence'],
                'medium' => ['text-amber-500',  '●●○', 'Medium confidence'],
                default  => ['text-gray-400',   '●○○', 'Low confidence'],
            };
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">

            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-gray-50">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        {{-- Rank Badge --}}
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br
                            @if($rank === 1) from-amber-400 to-amber-500
                            @elseif($rank === 2) from-slate-400 to-slate-500
                            @elseif($rank === 3) from-amber-600 to-amber-700
                            @else from-gray-200 to-gray-300 @endif
                            flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            #{{ $rank }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                {{ $school['name'] ?? 'Unknown School' }}
                            </h3>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                @if(!empty($school['type']))
                                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full font-medium">
                                    {{ $school['type'] }}
                                </span>
                                @endif
                                @if(!empty($school['board']))
                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full font-medium">
                                    {{ $school['board'] }}
                                </span>
                                @endif
                                @if(!empty($school['location']))
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    {{ $school['location'] }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- AI Score + Fees --}}
                    <div class="flex-shrink-0 flex flex-col items-end gap-2">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl ring-2 {{ $scoreColor }} font-bold tabular-nums">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                            {{ $score }}/100
                        </div>
                        <span class="text-sm font-bold text-gray-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $feesFmt }}/yr
                        </span>
                        <span class="text-xs border px-2 py-0.5 rounded-full font-medium {{ $badgeStyle }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Highlights --}}
                @if(!empty($school['highlights']))
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2.5">Highlights</h4>
                    <ul class="space-y-1.5">
                        @foreach(array_slice((array)$school['highlights'], 0, 5) as $h)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-3.5 h-3.5 text-primary-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ $h }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Streams + Facilities --}}
                <div class="space-y-4">
                    @if(!empty($school['streams_offered']))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Streams Offered</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach((array)$school['streams_offered'] as $s)
                            <span class="text-xs bg-purple-50 text-purple-700 border border-purple-100 px-2.5 py-0.5 rounded-full">{{ $s }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($school['facilities']))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Facilities</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice((array)$school['facilities'], 0, 8) as $f)
                            <span class="text-xs bg-teal-50 text-teal-700 border border-teal-100 px-2.5 py-0.5 rounded-full">{{ $f }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- AI Reasoning --}}
            @if(!empty($school['ranking_reason']))
            <div class="px-6 pb-5">
                <div class="bg-blue-50/60 border border-blue-100 rounded-xl px-4 py-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-blue-700 mb-0.5">
                                @if(($meta['search_type'] ?? 'ai') === 'assessment')
                                Assessment-Based Reasoning
                                @else
                                MetrixsMate AI Reasoning
                                @endif
                            </p>
                            <p class="text-sm text-blue-800 leading-relaxed italic">{{ $school['ranking_reason'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer: confidence --}}
            <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 flex items-center gap-1.5">
                    <span class="{{ $confIcon[0] }} font-mono">{{ $confIcon[1] }}</span>
                    {{ $confIcon[2] }}
                </span>
                <span class="text-xs text-gray-400">Rank #{{ $rank }} of {{ $meta['total'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Grounding Sources (AI-Powered Only) --}}
    @if(($meta['search_type'] ?? 'ai') === 'ai' && !empty($meta['grounding_sources']))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sources used by MetrixsMate AI</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($meta['grounding_sources'] as $src)
            <a href="{{ $src['uri'] }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 text-xs bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 hover:text-primary-700 px-3 py-1.5 rounded-full transition truncate max-w-xs">
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/></svg>
                {{ $src['title'] }}
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Bottom CTA --}}
    <div class="bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-100 rounded-2xl p-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h3 class="font-semibold text-gray-900">Start a New Search</h3>
            <p class="text-sm text-gray-600 mt-0.5">Refine your criteria or search for schools in a different location through MetrixsMate AI School Finder.</p>
        </div>
        <a href="{{ route('school-finder.ai.index') }}"
           class="flex-shrink-0 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm rounded-xl transition">
            New Search
        </a>
    </div>

    @endif {{-- end $schools check --}}

</div>
@endsection
