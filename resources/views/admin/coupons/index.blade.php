@extends('layouts.admin')
@section('title', 'Manage Coupons')
@section('page-title', 'Coupons')
@section('page-description', 'Manage discount coupons')

@section('page-actions')
    <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">+ Create Coupon</a>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by coupon code..." value="{{ request('search') }}"
                    class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
            </div>
            <select name="status" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.coupons') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">Clear</a>
            @endif
        </form>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Coupons</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_coupons'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Active</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active_coupons'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Expired</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expired_coupons'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Discount</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_discount'] ?? 0, 2) }}</p>
        </div>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Discount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Uses</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expires</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3">
                            <code class="text-sm font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-900">{{ strtoupper($coupon->code) }}</code>
                        </td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-900">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : number_format($coupon->discount_value, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $coupon->discount_type === 'percentage' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ ucfirst($coupon->discount_type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $coupon->usage_count ?? 0 }} / {{ $coupon->max_uses ?? '&infin;' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            @if($coupon->expires_at) {{ $coupon->expires_at->format('M d, Y') }} @else Never @endif
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                                $isMaxed = $coupon->max_uses && $coupon->usage_count >= $coupon->max_uses;
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ !$isExpired && !$isMaxed ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ !$isExpired && !$isMaxed ? 'Active' : ($isExpired ? 'Expired' : 'Max Uses') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Edit</a>
                                <button onclick="if(confirm('Delete this coupon?')) { document.getElementById('delete-{{ $coupon->id }}').submit(); }" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                <form id="delete-{{ $coupon->id }}" action="{{ route('admin.coupons.delete', $coupon) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-gray-400">No coupons found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 px-5 py-3">
        {{ $coupons->links() }}
    </div>
</div>
@endsection
