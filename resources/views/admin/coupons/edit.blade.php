@extends('layouts.app')
@section('title', 'Edit Coupon - ' . $coupon->code)

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Coupon</h1>
            <a href="{{ route('admin.coupons.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Coupons</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Basic Info --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code <span class="text-red-600">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $coupon->code) }}" placeholder="e.g., SAVE20" class="w-full px-4 py-2 border border-gray-300 rounded-lg uppercase @error('code') border-red-500 @enderror" required disabled>
                            <p class="text-xs text-gray-500 mt-1">Coupon code cannot be changed</p>
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" placeholder="What is this coupon for?" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('description') border-red-500 @enderror">{{ old('description', $coupon->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Discount Settings --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Discount</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type <span class="text-red-600">*</span></label>
                                <select name="discount_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                                    <option value="percentage" {{ old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value <span class="text-red-600">*</span></label>
                                <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01" min="0" placeholder="e.g., 20" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('discount_value') border-red-500 @enderror" required>
                                @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Purchase Amount (Optional)</label>
                            <input type="number" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount ?? 0) }}" step="0.01" min="0" placeholder="₹0 (no minimum)" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @error('minimum_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Usage Limits --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Usage Limits</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Uses (Leave empty for unlimited)</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" min="1" placeholder="e.g., 100" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('max_uses') border-red-500 @enderror">
                            @error('max_uses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 mt-1">Current uses: {{ $coupon->usage_count ?? 0 }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uses Per User (Leave empty for unlimited)</label>
                            <input type="number" name="uses_per_user" value="{{ old('uses_per_user', $coupon->uses_per_user ?? 1) }}" min="1" placeholder="e.g., 1" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('uses_per_user') border-red-500 @enderror">
                            @error('uses_per_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Expiry --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Expiration</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Leave empty for no expiration)</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('expires_at') border-red-500 @enderror">
                        @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm font-medium text-gray-700">Coupon is Active</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Uncheck to deactivate this coupon without deleting it</p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="border-t border-gray-200 pt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Coupon</button>
                    <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
