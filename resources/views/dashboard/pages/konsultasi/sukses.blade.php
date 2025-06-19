@extends('dashboard.layouts.adminlayout')

@section('title', 'Konsultasi Berhasil Diajukan')

@section('content')
<div class="bg-white p-6 rounded shadow text-center">
    <h2 class="text-2xl font-semibold text-green-600 mb-4">Berhasil Mengajukan Konsultasi {{ ucfirst($jenis) }}</h2>
    <p class="text-gray-700">Kode Konsultasi Anda:</p>
    <div class="text-4xl font-bold text-blue-600 mt-2">{{ $kode }}</div>
    <a href="{{ route('konsultasi.create', $jenis) }}" class="mt-6 inline-block text-sm text-blue-500 hover:underline">Ajukan lagi</a>
</div>
@endsection
