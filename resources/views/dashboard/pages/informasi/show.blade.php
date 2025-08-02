@extends('dashboard.layouts.adminlayout')

@section('title', 'Detail Informasi')

@section('content')
    <div class="bg-white border border-grey-200 sm:rounded-lg p-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Detail Informasi
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Informasi lengkap tentang {{ $informasi->judul }}.
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Judul
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $informasi->judul }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Deskripsi
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {!! nl2br(e($informasi->description)) !!}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Gambar Cover
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if ($informasi->image)
                            <img src="{{ Storage::url($informasi->image) }}" alt="{{ $informasi->judul }}" class="max-w-xs h-auto rounded-lg shadow-md">
                        @else
                            Tidak ada gambar cover.
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Lampiran
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if ($informasi->lampiran)
                            <a href="{{ Storage::url($informasi->lampiran->lampiran) }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-download mr-2"></i> Unduh Lampiran
                            </a>
                        @else
                            Tidak ada lampiran.
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Tanggal Dibuat
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $informasi->created_at->format('d F Y H:i') }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Terakhir Diperbarui
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $informasi->updated_at->format('d F Y H:i') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 flex items-center space-x-4">
        <x-form.button variant="secondary" href="{{ route('informasi.index') }}">Kembali</x-form.button>
        <x-form.button variant="primary" href="{{ route('informasi.edit', $informasi->id) }}">Edit</x-form.button>
    </div>
@endsection