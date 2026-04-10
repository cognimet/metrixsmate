@extends('layouts.admin')
@section('title', 'Coupon: ' . strtoupper($coupon->code))
@section('page-title', 'Coupon Detail')
@section('page-description', 'View coupon details and usage history')

@section('page-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Edit Coupon</a>
        <a href="{{ route('admin.coupons') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">← Back</a>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Coupon Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left: Core Details --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <code class="text-2xl font-bold tracking-widest bg-gray-100 px-4 py-1.5 rounded-lg text-gray-900">{{ strtoupper($coupon->code) }}</code>
                    @if($coupon->description)
                    <p class="text-sm text-gray-500 mt-2">{{ $coupon->description }}</p>
                    @endif
                </div>
                @php
                    $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                    $isMaxed   = $coupon->max_uses && $coupon->usage_count >= $coupon->max_uses;
                    $isActive  = $coupon->is_active && !$isExpired && !$isMaxed;
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                    {{ $isActive ? 'Active' : ($isExpired ? 'Expired' : ($isMaxed ? 'Limit Reached' : 'Inactive')) }}
                </span>
            </div>

            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-5">
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Discount</dt>
                    <dd class="mt-1 text-lg font-bold text-gray-900">
                        {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₹' . number_format($coupon->discount_value, 2) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Type</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $coupon->discount_type === 'percentage' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                            {{ ucfirst($coupon->discount_type) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Min. Order</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $coupon->minimum_amount ? '₹' . number_format($coupon->minimum_amount, 2) : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Max Uses</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $coupon->max_uses ?? 'Unlimited' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Per User</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $coupon->uses_per_user ?? 'Unlimited' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Expires</dt>
                    <dd class="mt-1 text-sm font-medium {{ $isExpired ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $coupon->created_at->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $coupon->updated_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Right: Usage Stats --}}
        <div class="flex flex-col gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Times Used</p>
                <p class="text-4xl font-bold text-gray-900">{{ $coupon->usage_count ?? 0 }}</p>
                @if($coupon->max_uses)
                <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-primary-500 h-2 rounded-full transition-all"
                         style="width: {{ min(($coupon->usage_count / $coupon->max_uses) * 100, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $coupon->usage_count }} of {{ $coupon->max_uses }} uses</p>
                @endif
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Unique Users</p>
                <p class="text-4xl font-bold text-primary-600">{{ $usages->unique('user_id')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Usage History --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Usage History</h2>
            <span class="text-xs text-gray-400">{{ $usages->count() }} {{ Str::plural('record', $usages->count()) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Quiz / Assessment</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Access Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Used On</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($usages as $i => $access)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            @if($access->user)
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $access->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $access->user->email }}</p>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">User deleted</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($access->quiz)
                            <p class="text-sm font-medium text-gray-900">{{ $access->quiz->title ?? $access->quiz->name ?? 'Quiz #' . $access->quiz_id }}</p>
                            @else
                            <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-teal-50 text-teal-700">
                                {{ ucfirst($access->access_type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $access->granted_at ? $access->granted_at->format('M d, Y h:i A') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($access->user)
                            <a href="{{ route('admin.users.show', $access->user) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View User</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center">
                            <p class="text-sm text-gray-400">No one has used this coupon yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
