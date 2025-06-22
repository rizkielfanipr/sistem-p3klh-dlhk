@props([
    'name',
    'type' => 'text',
    'required' => false,
    'value' => '',
    'label' => null
])

<div class="mb-4">
    {{-- Label --}}
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700">
        {{ $label ?? ucwords(str_replace('_', ' ', $name)) }}
    </label>

    {{-- Input --}}
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    />

    {{-- Error --}}
    <x-form.error :name="$name" />
</div>
