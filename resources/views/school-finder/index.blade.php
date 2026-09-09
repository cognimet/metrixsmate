@extends('layouts.app')
@section('title', 'School Finder – ' . __('app.brand'))

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">AI School Finder</h1>
    <p class="text-sm text-gray-500 mt-1">Find schools that match your assessment profile</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    @if(!$allCompleted)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
        <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Assessments Required</h3>
        <p class="text-sm text-yellow-700 mb-4">Complete all three assessments (OCEAN, RIASEC, Cognitive) to get personalized school recommendations.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-600 text-white text-sm font-medium rounded-xl hover:bg-yellow-700 transition">
            Go to Dashboard
        </a>
    </div>
    @else

    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden" x-data="schoolFinder()">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Find Your Ideal School</h2>
                    <p class="text-sm text-gray-500">Select your location to get AI-powered recommendations</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('school-finder.search') }}" class="p-6 space-y-5">
            @csrf

            {{-- Country --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                <select x-model="selectedCountry" @change="fetchStates()" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                    <option value="">Select a country</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- State --}}
            <div x-show="states.length > 0" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">State / Province</label>
                <select x-model="selectedState" @change="fetchCities()" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                    <option value="">Select a state</option>
                    <template x-for="state in states" :key="state.id">
                        <option :value="state.id" x-text="state.name"></option>
                    </template>
                </select>
            </div>

            {{-- City --}}
            <div x-show="cities.length > 0" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                <select x-model="selectedCity" name="city_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-white">
                    <option value="">Select a city</option>
                    <template x-for="city in cities" :key="city.id">
                        <option :value="city.id" x-text="city.name"></option>
                    </template>
                </select>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="flex items-center justify-center py-4">
                <svg class="animate-spin h-5 w-5 text-primary-500 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm text-gray-500">Loading...</span>
            </div>

            {{-- Submit --}}
            <button type="submit" :disabled="!selectedCity" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold text-sm hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>
                Find Matching Schools
            </button>
        </form>
    </div>

    {{-- How It Works --}}
    <div class="mt-8 bg-white rounded-2xl shadow-card border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">How It Works</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold text-blue-700">1</div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Select Location</p>
                    <p class="text-xs text-gray-500">Choose your preferred country, state, and city</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold text-green-700">2</div>
                <div>
                    <p class="text-sm font-medium text-gray-900">AI Analysis</p>
                    <p class="text-xs text-gray-500">We match your RIASEC, OCEAN & cognitive profiles</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold text-purple-700">3</div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Get Results</p>
                    <p class="text-xs text-gray-500">View ranked schools with compatibility scores</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function schoolFinder() {
    return {
        selectedCountry: '',
        selectedState: '',
        selectedCity: '',
        states: [],
        cities: [],
        loading: false,

        async fetchStates() {
            this.states = [];
            this.cities = [];
            this.selectedState = '';
            this.selectedCity = '';

            if (!this.selectedCountry) return;

            this.loading = true;
            try {
                const response = await fetch(`/school-finder/states/${this.selectedCountry}`);
                this.states = await response.json();
            } catch (error) {
                console.error('Failed to fetch states:', error);
            }
            this.loading = false;
        },

        async fetchCities() {
            this.cities = [];
            this.selectedCity = '';

            if (!this.selectedState) return;

            this.loading = true;
            try {
                const response = await fetch(`/school-finder/cities/${this.selectedState}`);
                this.cities = await response.json();
            } catch (error) {
                console.error('Failed to fetch cities:', error);
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
