@extends('layouts.user') {{-- Menggunakan layout user --}}

@section('title', 'Detail Informasi Layanan') {{-- Menetapkan judul halaman --}}
@section('description', $kategoriLayanan->nama_kategori) {{-- Menetapkan deskripsi halaman hanya dengan nama kategori --}}

@section('breadcrumb', 'Detail Layanan') {{-- Sesuaikan breadcrumb jika diperlukan --}}

@section('content')

<div class="bg-white p-6 rounded-lg border border-gray-200">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-info-circle text-blue-600 mr-3"></i> {{ $kategoriLayanan->nama_kategori }}
        </h1>
        <a href="{{ url('/') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>

    {{-- Content Section --}}
    <div class="grid grid-cols-1 gap-6 md:gap-8 mb-8">
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($layanan)
            <div class="bg-white rounded-lg p-4">
                <div class="prose max-w-none">
                    {!! $layanan->konten_layanan !!}
                </div>
            </div>
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg" role="alert">
                <p class="font-bold">Informasi</p>
                <p>Konten layanan untuk kategori "{{ $kategoriLayanan->nama_kategori }}" belum tersedia.</p>
            </div>
        @endif
    </div>
</div>
@endsection