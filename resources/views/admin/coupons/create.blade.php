@extends('layouts.admin')
@section('title', 'Create Coupon')
@section('page-title', 'Create Coupon')
@section('page-description', 'Add a new discount coupon')

@section('page-actions')
    <a href="{{ route('admin.coupons') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Coupon Code <span class="text-red-600">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g., SAVE20" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg uppercase focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none @error('code') border-red-500 @enderror" required>
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="What is this coupon for?" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Discount</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Discount Type <span class="text-red-600">*</span></label>
                            <select name="discount_type" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none" required>
                                <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Discount Value <span class="text-red-600">*</span></label>
                            <input type="number" name="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0" placeholder="e.g., 20" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Minimum Purchase Amount</label>
                        <input type="number" name="minimum_amount" value="{{ old('minimum_amount', 0) }}" step="0.01" min="0" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Usage Limits</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maximum Uses</label>
                        <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="empty = unlimited" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Uses Per User</label>
                        <input type="number" name="uses_per_user" value="{{ old('uses_per_user', 1) }}" min="1" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date (empty = no expiration)</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
            </div>

            <div class="border-t border-gray-100 pt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Coupon is Active</span>
                </label>
            </div>

            <div class="border-t border-gray-100 pt-6 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Create Coupon</button>
                <a href="{{ route('admin.coupons') }}" class="px-6 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
