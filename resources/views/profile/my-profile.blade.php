{{-- resources/views/profile/my-profile.blade.php --}}

@extends('layouts.user')

@section('title', 'Profil Saya')
@section('description', 'Kelola informasi profil dan pengaturan akun Anda dengan mudah dan aman.')

@section('breadcrumb', 'Profil')

@section('content')
    <div class="space-y-8 pb-8">

        {{-- Success/Error Messages (tetap di sini agar bisa tampil di halaman utama) --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg" role="alert">
                <p class="font-semibold text-lg mb-1"><i class="fas fa-check-circle mr-2"></i>Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg" role="alert">
                <p class="font-semibold text-lg mb-1"><i class="fas fa-times-circle mr-2"></i>Error!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg" role="alert">
                <p class="font-semibold text-lg mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Validasi Error!</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Profile Picture & Actions Card --}}
            <div class="md:col-span-1 bg-white p-6 rounded-xl border border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="relative w-36 h-36 rounded-full overflow-hidden border-4 border-blue-400 group">
                    <img id="foto-profil"
                        src="{{ $user->foto ? Storage::url($user->foto) : asset('images/default_profile.png') }}"
                        alt="Foto Profil"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full cursor-pointer">
                        <label for="foto" class="text-white text-lg hover:text-blue-200">
                            <i class="fas fa-camera"></i>
                            <span class="sr-only">Upload Foto</span>
                        </label>
                    </div>
                </div>
                {{-- Hidden file input --}}
                <input type="file" name="foto" id="foto" class="hidden" accept="image/*" form="form-profil">

                <h4 class="text-2xl font-bold text-gray-800 mt-4">{{ $user->nama ?? 'Nama Pengguna' }}</h4>
                <p class="text-gray-500 text-base mb-6">{{ $user->email }}</p>

                <button id="hapus-foto" type="button" class="mt-4 w-full px-6 py-3 border border-red-500 text-red-500 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center gap-2 text-base font-semibold">
                    <i class="fas fa-trash-alt"></i> Hapus Foto
                </button>
                <input type="hidden" name="hapus_foto" id="input-hapus-foto" value="0" form="form-profil">
            </div>

            {{-- Profile Details & Account Settings Card --}}
            <div class="md:col-span-2 bg-white p-8 rounded-xl border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Informasi Profil</h3>
                <form id="form-profil" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        {{-- Field Nama --}}
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" id="nama" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('nama') border-red-500 @enderror" placeholder="Masukkan nama lengkap Anda" required>
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Field No. Telepon --}}
                        <div>
                            <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" id="no_telp" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('no_telp') border-red-500 @enderror" placeholder="e.g., 081234567890">
                            @error('no_telp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Field Email --}}
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('email') border-red-500 @enderror" placeholder="email@example.com" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full sm:w-1/2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-lg font-semibold transition duration-200 transform hover:scale-105">
                            Simpan Perubahan
                        </button>
                        {{-- TOMBOL GANTI PASSWORD SEKARANG MEMBUKA MODAL --}}
                        <button type="button" onclick="window.openModal__changePasswordModal('changePasswordModal')" class="w-full sm:w-1/2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-lg font-semibold text-center flex items-center justify-center transition duration-200 transform hover:scale-105">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ini adalah bagian di mana Anda menyertakan file riwayat akun yang baru --}}
        @include('profile.account-history', ['konsultasiHistory' => $konsultasiHistory, 'perlingHistory' => $perlingHistory])

    </div>

    {{-- MODAL UNTUK GANTI PASSWORD --}}
    <x-modal id="changePasswordModal" title="Ganti Password" max-width="md" :show="false">
        {{-- Sertakan form ganti password yang sudah dibuat --}}
        @include('profile.change-password')

        <x-slot name="footer">
            <button type="button" onclick="window.closeModal__changePasswordModal('changePasswordModal')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                Batal
            </button>
            <button type="submit" form="change-password-form" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Simpan Password Baru
            </button>
        </x-slot>
    </x-modal>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoProfil = document.getElementById('foto-profil');
            const inputFoto = document.getElementById('foto');
            const hapusFotoBtn = document.getElementById('hapus-foto');
            const inputHapusFotoHidden = document.getElementById('input-hapus-foto'); // Hidden input for deletion

            // --- Profile Picture Handling ---
            inputFoto.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        fotoProfil.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                    inputHapusFotoHidden.value = "0"; // Reset delete flag if new photo is uploaded
                }
            });

            hapusFotoBtn.addEventListener('click', function() {
                // Set the image back to default
                fotoProfil.src = "{{ asset('images/default_profile.png') }}";
                // Clear the file input
                inputFoto.value = '';
                // Set the hidden input value to 1 to signal deletion
                inputHapusFotoHidden.value = "1";
            });

            // --- Modal Handling for Password Change ---
            // Ini akan menangani kasus di mana ada error validasi dari server
            // agar modal otomatis terbuka kembali dengan pesan error.
            @if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
                window.openModal__changePasswordModal('changePasswordModal');
            @endif
        });
    </script>
    @endpush
@endsection