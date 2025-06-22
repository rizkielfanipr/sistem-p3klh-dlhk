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

        <x-form.select
            name="role_id"
            label="Role"
            :options="[
                1 => 'Admin',
                2 => 'Front Office',
                3 => 'Pengguna'
            ] + $roles->whereNotIn('id', [1,2,3])->pluck('nama_role', 'id')->toArray()"
            value="{{ old('role_id') }}"
            required
        />

        <x-form.input
            name="no_telp"
            label="No. Telepon"
            value="{{ old('no_telp') }}"
            placeholder="Masukkan No. Telepon"
        />

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Simpan
        </button>
    </form>
</div>
@endsection
