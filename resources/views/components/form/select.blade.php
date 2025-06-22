@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'required' => false
])

<div class="mb-4">
    {{-- Label --}}
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    {{-- Select --}}
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    >
        <option value="">-- Pilih --</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    {{-- Error Display --}}
    <x-form.error :name="$name" />
</div>
