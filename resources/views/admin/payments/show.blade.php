@extends('layouts.app')
@section('title', 'Payment Details')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
            <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Payments</a>
        </div>

        {{-- Payment Info Card --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Transaction Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Payment ID</label>
                            <p class="text-gray-900 font-mono">{{ $payment->payment_id }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Amount</label>
                            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Payment Method</label>
                            <p class="text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Transaction ID</label>
                            <p class="text-gray-900 font-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Timeline</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Status</label>
                            <p>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Created At</label>
                            <p class="text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase">Verified At</label>
                            <p class="text-gray-900">{{ $payment->verified_at ? $payment->verified_at->format('M d, Y h:i A') : 'Pending' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Information --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-900 font-semibold">{{ $payment->user->name }}</p>
                    <p class="text-gray-600">{{ $payment->user->email }}</p>
                    <p class="text-sm text-gray-500">{{ $payment->user->mobile_no ?? 'No mobile' }}</p>
                </div>
                <a href="{{ route('admin.users.show', $payment->user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">View User Profile</a>
            </div>
        </div>

        {{-- Actions --}}
        @if($payment->status === 'pending')
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Verification</h2>
            <p class="text-gray-600 mb-6">Verify this payment to mark it as completed and grant the user search tokens.</p>
            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Verification Notes (Optional)</label>
                        <textarea name="verification_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Add any verification notes here..."></textarea>
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" onclick="return confirm('Mark this payment as verified?')">Verify Payment</button>
            </form>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Verification Status</h2>
            <p class="text-green-600 font-semibold">✓ This payment has been verified and processed.</p>
            @if($payment->verified_at)
                <p class="text-gray-600 text-sm mt-2">Verified on {{ $payment->verified_at->format('M d, Y h:i A') }}</p>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
