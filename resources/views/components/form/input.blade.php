@props(['name', 'type' => 'text', 'required' => false, 'value' => '', 'label' => null])

@if ($label)
    <x-form.label :for="$name" :value="$label" />
@else
    <x-form.label :for="$name" :value="ucwords(str_replace('_', ' ', $name))" />
@endif

<input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
       value="{{ old($name, $value) }}"
       {{ $required ? 'required' : '' }}
       class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />

<x-form.error :name="$name" />