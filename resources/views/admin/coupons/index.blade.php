@extends('layouts.app')
@section('title', 'Manage Coupons')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Coupon Management</h1>
            <a href="{{ route('admin.coupons.create') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Create Coupon</a>
        </div>

        {{-- Search & Filters --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="search" placeholder="Search by coupon code..." value="{{ request('search') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Coupons</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_coupons'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Active</h3>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active_coupons'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Expired</h3>
                <p class="text-2xl font-bold text-red-600">{{ $stats['expired_coupons'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Discount</h3>
                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['total_discount'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Coupons Table --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Code</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Discount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Uses</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Expires</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ strtoupper($coupon->code) }}</code>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₹' . number_format($coupon->discount_value, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $coupon->discount_type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($coupon->discount_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $coupon->usage_count ?? 0 }} / {{ $coupon->max_uses ?? '∞' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($coupon->expires_at)
                                {{ $coupon->expires_at->format('M d, Y') }}
                            @else
                                Never
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                                $isMaxed = $coupon->max_uses && $coupon->usage_count >= $coupon->max_uses;
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ !$isExpired && !$isMaxed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ !$isExpired && !$isMaxed ? 'Active' : ($isExpired ? 'Expired' : 'Max Uses') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-green-600 hover:text-green-800 mr-3">Edit</a>
                            <button onclick="if(confirm('Delete this coupon?')) { document.getElementById('delete-{{ $coupon->id }}').submit(); }" class="text-red-600 hover:text-red-800">Delete</button>
                            <form id="delete-{{ $coupon->id }}" action="{{ route('admin.coupons.delete', $coupon) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $coupons->links() }}
        </div>

    </div>
</div>
@endsection
