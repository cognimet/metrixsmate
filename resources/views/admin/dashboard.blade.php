@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()->name }}!</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Users --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $stats['active_users'] }} active</p>
                    </div>
                    <div class="text-4xl opacity-20">👥</div>
                </div>
            </div>

            {{-- Total Revenue --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($stats['total_revenue']) }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $stats['total_payments'] }} payments</p>
                    </div>
                    <div class="text-4xl opacity-20">💰</div>
                </div>
            </div>

            {{-- Active Coupons --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Coupons</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['active_coupons'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $stats['total_coupons'] }} total</p>
                    </div>
                    <div class="text-4xl opacity-20">🎟️</div>
                </div>
            </div>

            {{-- Certificates --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Certificates</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_certificates'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Generated</p>
                    </div>
                    <div class="text-4xl opacity-20">🎖️</div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.users') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow transition">
                <h3 class="font-semibold text-lg mb-2">Manage Users</h3>
                <p class="text-sm opacity-90">View, edit, and manage user accounts</p>
            </a>
            <a href="{{ route('admin.results') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white p-6 rounded-lg shadow transition">
                <h3 class="font-semibold text-lg mb-2">View Results</h3>
                <p class="text-sm opacity-90">{{ $stats['assessments_taken'] }} assessment records</p>
            </a>
            <a href="{{ route('admin.analytics') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white p-6 rounded-lg shadow transition">
                <h3 class="font-semibold text-lg mb-2">Analytics</h3>
                <p class="text-sm opacity-90">Comprehensive system analytics</p>
            </a>
            <a href="{{ route('admin.payments') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow transition">
                <h3 class="font-semibold text-lg mb-2">Verify Payments</h3>
                <p class="text-sm opacity-90">{{ $stats['pending_payments'] }} pending</p>
            </a>
        </div>

        {{-- Recent Payments --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->user->name }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">₹{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->paid_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Assessments --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Assessments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Assessment</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Score</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentAssessments as $assessment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $assessment->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 uppercase">{{ $assessment->assessment_type }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ round($assessment->percentage) }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $assessment->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
