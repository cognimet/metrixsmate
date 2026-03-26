@extends('layouts.app')
@section('title', 'Certificate – ' . __('app.brand'))

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Certificate</h1>
    <p class="text-sm text-gray-500 mt-1">Generate and download your assessment certificate</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Not all assessments completed --}}
    @if(!$allCompleted && !$certificate)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
        <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Assessments Incomplete</h3>
        <p class="text-sm text-yellow-700 mb-4">You need to complete all three assessments (OCEAN, RIASEC, and Cognitive) before generating your certificate.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-600 text-white text-sm font-medium rounded-xl hover:bg-yellow-700 transition">
            Go to Dashboard
        </a>
    </div>
    @endif

    {{-- Certificate exists - View/Download --}}
    @if($certificate)
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
        {{-- Certificate Preview --}}
        <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 200 200"><pattern id="cert-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white"/></pattern><rect width="200" height="200" fill="url(#cert-pattern)"/></svg>
            </div>
            <div class="relative">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                </div>
                <p class="text-sm text-primary-200 font-medium mb-1">Certificate of Assessment</p>
                <h2 class="text-2xl font-bold mb-1">{{ $certificate->full_name }}</h2>
                <p class="text-xs text-primary-200">Certificate #{{ $certificate->certificate_number }}</p>
            </div>
        </div>

        {{-- Certificate Details --}}
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $certificate->ocean_score }}%</p>
                    <p class="text-xs text-blue-600 font-medium mt-1">OCEAN</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $certificate->riasec_score }}%</p>
                    <p class="text-xs text-green-600 font-medium mt-1">RIASEC</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-purple-700">{{ $certificate->cognitive_score }}%</p>
                    <p class="text-xs text-purple-600 font-medium mt-1">Cognitive</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Holland Code</p>
                    <p class="text-lg font-bold text-gray-900">{{ $certificate->holland_code }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Top Trait</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $certificate->top_personality_trait }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Cognitive Strength</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $certificate->top_cognitive_strength }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">Issued: {{ $certificate->issued_at->format('F d, Y') }}</p>
                <a href="{{ route('certificates.download', $certificate) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white text-sm font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Verify Link --}}
    <div class="text-center mt-6">
        <p class="text-xs text-gray-400">Share your certificate for verification:</p>
        <div class="mt-2 flex items-center justify-center gap-2" x-data="{ copied: false }">
            <code class="text-xs bg-gray-100 px-3 py-1.5 rounded-lg text-gray-600">{{ url('/verify-certificate?certificate_number=' . $certificate->certificate_number) }}</code>
            <button @click="navigator.clipboard.writeText('{{ url('/verify-certificate?certificate_number=' . $certificate->certificate_number) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="text-xs px-2 py-1 rounded-md transition"
                    :class="copied ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                <span x-show="!copied">Copy</span>
                <span x-show="copied">Copied!</span>
            </button>
        </div>
    </div>

    @elseif($allCompleted)
    {{-- Generate Certificate Form --}}
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 text-center">
            <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Generate Your Certificate</h2>
            <p class="text-sm text-gray-500 mt-1">Congratulations on completing all assessments! Enter your full name as you'd like it to appear on the certificate.</p>
        </div>

        <div class="p-6">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">One-time generation</p>
                        <p class="text-xs text-amber-700 mt-0.5">Please double-check your name. The certificate can only be generated once and cannot be regenerated.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('certificates.generate') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name) }}" required minlength="2" maxlength="100"
                           placeholder="Enter your full name"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    @error('full_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">This name will appear on your certificate exactly as entered.</p>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold text-sm hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Generate Certificate
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
