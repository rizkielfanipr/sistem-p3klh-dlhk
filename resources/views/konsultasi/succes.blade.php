{{-- resources/views/konsultasi/success.blade.php --}}
@extends('layouts.user') {{-- Assuming you have a user layout --}}

@section('title', 'Permohonan Konsultasi Berhasil Diajukan!')
@section('description', 'Detail permohonan konsultasi Anda.')

@section('breadcrumb')
    <li><a href="{{ route('konsultasi.ajukan') }}" class="text-blue-500 hover:text-blue-700">Ajukan Konsultasi</a></li>
    <span class="mx-2">/</span>
    <li>Permohonan Berhasil</li>
@endsection

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200 shadow-md">
    <div class="text-center mb-6">
        <div class="bg-green-100 p-4 rounded-full inline-block mb-4">
            <i class="fas fa-check-circle text-green-600 text-5xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-green-700 mb-2">Permohonan Konsultasi Berhasil Diajukan!</h1>
        <p class="text-gray-600">Terima kasih telah mengajukan permohonan Anda. Berikut adalah detail permohonan Anda.</p>
    </div>

    <div class="space-y-4">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h2 class="text-xl font-semibold text-blue-800 mb-2 flex items-center">
                <i class="fas fa-clipboard-check mr-2"></i> Detail Permohonan Anda
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p class="font-medium">Kode Permohonan:</p>
                    <p class="text-lg font-bold text-blue-600">{{ $kode_konsultasi }}</p>
                </div>
                <div>
                    <p class="font-medium">Nama Pemohon:</p>
                    <p>{{ $nama_pemohon }}</p>
                </div>
                <div>
                    <p class="font-medium">Nama Usaha/Kegiatan:</p>
                    <p>{{ $nama_usaha }}</p> {{-- This will show 'Tidak Ada' if not set --}}
                </div>
                <div>
                    <p class="font-medium">Jenis Konsultasi:</p>
                    <p>{{ $jenis_konsultasi }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
            <h2 class="text-xl font-semibold text-gray-800 mb-3 flex items-center justify-center">
                <i class="fas fa-qrcode mr-2"></i> Kode QR Permohonan
            </h2>

            <div class="flex justify-center items-center mb-4">
                <canvas id="qrcodeCanvas" class="w-48 h-48 border border-gray-300 p-2 bg-white rounded-lg shadow-sm"></canvas>
            </div>

            <div class="mt-4 text-center">
                <button id="downloadQrCode"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition ease-in-out duration-150">
                    <i class="fas fa-download mr-2"></i> Unduh QR Code
                </button>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('konsultasi.ajukan') }}"
               class="inline-flex items-center px-6 py-3 bg-[#03346E] text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition ease-in-out duration-150 shadow-md">
                <i class="fas fa-arrow-left mr-3"></i> Ajukan Permohonan Lain
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrCodeData = "{{ $kode_konsultasi }}";

        if (qrCodeData) {
            const qrCanvas = document.getElementById('qrcodeCanvas');
            const downloadButton = document.getElementById('downloadQrCode');

            const qr = new QRious({
                element: qrCanvas,
                value: qrCodeData,
                size: 200,
                padding: 10,
                background: 'white',
                foreground: 'black'
            });

            downloadButton.addEventListener('click', function() {
                const dataURL = qrCanvas.toDataURL('image/png');
                const a = document.createElement('a');
                a.href = dataURL;
                a.download = `QR_Code_Konsultasi_${qrCodeData}.png`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        }
    });
</script>
@endpush
@endsection