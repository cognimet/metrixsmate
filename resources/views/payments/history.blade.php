@extends('layouts.app')
@section('title', 'Payment History – ' . __('app.brand'))

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
        <p class="text-sm text-gray-500 mt-1">View all your payment transactions</p>
    </div>
    <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
        ← Back
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    @if($payments->isEmpty())
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No payments yet</h3>
        <p class="text-sm text-gray-500 mb-4">Your payment history will appear here once you make a payment.</p>
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">Make a Payment</a>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
        {{-- Table Header --}}
        <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">
            <div class="col-span-3">Order ID</div>
            <div class="col-span-2">Date</div>
            <div class="col-span-2">Amount</div>
            <div class="col-span-2">Method</div>
            <div class="col-span-3">Status</div>
        </div>

        {{-- Rows --}}
        <div class="divide-y divide-gray-50">
            @foreach($payments as $payment)
            <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-gray-50/50 transition">
                <div class="col-span-3">
                    <p class="text-sm font-medium text-gray-900">{{ $payment->order_id }}</p>
                    @if($payment->transaction_id)
                    <p class="text-xs text-gray-400">TXN: {{ $payment->transaction_id }}</p>
                    @endif
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $payment->created_at->format('h:i A') }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm font-bold text-gray-900">₹{{ number_format($payment->amount, 2) }}</p>
                </div>
                <div class="col-span-2">
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded-md">
                        {{ strtoupper($payment->payment_method ?? 'UPI') }}
                    </span>
                </div>
                <div class="col-span-3">
                    @if($payment->isCompleted())
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Completed
                    </span>
                    @if($payment->paid_at)
                    <p class="text-xs text-gray-400 mt-1">{{ $payment->paid_at->format('M d, h:i A') }}</p>
                    @endif
                    @elseif($payment->isPending())
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-yellow-700 bg-yellow-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        Pending
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        Failed
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
