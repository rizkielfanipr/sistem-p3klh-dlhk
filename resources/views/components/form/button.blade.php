@props(['type' => 'submit', 'variant' => 'primary', 'href' => null])

@if ($href)
    <a href="{{ $href }}"
       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ $slot }}
    </button>
@endif
