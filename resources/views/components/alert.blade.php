@props(['type' => 'info', 'message'])

@php
$bgColor = match ($type) {
    'success' => 'bg-green-100 border-green-400 text-green-700',
    'error' => 'bg-red-100 border-red-400 text-red-700',
    'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    default => 'bg-blue-100 border-blue-400 text-blue-700',
};
@endphp

<div class="{{ $bgColor }} px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">{{ ucfirst($type) }}!</strong>
    <span class="block sm:inline">{{ $message }}</span>
</div>