@extends('layouts.app')
@section('title', 'AI School Finder (Gemini) – ' . config('app.name'))

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">AI School Finder</h1>
        <p class="text-sm text-gray-500 mt-1">Powered by Google Gemini — live, accurate, location-aware results</p>
    </div>
    <a href="{{ route('school-finder.index') }}"
       class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Assessment-based Finder
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Info banner --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-5 flex gap-4">
        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-blue-900">Gemini AI — Real-time School Intelligence</h3>
            <p class="text-sm text-blue-700 mt-0.5 leading-relaxed">
                This finder uses Google Gemini to fetch <strong>live, verified school data</strong> — no static datasets.
                Select your location, stream, and preferences to get ranked schools with fees, highlights, and AI reasoning.
            </p>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
            <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-800">Search Schools</h2>
        </div>

        <form method="POST" action="{{ route('school-finder.gemini.search') }}" id="geminiForm" class="p-6 space-y-6">
            @csrf

            {{-- Errors --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
            @endif

            {{-- ── Location ── --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Location</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" value="{{ old('country', 'India') }}"
                               placeholder="e.g. India"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">State / Province <span class="text-red-500">*</span></label>
                        <input type="text" name="state" value="{{ old('state') }}"
                               placeholder="e.g. Rajasthan"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="e.g. Jaipur"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                               required>
                    </div>
                </div>
            </div>

            {{-- ── Stream & Board ── --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Academic Preferences</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stream <span class="text-red-500">*</span></label>
                        <select name="stream" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            @foreach([
                                'PCM'        => 'PCM — Physics, Chemistry, Maths',
                                'PCB'        => 'PCB — Physics, Chemistry, Biology',
                                'PCMB'       => 'PCMB — Science with Maths & Biology',
                                'Commerce'   => 'Commerce',
                                'Arts'       => 'Arts / Humanities',
                                'Vocational' => 'Vocational / Skill-based',
                                'Any'        => 'Any Stream',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('stream') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Board Affiliation <span class="text-red-500">*</span></label>
                        <select name="board" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            @foreach([
                                'CBSE'      => 'CBSE — Central Board',
                                'RBSE'      => 'RBSE — Rajasthan State Board',
                                'ICSE'      => 'ICSE',
                                'IB'        => 'IB — International Baccalaureate',
                                'Cambridge' => 'Cambridge IGCSE/A-Levels',
                                'State'     => 'State Board',
                                'Any'       => 'Any Board',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('board') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── Fees & Academic Level ── --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Budget & Academic Level</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Min Fees (₹/yr)</label>
                        <input type="number" name="fees_min" value="{{ old('fees_min') }}"
                               placeholder="e.g. 20000" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Max Fees (₹/yr)</label>
                        <input type="number" name="fees_max" value="{{ old('fees_max') }}"
                               placeholder="e.g. 100000" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Academic Level</label>
                        <select name="academic_level"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Not specified</option>
                            <option value="below_average" {{ old('academic_level') === 'below_average' ? 'selected' : '' }}>Below Average (below 55%)</option>
                            <option value="average"       {{ old('academic_level') === 'average'       ? 'selected' : '' }}>Average (55–70%)</option>
                            <option value="above_average" {{ old('academic_level') === 'above_average' ? 'selected' : '' }}>Above Average (70–85%)</option>
                            <option value="excellent"     {{ old('academic_level') === 'excellent'     ? 'selected' : '' }}>Excellent (85%+)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── Number of Results ── --}}
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Results to show:</label>
                    <div class="flex gap-2">
                        @foreach([5, 10, 15, 20] as $n)
                        <label class="cursor-pointer">
                            <input type="radio" name="limit" value="{{ $n }}" {{ (old('limit', 10) == $n) ? 'checked' : '' }} class="sr-only peer">
                            <span class="px-3 py-1.5 rounded-lg border text-sm font-medium transition
                                peer-checked:bg-primary-600 peer-checked:text-white peer-checked:border-primary-600
                                border-gray-200 text-gray-600 hover:border-primary-400">{{ $n }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" id="searchBtn"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm rounded-xl transition-all duration-200 flex items-center gap-2 disabled:opacity-60">
                    <svg id="searchIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                    <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span id="searchBtnText">Find Schools with Gemini AI</span>
                </button>
            </div>
        </form>
    </div>

    {{-- How it works --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        @foreach([
            ['🗺️', 'Location-Aware', 'Searches real schools in your exact city and state'],
            ['🎓', 'Stream & Board', 'Filters schools by PCM/PCB, CBSE, RBSE and more'],
            ['💰', 'Fees Matching', 'Ranks schools within your specified budget range'],
            ['🤖', 'AI Ranking', 'Gemini reasons over 5 factors to rank each school'],
        ] as [$icon, $title, $desc])
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
            <div class="text-2xl mb-2">{{ $icon }}</div>
            <div class="text-sm font-semibold text-gray-800 mb-1">{{ $title }}</div>
            <div class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</div>
        </div>
        @endforeach
    </div>

</div>

<script>
document.getElementById('geminiForm').addEventListener('submit', function () {
    const btn  = document.getElementById('searchBtn');
    const icon = document.getElementById('searchIcon');
    const spin = document.getElementById('loadingIcon');
    const txt  = document.getElementById('searchBtnText');
    btn.disabled = true;
    icon.classList.add('hidden');
    spin.classList.remove('hidden');
    txt.textContent = 'Searching with Gemini AI…';
});
</script>
@endsection
