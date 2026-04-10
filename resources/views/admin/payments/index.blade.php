@extends('layouts.admin')
@section('title', 'Manage Payments')
@section('page-title', 'Payments')
@section('page-description', 'Manage transactions and verification')

@section('content')
<div class="space-y-4">

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}"
                    class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
            </div>
            <select name="status" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <select name="date" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Time</option>
                <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'status', 'date']))
            <a href="{{ route('admin.payments') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">Clear</a>
            @endif
        </form>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Transactions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_payments'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Pending Verification</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_payments'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Avg. Amount</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['avg_amount'] ?? 0, 0) }}</p>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.users.show', $payment->user) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ $payment->user->name }}</a>
                        </td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ substr($payment->payment_id, 0, 12) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $payment->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View</a>
                                @if($payment->status === 'pending')
                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-emerald-600 hover:text-emerald-700" onclick="return confirm('Mark as verified?')">Verify</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-gray-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 px-5 py-3">
        {{ $payments->links() }}
    </div>
</div>
@endsection
