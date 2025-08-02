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

        {{-- New field: Bidang Usaha --}}
        <x-form.input
            name="bidang_usaha"
            label="Bidang Usaha"
            value="{{ old('bidang_usaha') }}"
            required
            placeholder="Masukkan Bidang Usaha"
        />

        {{-- Changed from alamat_usaha to lokasi --}}
        <x-form.input
            name="lokasi"
            label="Lokasi Usaha" {{-- Label changed to reflect 'Lokasi' --}}
            value="{{ old('lokasi') }}"
            required
            placeholder="Masukkan Lokasi Usaha"
        />

        {{-- New field: Pemrakarsa --}}
        <x-form.input
            name="pemrakarsa"
            label="Nama Pemrakarsa"
            value="{{ old('pemrakarsa') }}"
            required
            placeholder="Masukkan Nama Pemrakarsa"
        />

        {{-- New field: Penanggung Jawab --}}
        <x-form.input
            name="penanggung_jawab"
            label="Nama Penanggung Jawab"
            value="{{ old('penanggung_jawab') }}"
            required
            placeholder="Masukkan Nama Penanggung Jawab"
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