@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Perling')

@section('content')
<div>
    <form action="{{ route('perling.update', $dokumen->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <x-form.input
            name="nama_pemohon"
            label="Nama Pemohon"
            value="{{ old('nama_pemohon', $dokumen->nama_pemohon) }}"
            required
            placeholder="Masukkan Nama Pemohon"
        />

        <x-form.input
            name="nama_usaha"
            label="Nama Usaha"
            value="{{ old('nama_usaha', $dokumen->nama_usaha) }}"
            required
            placeholder="Masukkan Nama Usaha"
        />

        <x-form.input
            name="alamat_usaha"
            label="Alamat Usaha"
            value="{{ old('alamat_usaha', $dokumen->alamat_usaha) }}"
            required
            placeholder="Masukkan Alamat Usaha"
        />

        <x-form.select
            name="jenis_perling_id"
            label="Jenis Perling"
            :options="$jenisPerlingList->pluck('nama_perling', 'id')->toArray()"
            :value="old('jenis_perling_id', $dokumen->jenis_perling_id)"
            required
        />

        @if ($dokumen->lampiran && $dokumen->lampiran->lampiran)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran Saat Ini</label>
                <a href="{{ asset('storage/' . $dokumen->lampiran->lampiran) }}" target="_blank" class="text-blue-600 underline">
                    Lihat Lampiran
                </a>
            </div>
        @endif

        <x-form.file-upload
            name="lampiran"
            label="Unggah Ulang Lampiran (Opsional)"
        />

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
