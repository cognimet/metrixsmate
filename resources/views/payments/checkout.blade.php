@extends('layouts.app')
@section('title', 'Checkout – ' . __('app.brand'))

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Complete Payment</h1>
    <p class="text-sm text-gray-500 mt-1">Order #{{ $payment->order_id }}</p>
</div>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">

        {{-- Amount Summary --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-white text-center">
            <p class="text-sm font-medium text-primary-100 mb-1">Amount to Pay</p>
            <p class="text-4xl font-bold">₹{{ number_format($payment->amount, 2) }}</p>
            <p class="text-xs text-primary-200 mt-2">Order: {{ $payment->order_id }}</p>
        </div>

        <div class="p-6">
            {{-- UPI Instructions --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pay using UPI</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700">1</div>
                        <p class="text-sm text-gray-700">Open any UPI app (GPay, PhonePe, Paytm, etc.)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700">2</div>
                        <p class="text-sm text-gray-700">Send <strong>₹{{ number_format($payment->amount, 2) }}</strong> to:</p>
                    </div>

                    {{-- UPI ID Display --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-3 flex items-center justify-between" x-data="{ copied: false }">
                        <div>
                            <p class="text-xs text-gray-500">UPI ID</p>
                            <p class="text-sm font-bold text-gray-900" id="upi-id">{{ $upiId }}</p>
                        </div>
                        <button @click="navigator.clipboard.writeText('{{ $upiId }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                :class="copied ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                            <span x-show="!copied">Copy</span>
                            <span x-show="copied">Copied!</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700">3</div>
                        <p class="text-sm text-gray-700">Add note: <strong>{{ $payment->order_id }}</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700">4</div>
                        <p class="text-sm text-gray-700">Enter the transaction ID below to confirm</p>
                    </div>
                </div>
            </div>

            {{-- Quick Pay Link (Mobile) or QR Code (Desktop) --}}
            <div class="mb-6 text-center" x-data="{ isMobile: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) }">
                <!-- Mobile: Direct UPI Link -->
                <a x-show="isMobile" href="{{ $upiLink }}" class="w-full flex items-center justify-center gap-2 py-3 bg-green-600 text-white rounded-xl font-semibold text-sm hover:bg-green-700 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Open UPI App to Pay
                </a>
                <p x-show="isMobile" class="text-xs text-gray-400 text-center mt-2">This will open your default UPI app</p>

                <!-- Desktop: QR Code -->
                <div x-show="!isMobile" class="space-y-3">
                    <p class="text-sm font-medium text-gray-700">Scan with any UPI app</p>
                    <div class="bg-white p-4 rounded-xl border border-gray-200 inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($upiLink) }}" alt="UPI QR Code" class="w-48 h-48">
                    </div>
                    <p class="text-xs text-gray-400">Or copy UPI details below</p>
                </div>
            </div>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">After payment</span></div>
            </div>

            {{-- Verify Payment Form --}}
            <form method="POST" action="{{ route('payments.verify', $payment) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">UPI Transaction ID <span class="text-red-500">*</span></label>
                        <input type="text" name="transaction_id" required minlength="6" maxlength="50"
                               placeholder="e.g., 425698712345"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('transaction_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Your UPI ID (optional)</label>
                        <input type="text" name="upi_id"
                               placeholder="e.g., yourname@upi"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold text-sm hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-sm hover:shadow-md">
                        Verify Payment
                    </button>
                </div>
            </form>

            <p class="text-xs text-gray-400 text-center mt-4">
                Payment verification is manual. If there are any issues, please contact support.
            </p>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">← Back to Payments</a>
    </div>
</div>
@endsection
