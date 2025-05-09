@props(['name'])

@error($name)
    <p class="text-red-500 text-xs italic">{{ $message }}</p>
@enderror
