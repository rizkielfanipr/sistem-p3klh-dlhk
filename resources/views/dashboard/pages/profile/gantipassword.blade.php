@extends('dashboard.layouts.adminlayout')

@section('title', 'Ganti Password')

@section('content')
    <div class="max-w-full bg-white rounded">

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="text-sm list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profil.password.update') }}" method="POST" class="space-y-6 w-full max-w-lg">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium">Password Lama</label>
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" required class="w-full border p-3 rounded pr-10">
                    <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-3 top-3 text-gray-500">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium">Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required class="w-full border p-3 rounded pr-10">
                    <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-3 top-3 text-gray-500">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="w-full border p-3 rounded pr-10">
                    <button type="button" onclick="togglePassword('new_password_confirmation', this)" class="absolute right-3 top-3 text-gray-500">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Simpan</button>
        </form>
    </div>

    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
