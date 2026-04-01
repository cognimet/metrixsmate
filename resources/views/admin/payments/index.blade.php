@extends('layouts.app')
@section('title', 'Manage Payments')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
        </div>

        {{-- Search & Filters --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <select name="date" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Revenue</h3>
                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Transactions</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_payments'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Pending Verification</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_payments'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Avg. Amount</h3>
                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['avg_amount'] ?? 0, 0) }}</p>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Method</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <a href="{{ route('admin.users.show', $payment->user) }}" class="text-blue-600 hover:text-blue-800">{{ $payment->user->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ substr($payment->payment_id, 0, 12) }}...</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            @if($payment->status === 'pending')
                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800" onclick="return confirm('Mark as verified?')">Verify</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $payments->links() }}
        </div>

    </div>
</div>
@endsection
