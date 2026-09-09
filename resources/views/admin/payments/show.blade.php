@extends('layouts.admin')
@section('title', 'Payment Details')
@section('page-title', 'Payment Details')
@section('page-description', 'Transaction #' . substr($payment->payment_id, 0, 12))

@section('page-actions')
    <a href="{{ route('admin.payments') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
@endsection

@section('content')
<div class="space-y-4 max-w-3xl">

    {{-- Payment Info --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Transaction Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Payment ID</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $payment->payment_id }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Amount</label>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Payment Method</label>
                        <p class="text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Transaction ID</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Status & Timeline</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $payment->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Created At</label>
                        <p class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Verified At</label>
                        <p class="text-sm text-gray-900">{{ $payment->verified_at ? $payment->verified_at->format('M d, Y h:i A') : 'Pending' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Info --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">User Information</h2>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ substr($payment->user->name, 0, 1) }}</div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $payment->user) }}" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">View Profile</a>
        </div>
    </div>

    {{-- Verification --}}
    @if($payment->status === 'pending')
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Verification</h2>
        <p class="text-sm text-gray-600 mb-4">Verify this payment to mark it as completed and grant the user search tokens.</p>
        <form action="{{ route('admin.payments.verify', $payment) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Verification Notes (Optional)</label>
                <textarea name="verification_notes" rows="3" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none" placeholder="Add verification notes..."></textarea>
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition" onclick="return confirm('Mark as verified?')">Verify Payment</button>
        </form>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Verification Status</h2>
        <p class="text-sm text-emerald-600 font-medium">&#10003; This payment has been verified and processed.</p>
        @if($payment->verified_at)
            <p class="text-xs text-gray-500 mt-1">Verified on {{ $payment->verified_at->format('M d, Y h:i A') }}</p>
        @endif
    </div>
    @endif
</div>
@endsection
