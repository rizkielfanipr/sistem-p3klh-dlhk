@props(['name', 'required' => false, 'label' => null, 'value' => ''])

@if ($label)
    <x-form.label :for="$name" :value="$label" />
@else
    <x-form.label :for="$name" :value="ucwords(str_replace('_', ' ', $name))" />
@endif

<textarea name="{{ $name }}" id="{{ $name }}"
          {{ $required ? 'required' : '' }}
          class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old($name, $value) }}</textarea>

<x-form.error :name="$name" />