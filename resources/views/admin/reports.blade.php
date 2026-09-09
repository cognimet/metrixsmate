@extends('layouts.admin')
@section('title', 'Reports & Analytics')
@section('page-title', 'Reports')
@section('page-description', 'System reports and growth analytics')

@section('page-actions')
    <a href="{{ route('admin.reports.generate') }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Generate PDF Report</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Key Metrics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] ?? 0 }}</p>
            @php
                $percentChange = isset($stats['user_growth']) && ($stats['total_users'] - ($stats['user_growth'] ?? 0)) > 0
                    ? (($stats['user_growth'] / ($stats['total_users'] - $stats['user_growth'])) * 100)
                    : 0;
            @endphp
            <p class="text-xs text-gray-500 mt-1">
                <span class="inline-block w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1"></span>
                {{ number_format($percentChange, 1) }}% growth
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">
                <span class="inline-block w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span>
                {{ $stats['total_payments'] ?? 0 }} transactions
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Assessments</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_assessments'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">
                <span class="inline-block w-1.5 h-1.5 bg-purple-500 rounded-full mr-1"></span>
                Across all types
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Recommendations</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_recommendations'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">
                <span class="inline-block w-1.5 h-1.5 bg-amber-500 rounded-full mr-1"></span>
                All methods
            </p>
        </div>
    </div>

    {{-- Growth Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">User Growth (Last 7 Days)</h2>
            @if(isset($stats['user_growth_data']) && count($stats['user_growth_data']))
            <div class="h-48 flex items-end gap-2">
                @php $maxUsers = max($stats['user_growth_data'] ?? [1]); @endphp
                @foreach($stats['user_growth_data'] as $day => $count)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-primary-500 rounded-t transition-all" style="height: {{ $maxUsers > 0 ? ($count / $maxUsers) * 100 : 0 }}%;"></div>
                    <p class="text-[10px] text-gray-500 mt-1.5">{{ $day }}</p>
                    <p class="text-[10px] font-semibold text-gray-900">{{ $count }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-12">No data available</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Revenue Growth (Last 7 Days)</h2>
            @if(isset($stats['revenue_growth_data']) && count($stats['revenue_growth_data']))
            <div class="h-48 flex items-end gap-2">
                @php $maxRevenue = max($stats['revenue_growth_data'] ?? [1]); @endphp
                @foreach($stats['revenue_growth_data'] as $day => $amount)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-emerald-500 rounded-t transition-all" style="height: {{ $maxRevenue > 0 ? ($amount / $maxRevenue) * 100 : 0 }}%;"></div>
                    <p class="text-[10px] text-gray-500 mt-1.5">{{ $day }}</p>
                    <p class="text-[10px] font-semibold text-gray-900">{{ number_format($amount, 0) }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-12">No data available</p>
            @endif
        </div>
    </div>

    {{-- Assessment Distribution & Top Schools --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Assessment Distribution</h2>
            <div class="space-y-4">
                @php
                    $assessmentTypes = ['OCEAN' => $stats['ocean_assessments'] ?? 0, 'RIASEC' => $stats['riasec_assessments'] ?? 0, 'Cognitive' => $stats['cognitive_assessments'] ?? 0];
                    $totalAssessments = array_sum($assessmentTypes);
                @endphp
                @foreach($assessmentTypes as $type => $count)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-medium text-gray-700">{{ $type }}</span>
                        <span class="text-xs font-bold text-gray-900">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-primary-500 h-1.5 rounded-full transition-all" style="width: {{ $totalAssessments > 0 ? ($count / $totalAssessments) * 100 : 0 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Top Schools</h2>
            @if(isset($stats['top_schools']) && count($stats['top_schools']))
            <div class="space-y-3">
                @foreach($stats['top_schools'] as $school)
                <div class="flex justify-between items-center pb-2.5 border-b border-gray-100 last:border-0">
                    <span class="text-sm font-medium text-gray-900">{{ $school['name'] ?? 'Unknown' }}</span>
                    <span class="text-xs font-bold text-gray-500">{{ $school['recommendations'] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-8">No data available</p>
            @endif
        </div>
    </div>

    {{-- Location Analysis --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">User Distribution by Location</h2>
        @if(isset($stats['top_cities']) && count($stats['top_cities']))
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($stats['top_cities'] as $city)
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-sm font-semibold text-gray-900">{{ $city['name'] ?? 'Unknown' }}</p>
                <p class="text-xl font-bold text-gray-600 mt-0.5">{{ $city['users'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">users</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-8">No location data available</p>
        @endif
    </div>

</div>
@endsection
