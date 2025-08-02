<div class="w-full md:w-1/3 relative {{ $attributes->get('class') }}">
    <input type="text"
           id="{{ $id }}"
           onkeyup="{{ $onkeyup }}"
           placeholder="{{ $placeholder }}"
           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <i class="fas fa-search text-gray-400"></i>
    </div>
</div>