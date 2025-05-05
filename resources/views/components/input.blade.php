<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name) }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" />
</div>
