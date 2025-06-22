@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Konsultasi ' . ucfirst($jenis))

@section('content')
<form action="{{ route('konsultasi.update', [$jenis, $detail->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Topik --}}
    <x-form.select 
        name="topik_id" 
        :options="$topik->pluck('nama_topik', 'id')" 
        label="Topik Konsultasi" 
        :value="old('topik_id', $detail->topik_id)"
        required 
    />

    @if($jenis !== 'daring')
        {{-- Tanggal --}}
        <x-form.input 
            name="tanggal_konsultasi" 
            label="Tanggal Konsultasi" 
            type="date" 
            :value="old('tanggal_konsultasi', $detail->tanggal_konsultasi)"
            required 
        />

        {{-- Sesi --}}
        <x-form.select 
            name="sesi_konsultasi_id" 
            :options="$sesi->pluck('nama_sesi', 'id')" 
            label="Sesi Konsultasi" 
            :value="old('sesi_konsultasi_id', $detail->sesi_konsultasi_id)"
            required 
        />
    @endif

    {{-- Catatan --}}
    <x-quill-editor 
        label="Catatan Konsultasi"
        name="catatan_konsultasi" 
        :value="old('catatan_konsultasi', $detail->catatan_konsultasi)" 
        placeholder="Tuliskan catatan tambahan..."
    />

    {{-- Lampiran baru --}}
    <x-form.file-upload 
        name="lampiran" 
        label="Ganti Lampiran (Opsional)"
    />

    {{-- Lampiran lama --}}
    @if ($detail->lampiran)
        <p class="text-sm text-gray-600">
            Lampiran sebelumnya: 
            <a href="{{ asset('storage/' . $detail->lampiran->lampiran) }}" class="text-blue-600 underline" target="_blank">
                {{ basename($detail->lampiran->lampiran) }}
            </a>
        </p>
    @endif

    <x-form.button>Perbarui</x-form.button>
</form>
@endsection
