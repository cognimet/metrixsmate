@extends('layouts.app')
@section('title', 'Payments - ' . __('app.brand'))

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your assessment payments</p>
    </div>
    <a href="{{ route('payments.history') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Payment History
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    @if($hasPaid)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-800">Payment Complete</h3>
                    <p class="text-sm text-green-600">Your payment has been verified. You have full access to all features.</p>
                </div>
            </div>
        </div>
    @endif

    @if(!$hasPaid)
        <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Make a Payment</h2>
                <p class="text-sm text-gray-500 mt-1">Pay securely via PayPal or Razorpay to unlock premium features</p>
            </div>

            <div class="p-6">
                <div class="bg-gradient-to-br from-primary-50 to-primary-100/40 rounded-xl p-6 mb-6 border border-primary-100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-primary-700 bg-primary-100 px-3 py-1 rounded-full">Assessment Pack</span>
                        <div class="text-right">
                            <div id="priceDisplay">
                                <span class="text-3xl font-bold text-gray-900" id="finalPrice">$15.00</span>
                                <span class="text-sm text-gray-500">/one-time</span>
                            </div>
                            <div id="originalPriceRow" class="hidden mt-0.5">
                                <span class="text-sm text-gray-400 line-through" id="originalPrice"></span>
                                <span class="text-xs font-semibold text-green-600 ml-1" id="discountBadge"></span>
                            </div>
                        </div>
                    </div>
                    <ul class="space-y-2.5">
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            OCEAN Personality Assessment
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            RIASEC Career Interest Assessment
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Cognitive Abilities Assessment
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Detailed PDF Reports and Certificate
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            AI School Finder Access
                        </li>
                    </ul>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose payment method</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label id="method-paypal" class="cursor-pointer rounded-xl border border-gray-200 p-4 transition">
                            <input type="radio" class="sr-only" name="paymentMethodOption" value="paypal" checked onchange="setPaymentMethod('paypal')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">PayPal</p>
                                    <p class="text-xs text-gray-500">Global card and wallet checkout</p>
                                </div>
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                            </div>
                        </label>
                        <label id="method-razorpay" class="cursor-pointer rounded-xl border border-gray-200 p-4 transition">
                            <input type="radio" class="sr-only" name="paymentMethodOption" value="razorpay" onchange="setPaymentMethod('razorpay')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Razorpay</p>
                                    <p class="text-xs text-gray-500">Card, wallet, and netbanking checkout</p>
                                </div>
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Have a coupon code?</label>
                    <div class="flex gap-2">
                        <input type="text" id="couponInput" placeholder="Enter coupon code"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm uppercase tracking-wider focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               autocomplete="off" oninput="this.value=this.value.toUpperCase()">
                        <button type="button" onclick="applyCoupon()"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition whitespace-nowrap">
                            Apply
                        </button>
                        <button type="button" id="removeCouponBtn" onclick="removeCoupon()"
                                class="hidden px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-xl transition">
                            X
                        </button>
                    </div>
                    <div id="couponFeedback" class="hidden mt-2 text-sm px-3 py-2 rounded-lg"></div>
                </div>

                <form method="POST" action="{{ route('payments.initiate') }}" id="paymentForm">
                    @csrf
                    <input type="hidden" name="amount" id="paymentAmount" value="15">
                    <input type="hidden" name="coupon_code" id="paymentCoupon" value="">
                    <input type="hidden" name="payment_method" id="paymentMethod" value="paypal">
                    <button type="submit" id="payBtn" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold text-sm hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span id="payBtnLabel">Pay $15.00 with PayPal</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if($payments->isNotEmpty())
        <div class="mt-8 bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Recent Payments</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($payments->take(5) as $payment)
                    @php $currencySymbol = strtoupper($payment->currency) === 'USD' ? '$' : '₹'; @endphp
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $payment->isCompleted() ? 'bg-green-100' : ($payment->isPending() ? 'bg-yellow-100' : 'bg-red-100') }}">
                                @if($payment->isCompleted())
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($payment->isPending())
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $payment->order_id }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($payment->amount, 2) }}</p>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $payment->isCompleted() ? 'bg-green-100 text-green-700' : ($payment->isPending() ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const BASE_PRICE = 15;
const METHOD_LABELS = {
    paypal: 'PayPal',
    razorpay: 'Razorpay',
};

let selectedMethod = 'paypal';

function formatMoney(amount) {
    const value = Number(amount || 0);
    return '$' + value.toFixed(2);
}

function updateMethodCards() {
    const paypalCard = document.getElementById('method-paypal');
    const razorpayCard = document.getElementById('method-razorpay');

    if (!paypalCard || !razorpayCard) {
        return;
    }

    const methods = {
        paypal: paypalCard,
        razorpay: razorpayCard,
    };

    Object.keys(methods).forEach(function (method) {
        const card = methods[method];
        const active = method === selectedMethod;

        card.classList.toggle('ring-2', active);
        card.classList.toggle('ring-primary-500', active);
        card.classList.toggle('border-primary-300', active);
        card.classList.toggle('bg-primary-50', active);
    });
}

function updatePayButton(amount) {
    const payBtnLabel = document.getElementById('payBtnLabel');
    if (!payBtnLabel) {
        return;
    }

    const value = Number(amount || 0);
    if (value <= 0) {
        payBtnLabel.textContent = 'Get Free Access';
        return;
    }

    payBtnLabel.textContent = 'Pay ' + formatMoney(value) + ' with ' + METHOD_LABELS[selectedMethod];
}

function setPaymentMethod(method) {
    selectedMethod = method;
    document.getElementById('paymentMethod').value = method;
    updateMethodCards();
    updatePayButton(document.getElementById('paymentAmount').value);
}

// Pre-fill coupon from URL ?coupon=CODE
(function () {
    const params = new URLSearchParams(window.location.search);
    const code = params.get('coupon');

    updateMethodCards();
    updatePayButton(BASE_PRICE);

    if (code) {
        document.getElementById('couponInput').value = code.toUpperCase();
        applyCoupon();
    }
})();

async function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    const feedback = document.getElementById('couponFeedback');

    if (!code) {
        return;
    }

    feedback.className = 'mt-2 text-sm px-3 py-2 rounded-lg bg-gray-50 text-gray-500';
    feedback.textContent = 'Checking...';
    feedback.classList.remove('hidden');

    try {
        const res = await fetch('{{ route("coupons.apply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ code, amount: BASE_PRICE }),
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('finalPrice').textContent = formatMoney(data.discounted_price);
            document.getElementById('originalPrice').textContent = formatMoney(data.original_price);
            document.getElementById('discountBadge').textContent = data.discount_label;
            document.getElementById('originalPriceRow').classList.remove('hidden');

            document.getElementById('paymentAmount').value = data.discounted_price;
            document.getElementById('paymentCoupon').value = data.coupon_code;
            updatePayButton(data.discounted_price);

            feedback.className = 'mt-2 text-sm px-3 py-2 rounded-lg bg-green-50 text-green-700 border border-green-200';
            feedback.textContent = data.message;

            document.getElementById('couponInput').readOnly = true;
            document.getElementById('removeCouponBtn').classList.remove('hidden');
        } else {
            feedback.className = 'mt-2 text-sm px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200';
            feedback.textContent = data.message;
        }
    } catch {
        feedback.className = 'mt-2 text-sm px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200';
        feedback.textContent = 'Something went wrong. Please try again.';
    }
}

function removeCoupon() {
    document.getElementById('couponInput').value = '';
    document.getElementById('couponInput').readOnly = false;
    document.getElementById('couponFeedback').classList.add('hidden');
    document.getElementById('removeCouponBtn').classList.add('hidden');
    document.getElementById('originalPriceRow').classList.add('hidden');

    document.getElementById('finalPrice').textContent = formatMoney(BASE_PRICE);
    document.getElementById('paymentAmount').value = BASE_PRICE;
    document.getElementById('paymentCoupon').value = '';

    updatePayButton(BASE_PRICE);
}
</script>
@endpush