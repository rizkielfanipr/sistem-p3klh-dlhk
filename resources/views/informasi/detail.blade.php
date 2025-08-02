@extends('layouts.user') {{-- Menggunakan layout user --}}

@section('title', $informasi->judul) {{-- Menetapkan judul halaman --}}
@section('description', 'Detail Informasi') {{-- Deskripsi dari isi informasi --}}

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Beranda</a>
    <span class="mx-2">/</span>
    <a href="{{ route('beranda.informasi') }}#informasi-section" class="text-blue-600 hover:underline">Informasi</a>
    <span class="mx-2">/</span>
    <span>Detail Informasi</span>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white border border-grey-200 rounded-xl overflow-hidden mb-8">
        <div class="p-6 md:p-8">
            <h1 class="text-4xl font-extrabold text-[#03346E] mb-4 leading-tight">{{ $informasi->judul }}</h1>

            <div class="flex items-center text-gray-600 text-sm mb-6">
                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($informasi->created_at)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB</span>
            </div>

            @if ($informasi->image)
                {{-- Efek hover (transform transition-transform duration-500 hover:scale-105) dihilangkan --}}
                <div class="mb-8 overflow-hidden rounded-lg shadow-md">
                    <img src="{{ Storage::url($informasi->image) }}" alt="Gambar Informasi: {{ $informasi->judul }}" class="w-full h-64 object-cover object-center">
                </div>
            @endif

            <div class="prose max-w-none text-gray-800 leading-relaxed text-lg mb-8" style="font-family: 'Open Sans', sans-serif;">
                {!! $informasi->description !!} {{-- Konten deskripsi informasi (diasumsikan HTML) --}}
            </div>

            @if ($informasi->lampiran)
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg flex items-center justify-between flex-wrap gap-4" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-paperclip text-2xl mr-3"></i>
                        <p class="font-semibold">Lampiran Tersedia:</p>
                        <span class="ml-2 text-sm text-blue-700">
                            {{ basename(Storage::url($informasi->lampiran->lampiran)) ?? $informasi->lampiran->lampiran }}
                        </span>
                    </div>
                    <div>
                        <a href="{{ Storage::url($informasi->lampiran->lampiran) }}" target="_blank" class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-[#03346E] hover:bg-[#02264E] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#03346E]">
                            <i class="fas fa-download mr-2"></i> Unduh Lampiran
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Tombol "Kembali ke Daftar Informasi" dihilangkan --}}
</div>
@endsection