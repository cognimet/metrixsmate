@extends('layouts.app')

@section('title', __('app.assess_title', ['name' => $quiz->locale_title]) . ' — ' . __('app.brand'))

@section('content')
<div class="max-w-4xl mx-auto" x-data="assessmentForm()">

    {{-- ═══ Validation Errors ═══ --}}
    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <h2 class="font-semibold text-red-700 text-sm mb-2">{{ __('app.assess_fix_issues') }}</h2>
        <ul class="list-disc pl-5 text-red-600 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ═══ Header ═══ --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">
            {{ __('app.assess_title', ['name' => $quiz->locale_title]) }}
        </h1>
        <p class="mt-2 text-gray-500">
            {{ $quiz->locale_description ?? __('app.assess_generic_instr') }}
        </p>
    </div>

    {{-- ═══ Progress Bar ═══ --}}
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-4 mb-6 sticky top-[68px] z-30 backdrop-blur-xl bg-white/90">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-600" x-text="progressText"></span>
            <div class="flex items-center gap-3">
                @if(strtolower($quiz->title) === 'cognitive')
                {{-- Cognitive Timer --}}
                <div x-data="cognitiveTimer()" x-init="startTimer()" class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" :class="timeLeft <= 60 ? 'text-red-500 animate-pulse' : 'text-amber-500'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-bold tabular-nums" :class="timeLeft <= 60 ? 'text-red-600' : 'text-amber-600'" x-text="timerDisplay"></span>
                </div>
                @endif
                <span class="text-sm font-bold text-primary-600" x-text="Math.round(progressPct) + '%'"></span>
            </div>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-500 to-accent-500 h-2.5 rounded-full transition-all duration-300" :style="'width:' + progressPct + '%'"></div>
        </div>
    </div>

    {{-- ═══ Instructions ═══ --}}
    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-8 flex items-start gap-3">
        <svg class="w-5 h-5 text-primary-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <div>
            <h2 class="font-semibold text-primary-900 text-sm">{{ __('app.assess_instructions') }}</h2>
            <p class="text-primary-700 text-sm mt-1">
                @if (strtolower($quiz->title) === 'ocean' || strtolower($quiz->title) === 'riasec')
                    {{ __('app.assess_likert_instr') }}
                @elseif (strtolower($quiz->title) === 'cognitive')
                    {{ __('app.assess_cognitive_instr') }}
                @else
                    {{ __('app.assess_generic_instr') }}
                @endif
            </p>
        </div>
    </div>

    {{-- ═══ Question Form ═══ --}}
    <form id="quizForm" action="{{ route('assessments.submitAll', $quiz->id) }}" method="POST" novalidate @submit="handleSubmit($event)">
        @csrf

        @php
            $likert = [
                1 => __('app.likert_1'),
                2 => __('app.likert_2'),
                3 => __('app.likert_3'),
                4 => __('app.likert_4'),
                5 => __('app.likert_5'),
            ];
            $totalQ = count($questions);
        @endphp

        <div class="space-y-6">
            @foreach ($questions as $index => $question)
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 question-block transition-all"
                 data-question="{{ $question->id }}"
                 :class="{ 'ring-2 ring-red-300 border-red-200': errors.includes({{ $question->id }}) }">

                {{-- Question text --}}
                <div class="flex items-start gap-3 mb-5">
                    <span class="flex-shrink-0 w-8 h-8 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                    <p class="text-gray-900 font-medium leading-relaxed pt-1">{{ $question->locale_title }}</p>
                </div>

                {{-- OCEAN & RIASEC → Likert Pills --}}
                @if (strtolower($quiz->title) === 'ocean' || strtolower($quiz->title) === 'riasec')
                <div class="grid grid-cols-5 gap-1.5 sm:gap-3">
                    @foreach ($likert as $value => $label)
                    <label class="cursor-pointer group">
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $value }}"
                               class="hidden peer"
                               required
                               @change="markAnswered({{ $question->id }})">
                        <div class="flex flex-col items-center p-2 sm:p-3 rounded-xl border-2 border-gray-200
                                    peer-checked:border-primary-500 peer-checked:bg-primary-50
                                    group-hover:border-primary-300 transition-all min-h-[60px] sm:min-h-0 justify-center">
                            <span class="text-base sm:text-lg font-bold text-gray-400 peer-checked:text-primary-600 group-hover:text-primary-500">{{ $value }}</span>
                            <span class="text-[8px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1 text-center leading-tight hidden sm:block">{{ $label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                {{-- Mobile Likert Labels --}}
                <div class="flex justify-between mt-1 sm:hidden px-1">
                    <span class="text-[9px] text-gray-400">{{ $likert[1] }}</span>
                    <span class="text-[9px] text-gray-400">{{ $likert[5] }}</span>
                </div>

                {{-- Cognitive → MCQ cards --}}
                @elseif (strtolower($quiz->title) === 'cognitive')
                @php $decodedOptions = $question->locale_options ?? []; @endphp
                <div class="space-y-2.5">
                    @foreach ($decodedOptions as $optLabel => $optValue)
                    <label class="flex items-center p-3.5 bg-gray-50 rounded-xl border-2 border-transparent cursor-pointer
                                  hover:bg-primary-50 hover:border-primary-200 transition-all
                                  has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $optValue }}"
                               class="w-4 h-4 text-primary-600 focus:ring-primary-500 cursor-pointer"
                               required
                               @change="markAnswered({{ $question->id }})">
                        <span class="ml-3 text-gray-700 text-sm">{{ $optLabel }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                {{-- Inline error --}}
                <p class="text-sm text-red-500 mt-3 hidden error-message">{{ __('app.assess_validation_msg') }}</p>
            </div>
            @endforeach
        </div>

        {{-- ═══ Submit ═══ --}}
        <div class="mt-10 text-center">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-10 py-3.5 bg-primary-600 text-white font-bold rounded-xl shadow-lg hover:bg-primary-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="submitting">
                <template x-if="submitting">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </template>
                <template x-if="!submitting">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <span x-text="submitting ? '{{ __('app.assess_completing') }}' : '{{ __('app.assess_submit') }}'"></span>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function assessmentForm() {
    const total = {{ $totalQ }};
    return {
        answered: new Set(),
        errors: [],
        submitting: false,
        get progressPct() { return (this.answered.size / total) * 100; },
        get progressText() {
            const tpl = '{{ __('app.assess_progress', ['current' => '__CURRENT__', 'total' => $totalQ]) }}';
            return tpl.replace('__CURRENT__', this.answered.size);
        },
        markAnswered(id) {
            this.answered.add(id);
            this.errors = this.errors.filter(e => e !== id);
        },
        handleSubmit(e) {
            this.errors = [];
            let firstErr = null;
            document.querySelectorAll('.question-block').forEach(block => {
                const qId = parseInt(block.dataset.question);
                const radios = block.querySelectorAll("input[type='radio']");
                const checked = Array.from(radios).some(r => r.checked);
                const msg = block.querySelector('.error-message');
                if (!checked) {
                    this.errors.push(qId);
                    msg.classList.remove('hidden');
                    if (!firstErr) firstErr = block;
                } else {
                    msg.classList.add('hidden');
                }
            });
            if (this.errors.length) {
                e.preventDefault();
                firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                this.submitting = true;
            }
        },
        init() {
            document.querySelectorAll('.question-block').forEach(block => {
                const qId = parseInt(block.dataset.question);
                const checked = block.querySelector("input[type='radio']:checked");
                if (checked) this.answered.add(qId);
            });
        }
    }
}

@if(strtolower($quiz->title) === 'cognitive')
function cognitiveTimer() {
    return {
        timeLeft: 5 * 60, // 5 minutes in seconds
        timerInterval: null,
        get timerDisplay() {
            const m = Math.floor(this.timeLeft / 60);
            const s = this.timeLeft % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        },
        startTimer() {
            this.timerInterval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timerInterval);
                    this.timeLeft = 0;
                    // Auto-submit the form
                    const form = document.getElementById('quizForm');
                    if (form) {
                        alert('{{ __('app.assess_time_up') }}');
                        form.submit();
                    }
                }
            }, 1000);
        },
        destroy() {
            if (this.timerInterval) clearInterval(this.timerInterval);
        }
    }
}
@endif
</script>
@endpush
