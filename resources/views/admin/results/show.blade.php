@extends('layouts.app')
@section('title', 'Assessment Result - ' . $result->user->name)

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.results') }}" class="text-blue-600 hover:text-blue-800">← Back to Results</a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $result->user->name }}</h1>
                    <p class="text-gray-600">{{ $result->user->email }}</p>
                </div>
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                    {{ $result->assessment_type === 'ocean' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $result->assessment_type === 'riasec' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $result->assessment_type === 'cognitive' ? 'bg-purple-100 text-purple-800' : '' }}
                ">
                    {{ strtoupper($result->assessment_type) }} Assessment
                </span>
            </div>

            <!-- Score Section -->
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-600 text-sm mb-2">Assessment</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $result->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-2">Score</p>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 rounded-full border-4 border-green-500 flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">{{ round($result->percentage, 1) }}%</span>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-2">Completed</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $result->created_at->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $result->created_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Result Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Assessment Details</h2>
            
            <div class="space-y-4">
                @if($result->level)
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-sm font-semibold text-gray-700 uppercase">Level</p>
                    <p class="text-gray-900 mt-1 text-lg font-bold">{{ $result->level }}</p>
                </div>
                @endif

                @if($result->level_description)
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-sm font-semibold text-gray-700 uppercase">Level Description</p>
                    <p class="text-gray-900 mt-1">{{ $result->level_description }}</p>
                </div>
                @endif

                @if($result->actionable_insights)
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-sm font-semibold text-gray-700 uppercase">Actionable Insights</p>
                    <p class="text-gray-900 mt-1">{{ $result->actionable_insights }}</p>
                </div>
                @endif

                @if($result->reliability_score)
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-sm font-semibold text-gray-700 uppercase">Reliability Score</p>
                    <p class="text-gray-900 mt-1">{{ round($result->reliability_score, 1) }}%</p>
                </div>
                @endif

                @if($result->description)
                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                    <p class="text-sm font-semibold text-gray-700 uppercase">Description</p>
                    <p class="text-gray-900 mt-1">{{ $result->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">User Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="text-gray-900 font-semibold">{{ $result->user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Mobile</p>
                        <p class="text-gray-900 font-semibold">{{ $result->user->mobile_no ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-gray-900 font-semibold">
                            <span class="px-2 py-1 rounded text-xs {{ $result->user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $result->user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Test Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="text-gray-900 font-semibold">{{ $result->duration ?? 'N/A' }} minutes</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Questions Answered</p>
                        <p class="text-gray-900 font-semibold">{{ $result->questions_answered ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-gray-900 font-semibold">
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                Completed
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
