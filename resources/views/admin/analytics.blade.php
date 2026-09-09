@extends('layouts.admin')
@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics')
@section('page-description', 'Comprehensive system analytics and insights')

@section('content')
<div class="space-y-6">

    {{-- User Statistics --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 mb-3">User Statistics</h2>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Users</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($userStats['total_users']) }}</p>
                <p class="text-xs text-gray-500 mt-1">+{{ $userStats['users_this_month'] }} this month</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Students</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($userStats['total_students']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($userStats['total_students'] / max($userStats['total_users'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Admins</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($userStats['total_admins']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Active</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($userStats['active_users']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}% rate</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Growth</p>
                @php
                    $lastMonthUsers = \App\Models\User::whereMonth('created_at', now()->subMonth()->month)
                        ->whereYear('created_at', now()->subMonth()->year)->count();
                    $growth = $lastMonthUsers > 0 ? round((($userStats['users_this_month'] - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;
                @endphp
                <p class="text-2xl font-bold {{ $growth >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">{{ $growth > 0 ? '+' : '' }}{{ $growth }}%</p>
            </div>
        </div>
    </div>

    {{-- Assessment Statistics --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Assessment Statistics</h2>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($assessmentStats['total_assessments']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">OCEAN</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($assessmentStats['ocean_assessments']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($assessmentStats['ocean_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">RIASEC</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($assessmentStats['riasec_assessments']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($assessmentStats['riasec_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Cognitive</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($assessmentStats['cognitive_assessments']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($assessmentStats['cognitive_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Avg Score</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ round($assessmentStats['avg_score'], 1) }}%</p>
            </div>
        </div>
    </div>

    {{-- Payment Statistics --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Payment Statistics</h2>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($paymentStats['total_revenue'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">This month: {{ number_format($paymentStats['revenue_this_month'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Payments</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($paymentStats['total_payments']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Completed</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($paymentStats['completed_payments']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($paymentStats['completed_payments'] / max($paymentStats['total_payments'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Pending</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($paymentStats['pending_payments']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Avg Payment</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($paymentStats['avg_payment'], 2) }}</p>
            </div>
        </div>
    </div>

    {{-- School Recommendation Feedback --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Recommendation Feedback</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($schoolStats['total_recommendations']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Positive</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($schoolStats['positive_feedbacks']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($schoolStats['positive_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Neutral</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($schoolStats['neutral_feedbacks']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($schoolStats['neutral_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Negative</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($schoolStats['negative_feedbacks']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ round(($schoolStats['negative_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
            </div>
        </div>
    </div>

    {{-- Top Schools & Cities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Top 10 Recommended Schools</h3>
            <div class="space-y-3">
                @forelse($topSchools as $school)
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $school->name }}</p>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1.5">
                            <div class="h-full bg-primary-500 rounded-full" style="width: {{ ($school->recommendations / max($topSchools->pluck('recommendations')->max(), 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 flex-shrink-0">{{ number_format($school->recommendations) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Top 10 Cities</h3>
            <div class="space-y-3">
                @forelse($topCities as $city)
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $city->name ?? 'Unknown' }}</p>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1.5">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($city->user_count / max($topCities->pluck('user_count')->max(), 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 flex-shrink-0">{{ number_format($city->user_count) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
