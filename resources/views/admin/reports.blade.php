@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Reports & Analytics</h1>

        {{-- Key Metrics --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Users</h3>
                <p class="text-4xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                    @php
                        $percentChange = isset($stats['user_growth']) && $stats['total_users'] > 0 
                            ? (($stats['user_growth'] / ($stats['total_users'] - $stats['user_growth'])) * 100) 
                            : 0;
                    @endphp
                    {{ number_format($percentChange, 1) }}% growth
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Revenue</h3>
                <p class="text-4xl font-bold text-gray-900">₹{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                    {{ $stats['total_payments'] ?? 0 }} transactions
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Assessments</h3>
                <p class="text-4xl font-bold text-gray-900">{{ $stats['total_assessments'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="inline-block w-2 h-2 bg-purple-500 rounded-full mr-1"></span>
                    Across all types
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Recommendations</h3>
                <p class="text-4xl font-bold text-gray-900">{{ $stats['total_recommendations'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                    All methods
                </p>
            </div>
        </div>

        {{-- User Growth Chart Section --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Growth (Last 7 Days)</h2>
                @if(isset($stats['user_growth_data']) && count($stats['user_growth_data']))
                    <div class="h-64 flex items-end gap-2">
                        @php
                            $maxUsers = max($stats['user_growth_data'] ?? [1]);
                        @endphp
                        @foreach($stats['user_growth_data'] as $day => $count)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-500 rounded-t" style="height: {{ ($count / $maxUsers) * 100 }}%;"></div>
                                <p class="text-xs text-gray-600 mt-2">{{ $day }}</p>
                                <p class="text-xs font-semibold text-gray-900">{{ $count }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-12">No data available</p>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Revenue Growth (Last 7 Days)</h2>
                @if(isset($stats['revenue_growth_data']) && count($stats['revenue_growth_data']))
                    <div class="h-64 flex items-end gap-2">
                        @php
                            $maxRevenue = max($stats['revenue_growth_data'] ?? [1]);
                        @endphp
                        @foreach($stats['revenue_growth_data'] as $day => $amount)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-green-500 rounded-t" style="height: {{ ($amount / $maxRevenue) * 100 }}%;"></div>
                                <p class="text-xs text-gray-600 mt-2">{{ $day }}</p>
                                <p class="text-xs font-semibold text-gray-900">₹{{ number_format($amount, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-12">No data available</p>
                @endif
            </div>
        </div>

        {{-- Assessment Distribution --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Completion Rate</h2>
                <div class="space-y-4">
                    @php
                        $assessmentTypes = ['OCEAN' => $stats['ocean_assessments'] ?? 0, 'RIASEC' => $stats['riasec_assessments'] ?? 0, 'Cognitive' => $stats['cognitive_assessments'] ?? 0];
                        $totalAssessments = array_sum($assessmentTypes);
                    @endphp
                    @foreach($assessmentTypes as $type => $count)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $type }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalAssessments > 0 ? ($count / $totalAssessments) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Schools</h2>
                @if(isset($stats['top_schools']) && count($stats['top_schools']))
                    <div class="space-y-3">
                        @foreach($stats['top_schools'] as $school)
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-sm font-medium text-gray-900">{{ $school['name'] ?? 'Unknown' }}</span>
                                <span class="text-sm font-bold text-gray-600">{{ $school['recommendations'] ?? 0 }} recommendations</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No recommendation data available</p>
                @endif
            </div>
        </div>

        {{-- Location Analysis --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">User Distribution by Location</h2>
            @if(isset($stats['top_cities']) && count($stats['top_cities']))
                <div class="grid grid-cols-3 gap-4">
                    @foreach($stats['top_cities'] as $city)
                        <div class="border border-gray-200 rounded p-4">
                            <p class="font-semibold text-gray-900">{{ $city['name'] ?? 'Unknown' }}</p>
                            <p class="text-2xl font-bold text-gray-600 mt-1">{{ $city['users'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">users</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No location data available</p>
            @endif
        </div>

    </div>
</div>
@endsection
