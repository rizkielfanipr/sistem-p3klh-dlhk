{{-- resources/views/profile/partials/change-password-form.blade.php --}}

<form id="change-password-form" method="POST" action="{{ route('profil.password.update') }}">
    @csrf

    <div class="space-y-6">
        {{-- Password Saat Ini --}}
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
            <div class="relative">
                <input id="current_password" name="current_password" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 pr-10 @error('current_password') border-red-500 @enderror" required autocomplete="current-password">
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500" onclick="togglePasswordVisibility('current_password', this)">
                    <i class="fas fa-eye"></i> {{-- Icon default: eye --}}
                </span>
            </div>
            @error('current_password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
            <div class="relative">
                <input id="new_password" name="new_password" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 pr-10 @error('new_password') border-red-500 @enderror" required autocomplete="new-password">
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500" onclick="togglePasswordVisibility('new_password', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('new_password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password Baru --}}
        <div>
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
            <div class="relative">
                <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 pr-10 @error('new_password_confirmation') border-red-500 @enderror" required autocomplete="new-password">
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('new_password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</form>

<script>
    // Pastikan Anda sudah memuat Font Awesome CSS di layout Anda.
    // Contoh: <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    /**
     * Mengubah tipe input password menjadi teks atau sebaliknya,
     * dan mengubah ikon mata (eye/eye-slash).
     * @param {string} inputId ID dari elemen input password.
     * @param {HTMLElement} iconContainer Elemen span yang berisi ikon.
     */
    function togglePasswordVisibility(inputId, iconContainer) {
        const passwordInput = document.getElementById(inputId);
        const icon = iconContainer.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>