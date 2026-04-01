@extends('layouts.app')
@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800">← Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Basic Info --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('name') border-red-500 @enderror" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('email') border-red-500 @enderror" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <input type="text" name="mobile_no" value="{{ old('mobile_no', $user->mobile_no) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                {{-- Account Settings --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) == true ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm font-medium text-gray-700">Account Active</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Unchecking will deactivate the account and log user out</p>
                        </div>
                    </div>
                </div>

                {{-- Search Tokens --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Search Tokens</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Free AI Searches</label>
                            <input type="number" name="free_ai_searches" value="{{ old('free_ai_searches', $user->free_ai_searches ?? 3) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Free Assessment Searches</label>
                            <input type="number" name="free_assessment_searches" value="{{ old('free_assessment_searches', $user->free_assessment_searches ?? 3) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Paid Search Tokens</label>
                            <input type="number" name="paid_search_tokens" value="{{ old('paid_search_tokens', $user->paid_search_tokens ?? 0) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Admin Notes</h2>
                    
                    <div>
                        <textarea name="admin_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('admin_notes') border-red-500 @enderror">{{ old('admin_notes', $user->admin_notes) }}</textarea>
                        @error('admin_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="border-t border-gray-200 pt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
                    <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
