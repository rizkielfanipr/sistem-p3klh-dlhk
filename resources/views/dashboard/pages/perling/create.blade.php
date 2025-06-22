@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Perling')

@section('content')
<div>
    <form action="{{ route('perling.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <x-form.input
            name="nama_pemohon"
            label="Nama Pemohon"
            value="{{ old('nama_pemohon') }}"
            required
            placeholder="Masukkan Nama Pemohon"
        />

        <x-form.input
            name="nama_usaha"
            label="Nama Usaha"
            value="{{ old('nama_usaha') }}"
            required
            placeholder="Masukkan Nama Usaha"
        />

        <x-form.input
            name="alamat_usaha"
            label="Alamat Usaha"
            value="{{ old('alamat_usaha') }}"
            required
            placeholder="Masukkan Alamat Usaha"
        />

        <x-form.select
            name="jenis_perling_id"
            label="Jenis Perling"
            :options="$jenisPerlingList->pluck('nama_perling', 'id')->toArray()"
            :value="old('jenis_perling_id')"
            required
        />

        <x-form.file-upload
            name="lampiran"
            label="Lampiran"
        />

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Simpan
        </button>
    </form>
</div>
@endsection
