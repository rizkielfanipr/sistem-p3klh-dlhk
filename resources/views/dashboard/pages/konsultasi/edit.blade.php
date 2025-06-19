@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Konsultasi ' . ucfirst($jenis))

@section('content')
<form action="{{ route('konsultasi.update', [$jenis, $detail->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <x-form.select 
        name="topik_id" 
        :options="$topik->pluck('nama_topik', 'id')" 
        label="Topik Konsultasi" 
        :value="$detail->topik_id"
        required 
    />

    <x-form.input 
        name="tanggal_konsultasi" 
        label="Tanggal Konsultasi" 
        type="date" 
        :value="$detail->tanggal_konsultasi"
        required 
    />

    <x-form.select 
        name="sesi_konsultasi_id" 
        :options="$sesi->pluck('nama_sesi', 'id')" 
        label="Sesi Konsultasi" 
        :value="$detail->sesi_konsultasi_id"
        required 
    />

    <x-form.textarea 
        name="catatan_konsultasi" 
        label="Catatan Tambahan"
    >{{ $detail->catatan_konsultasi }}</x-form.textarea>

    <x-form.file-upload 
        name="lampiran" 
        label="Ganti Lampiran (Opsional)"
    />

    @if ($detail->lampiran)
        <p class="text-sm text-gray-600">Lampiran sebelumnya: 
            <a href="{{ asset('storage/' . $detail->lampiran->path) }}" class="text-blue-600 underline" target="_blank">
                {{ $detail->lampiran->nama_file }}
            </a>
        </p>
    @endif

    <x-form.button>Perbarui</x-form.button>
</form>
@endsection
