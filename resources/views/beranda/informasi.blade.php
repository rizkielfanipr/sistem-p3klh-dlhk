@extends('layouts.user')

@section('title', 'Informasi Pelayanan')
@section('description', 'Dapatkan informasi terbaru penting dari kami.')
@section('breadcrumb', 'Informasi')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-hand-holding-usd text-blue-600 mr-3"></i> Informasi Pelayanan {{-- Icon diubah --}}
        </h1>
    </div>

    <div class="prose max-w-none mb-8">
        <h2 class="text-2xl font-bold text-[#03346E]">Informasi Pelayanan</h2> {{-- Judul sub-section diubah --}}
        <p class="text-gray-600">
            Halaman ini menyediakan berbagai informasi terkait pelayanan persetujuan lingkungan.
    </div>

    {{-- Data informasiItems akan datang dari controller --}}
    @if (!empty($informasiItems) && count($informasiItems) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($informasiItems as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col transition-all duration-200 ease-in-out">
                    @if ($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['judul'] }}" class="mb-4 w-full h-40 object-cover rounded-md">
                    @else
                        <div class="mb-4 w-full h-40 flex items-center justify-center bg-gray-100 rounded-md text-gray-500">
                            <i class="fas fa-image fa-3x"></i>
                        </div>
                    @endif
                    <h3 class="font-bold text-[#03346E] text-lg mb-2">{{ $item['judul'] }}</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        <i class="fas fa-calendar-alt mr-2"></i> Dibuat pada: {{ \Carbon\Carbon::parse($item['created_at'])->locale('id')->isoFormat('D MMMM YYYY') }}
                    </p>
                    <a href="{{ route('user.informasi.show', $item['id']) }}"
                       class="mt-auto inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-[#03346E] rounded-lg hover:bg-[#02264E] transition-colors duration-200 self-start">
                        <i class="fas fa-info-circle mr-2"></i> Lihat Detail
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 text-center py-8">Tidak ada informasi pelayanan yang tersedia saat ini.</p>
    @endif
</div>
@endsection