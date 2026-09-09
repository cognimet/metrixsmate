@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="space-y-4">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_users'] }} active</p>
                </div>
                <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_payments'] }} payments</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Active Coupons</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['active_coupons'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_coupons'] }} total</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Certificates</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_certificates'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Generated</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <a href="{{ route('admin.users') }}" class="bg-primary-600 hover:bg-primary-700 text-white p-5 rounded-xl transition group">
            <h3 class="font-semibold text-sm mb-1">Manage Users</h3>
            <p class="text-xs opacity-80">View & manage accounts</p>
        </a>
        <a href="{{ route('admin.results') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white p-5 rounded-xl transition group">
            <h3 class="font-semibold text-sm mb-1">View Results</h3>
            <p class="text-xs opacity-80">{{ $stats['assessments_taken'] }} assessments</p>
        </a>
        <a href="{{ route('admin.analytics') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white p-5 rounded-xl transition group">
            <h3 class="font-semibold text-sm mb-1">Analytics</h3>
            <p class="text-xs opacity-80">System analytics</p>
        </a>
        <a href="{{ route('admin.payments') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white p-5 rounded-xl transition group">
            <h3 class="font-semibold text-sm mb-1">Verify Payments</h3>
            <p class="text-xs opacity-80">{{ $stats['pending_payments'] }} pending</p>
        </a>
        <a href="{{ route('admin.reports.generate') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-5 rounded-xl transition group">
            <h3 class="font-semibold text-sm mb-1">Generate Reports</h3>
            <p class="text-xs opacity-80">School, Uni & Company</p>
        </a>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Recent Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentPayments as $payment)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 text-sm text-gray-900">{{ $payment->user->name }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-emerald-600">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $payment->paid_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assessment Completion Overview --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Assessment Completion Status</h2>
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>OCEAN: {{ $stats['ocean_completed'] }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>RIASEC: {{ $stats['riasec_completed'] }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500"></span>Cognitive: {{ $stats['cognitive_completed'] }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">OCEAN</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">RIASEC</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Cognitive</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Last Activity</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentAssessments as $user)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($user->ocean_done)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Done
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($user->riasec_done)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Done
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($user->cognitive_done)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Done
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $user->last_assessment_at ? \Carbon\Carbon::parse($user->last_assessment_at)->format('M d, Y') : '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View Profile</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
