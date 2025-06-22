@props([
    'name',
    'required' => false,
    'label' => null,
    'value' => ''
])

<div class="mb-4">
    {{-- Label --}}
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700">
        {{ $label ?? ucwords(str_replace('_', ' ', $name)) }}
    </label>

    {{-- Textarea --}}
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        rows="4"
    >{{ old($name, $value) }}</textarea>

    {{-- Error --}}
    <x-form.error :name="$name" />
</div>
