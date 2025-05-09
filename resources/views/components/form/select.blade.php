@props(['name', 'options' => [], 'required' => false, 'selected' => '', 'label' => null])

@if ($label)
    <x-form.label :for="$name" :value="$label" />
@else
    <x-form.label :for="$name" :value="ucwords(str_replace('_', ' ', $name))" />
@endif

<select name="{{ $name }}" id="{{ $name }}"
        class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        {{ $required ? 'required' : '' }}>
    <option value="">Pilih {{ $label ?? ucwords(str_replace('_', ' ', $name)) }}</option>
    @foreach ($options as $key => $value)
        <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>
            {{ $value }}
        </option>
    @endforeach
</select>

<x-form.error :name="$name" />