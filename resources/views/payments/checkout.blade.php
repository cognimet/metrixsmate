@extends('layouts.app')
@section('title', 'Checkout - ' . __('app.brand'))

@php
    $method = strtolower((string) ($payment->payment_method ?? 'paypal'));
    $currency = strtoupper((string) ($payment->currency ?? 'USD'));
    $currencySymbol = $currency === 'USD' ? '$' : '₹';
    $methodLabel = $method === 'razorpay' ? 'Razorpay' : 'PayPal';
@endphp

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Complete Payment</h1>
    <p class="text-sm text-gray-500 mt-1">Order #{{ $payment->order_id }}</p>
</div>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">

        <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-white text-center">
            <p class="text-sm font-medium text-primary-100 mb-1">Amount to Pay</p>
            <p class="text-4xl font-bold">{{ $currencySymbol }}{{ number_format((float) $payment->amount, 2) }}</p>
            <p class="text-xs text-primary-200 mt-2">Order: {{ $payment->order_id }}</p>
            <span class="inline-flex items-center mt-3 px-2.5 py-1 rounded-full bg-white/15 text-xs font-semibold">Method: {{ $methodLabel }}</span>
        </div>

        <div class="p-6">
            @if($method === 'paypal')
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Pay using PayPal</h3>
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <p class="text-sm text-gray-700">1. Click the PayPal button below.</p>
                        <p class="text-sm text-gray-700">2. Complete payment in PayPal securely.</p>
                        <p class="text-sm text-gray-700">3. Access will be activated automatically after capture.</p>
                    </div>
                </div>

                @if(!empty($gatewayData['is_configured']) && !empty($gatewayData['paypal_order_id']) && !empty($gatewayData['client_id']))
                    <div id="paypal-button-container" class="mb-4"></div>
                    <p id="paypalStatusMessage" class="hidden text-xs rounded-lg px-3 py-2 mb-4"></p>
                @else
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-800">
                        {{ $gatewayData['message'] ?? 'PayPal is not configured yet.' }}
                    </div>
                @endif
            @endif

            @if($method === 'razorpay')
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Pay using Razorpay</h3>
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <p class="text-sm text-gray-700">1. Open Razorpay checkout.</p>
                        <p class="text-sm text-gray-700">2. Complete payment with your preferred method.</p>
                        <p class="text-sm text-gray-700">3. Access will be activated automatically after validation.</p>
                    </div>
                </div>

                @if(!empty($gatewayData['is_configured']))
                    <button type="button" id="payWithRazorpayBtn" class="w-full flex items-center justify-center gap-2 py-3 bg-[#0B72E7] text-white rounded-xl font-semibold text-sm hover:bg-[#0a63c9] transition shadow-sm mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15A8 8 0 015 4.6"/></svg>
                        Pay with Razorpay
                    </button>
                    <p id="gatewayStatusMessage" class="hidden text-xs rounded-lg px-3 py-2 mb-4"></p>
                @else
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-800">
                        {{ $gatewayData['message'] ?? 'Razorpay is not configured yet.' }}
                    </div>
                @endif
            @endif

            {{-- Legacy UPI checkout UI intentionally disabled.
            <div>
                Old UPI deep link and QR-based payment instructions were removed.
            </div>
            --}}

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">Secure Gateway Confirmation</span></div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs text-blue-800">
                    <strong>Important:</strong> This page only accepts gateway-confirmed transactions. Manual transaction entry is disabled.
                </p>
            </div>

            <p class="text-xs text-gray-400 text-center mt-4">
                Payment completion happens only after secure confirmation from Razorpay or PayPal.
            </p>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Back to Payments</a>
    </div>
</div>
@endsection

@if($method === 'razorpay' && !empty($gatewayData['is_configured']))
    @push('scripts')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payButton = document.getElementById('payWithRazorpayBtn');
                const statusMessage = document.getElementById('gatewayStatusMessage');
                if (!payButton || typeof Razorpay === 'undefined') {
                    return;
                }

                function setStatus(text, isError) {
                    if (!statusMessage) {
                        return;
                    }

                    statusMessage.classList.remove('hidden');
                    statusMessage.className = 'text-xs rounded-lg px-3 py-2 mb-4 ' + (isError
                        ? 'text-red-700 bg-red-50 border border-red-200'
                        : 'text-green-700 bg-green-50 border border-green-200');
                    statusMessage.textContent = text;
                }

                async function completeRazorpayPayment(response) {
                    const request = await fetch(@json(route('payments.razorpay.complete', $payment)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                        },
                        body: JSON.stringify({
                            razorpay_order_id: response.razorpay_order_id || '',
                            razorpay_payment_id: response.razorpay_payment_id || '',
                            razorpay_signature: response.razorpay_signature || '',
                        }),
                    });

                    const data = await request.json();

                    if (!request.ok || !data.success) {
                        throw new Error(data.message || 'Unable to confirm Razorpay payment.');
                    }

                    setStatus(data.message || 'Payment completed successfully. Redirecting...', false);
                    window.location.href = data.redirect_url || @json(route('payments.index'));
                }

                payButton.addEventListener('click', function () {
                    payButton.disabled = true;
                    payButton.classList.add('opacity-70', 'cursor-not-allowed');

                    const options = {
                        key: @json($gatewayData['key_id'] ?? ''),
                        amount: @json($gatewayData['amount'] ?? 0),
                        currency: @json($gatewayData['currency'] ?? $currency),
                        name: @json($gatewayData['name'] ?? config('app.name', 'MetrixsMate')),
                        description: @json($gatewayData['description'] ?? 'MetrixsMate Payment'),
                        order_id: @json($gatewayData['razorpay_order_id'] ?? ''),
                        prefill: @json($gatewayData['prefill'] ?? []),
                        theme: {
                            color: '#4f46e5'
                        },
                        modal: {
                            ondismiss: function () {
                                payButton.disabled = false;
                                payButton.classList.remove('opacity-70', 'cursor-not-allowed');
                            }
                        },
                        handler: async function (response) {
                            try {
                                await completeRazorpayPayment(response);
                            } catch (error) {
                                setStatus(error.message || 'Unable to complete payment. Please try again.', true);
                                payButton.disabled = false;
                                payButton.classList.remove('opacity-70', 'cursor-not-allowed');
                            }
                        }
                    };

                    const razorpayCheckout = new Razorpay(options);
                    razorpayCheckout.open();
                });
            });
        </script>
    @endpush
@endif

@if($method === 'paypal' && !empty($gatewayData['is_configured']) && !empty($gatewayData['client_id']) && !empty($gatewayData['paypal_order_id']))
    @push('scripts')
        <script src="https://www.paypal.com/sdk/js?client-id={{ urlencode((string) $gatewayData['client_id']) }}&currency={{ urlencode((string) ($gatewayData['currency'] ?? $currency)) }}&intent=capture"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const paypalMessage = document.getElementById('paypalStatusMessage');
                const buttonContainer = document.getElementById('paypal-button-container');

                if (!buttonContainer || typeof paypal === 'undefined' || !paypal.Buttons) {
                    return;
                }

                function setPayPalMessage(text, isError) {
                    if (!paypalMessage) {
                        return;
                    }

                    paypalMessage.classList.remove('hidden');
                    paypalMessage.className = 'text-xs rounded-lg px-3 py-2 mb-4 ' + (isError
                        ? 'text-red-700 bg-red-50 border border-red-200'
                        : 'text-green-700 bg-green-50 border border-green-200');
                    paypalMessage.textContent = text;
                }

                paypal.Buttons({
                    createOrder: function () {
                        return @json((string) ($gatewayData['paypal_order_id'] ?? ''));
                    },
                    onApprove: async function (data) {
                        const response = await fetch(@json(route('payments.paypal.capture', $payment)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            body: JSON.stringify({
                                paypal_order_id: data.orderID || @json((string) ($gatewayData['paypal_order_id'] ?? '')),
                            }),
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.success) {
                            setPayPalMessage(payload.message || 'Unable to capture PayPal payment.', true);
                            return;
                        }

                        setPayPalMessage(payload.message || 'Payment completed successfully. Redirecting...', false);
                        window.location.href = payload.redirect_url || @json(route('payments.index'));
                    },
                    onError: function () {
                        setPayPalMessage('PayPal checkout failed. Please try again.', true);
                    },
                    onCancel: function () {
                        setPayPalMessage('PayPal checkout was cancelled.', true);
                    }
                }).render('#paypal-button-container');
            });
        </script>
    @endpush
@endif