@extends('layouts.admin')
@section('title', 'User Management')
@section('page-title', 'Users')
@section('page-description', 'Manage user accounts and permissions')

@section('content')
<div class="space-y-4" x-data="userTable()">

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}"
                    class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
            </div>
            <select name="role" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Roles</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <select name="status" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'role', 'status']))
            <a href="{{ route('admin.users') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">Clear</a>
            @endif
        </form>
    </div>

    {{-- Bulk Action Toolbar (visible when rows selected) --}}
    <div x-show="selected.length > 0" x-transition class="bg-red-50 border border-red-200 rounded-xl px-5 py-3 flex items-center justify-between">
        <span class="text-sm font-medium text-red-700"><span x-text="selected.length"></span> user(s) selected</span>
        <div class="flex items-center gap-3">
            <button @click="selected = []" class="text-xs text-gray-500 hover:text-gray-700">Deselect all</button>
            <form :action="'{{ route('admin.users.bulk-delete') }}'" method="POST" @submit.prevent="confirmBulkDelete($el)">
                @csrf
                @method('DELETE')
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Selected
                </button>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" @change="toggleAll($event)"
                                :checked="selected.length === rowIds.length && rowIds.length > 0"
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition" :class="selected.includes({{ $user->id }}) ? 'bg-red-50/40' : ''">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $user->id }}" x-model="selected"
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ substr($user->name, 0, 1) }}</div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $user->isAdmin() ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->isActive() ? 'text-emerald-700' : 'text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->isActive() ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $user->isActive() ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                                    @submit.prevent="if(confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-gray-400">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 px-5 py-3">
        {{ $users->links() }}
    </div>
</div>

@push('scripts')
<script>
function userTable() {
    return {
        selected: [],
        rowIds: @json($users->pluck('id')->toArray()),
        toggleAll(e) {
            this.selected = e.target.checked ? [...this.rowIds] : [];
        },
        confirmBulkDelete(form) {
            if (confirm(`Delete ${this.selected.length} selected user(s)? This cannot be undone.`)) {
                form.submit();
            }
        },
    };
}
</script>
@endpush
@endsection
