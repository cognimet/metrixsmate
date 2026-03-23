@extends('layouts.app')
@section('title', $quiz->title.' — '.$domain->title)
@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>
        <p class="text-sm text-gray-500">{{ $domain->title }}</p>
    </div>
    <a href="{{ route('quizzes.start', $quiz->id) }}" class="text-sm text-gray-500">← Back to domains</a>
</div>

<form action="{{ route('assessments.submit', ['quiz'=>$quiz->id, 'domainId'=>$domain->id]) }}" method="POST">
    @csrf
    <div class="space-y-4">
        @foreach($questions as $q)
            <div class="p-4 bg-white rounded-xl shadow">
                <div class="flex justify-between items-start">
                    <h3 class="font-medium">{{ $q->order }}. {{ $q->title }}</h3>
                    @if($q->max_points && $q->min_points)
                        <span class="text-xs text-gray-400">Likert ({{ $q->min_points }}–{{ $q->max_points }})</span>
                    @endif
                </div>

                <div class="mt-3 space-y-2">
                    {{-- If options exist as JSON with keys in order, render them as enumerated options.
                         We will render radio values as integer positions (1..n) to match correct_answer storage. --}}
                    @php
                        $opts = $q->options ? (is_array($q->options) ? $q->options : json_decode($q->options, true)) : null;
                        $i = 0;
                    @endphp

                    @if($opts)
                        @foreach($opts as $optLabel => $val)
                            @php $i++; @endphp
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input required type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}" class="form-radio">
                                <span>{{ $optLabel }}</span>
                            </label>
                        @endforeach
                    @else
                        {{-- Render Likert scale if min/max provided --}}
                        @if($q->min_points !== null && $q->max_points !== null)
                            @for($v = $q->min_points; $v <= $q->max_points; $v++)
                                <label class="inline-flex items-center mr-4">
                                    <input required type="radio" name="answers[{{ $q->id }}]" value="{{ $v }}" class="form-radio">
                                    <span class="ml-2 text-sm">{{ $v }}</span>
                                </label>
                            @endfor
                        @else
                            {{-- fallback: simple 4-option placeholder --}}
                            @for($v = 1; $v <= 4; $v++)
                                <label class="inline-flex items-center mr-4">
                                    <input required type="radio" name="answers[{{ $q->id }}]" value="{{ $v }}" class="form-radio">
                                    <span class="ml-2 text-sm">Option {{ $v }}</span>
                                </label>
                            @endfor
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Submit Answers</button>
    </div>
</form>
@endsection
