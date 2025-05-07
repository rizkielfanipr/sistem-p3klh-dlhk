@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Pengguna')

@section('content')
    <div>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                    class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('nama')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="appearance-none border border-gray-200 rounded w-full py-2 pr-8 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <div class="absolute inset-y-0 right-2 flex items-center cursor-pointer">
                        <i id="togglePassword" class="fas fa-eye text-gray-500 p-3"></i>
                    </div>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="appearance-none border border-gray-200 rounded w-full py-2 pr-8 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <div class="absolute inset-y-0 right-2 flex items-center cursor-pointer">
                        <i id="toggleConfirmPassword" class="fas fa-eye text-gray-500 p-3"></i>
                    </div>
                </div>
                @error('password_confirmation')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="role_id" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                <select name="role_id" id="role_id" required
                    class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Pilih Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            @if ($role->id == 1)
                                Admin
                            @elseif ($role->id == 2)
                                Front Office
                            @elseif ($role->id == 3)
                                Pengguna
                            @else
                                {{ $role->nama_role }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="no_telp" class="block text-gray-700 text-sm font-bold mb-2">No. Telepon</label>
                <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}"
                    class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('no_telp')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const passwordConfirmation = document.querySelector('#password_confirmation');

        toggleConfirmPassword.addEventListener('click', function (e) {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
@endsection