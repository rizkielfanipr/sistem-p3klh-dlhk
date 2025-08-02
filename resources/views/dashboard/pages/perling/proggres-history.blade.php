{{-- resources/views/dashboard/pages/perling/progress_history.blade.php --}}

@extends('dashboard.layouts.adminlayout')

@section('title', 'Riwayat Progres Dokumen')

@section('content')

<div class="container mx-auto">
    <div class="bg-white p-6 rounded-md border border-gray-100">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-3 md:mb-0">
                <i class="fas fa-history mr-3 text-blue-600 text-3xl"></i>
                <span>Riwayat Progres: <span class="text-blue-700 text-lg">{{ $dokumen->kode_perling ?? 'N/A' }}</span></span>
            </h2>
            <a href="{{ route('perling.detail', $dokumen->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <hr class="my-6 border-gray-200">

        {{-- Progress History List --}}
        @if($progressHistory->isEmpty())
            <div class="text-center py-8 bg-gray-50 rounded-md border border-gray-200">
                <p class="text-gray-600 text-lg font-medium">
                    <i class="fas fa-box-open text-gray-400 mr-2"></i> Belum ada riwayat progres.
                </p>
            </div>
        @else
            {{-- Wrapper untuk seluruh timeline, ini yang menjadi konteks relatif untuk garis vertikal --}}
            <div class="relative pl-6 sm:pl-12">
                {{-- Garis Vertikal untuk Timeline --}}
                {{-- Posisi absolute inset-y-0 akan membuat garis memanjang sepanjang tinggi parent relative ini --}}
                <div class="absolute inset-y-0 left-2.5 sm:left-5 w-0.5 bg-gray-200"></div>

                <div class="space-y-6">
                    @foreach($progressHistory as $progres)
                        {{-- Setiap item progres adalah kontainer relatif untuk dot-nya --}}
                        <div class="relative">
                            {{-- Dot untuk Timeline: Posisinya diatur relatif terhadap div item progres ini (relative parent-nya) --}}
                            {{-- top-0 memastikan dot berada di bagian paling atas dari item progresnya --}}
                            <div class="absolute -left-3 sm:-left-5 top-0 flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white z-10">
                                <i class="fas fa-check-circle text-xs"></i>
                            </div>

                            <div class="bg-white p-4 rounded-md border border-gray-100 ml-3 sm:ml-0">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3">
                                    <p class="text-xs font-medium text-gray-500 flex items-center mb-1 sm:mb-0">
                                        <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                        {{ \Carbon\Carbon::parse($progres->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                                    </p>
                                    @php
                                        $statusColors = [
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Pemeriksaan Administrasi' => 'bg-yellow-100 text-yellow-800',
                                            'Perbaikan Administrasi' => 'bg-red-100 text-red-800',
                                            'Administrasi Lengkap' => 'bg-green-100 text-green-800',
                                            'Pengumuman Publik' => 'bg-emerald-100 text-emerald-800',
                                            'Rapat Koordinasi' => 'bg-indigo-100 text-indigo-800',
                                            'Pemeriksaan Substansi' => 'bg-purple-100 text-purple-800',
                                            'Perbaikan Substansi' => 'bg-orange-100 text-orange-800',
                                            'Substansi Lengkap' => 'bg-teal-100 text-teal-800',
                                            'Proses Penerbitan' => 'bg-cyan-100 text-cyan-800',
                                            'Terbit' => 'bg-lime-100 text-lime-800',
                                        ];
                                        $statusClass = $statusColors[$progres->status->nama_status ?? ''] ?? 'bg-gray-200 text-gray-800';
                                    @endphp
                                    <span class="px-4 py-1 text-sm rounded-full {{ $statusClass }}">
                                        {{ $progres->status->nama_status ?? 'Status' }}
                                    </span>
                                </div>
                                <h3 class="text-base font-semibold text-gray-800 mb-1">{{ $progres->status->nama_status ?? 'Status' }}</h3>
                                <p class="text-gray-700 leading-relaxed text-sm mb-2">
                                    {{ $progres->catatan ?: 'Tidak ada catatan.' }}
                                </p>
                                <div class="mt-2 border-t border-gray-100 pt-2">
                                    <span class="font-semibold text-gray-700 block mb-1 text-xs"><i class="fas fa-paperclip mr-1"></i>Lampiran:</span>
                                    @if($progres->lampiran)
                                        @php
                                            $progresFilePath = $progres->lampiran->lampiran;
                                            $progresFileUrl = asset('storage/' . $progresFilePath);
                                            $statusNama = $progres->status->nama_status ?? 'File';
                                        @endphp
                                        <a href="{{ $progresFileUrl }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                                            <i class="fas fa-download mr-1"></i> Unduh Lampiran ({{ $statusNama }})
                                        </a>
                                    @else
                                        <p class="text-gray-500 text-xs">- Tidak ada lampiran -</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection