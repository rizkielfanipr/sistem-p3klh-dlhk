@props(['name', 'label'])

<x-form.label :for="$name" :value="$label" />

<input type="file" name="{{ $name }}" id="{{ $name }}"
       class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

<div class="mt-2">
    <img id="preview" src="#" alt="Preview" class="hidden h-20 rounded">
</div>

<x-form.error :name="$name" />