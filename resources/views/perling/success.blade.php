{{-- resources/views/perling/success.blade.php --}}
@extends('layouts.user') {{-- Assuming you have a user layout --}}

@section('title', 'Permohonan Berhasil Diajukan!')
@section('description', 'Detail permohonan persetujuan lingkungan Anda.')

@section('breadcrumb', 'Permohonan Berhasil')

@section('content')
{{-- Remove max-w-2xl from this div to let the container in layout handle the width --}}
<div class="bg-white p-6 rounded-lg border border-gray-200 shadow-md">
    <div class="text-center mb-6">
        <div class="bg-green-100 p-4 rounded-full inline-block mb-4">
            <i class="fas fa-check-circle text-green-600 text-5xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-green-700 mb-2">Permohonan Berhasil Diajukan!</h1>
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
                    <p class="text-lg font-bold text-blue-600">{{ $kode_perling }}</p>
                </div>
                <div>
                    <p class="font-medium">Nama Pemohon:</p>
                    <p>{{ $nama_pemohon }}</p>
                </div>
                <div>
                    <p class="font-medium">Nama Usaha/Kegiatan:</p>
                    <p>{{ $nama_usaha }}</p>
                </div>
                <div>
                    <p class="font-medium">Jenis Persetujuan Lingkungan:</p>
                    <p>{{ $jenis_perling }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
            <h2 class="text-xl font-semibold text-gray-800 mb-3 flex items-center justify-center">
                <i class="fas fa-qrcode mr-2"></i> Kode QR Permohonan
            </h2>
            <p class="text-sm text-gray-600 mb-4">Gunakan QR Code ini untuk melacak status permohonan Anda.</p>

            <div class="flex justify-center items-center mb-4">
                {{-- Canvas element where QR code will be drawn --}}
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
            <a href="{{ route('perling.ajukan') }}" {{-- Changed to createForUser as per your `createForUser` method --}}
               class="inline-flex items-center px-6 py-3 bg-[#03346E] text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition ease-in-out duration-150 shadow-md">
                <i class="fas fa-arrow-left mr-3"></i> Ajukan Permohonan Lain
            </a>
            {{-- Optionally add a link to track the application --}}
            <a href="{{ route('perling.ajukan') }}#track-tab" {{-- Changed to createForUser --}}
               class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-800 text-lg font-semibold rounded-lg hover:bg-gray-300 transition ease-in-out duration-150 ml-4 shadow-md">
                <i class="fas fa-search-location mr-3"></i> Lacak Permohonan
            </a>
        </div>
    </div>
</div>

{{-- CDN for qrious.js (a simple QR code generator) --}}
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrCodeData = "{{ $kode_perling }}"; // Get the kode_perling from Blade

        if (qrCodeData) {
            const qrCanvas = document.getElementById('qrcodeCanvas');
            const downloadButton = document.getElementById('downloadQrCode');

            // Initialize QRious
            const qr = new QRious({
                element: qrCanvas,
                value: qrCodeData,
                size: 200, // Adjust size as needed, this will be the base resolution
                padding: 10,
                background: 'white', // Background color of the QR code area
                foreground: 'black'  // Foreground color of the QR code
            });

            // Make the QR code downloadable
            downloadButton.addEventListener('click', function() {
                // Get the data URL of the canvas
                const dataURL = qrCanvas.toDataURL('image/png');

                // Create a temporary link element
                const a = document.createElement('a');
                a.href = dataURL;
                a.download = `QR_Code_Perling_${qrCodeData}.png`; // Filename for download
                document.body.appendChild(a); // Append to body is required for Firefox
                a.click(); // Programmatically click the link to trigger download
                document.body.removeChild(a); // Clean up the temporary link
            });
        }
    });
</script>
@endpush
@endsection