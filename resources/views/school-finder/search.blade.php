@extends('layouts.app')
@section('title', 'MetrixsMate AI School Finder')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">School Finder</h1>
        <p class="text-sm text-gray-500 mt-1">Powered by MetrixsMate AI — discover schools perfectly suited to your child's needs</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Features Banner --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        @foreach([
            ['🏫', 'All Levels', 'Pre-Primary to Senior (Matric)'],
            ['📍', 'Live Data', 'Real schools verified from the web'],
            ['🤖', 'AI-Powered', 'MetrixsMate AI ranks perfect matches'], 
            ['💰', 'Budget Smart', 'Filter by fees and affordability'],
        ] as [$icon, $title, $desc])
        <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
            <div class="text-2xl mb-1">{{ $icon }}</div>
            <div class="text-xs font-semibold text-gray-800">{{ $title }}</div>
            <div class="text-xs text-gray-500 leading-relaxed mt-0.5">{{ $desc }}</div>
        </div>
        @endforeach
    </div>

    {{-- Search Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-blue-50 flex items-center gap-3">
            <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-800">Find Your Perfect School</h2>
        </div>

        <form method="POST" action="{{ route('school-finder.ai.search') }}" id="searchForm" class="p-6 space-y-6">
            @csrf

            {{-- Errors --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
            @endif

            {{-- Search Mode Toggle --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Search Method</p>
                        <p class="text-xs text-gray-600 mt-0.5">Choose between AI-Powered or Assessment-Based search</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="search_mode" value="ai" checked id="aiMode" class="w-4 h-4 text-primary-600 cursor-pointer">
                            <span class="text-sm font-medium text-gray-700">AI-Powered</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer {{ !$assessmentsCompleted ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="radio" name="search_mode" value="assessment" id="assessmentMode" 
                                   class="w-4 h-4 text-primary-600 cursor-pointer" 
                                   {{ !$assessmentsCompleted ? 'disabled' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Assessment-Based</span>
                        </label>
                    </div>
                </div>
                
                @if(!$assessmentsCompleted)
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-xs font-medium text-amber-800">
                        <span class="inline-block mr-1">⚠️</span>
                        Complete all assessments to use Assessment-Based search
                    </p>
                    <p class="text-xs text-amber-700 mt-1">
                        Take the RIASEC, Cognitive, and OCEAN assessments to unlock personalized school recommendations based on your profile.
                    </p>
                    <a href="{{ route('dashboard') }}" class="inline-block mt-2 text-xs font-semibold text-amber-800 hover:text-amber-900 underline">
                        Go to Assessments →
                    </a>
                </div>
                @endif
            </div>

            {{-- Token Status --}}
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $aiSearchesRemaining }}</div>
                        <p class="text-xs text-gray-600 mt-1">Free AI Searches</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-600">{{ $assessmentSearchesRemaining }}</div>
                        <p class="text-xs text-gray-600 mt-1">Free Assessment Searches</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ $paidTokensRemaining }}</div>
                        <p class="text-xs text-gray-600 mt-1">Paid Tokens</p>
                    </div>
                </div>
                
                @if($aiSearchesRemaining == 0 || $assessmentSearchesRemaining == 0)
                <div class="mt-4 pt-4 border-t border-purple-200">
                    <p class="text-xs text-gray-700 mb-3">Out of free searches?</p>
                    <button type="button" class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition" onclick="openTokenPurchaseModal()">
                        🎁 Purchase Tokens - ₹100 for 3 Searches
                    </button>
                </div>
                @endif
            </div>

            {{-- Location Section --}}
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
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">State/Province <span class="text-red-500">*</span></label>
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

            {{-- School Level & Class Range --}}
            <div id="schoolDetailsSection">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">School Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">School Level <span class="text-red-500">*</span></label>
                        <select name="school_level" id="schoolLevelSelect"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Select school level...</option>
                            @foreach([
                                'pre-primary' => 'Pre-Primary (Nursery–UKG)',
                                'primary'     => 'Primary (Classes 1–5)',
                                'secondary'   => 'Secondary (Classes 6–10)',
                                'senior'      => 'Senior (Classes 11–12)',
                                'matric'      => 'Matric/Full School (Classes 1–10)',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('school_level') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Class/Grade Range <span class="text-red-500">*</span></label>
                        <select name="class_range" id="classRangeSelect"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Select class range...</option>
                            @foreach([
                                'nursery-to-3'  => 'Sub-Junior (Nursery–3)',
                                'class-4-to-7'  => 'Junior (Classes 4–7)',
                                'class-8-to-10' => 'Sub-Senior (Classes 8–10)',
                                'class-10-plus' => 'Senior (Classes 10+)',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('class_range') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Academic Preferences (for secondary/senior) --}}
            <div id="streamBoardSection" style="display: none;">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Academic Preferences</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stream</label>
                        <select name="stream"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Any Stream</option>
                            @foreach([
                                'PCM'        => 'PCM — Physics, Chemistry, Maths',
                                'PCB'        => 'PCB — Physics, Chemistry, Biology',
                                'PCMB'       => 'PCMB — Science with Maths & Biology',
                                'Commerce'   => 'Commerce',
                                'Arts'       => 'Arts / Humanities',
                                'Vocational' => 'Vocational / Skill-based',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('stream') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Board Affiliation</label>
                        <select name="board"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Any Board</option>
                            @foreach([
                                'CBSE'      => 'CBSE — Central Board',
                                'RBSE'      => 'RBSE — Rajasthan State Board',
                                'ICSE'      => 'ICSE',
                                'IB'        => 'IB — International Baccalaureate',
                                'Cambridge' => 'Cambridge IGCSE/A-Levels',
                                'State'     => 'State Board',
                            ] as $val => $label)
                            <option value="{{ $val }}" {{ old('board') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Budget & Academic Level --}}
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
                               placeholder="e.g. 150000" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Academic Level</label>
                        <select name="academic_level"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                            <option value="">Not specified</option>
                            <option value="below_average" {{ old('academic_level') === 'below_average' ? 'selected' : '' }}>Below Average</option>
                            <option value="average" {{ old('academic_level') === 'average' ? 'selected' : '' }}>Average</option>
                            <option value="above_average" {{ old('academic_level') === 'above_average' ? 'selected' : '' }}>Above Average</option>
                            <option value="excellent" {{ old('academic_level') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Number of Results & Submit --}}
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span id="searchBtnText">Find Perfect Schools</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
// Toggle between AI and Assessment modes
const aiMode = document.getElementById('aiMode');
const assessmentMode = document.getElementById('assessmentMode');
const searchForm = document.getElementById('searchForm');
const schoolDetailsSection = document.getElementById('schoolDetailsSection');
const schoolLevelSelect = document.getElementById('schoolLevelSelect');
const classRangeSelect = document.getElementById('classRangeSelect');
const searchBtn = document.getElementById('searchBtn');
const searchBtnText = document.getElementById('searchBtnText');
const streamBoardSection = document.getElementById('streamBoardSection');
const assessmentsCompleted = {{ $assessmentsCompleted ? 'true' : 'false' }};

// Handle mode switching
[aiMode, assessmentMode].forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'assessment') {
            // Switch to assessment mode
            searchForm.action = "{{ route('school-finder.search') }}";
            schoolDetailsSection.style.display = 'none';
            schoolLevelSelect.required = false;
            classRangeSelect.required = false;
            streamBoardSection.style.display = 'none';
            searchBtnText.textContent = 'Find Schools by Assessment';
        } else {
            // Switch to AI mode
            searchForm.action = "{{ route('school-finder.ai.search') }}";
            schoolDetailsSection.style.display = 'block';
            schoolLevelSelect.required = true;
            classRangeSelect.required = true;
            searchBtnText.textContent = 'Find Perfect Schools';
        }
    });
});

// Show/hide stream & board section based on school level
document.querySelector('select[name="school_level"]').addEventListener('change', function() {
    const section = document.getElementById('streamBoardSection');
    const level = this.value;
    section.style.display = (level === 'secondary' || level === 'senior' || level === 'matric') ? 'block' : 'none';
});

document.getElementById('searchForm').addEventListener('submit', function(e) {
    const mode = document.querySelector('input[name="search_mode"]:checked').value;
    
    // Validate: Assessment mode requires completed assessments
    if (mode === 'assessment' && !assessmentsCompleted) {
        e.preventDefault();
        alert('Please complete all assessments (RIASEC, Cognitive, and OCEAN) to use Assessment-Based search.\n\nGo to the Assessments page to complete them.');
        return false;
    }
    
    const btn  = document.getElementById('searchBtn');
    const icon = document.getElementById('searchIcon');
    const spin = document.getElementById('loadingIcon');
    const txt  = document.getElementById('searchBtnText');
    btn.disabled = true;
    icon.classList.add('hidden');
    spin.classList.remove('hidden');
    
    if (mode === 'assessment') {
        txt.textContent = 'Finding schools from your assessment…';
    } else {
        txt.textContent = 'Searching with MetrixsMate AI…';
    }
});

// Token Purchase Modal Functions
function openTokenPurchaseModal() {
    document.getElementById('tokenPurchaseModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTokenPurchaseModal() {
    document.getElementById('tokenPurchaseModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>

{{-- Token Purchase Modal --}}
<div id="tokenPurchaseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-8 text-white">
            <button onclick="closeTokenPurchaseModal()" class="float-right text-white hover:opacity-80 text-2xl leading-none">&times;</button>
            <h3 class="text-xl font-bold mb-2">🎁 Unlock More Searches</h3>
            <p class="text-sm opacity-90">Purchase tokens to continue searching</p>
        </div>

        {{-- Content --}}
        <div class="px-6 py-6 space-y-6">
            {{-- Token Package --}}
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Search Tokens Bundle</p>
                        <p class="text-xs text-gray-600 mt-0.5">Unlock unlimited searches</p>
                    </div>
                    <div class="text-3xl">🔑</div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">3 Search Tokens</span>
                        <span class="font-bold text-purple-600">₹100</span>
                    </div>
                    <p class="text-xs text-gray-600">Valid for both AI-Powered and Assessment-Based searches</p>
                </div>
            </div>

            {{-- How It Works --}}
            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-800">How Tokens Work:</p>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex gap-2">
                        <span class="font-bold text-purple-600">•</span>
                        <span>Each search uses 1 token (AI or Assessment)</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold text-purple-600">•</span>
                        <span>Free searches are used first</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold text-purple-600">•</span>
                        <span>Paid tokens are used after free ones</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold text-purple-600">•</span>
                        <span>Tokens never expire</span>
                    </div>
                </div>
            </div>

            {{-- CTA Button --}}
            <button type="button" id="purchaseTokensBtn" class="w-full px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:shadow-lg transition">
                Buy 3 Tokens for ₹100
            </button>

            <button type="button" onclick="closeTokenPurchaseModal()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                Maybe Later
            </button>
        </div>
    </div>
</div>

{{-- Hidden Form for Token Purchase --}}
<form id="tokenPurchaseForm" action="{{ route('payments.initiate') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="type" value="tokens">
    <input type="hidden" name="amount" value="100">
    <input type="hidden" name="description" value="Purchase 3 Search Tokens">
</form>

<script>
// Attach event listeners after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Modal background click to close
    const modal = document.getElementById('tokenPurchaseModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTokenPurchaseModal();
            }
        });
    }
    
    // Purchase button click
    const btn = document.getElementById('purchaseTokensBtn');
    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('tokenPurchaseForm');
            if (form) {
                form.submit();
            }
        });
    }
});
</script>

@endsection
