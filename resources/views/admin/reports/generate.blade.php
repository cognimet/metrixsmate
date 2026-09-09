@extends('layouts.admin')
@section('title', 'Generate Reports')
@section('page-title', 'Generate Reports')
@section('page-description', 'Create premium PDF reports for schools, universities, or companies')

@section('content')
<div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500 font-medium">Total Students</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalStudents }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500 font-medium">Active Students</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeStudents }}</p>
            </div>
        </div>

        {{-- Report Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- School Report --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">School Report</h3>
                            <p class="text-emerald-100 text-xs">Talent & Development</p>
                        </div>
                    </div>
                    <p class="text-emerald-100 text-sm leading-relaxed">Learning styles, foundational skills, early talent identification, teacher action plans, and parent guidance.</p>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <ul class="text-sm text-gray-600 space-y-2 mb-5 flex-1">
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Personality, Career & Cognitive averages</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Performance distribution & benchmarks</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Learning style analysis</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Stream recommendations</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Top students & intervention list</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">&#10003;</span> Teacher & parent action plans</li>
                    </ul>
                    <form method="POST" action="{{ route('admin.reports.school') }}" class="space-y-3">
                        @csrf
                        <input type="text" name="institution_name" placeholder="School Name *" required
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <input type="text" name="user_ids" placeholder="User IDs (comma-separated, or blank for all)"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download School Report
                        </button>
                    </form>
                </div>
            </div>

            {{-- University Report --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6 text-white">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">University Report</h3>
                            <p class="text-indigo-100 text-xs">Career Readiness</p>
                        </div>
                    </div>
                    <p class="text-indigo-100 text-sm leading-relaxed">Career readiness, employability index, specialization alignment, and industry readiness insights.</p>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <ul class="text-sm text-gray-600 space-y-2 mb-5 flex-1">
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Employability Index score</li>
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Career readiness percentage</li>
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Holland Code distribution</li>
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Industry readiness analysis</li>
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Specialization alignment</li>
                        <li class="flex items-center gap-2"><span class="text-indigo-500">&#10003;</span> Placement strategy recommendations</li>
                    </ul>
                    <form method="POST" action="{{ route('admin.reports.university') }}" class="space-y-3">
                        @csrf
                        <input type="text" name="institution_name" placeholder="University Name *" required
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <input type="text" name="user_ids" placeholder="User IDs (comma-separated, or blank for all)"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download University Report
                        </button>
                    </form>
                </div>
            </div>

            {{-- Company Report --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-red-500 to-red-700 p-6 text-white">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Company Report</h3>
                            <p class="text-red-100 text-xs">Workforce Intelligence</p>
                        </div>
                    </div>
                    <p class="text-red-100 text-sm leading-relaxed">Role fitment, performance optimization, upskilling priorities, leadership pipeline, and workforce planning.</p>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <ul class="text-sm text-gray-600 space-y-2 mb-5 flex-1">
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Role fitment analysis</li>
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Team composition breakdown</li>
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Leadership pipeline count</li>
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Innovation champions identification</li>
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Upskilling priorities with gaps</li>
                        <li class="flex items-center gap-2"><span class="text-red-500">&#10003;</span> Strategic workforce recommendations</li>
                    </ul>
                    <form method="POST" action="{{ route('admin.reports.company') }}" class="space-y-3">
                        @csrf
                        <input type="text" name="institution_name" placeholder="Company Name *" required
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <input type="text" name="user_ids" placeholder="User IDs (comma-separated, or blank for all)"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download Company Report
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Instructions --}}
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">How It Works</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <p class="font-semibold text-gray-800 mb-1">1. Choose Report Type</p>
                    <p>Select the report that matches your audience — school administrators, university faculty, or corporate HR teams.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 mb-1">2. Enter Institution Name</p>
                    <p>This appears on the report header. Optionally specify user IDs to generate for a subset of students.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 mb-1">3. Download PDF</p>
                    <p>A comprehensive, professionally formatted PDF is generated instantly with all assessment data, insights, and recommendations.</p>
                </div>
            </div>
        </div>
</div>
@endsection
