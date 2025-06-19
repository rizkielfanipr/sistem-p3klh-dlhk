@extends('dashboard.layouts.adminlayout')

@section('title', 'Ajukan Konsultasi ' . ucfirst($jenis))

@section('content')
<form action="{{ route('konsultasi.store', $jenis) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <x-form.select 
        name="topik_id" 
        :options="$topik->pluck('nama_topik', 'id')" 
        label="Topik Konsultasi" 
        required 
    />

    <x-form.input 
        name="tanggal_konsultasi" 
        label="Tanggal Konsultasi" 
        type="date"
        required 
    />

    <x-form.select 
        name="sesi_konsultasi_id" 
        :options="$sesi->pluck('nama_sesi', 'id')" 
        label="Sesi Konsultasi" 
        required 
    />

    <x-form.textarea 
        name="catatan_konsultasi" 
        label="Catatan Tambahan" 
    />

    <x-form.file-upload 
        name="lampiran" 
        label="Lampiran (Opsional)" 
    />

    <x-form.button>Simpan</x-form.button>
</form>
@endsection
