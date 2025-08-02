@extends('layouts.user')

@section('title', 'Ajukan Konsultasi')
@section('description', 'Isi formulir ini untuk mengajukan permohonan konsultasi baru.')

@section('breadcrumb', 'Ajukan Konsultasi')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200 shadow-md">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-comments text-blue-600 mr-3"></i> Ajukan Konsultasi Baru
        </h1>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $daringType = $jenisKonsultasis->firstWhere('nama_jenis', 'daring');
        $jenisForFormAction = $daringType ? $daringType->nama_jenis : ($jenisKonsultasis->first()->nama_jenis ?? 'default');
    @endphp

    <form action="{{ route('konsultasi.storeForUser', ['jenis' => $jenisForFormAction]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Jenis Konsultasi - MENGGUNAKAN DISPLAY_NAME BARU DARI CONTROLLER --}}
        <x-form.select
            name="jenis_konsultasi_id"
            id="jenis_konsultasi_id"
            :options="$jenisKonsultasis->pluck('display_name', 'id')"
            label="Jenis Konsultasi"
            required
            :selected="old('jenis_konsultasi_id', $daringType->id ?? '')"
        >
            <option value="">Pilih Jenis Konsultasi</option> {{-- Tetap tambahkan opsi default ini --}}
        </x-form.select>


        <x-form.select
            name="topik_id"
            :options="$topiks->pluck('nama_topik', 'id')"
            label="Topik Konsultasi"
            required
        />

        {{-- Tanggal dan Sesi: Secara default akan terlihat, tapi akan disembunyikan oleh JS jika 'daring' dipilih --}}
        <div id="tanggal-sesi-section" class="space-y-6">
            <x-form.input
                name="tanggal_konsultasi"
                label="Tanggal Konsultasi"
                type="date"
                required
            />

            <x-form.select
                name="sesi_konsultasi_id"
                :options="$sesis->pluck('nama_sesi', 'id')"
                label="Sesi Konsultasi"
                required
            />
        </div>

        <x-quill-editor
            label="Catatan Konsultasi"
            name="catatan_konsultasi"
            :value="old('catatan_konsultasi')"
            placeholder="Tuliskan catatan tambahan..."
        />

        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-2 flex items-center">
                <i class="fas fa-paperclip mr-2"></i> Informasi Lampiran (Opsional)
            </h3>
            <p class="mb-2">Anda dapat melampirkan dokumen pendukung untuk permohonan konsultasi Anda. Ini opsional, namun sangat direkomendasikan agar kami dapat lebih memahami kebutuhan Anda sebelum konsultasi.</p>
            <ul class="list-disc pl-5 mb-2">
                <li>Jenis File yang Didukung: PDF (.pdf), Dokumen Word (.doc, .docx), Gambar (.jpg, .jpeg, .png, .gif).</li>
                <li>Batas Ukuran File: Maksimal 2MB per lampiran. Jika file Anda lebih besar, mohon kompres atau berikan tautan ke layanan penyimpanan cloud di bagian "Catatan Konsultasi".</li>
            </ul>
            <p>
                Petunjuk Unggah: Klik tombol "Pilih File" atau "Browse" di bawah ini, lalu pilih file yang ingin Anda lampirkan dari perangkat Anda. Nama file akan muncul setelah dipilih.
            </p>
        </div>

        <x-form.file-upload
            name="lampiran"
            label="Lampiran (Opsional)"
        />

        <div class="text-right">
            <x-form.button type="submit">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Permohonan
            </x-form.button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisKonsultasiSelect = document.getElementById('jenis_konsultasi_id');
            const tanggalSesiSection = document.getElementById('tanggal-sesi-section');
            const tanggalKonsultasiInput = document.getElementById('tanggal_konsultasi');
            const sesiKonsultasiSelect = document.getElementById('sesi_konsultasi_id');

            let daringJenisId = null;
            const jenisKonsultasiData = @json($jenisKonsultasis->keyBy('nama_jenis'));

            if (jenisKonsultasiData.daring) {
                daringJenisId = jenisKonsultasiData.daring.id;
            }

            // Get the unavailable dates for luring consultations
            const unavailableLuringDates = @json($tanggalTidakTersediaLuring ?? []);

            function toggleTanggalSesiFields() {
                if (daringJenisId === null) {
                    console.error("Kesalahan: ID untuk jenis 'daring' tidak ditemukan. Pastikan ada di tabel 'jenis_konsultasi'.");
                    tanggalSesiSection.classList.remove('hidden');
                    tanggalKonsultasiInput.setAttribute('required', 'required');
                    sesiKonsultasiSelect.setAttribute('required', 'required');
                    return;
                }

                if (jenisKonsultasiSelect.value == daringJenisId) {
                    tanggalSesiSection.classList.add('hidden');
                    tanggalKonsultasiInput.removeAttribute('required');
                    tanggalKonsultasiInput.value = '';
                    sesiKonsultasiSelect.removeAttribute('required');
                    sesiKonsultasiSelect.value = '';
                } else {
                    tanggalSesiSection.classList.remove('hidden');
                    tanggalKonsultasiInput.setAttribute('required', 'required');
                    sesiKonsultasiSelect.setAttribute('required', 'required');
                }
            }

            toggleTanggalSesiFields();
            jenisKonsultasiSelect.addEventListener('change', toggleTanggalSesiFields);

            // Add Flatpickr for date input with disabled dates
            flatpickr(tanggalKonsultasiInput, {
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: [
                    // Use unavailableLuringDates for disabling
                    function(date) {
                        return unavailableLuringDates.some(item => date.toISOString().slice(0, 10) === item.date);
                    },
                ],
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Use unavailableLuringDates for displaying reasons
                    const matchingItem = unavailableLuringDates.find(item => dayElem.dateObj.toISOString().slice(0, 10) === item.date);
                    if (matchingItem) {
                        dayElem.innerHTML += "<span class='unavailable-reason' style='font-size: 0.7em; color: #ff0000; display: block;'>(" + matchingItem.reason + ")</span>";
                        dayElem.style.backgroundColor = '#ffe0e0';
                        dayElem.style.cursor = 'not-allowed';
                    }
                }
            });
        });
    </script>
@endsection