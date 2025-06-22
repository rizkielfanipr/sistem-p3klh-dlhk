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

    @if($jenis !== 'daring')
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
    @endif

    <x-quill-editor
        label="Catatan Konsultasi" 
        name="catatan_konsultasi" 
        :value="old('catatan_konsultasi')" 
        placeholder="Tuliskan catatan tambahan..." 
    />

    <x-form.file-upload 
        name="lampiran" 
        label="Lampiran (Opsional)" 
    />

    <x-form.button>Simpan</x-form.button>
</form>
@endsection
