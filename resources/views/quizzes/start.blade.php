@extends('layouts.app')
@section('title','Start '.$quiz->title)
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Start {{ $quiz->title }}</h1>
    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500">← Back</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($domains as $d)
    <div class="p-6 bg-white rounded-xl shadow flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ $d->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">Questions: {{ $d->questions()->count() }}</p>
        </div>
        <a href="{{ route('assessments.show', ['quiz'=>$quiz->id, 'domainId'=>$d->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Start</a>
    </div>
    @endforeach
</div>
@endsection
