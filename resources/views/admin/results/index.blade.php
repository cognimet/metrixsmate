@extends('layouts.admin')
@section('title', 'Assessment Results')
@section('page-title', 'Results')
@section('page-description', 'View all user assessment results')

@section('content')
<div class="space-y-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Completed</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_assessments']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">OCEAN</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['ocean_assessments']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">RIASEC</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['riasec_assessments']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Cognitive</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['cognitive_assessments']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Avg Score</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ round($stats['avg_score'], 1) }}%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">Coupon Uses</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($stats['coupon_uses']) }}</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}"
                    class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
            </div>
            <select name="quiz_type" class="px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition outline-none">
                <option value="">All Types</option>
                <option value="ocean" {{ request('quiz_type') === 'ocean' ? 'selected' : '' }}>OCEAN</option>
                <option value="riasec" {{ request('quiz_type') === 'riasec' ? 'selected' : '' }}>RIASEC</option>
                <option value="cognitive" {{ request('quiz_type') === 'cognitive' ? 'selected' : '' }}>Cognitive</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'quiz_type']))
            <a href="{{ route('admin.results') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">Clear</a>
            @endif
        </form>
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Score</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Completed</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results as $result)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $result->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $result->user->email }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                {{ $result->assessment_type === 'ocean' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $result->assessment_type === 'riasec' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $result->assessment_type === 'cognitive' ? 'bg-purple-50 text-purple-700' : '' }}
                            ">{{ strtoupper($result->assessment_type) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary-500 rounded-full" style="width: {{ $result->percentage }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ round($result->percentage, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $result->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.results.show', $result) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">No results found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 px-5 py-3">
        {{ $results->links() }}
    </div>
</div>
@endsection
