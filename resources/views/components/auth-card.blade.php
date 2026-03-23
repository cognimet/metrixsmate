@props(['title' => null, 'subtitle' => null])

<div class="bg-white shadow-xl rounded-2xl p-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">{{ $title }}</h2>
        @if($subtitle)
            <p class="mt-2 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>

    {{ $slot }}
</div>
