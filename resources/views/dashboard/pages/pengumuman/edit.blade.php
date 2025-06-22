@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Pengumuman')

@section('content')
    <form
        action="{{ route('pengumuman.update', $pengumuman->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <x-form.input
            name="judul"
            label="Judul Pengumuman"
            required
            :value="$pengumuman->judul"
        />

        {{-- Konten --}}
        <x-form.textarea
            name="konten"
            label="Isi Pengumuman"
            :value="$pengumuman->konten"
            required
        />

        {{-- Lampiran --}}
        <x-form.file-upload 
            name="lampiran" 
            label="Ganti File Lampiran (Opsional)" 
        />

        {{-- Tampilkan Lampiran Sebelumnya --}}
        @php
    $lampiranPath = optional($pengumuman->lampiran)->lampiran;
@endphp

@if ($lampiranPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($lampiranPath))
    <div class="text-sm text-gray-600 mt-2">
        <span class="font-semibold">Lampiran saat ini:</span>
        <a href="{{ Storage::url($lampiranPath) }}" target="_blank" class="text-blue-600 underline">
            {{ basename($lampiranPath) }}
        </a>
    </div>
@else
    <div class="text-sm text-red-600 mt-2">Lampiran sebelumnya tidak ditemukan di server.</div>
@endif


        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-start space-x-4">
            <x-form.button variant="primary">Update</x-form.button>
            <x-form.button href="{{ route('pengumuman.index') }}" variant="secondary" type="button">Batal</x-form.button>
        </div>
    </form>
@endsection
