{{-- resources/views/perling/create.blade.php --}}
@extends('layouts.user')

@section('title', 'Ajukan Permohonan Perling')
@section('description', 'Isi formulir ini untuk mengajukan permohonan persetujuan lingkungan.')

@section('breadcrumb', 'Ajukan Permohonan Perling')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200 shadow-md">

    {{-- Tab Navigasi --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-4" aria-label="Tabs">
            <button type="button"
                class="tab-button text-[#03346E] border-b-2 border-[#03346E] whitespace-nowrap py-4 px-1 text-sm font-medium"
                data-target="form-tab"
                data-title="Ajukan Permohonan Perling">
                <i class="fas fa-file-signature mr-2"></i> Ajukan Permohonan Perling
            </button>
            <button type="button"
                class="tab-button text-gray-600 hover:text-green-600 whitespace-nowrap py-4 px-1 text-sm font-medium"
                data-target="track-tab"
                data-title="Lacak Permohonan Perling">
                <i class="fas fa-search-location mr-2"></i> Lacak Permohonan Perling
            </button>
        </nav>
    </div>

    {{-- Judul Dinamis --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 id="tab-title" class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-file-alt text-green-600 mr-3"></i> Ajukan Permohonan Perling
        </h1>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Validasi Error Laravel --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bagian Ajukan --}}
    <div id="form-tab" class="tab-content">
        <form action="{{ route('perling.storeForUser') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <x-form.input
                name="nama_pemohon"
                label="Nama Pemohon"
                type="text"
                placeholder="Masukkan nama pemohon"
                :value="old('nama_pemohon', Auth::user()->name ?? '')"
                required
            />

            <x-form.input
                name="nama_usaha"
                label="Nama Usaha/Kegiatan"
                type="text"
                placeholder="Masukkan nama usaha atau kegiatan"
                :value="old('nama_usaha')"
                required
            />

            <x-form.input
                name="bidang_usaha"
                label="Bidang Usaha/Kegiatan"
                type="text"
                placeholder="Masukkan bidang usaha atau kegiatan"
                :value="old('bidang_usaha')"
                required
            />

            <x-form.input
                name="penanggung_jawab"
                label="Nama Penanggung Jawab"
                type="text"
                placeholder="Masukkan nama penanggung jawab"
                :value="old('penanggung_jawab')"
                required
            />
            
            <x-form.input
                name="pemrakarsa"
                label="Nama Pemrakarsa"
                type="text"
                placeholder="Masukkan nama pemrakarsa"
                :value="old('pemrakarsa')"
                required
            />

            <x-form.textarea
                name="lokasi"
                label="Lokasi Usaha/Kegiatan"
                placeholder="Masukkan lokasi lengkap usaha atau kegiatan"
                :value="old('lokasi')"
                required
            />

            <x-form.select
                name="jenis_perling_id"
                label="Jenis Persetujuan Lingkungan"
                :options="$jenisPerlingList->pluck('nama_perling', 'id')->toArray()"
                required
            >
                <option value="">Pilih Jenis Perling</option>
            </x-form.select>

            {{-- New Section for Lampiran Instructions --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 mb-6" role="alert">
                <h4 class="font-bold mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i> Petunjuk Pengunggahan Lampiran
                </h4>
                <p class="text-sm">
                    Mohon siapkan dokumen-dokumen berikut dan **satukan dalam satu file PDF** sebelum diunggah. Pastikan semua informasi dalam dokumen jelas dan terbaca.
                </p>
                <ul class="list-disc list-inside text-sm mt-3 space-y-1">
                    <li>Fotokopi KTP Pemohon/Penanggung Jawab</li>
                    <li>Surat Permohonan (Contoh format dapat diunduh di [link contoh surat permohonan])</li>
                    <li>Dokumen Teknis Usaha/Kegiatan (misalnya: denah lokasi, deskripsi kegiatan, spesifikasi teknis)</li>
                    <li>Surat Pernyataan Kesanggupan Pengelolaan Lingkungan</li>
                    <li>Dokumen Pendukung Lainnya yang relevan (misalnya: Izin lokasi, Sertifikat Tanah, dll.)</li>
                    <li>Ukuran file maksimal: **10 MB**.</li>
                </ul>
                <p class="text-sm mt-3">
                    Untuk menggabungkan beberapa file PDF, Anda bisa menggunakan layanan online gratis seperti <a href="https://www.ilovepdf.com/merge_pdf" target="_blank" class="text-blue-700 underline hover:text-blue-900">iLovePDF</a> atau <a href="https://smallpdf.com/merge-pdf" target="_blank" class="text-blue-700 underline hover:text-blue-900">Smallpdf</a>.
                </p>
            </div>
            {{-- End New Section for Lampiran Instructions --}}

            <x-form.file-upload
                name="lampiran"
                label="Unggah Dokumen Lampiran (Format PDF)"
                required
            />

            <div class="text-right">
                <x-form.button type="submit">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Permohonan
                </x-form.button>
            </div>
        </form>
    </div>

    {{-- Bagian Lacak --}}
    <div id="track-tab" class="tab-content hidden">
        <div class="mb-6 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-inner border border-blue-200">
            <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                <i class="fas fa-search-dollar mr-2 text-blue-600"></i> Lacak Permohonan Anda
            </h3>
            <label for="search_perling" class="block text-sm font-medium text-gray-700 mb-2">
                Masukkan Kode Perling atau Nama Usaha:
            </label>
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search Input --}}
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-fingerprint text-gray-400"></i>
                    </div>
                    <input type="text" id="search_perling" name="search"
                                placeholder="Contoh: PRL-2025-001 atau PT ABC Jaya"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 shadow-sm transition duration-150 ease-in-out text-gray-800 placeholder-gray-400">
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 w-full sm:w-auto">
                    <button type="button" id="search_button"
                                class="flex-grow sm:flex-none px-6 py-2.5 bg-[#03346E] text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-blue-50 shadow-md transition ease-in-out duration-150 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    <button type="button" id="scan_qr_button_ui"
                                class="flex-grow sm:flex-none px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-green-50 shadow-md transition ease-in-out duration-150 flex items-center justify-center">
                        <i class="fas fa-qrcode mr-2"></i> Scan Kode QR
                    </button>
                </div>
            </div>

            {{-- Hidden input, canvas, status, and preview for QR image upload --}}
            <input type="file" id="qr_image_upload" accept="image/*" class="hidden">
            <canvas id="qr_canvas" class="hidden"></canvas>

            {{-- QR Scan Status & Preview Area --}}
            <div id="qr_feedback_area" class="mt-4 hidden p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div id="qr_upload_status" class="text-sm text-gray-700 italic flex items-center"></div>
                <img id="qr_image_preview" class="mt-3 hidden max-w-full h-auto rounded-lg border border-gray-300" alt="Pratinjau QR Code">
            </div>

        </div>

        <div id="track-results" class="mt-8">
            <p class="text-gray-500 italic text-center py-4">Hasil pencarian akan ditampilkan di sini.</p>
        </div>
    </div>
</div>

{{-- CDN for jsQR library --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.1.0/dist/jsQR.min.js"></script>

{{-- Script --}}
@push('scripts')
<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-target');
            const title = button.getAttribute('data-title');

            // Update judul
            document.getElementById('tab-title').innerHTML =
                `<i class="fas fa-file-alt text-green-600 mr-3"></i> ${title}`;

            // Sembunyikan semua tab content
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));

            // Reset tab button style
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-[#03346E]', 'border-[#03346E]', 'border-b-2');
                btn.classList.add('text-gray-600');
            });

            // Aktifkan tab yang dipilih
            document.getElementById(target).classList.remove('hidden');
            button.classList.add('text-[#03346E]', 'border-b-2', 'border-[#03346E]');
            button.classList.remove('text-gray-600');

            // Jika tab track aktif, kosongkan hasil sebelumnya dan reset QR UI
            if (target === 'track-tab') {
                document.getElementById('track-results').innerHTML = '<p class="text-gray-500 italic text-center py-4">Hasil pencarian akan ditampilkan di sini.</p>';
                document.getElementById('search_perling').value = ''; // Kosongkan input pencarian
                
                // Reset dan sembunyikan area feedback QR
                document.getElementById('qr_upload_status').innerHTML = '';
                document.getElementById('qr_image_preview').classList.add('hidden');
                document.getElementById('qr_image_preview').src = '';
                document.getElementById('qr_feedback_area').classList.add('hidden');
            }
        });
    });

    // Fungsi untuk melakukan pencarian
    function performSearch() {
        let searchQuery = document.getElementById('search_perling').value.trim();
        const trackResultsDiv = document.getElementById('track-results');

        // Sembunyikan area feedback QR saat melakukan pencarian manual
        document.getElementById('qr_feedback_area').classList.add('hidden');
        document.getElementById('qr_upload_status').innerHTML = '';
        document.getElementById('qr_image_preview').classList.add('hidden');
        document.getElementById('qr_image_preview').src = '';


        // Tampilkan loading spinner atau pesan
        trackResultsDiv.innerHTML = '<p class="text-center text-gray-500 py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Mencari...</p>';

        if (searchQuery.length >= 1) { // Minimum 1 karakter untuk mencari
            fetch(`{{ route('perling.track') }}?search=${encodeURIComponent(searchQuery)}`) // encodeURIComponent untuk parameter URL
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}, message: ${text}`) });
                    }
                    return response.text();
                })
                .then(data => {
                    trackResultsDiv.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching track data:', error);
                    trackResultsDiv.innerHTML = `<p class="text-red-500 text-center py-4">Terjadi kesalahan saat mencari. Silakan coba lagi. Detail: ${error.message}</p>`;
                });
        } else {
            trackResultsDiv.innerHTML = '<p class="text-gray-500 italic text-center py-4">Silakan masukkan kode permohonan atau nama usaha untuk mencari.</p>';
        }
    }

    // Handle search when button is clicked
    document.getElementById('search_button').addEventListener('click', performSearch);

    // Allow pressing Enter in the input field to trigger search
    document.getElementById('search_perling').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Mencegah form submit
            performSearch();
        }
    });

    // --- QR Code Image Upload Functionality (triggered by "Scan Kode QR" button) ---
    const scanQrButtonUI = document.getElementById('scan_qr_button_ui'); // This is the visible UI button
    const qrImageUpload = document.getElementById('qr_image_upload'); // This is the hidden file input
    const qrCanvas = document.getElementById('qr_canvas');
    const qrUploadStatus = document.getElementById('qr_upload_status');
    const qrImagePreview = document.getElementById('qr_image_preview');
    const qrFeedbackArea = document.getElementById('qr_feedback_area');
    const ctx = qrCanvas.getContext('2d');

    // Trigger hidden file input when "Scan Kode QR" button is clicked
    scanQrButtonUI.addEventListener('click', () => {
        qrImageUpload.click();
        qrFeedbackArea.classList.remove('hidden'); // Show feedback area
        qrUploadStatus.innerHTML = ''; // Clear previous status
        qrImagePreview.classList.add('hidden'); // Hide previous preview
        qrImagePreview.src = '';
        document.getElementById('search_perling').value = ''; // Clear search field
    });

    // Handle file selection
    qrImageUpload.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) {
            qrUploadStatus.innerHTML = '';
            qrImagePreview.classList.add('hidden');
            qrImagePreview.src = '';
            qrFeedbackArea.classList.add('hidden'); // Hide feedback area if no file
            return;
        }

        if (!file.type.startsWith('image/')) {
            qrUploadStatus.innerHTML = '<span class="text-red-600"><i class="fas fa-times-circle mr-1"></i> File yang diunggah harus berupa gambar.</span>';
            qrImagePreview.classList.add('hidden');
            qrImagePreview.src = '';
            return;
        }

        qrUploadStatus.innerHTML = '<span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i> Memproses gambar QR Code...</span>';
        qrImagePreview.classList.add('hidden');
        qrImagePreview.src = ''; // Clear previous preview

        const reader = new FileReader();

        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Display image preview
                qrImagePreview.src = e.target.result;
                qrImagePreview.classList.remove('hidden');

                // Set canvas dimensions to match image
                qrCanvas.width = img.width;
                qrCanvas.height = img.height;

                // Draw image on canvas
                ctx.drawImage(img, 0, 0, img.width, img.height);

                // Get image data from canvas
                const imageData = ctx.getImageData(0, 0, img.width, img.height);
                try {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        document.getElementById('search_perling').value = code.data;
                        qrUploadStatus.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i> QR Code ditemukan! Data terisi, klik Cari.</span>';
                        // Tidak auto-performSearch di sini, biarkan user klik tombol Cari jika sudah puas.
                        // performSearch(); // Trigger search with QR data (optional, removed for manual trigger)
                    } else {
                        qrUploadStatus.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ditemukan QR Code dalam gambar ini.</span>';
                        document.getElementById('search_perling').value = ''; // Clear search field if no QR found
                    }
                } catch (error) {
                    console.error("Error decoding QR code:", error);
                    qrUploadStatus.innerHTML = '<span class="text-red-600"><i class="fas fa-times-circle mr-1"></i> Terjadi kesalahan saat mendekode QR Code.</span>';
                }
            };
            img.onerror = function() {
                qrUploadStatus.innerHTML = '<span class="text-red-600"><i class="fas fa-times-circle mr-1"></i> Gagal memuat gambar. Pastikan ini adalah file gambar yang valid.</span>';
                qrImagePreview.classList.add('hidden');
                qrImagePreview.src = '';
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
@endsection