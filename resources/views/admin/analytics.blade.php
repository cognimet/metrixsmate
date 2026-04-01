@extends('layouts.app')
@section('title', 'Analytics Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Analytics Dashboard</h1>
                <p class="text-gray-600 mt-2">Comprehensive system analytics and insights</p>
            </div>

            <!-- User Statistics -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">User Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Total Users</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($userStats['total_users']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">+{{ $userStats['users_this_month'] }} this month</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Students</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($userStats['total_students']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($userStats['total_students'] / max($userStats['total_users'], 1)) * 100, 1) }}% of total</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Admins</div>
                        <div class="text-3xl font-bold text-red-600 mt-2">{{ number_format($userStats['total_admins']) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Active Users</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($userStats['active_users']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}% active</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Growth</div>
                        <div class="text-3xl font-bold text-purple-600 mt-2">
                            @php
                                $lastMonthUsers = \App\Models\User::whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->count();
                                $growth = $lastMonthUsers > 0 ? round((($userStats['users_this_month'] - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;
                            @endphp
                            {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Statistics -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Assessment Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Total Assessments</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($assessmentStats['total_assessments']) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">OCEAN</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($assessmentStats['ocean_assessments']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($assessmentStats['ocean_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">RIASEC</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($assessmentStats['riasec_assessments']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($assessmentStats['riasec_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Cognitive</div>
                        <div class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($assessmentStats['cognitive_assessments']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($assessmentStats['cognitive_assessments'] / max($assessmentStats['total_assessments'], 1)) * 100, 1) }}%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Avg Score</div>
                        <div class="text-3xl font-bold text-orange-600 mt-2">{{ round($assessmentStats['avg_score'], 1) }}%</div>
                    </div>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Payment Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Total Revenue</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($paymentStats['total_revenue'], 2) }}</div>
                        <p class="text-xs text-gray-600 mt-2">This month: ₹{{ number_format($paymentStats['revenue_this_month'], 2) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Total Payments</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($paymentStats['total_payments']) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Completed</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($paymentStats['completed_payments']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($paymentStats['completed_payments'] / max($paymentStats['total_payments'], 1)) * 100, 1) }}% success</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Pending</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($paymentStats['pending_payments']) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Avg Payment</div>
                        <div class="text-3xl font-bold text-purple-600 mt-2">₹{{ number_format($paymentStats['avg_payment'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- School Recommendation Statistics -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">School Recommendation Feedback</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Total Recommendations</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($schoolStats['total_recommendations']) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Positive Feedback</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($schoolStats['positive_feedbacks']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($schoolStats['positive_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Neutral Feedback</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($schoolStats['neutral_feedbacks']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($schoolStats['neutral_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-semibold uppercase">Negative Feedback</div>
                        <div class="text-3xl font-bold text-red-600 mt-2">{{ number_format($schoolStats['negative_feedbacks']) }}</div>
                        <p class="text-xs text-gray-600 mt-2">{{ round(($schoolStats['negative_feedbacks'] / max($schoolStats['total_recommendations'], 1)) * 100, 1) }}%</p>
                    </div>
                </div>
            </div>

            <!-- Top Schools and Cities -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Top Schools -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Top 10 Recommended Schools</h3>
                    <div class="space-y-3">
                        @forelse($topSchools as $school)
                        <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $school->name }}</p>
                                <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($school->recommendations / max($topSchools->pluck('recommendations')->max(), 1)) * 100 }}%"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 ml-4">{{ number_format($school->recommendations) }}</p>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Cities -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Top 10 Cities</h3>
                    <div class="space-y-3">
                        @forelse($topCities as $city)
                        <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $city->name ?? 'Unknown' }}</p>
                                <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ ($city->user_count / max($topCities->pluck('user_count')->max(), 1)) * 100 }}%"></div>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900 ml-4">{{ number_format($city->user_count) }}</p>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
