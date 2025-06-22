@props([
    'type' => 'submit',
    'variant' => 'primary', // primary, secondary, danger, dll jika ingin dikembangkan
    'href' => null
])

@php
    $baseClasses = "inline-flex items-center justify-center rounded-lg px-5 py-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2";

    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }}">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}">
        {{ $slot }}
    </button>
@endif
