{{-- resources/views/konsultasi/detail.blade.php --}}

@extends('layouts.user') {{-- Menggunakan layout 'layouts.user' --}}

@section('title', 'Detail Konsultasi') {{-- Menambahkan section title --}}
@section('description', 'Lihat detail lengkap riwayat konsultasi Anda.') {{-- Menambahkan section description --}}

@section('breadcrumb')
    <a href="{{ route('profil.my_profile') }}" class="text-blue-500 hover:text-blue-700">Profil Saya</a>
    <span class="mx-2">/</span>
    Detail Konsultasi
@endsection

@section('content')
<div class="container mx-auto px-4 py-6"> {{-- px-4 py-6 (dikurangi) --}}
    {{-- Menghilangkan shadow-md dari kontainer utama --}}
    <div class="bg-white rounded-lg p-5 md:p-6 lg:p-8 border border-gray-200"> {{-- p-5 md:p-6 lg:p-8 (dikurangi) --}}

        {{-- Header - Menyesuaikan gaya dari layout admin --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4"> {{-- mb-4 (dikurangi) --}}
            <div class="mb-3 md:mb-0"> {{-- mb-3 (dikurangi) --}}
                <span class="inline-block bg-blue-500 text-white text-2xl font-bold px-3 py-1.5 rounded-lg"> {{-- text-2xl, px-3 py-1.5 (dikurangi) --}}
                    <i class="fas fa-barcode mr-2"></i>Kode: {{ $konsultasiDetail->kode_konsultasi ?? 'N/A' }}
                </span>
            </div>
            {{-- Bagian informasi pengguna (foto, nama, email) dihapus di sini --}}
        </div>

        <hr class="my-4 border-gray-200"> {{-- my-4 (dikurangi) --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"> {{-- gap-6 (dikurangi) --}}
            {{-- Detail Kolom - Menyesuaikan gaya dari layout admin --}}
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-3 text-base text-gray-800"> {{-- gap-x-5 gap-y-3 (dikurangi) --}}

                @php
                    $isLuring = strtolower($konsultasiDetail->konsultasi->jenisKonsultasi->nama_jenis ?? '') === 'luring';
                    $fields = [
                        'Jenis Konsultasi' => $isLuring ? 'Konsultasi Luring' : 'Konsultasi Daring',
                        'Topik' => $konsultasiDetail->topik->nama_topik ?? '-',
                        'Tanggal Dibuat' => $konsultasiDetail->konsultasi->created_at->format('d F Y H:i'),
                    ];

                    if ($isLuring && $konsultasiDetail->tanggal_konsultasi) { // Pastikan tanggal_konsultasi tidak null
                        $fields['Tanggal Konsultasi'] = \Carbon\Carbon::parse($konsultasiDetail->tanggal_konsultasi)->format('d F Y');
                        $fields['Sesi'] = $konsultasiDetail->sesi->sesi ?? '-';
                    }
                @endphp

                @foreach ($fields as $label => $value)
                    <div>
                        <span class="font-semibold text-gray-700"><i class="fas fa-circle-info mr-1"></i>{{ $label }}:</span>
                        <p class="mt-1">{{ $value }}</p>
                    </div>
                @endforeach

                {{-- Status - Menyesuaikan gaya dari layout admin --}}
                <div>
                    <span class="font-semibold text-gray-700"><i class="fas fa-flag mr-1"></i>Status:</span>
                    <p class="mt-1">
                        @php
                            $statusName = strtolower($konsultasiDetail->status->nama_status ?? '');
                            $statusClass = match($statusName) {
                                'diajukan' => 'bg-blue-100 text-blue-800',
                                'diproses' => 'bg-yellow-100 text-yellow-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'batal' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($konsultasiDetail->status->nama_status ?? '-') }}
                        </span>
                    </p>
                </div>

                {{-- Catatan Konsultasi - Menyesuaikan gaya dari layout admin --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-sticky-note mr-1"></i>Catatan Konsultasi:</span>
                    <div class="mt-1 text-gray-800 leading-relaxed">
                        {!! $konsultasiDetail->catatan_konsultasi ?? 'Tidak ada catatan detail.' !!}
                    </div>
                </div>

                {{-- Lampiran - Menyesuaikan gaya dari layout admin --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-paperclip mr-1"></i>Lampiran:</span><br>
                    @php
                        $filePath = $konsultasiDetail->lampiran->lampiran ?? null;
                        $fileUrl = $filePath ? Storage::url($filePath) : null;
                        $fileName = $filePath ? basename($filePath) : null;
                        $extension = strtolower(pathinfo($fileName ?? '', PATHINFO_EXTENSION)); // Handle null fileName
                        $iconClass = match ($extension) {
                            'pdf' => 'fa-file-pdf text-red-600',
                            'doc', 'docx' => 'fa-file-word text-blue-600',
                            'xls', 'xlsx' => 'fa-file-excel text-green-600',
                            'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-purple-500',
                            'zip', 'rar' => 'fa-file-archive text-yellow-600',
                            'txt' => 'fa-file-alt text-gray-600',
                            default => 'fa-file text-gray-500',
                        };
                    @endphp

                    @if($filePath)
                        <a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline mt-1 inline-flex items-center">
                            <i class="fas {{ $iconClass }} mr-2 text-lg"></i>
                            {{ $fileName }}
                        </a>
                    @else
                        <p class="mt-1 text-gray-500">- Tidak ada lampiran -</p>
                    @endif
                </div>
            </div>

            {{-- QR Code - Menyesuaikan gaya dari layout admin --}}
            <div class="lg:col-span-1 flex items-center justify-center p-4 bg-gray-50 border rounded-lg">
                {!! QrCode::size(250)->generate(route('user.konsultasi.detail', $konsultasiDetail->id)) !!} {{-- Menggunakan rute detail user --}}
            </div>
        </div>

        {{-- Bagian untuk Menampilkan Riwayat Tindak Lanjut (jika ada) --}}
        {{-- Mengambil hanya tindak lanjut terbaru, mirip dengan admin --}}
        @php
            $latestTindakLanjut = null;
            if ($konsultasiDetail->konsultasi && $konsultasiDetail->konsultasi->tindakLanjut) {
                $latestTindakLanjut = $konsultasiDetail->konsultasi->tindakLanjut->sortByDesc('created_at')->first();
            }
        @endphp

        @if($latestTindakLanjut)
            <div class="mt-6 pt-6 border-t border-gray-200"> {{-- mt-6 pt-6 (dikurangi) --}}
                <h2 class="text-xl font-semibold text-gray-800 mb-3"><i class="fas fa-comment-dots mr-2"></i>Catatan Tindak Lanjut</h2>
                <div class="bg-gray-100 border border-gray-200 rounded-lg p-4"> {{-- p-4 (tetap, cukup baik) --}}
                    <p class="text-sm text-gray-600 mb-1">Pada: {{ \Carbon\Carbon::parse($latestTindakLanjut->created_at)->format('d F Y H:i') }}</p>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! $latestTindakLanjut->catatan_tindaklanjut !!}
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 pt-6 border-t border-gray-200 text-gray-600"> {{-- mt-6 pt-6 (dikurangi) --}}
                <p>Belum ada riwayat tindak lanjut untuk konsultasi ini.</p>
            </div>
        @endif

    </div>
</div>
@endsection
