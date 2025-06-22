@props([
    'name',
    'required' => false,
    'value' => '',
    'label' => null,
    'placeholder' => '',
])

<div class="mb-4">
    {{-- Label --}}
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700">
        {{ $label ?? ucwords(str_replace('_', ' ', $name)) }}
    </label>

    <div class="relative">
        {{-- Input --}}
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 pr-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />

        {{-- Toggle Icon --}}
        <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
            onclick="togglePassword('{{ $name }}')"
        >
            <i id="icon-{{ $name }}" class="fa-solid fa-eye"></i>
        </div>
    </div>

    {{-- Error --}}
    <x-form.error :name="$name" />
</div>

@once
    @push('scripts')
        <script>
            function togglePassword(id) {
                const input = document.getElementById(id);
                const icon = document.getElementById('icon-' + id);
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        </script>
    @endpush
@endonce
