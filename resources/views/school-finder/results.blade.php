@extends('layouts.app')
@section('title', 'School Recommendations – ' . __('app.brand'))

@section('header')
<div class="flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h1 class="text-2xl font-bold text-gray-900">AI-Powered School Recommendations</h1>
            <span class="inline-flex items-center gap-1 bg-primary-100 text-primary-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                Personalized
            </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">Schools in {{ $city->name }}, {{ $city->state->name }} ranked by AI based on your assessment profile</p>
    </div>
    <a href="{{ route('school-finder.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
        ← New Search
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Profile Summary --}}
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-5 mb-8">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Your Profile Summary</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-primary-50 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-primary-700">{{ $profileSummary['holland_code'] }}</p>
                <p class="text-xs text-primary-600">Holland Code</p>
            </div>
            @foreach($profileSummary['top_interests'] as $interest)
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-sm font-semibold text-gray-900">{{ $interest }}</p>
                <p class="text-xs text-gray-500">Interest</p>
            </div>
            @endforeach
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-sm font-semibold text-gray-900">{{ $profileSummary['top_cognitive'] }}</p>
                <p class="text-xs text-gray-500">Cognitive</p>
            </div>
        </div>

        {{-- Recommended Types --}}
        <div class="mt-3 flex items-center gap-2 flex-wrap">
            <span class="text-xs text-gray-500">Recommended types:</span>
            @foreach($recommendedTypes as $type)
            <span class="text-xs font-medium bg-accent-100 text-accent-700 px-2 py-0.5 rounded-full">{{ ucfirst($type) }}</span>
            @endforeach
        </div>
    </div>

    {{-- Results --}}
    @if(count($recommendations) === 0)
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Schools Found</h3>
        <p class="text-sm text-gray-500 mb-4">We don't have schools listed in {{ $city->name }} yet. Try a different location or check back later.</p>
        <a href="{{ route('school-finder.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
            Search Another City
        </a>
    </div>
    @else

    <div class="space-y-4">
        @foreach($recommendations as $index => $item)
        @php $school = $item['school']; @endphp
        <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden hover:shadow-card-hover transition-shadow duration-300 {{ $index === 0 ? 'ring-2 ring-primary-200' : '' }}">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        {{-- Rank Badge --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-sm font-bold
                            {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : ($index === 1 ? 'bg-gray-100 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-500')) }}">
                            #{{ $index + 1 }}
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $school->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-medium bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full">{{ ucfirst($school->type) }}</span>
                                @if($school->board)
                                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ strtoupper($school->board) }}</span>
                                @endif
                                @if($school->rating)
                                <span class="flex items-center gap-0.5 text-xs text-yellow-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $school->rating }}
                                </span>
                                @endif
                            </div>
                            @if($school->address)
                            <p class="text-xs text-gray-500 mt-1.5">{{ $school->address }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Compatibility Score --}}
                    <div class="text-center flex-shrink-0">
                        <div class="w-16 h-16 relative">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                <path d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                                <path d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" fill="none"
                                      stroke="{{ $item['compatibility_score'] >= 70 ? '#22c55e' : ($item['compatibility_score'] >= 40 ? '#eab308' : '#ef4444') }}"
                                      stroke-width="3" stroke-dasharray="{{ $item['compatibility_score'] }}, 100"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-xs font-bold {{ $item['compatibility_score'] >= 70 ? 'text-green-700' : ($item['compatibility_score'] >= 40 ? 'text-yellow-700' : 'text-red-700') }}">{{ $item['compatibility_score'] }}%</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Match</p>
                    </div>
                </div>

                {{-- AI Recommendation --}}
                <div class="mt-4 p-3 bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl border border-primary-100">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/></svg>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-primary-700">AI Recommendation</p>
                            <p class="text-xs text-gray-700 mt-0.5">{{ $item['ai_reason'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Match Reasons --}}
                @if(!empty($item['match_reasons']))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($item['match_reasons'] as $reason)
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-md">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ $reason }}
                    </span>
                    @endforeach
                </div>
                @endif

                {{-- Facilities --}}
                @if($school->facilities && count($school->facilities) > 0)
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach(array_slice($school->facilities, 0, 6) as $facility)
                    <span class="text-xs text-gray-500 bg-gray-50 px-2 py-0.5 rounded">{{ $facility }}</span>
                    @endforeach
                    @if(count($school->facilities) > 6)
                    <span class="text-xs text-gray-400">+{{ count($school->facilities) - 6 }} more</span>
                    @endif
                </div>
                @endif

                {{-- Contact --}}
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-4">
                    @if($school->phone)
                    <a href="tel:{{ $school->phone }}" class="text-xs text-gray-500 hover:text-primary-600 flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        {{ $school->phone }}
                    </a>
                    @endif
                    @if($school->email)
                    <a href="mailto:{{ $school->email }}" class="text-xs text-gray-500 hover:text-primary-600 flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        {{ $school->email }}
                    </a>
                    @endif
                    @if($school->website)
                    <a href="{{ $school->website }}" target="_blank" class="text-xs text-gray-500 hover:text-primary-600 flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        Website
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Search Again --}}
    <div class="mt-8 text-center">
        <a href="{{ route('school-finder.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-sm font-medium text-gray-700 rounded-xl hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            Search Another City
        </a>
    </div>
    @endif
</div>
@endsection
