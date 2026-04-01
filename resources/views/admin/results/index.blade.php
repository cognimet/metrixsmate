@extends('layouts.app')
@section('title', 'Assessment Results')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Assessment Results</h1>
                <p class="text-gray-600 mt-2">View all user assessment results and analytics</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-gray-500 text-sm font-semibold uppercase">Total Assessments</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_assessments']) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-gray-500 text-sm font-semibold uppercase">OCEAN</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['ocean_assessments']) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-gray-500 text-sm font-semibold uppercase">RIASEC</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['riasec_assessments']) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-gray-500 text-sm font-semibold uppercase">Cognitive</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['cognitive_assessments']) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-gray-500 text-sm font-semibold uppercase">Avg Score</div>
                    <div class="text-3xl font-bold text-orange-600 mt-2">{{ round($stats['avg_score'], 1) }}%</div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" placeholder="Search by user name or email..." 
                            value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select name="quiz_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Assessment Types</option>
                        <option value="ocean" {{ request('quiz_type') === 'ocean' ? 'selected' : '' }}>OCEAN</option>
                        <option value="riasec" {{ request('quiz_type') === 'riasec' ? 'selected' : '' }}>RIASEC</option>
                        <option value="cognitive" {{ request('quiz_type') === 'cognitive' ? 'selected' : '' }}>Cognitive</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Assessment Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($results as $result)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $result->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $result->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $result->assessment_type === 'ocean' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $result->assessment_type === 'riasec' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $result->assessment_type === 'cognitive' ? 'bg-purple-100 text-purple-800' : '' }}
                                ">
                                    {{ strtoupper($result->assessment_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: {{ $result->percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ round($result->percentage, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $result->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.results.show', $result) }}" class="text-blue-600 hover:text-blue-800 font-semibold">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No results found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $results->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
