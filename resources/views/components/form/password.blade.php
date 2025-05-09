@props(['name', 'label', 'placeholder' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-gray-700 text-sm font-bold mb-2">{{ $label }}</label>
    <div class="relative">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $name }}"
            class="appearance-none border border-gray-200 rounded w-full py-2 pr-8 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            placeholder="{{ $placeholder }}"
            {{ $attributes }}
        >
        <div class="absolute inset-y-0 right-2 flex items-center cursor-pointer">
            <i id="toggle{{ Str::ucfirst($name) }}" class="fas fa-eye text-gray-500 p-3"></i>
        </div>
    </div>
    @error($name)
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<script>
    const toggle{{ Str::ucfirst($name) }} = document.querySelector('#toggle{{ Str::ucfirst($name) }}');
    const {{ $name }}Input = document.querySelector('#{{ $name }}');

    toggle{{ Str::ucfirst($name) }}.addEventListener('click', function (e) {
        const type = {{ $name }}Input.getAttribute('type') === 'password' ? 'text' : 'password';
        {{ $name }}Input.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>