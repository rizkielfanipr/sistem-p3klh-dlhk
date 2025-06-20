@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Pengguna')

@section('content')
    <div>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <x-form.input
                name="nama"
                label="Nama"
                value="{{ old('nama') }}"
                required
                placeholder="Masukkan Nama"
            />
            <x-form.input
                name="email"
                label="Email"
                type="email"
                value="{{ old('email') }}"
                required
                placeholder="Masukkan Email"
            />
            <x-form.password
                name="password"
                label="Password"
                required
                placeholder="Masukkan Password"
            />
            <x-form.password
                name="password_confirmation"
                label="Konfirmasi Password"
                required
                placeholder="Masukkan Konfirmasi Password"
            />
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
            <x-form.input
                name="no_telp"
                label="No. Telepon"
                value="{{ old('no_telp') }}"
                placeholder="Masukkan No. Telepon"
            />
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
        </form>
    </div>
@endsection
