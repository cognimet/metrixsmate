@extends('layouts.admin')
@section('title', 'Assessment Result - ' . $result->user->name)
@section('page-title', strtoupper($result->assessment_type) . ' Result')
@section('page-description', $result->user->name)

@section('page-actions')
    <a href="{{ route('admin.results') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
@endsection

@section('content')
<div class="space-y-4 max-w-3xl">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $result->user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $result->user->email }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                {{ $result->assessment_type === 'ocean' ? 'bg-blue-50 text-blue-700' : '' }}
                {{ $result->assessment_type === 'riasec' ? 'bg-emerald-50 text-emerald-700' : '' }}
                {{ $result->assessment_type === 'cognitive' ? 'bg-purple-50 text-purple-700' : '' }}
            ">{{ strtoupper($result->assessment_type) }}</span>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-500 uppercase">Assessment</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $result->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Score</p>
                <p class="text-2xl font-bold text-primary-600 mt-1">{{ round($result->percentage, 1) }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Completed</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $result->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $result->created_at->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Assessment Details</h3>
        <div class="space-y-4">
            @if($result->level)
            <div class="border-b border-gray-100 pb-3">
                <p class="text-xs font-medium text-gray-500 uppercase">Level</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $result->level }}</p>
            </div>
            @endif
            @if($result->level_description)
            <div class="border-b border-gray-100 pb-3">
                <p class="text-xs font-medium text-gray-500 uppercase">Level Description</p>
                <p class="text-sm text-gray-700 mt-1">{{ $result->level_description }}</p>
            </div>
            @endif
            @if($result->actionable_insights)
            <div class="border-b border-gray-100 pb-3">
                <p class="text-xs font-medium text-gray-500 uppercase">Actionable Insights</p>
                <p class="text-sm text-gray-700 mt-1">{{ $result->actionable_insights }}</p>
            </div>
            @endif
            @if($result->reliability_score)
            <div class="border-b border-gray-100 pb-3">
                <p class="text-xs font-medium text-gray-500 uppercase">Reliability Score</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ round($result->reliability_score, 1) }}%</p>
            </div>
            @endif
            @if($result->description)
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Description</p>
                <p class="text-sm text-gray-700 mt-1">{{ $result->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">User Information</h3>
            <div class="space-y-2.5">
                <div>
                    <p class="text-xs text-gray-500">Member Since</p>
                    <p class="text-sm font-medium text-gray-900">{{ $result->user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Mobile</p>
                    <p class="text-sm font-medium text-gray-900">{{ $result->user->mobile_no ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $result->user->is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $result->user->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $result->user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Test Information</h3>
            <div class="space-y-2.5">
                <div>
                    <p class="text-xs text-gray-500">Duration</p>
                    <p class="text-sm font-medium text-gray-900">{{ $result->duration ?? 'N/A' }} minutes</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Questions Answered</p>
                    <p class="text-sm font-medium text-gray-900">{{ $result->questions_answered ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700">Completed</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
