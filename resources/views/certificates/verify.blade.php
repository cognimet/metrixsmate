<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate – MetrixsMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'},
                },
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-primary-50/30 flex flex-col">

    {{-- Header --}}
    <header class="border-b border-gray-200/60 bg-white/80 backdrop-blur-lg">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-lg font-bold text-gray-900">MetrixsMate</span>
            </a>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">Certificate Verification</span>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">

            {{-- Search Form --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 text-center">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">Verify Certificate</h1>
                    <p class="text-sm text-gray-500 mt-1">Enter a certificate number to verify its authenticity</p>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('certificates.verify') }}">
                        <div class="flex gap-2">
                            <input type="text" name="certificate_number"
                                   value="{{ request('certificate_number') }}"
                                   placeholder="e.g., MM-2025-A1B2C3D4"
                                   required
                                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            <button type="submit" class="px-5 py-3 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition">
                                Verify
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Results --}}
                @if($searched)
                <div class="border-t border-gray-100">
                    @if($certificate)
                    {{-- Valid Certificate --}}
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-green-800">Certificate Verified</h3>
                                <p class="text-xs text-green-600">This certificate is authentic and valid.</p>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-xl p-5 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Certificate ID</span>
                                <span class="font-semibold text-gray-900">{{ $certificate->certificate_number }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Name</span>
                                <span class="font-semibold text-gray-900">{{ $certificate->full_name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Holland Code</span>
                                <span class="font-bold text-primary-700 bg-primary-100 px-2 py-0.5 rounded">{{ $certificate->holland_code }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Issued On</span>
                                <span class="font-semibold text-gray-900">{{ $certificate->issued_at->format('F d, Y') }}</span>
                            </div>

                            <div class="pt-3 border-t border-green-200">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-blue-700">{{ $certificate->ocean_score }}%</p>
                                        <p class="text-xs text-gray-500">OCEAN</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-green-700">{{ $certificate->riasec_score }}%</p>
                                        <p class="text-xs text-gray-500">RIASEC</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-purple-700">{{ $certificate->cognitive_score }}%</p>
                                        <p class="text-xs text-gray-500">Cognitive</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-green-200 grid grid-cols-2 gap-3">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-0.5">Top Personality Trait</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $certificate->top_personality_trait }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-0.5">Cognitive Strength</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $certificate->top_cognitive_strength }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Invalid Certificate --}}
                    <div class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-red-800">Certificate Not Found</h3>
                                <p class="text-xs text-red-600">No certificate found with the provided ID. Please check the number and try again.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200/60 bg-white py-4 text-center">
        <p class="text-xs text-gray-400">&copy; {{ date('Y') }} MetrixsMate. All rights reserved.</p>
    </footer>
</body>
</html>
