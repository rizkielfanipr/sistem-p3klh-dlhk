@extends('dashboard.layouts.adminlayout')

@section('title', 'Detail Dokumen Lingkungan')

@section('content')

{{-- Success/Error/Validation Messages --}}
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Sukses!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Validasi Gagal!</strong>
        <span class="block sm:inline">Silakan periksa kembali input Anda.</span>
        <ul class="mt-3 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
@endif

<div class="container mx-auto">
    <div class="bg-white p-8 rounded-lg border border-gray-200 shadow-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div class="mb-4 md:mb-0 flex items-center space-x-4">
                <span class="inline-block bg-green-600 text-white text-3xl font-bold px-4 py-2 rounded-lg shadow-md">
                    <i class="fas fa-barcode mr-2"></i>Kode: {{ $dokumen->kode_perling ?? 'N/A' }}
                </span>
                <a href="{{ route('perling.progress_history', $dokumen->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                    <i class="fas fa-tasks mr-2"></i> Cek Progres
                </a>
            </div>

            <div class="flex items-center space-x-4">
                <img src="{{ $dokumen->user->foto ? asset('storage/' . $dokumen->user->foto) : asset('default-profile.png') }}"
                    class="w-16 h-16 rounded-full object-cover border-2 border-green-300 shadow-md" alt="Foto Profil">
                <div>
                    <div class="font-semibold text-lg text-gray-900">
                        <i class="fas fa-user mr-1"></i>{{ $dokumen->user->nama ?? 'Nama Tidak Tersedia' }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        <i class="fas fa-envelope mr-1"></i>{{ $dokumen->user->email ?? 'Email Tidak Tersedia' }}
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        {{-- Document Information & Attachments --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-base text-gray-800">
                @foreach ([
                    'Nama Pemohon' => ['field' => 'nama_pemohon', 'icon' => 'fas fa-user-check'],
                    'Tanggal Submit' => ['field' => 'created_at', 'icon' => 'fas fa-calendar-plus', 'format' => fn($val) => $val->format('d F Y H:i')],
                    'Nama Usaha' => ['field' => 'nama_usaha', 'icon' => 'fas fa-building'],
                    'Bidang Usaha' => ['field' => 'bidang_usaha', 'icon' => 'fas fa-industry'],
                    'Pemrakarsa' => ['field' => 'pemrakarsa', 'icon' => 'fas fa-user-tie'],
                    'Penanggung Jawab' => ['field' => 'penanggung_jawab', 'icon' => 'fas fa-user-shield'],
                    'Jenis Perling' => ['field' => 'jenisPerling.nama_perling', 'icon' => 'fas fa-layer-group'],
                    'Lokasi Usaha' => ['field' => 'lokasi', 'icon' => 'fas fa-map-marker-alt'],
                ] as $label => $data)
                    <div>
                        <span class="font-semibold text-gray-700"><i class="{{ $data['icon'] }} mr-1"></i>{{ $label }}:</span>
                        <p class="mt-1">
                            @if (isset($data['format']))
                                {{ $data['format'](data_get($dokumen, $data['field'])) }}
                            @else
                                {{ data_get($dokumen, $data['field']) ?? 'N/A' }}
                            @endif
                        </p>
                    </div>
                @endforeach

                <div>
                    <span class="font-semibold text-gray-700"><i class="fas fa-info-circle mr-1"></i>Status Dokumen:</span>
                    <p class="mt-1">
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
                                'Revisi Substansi' => 'bg-cyan-100 text-cyan-800',
                                'Substansi Lengkap' => 'bg-teal-100 text-teal-800',
                                'Proses Penerbitan' => 'bg-cyan-100 text-cyan-800',
                                'Terbit' => 'bg-lime-100 text-lime-800',
                            ];
                            $statusClass = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm rounded-full {{ $statusClass }}">
                            {{ $displayStatus }}
                        </span>
                    </p>
                </div>

                {{-- Original Attachment --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-paperclip mr-1"></i>Lampiran Awal:</span><br>
                    @php
                        $filePath = $dokumen->lampiran->lampiran ?? null;
                        $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
                        $fileName = $filePath ? basename($filePath) : null;
                        $extension = $fileName ? strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) : '';
                        $iconClass = '';

                        switch ($extension) {
                            case 'pdf': $iconClass = 'fa-file-pdf text-red-600'; break;
                            case 'doc': case 'docx': $iconClass = 'fa-file-word text-blue-600'; break;
                            case 'xls': case 'xlsx': $iconClass = 'fa-file-excel text-green-600'; break;
                            case 'ppt': case 'pptx': $iconClass = 'fa-file-powerpoint text-orange-500'; break;
                            case 'jpg': case 'jpeg': case 'png': case 'gif': $iconClass = 'fa-file-image text-blue-500'; break;
                            case 'zip': case 'rar': $iconClass = 'fa-file-archive text-yellow-600'; break;
                            case 'txt': $iconClass = 'fa-file-alt text-gray-600'; break;
                            default: $iconClass = 'fa-file text-gray-500'; break;
                        }
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

                {{-- Revised Document Attachments (Administrasi) --}}
                @if($revisedAdministrasiProgresses->isNotEmpty())
                    <div class="md:col-span-2 mt-4">
                        <span class="font-semibold text-gray-700"><i class="fas fa-file-upload mr-1"></i>Lampiran Perbaikan Administrasi:</span><br>
                        @foreach($revisedAdministrasiProgresses as $revisedProgress)
                            @php
                                $revisedFilePath = $revisedProgress->lampiran->lampiran ?? null;
                                $revisedFileUrl = $revisedFilePath ? asset('storage/' . $revisedFilePath) : null;
                                $revisedFileName = $revisedFilePath ? basename($revisedFilePath) : null;
                                $revisedExtension = $revisedFileName ? strtolower(pathinfo($revisedFileName, PATHINFO_EXTENSION)) : '';
                                $revisedIconClass = '';

                                switch ($revisedExtension) {
                                    case 'pdf': $revisedIconClass = 'fa-file-pdf text-red-600'; break;
                                    case 'doc': case 'docx': $revisedIconClass = 'fa-file-word text-blue-600'; break;
                                    case 'xls': case 'xlsx': $revisedIconClass = 'fa-file-excel text-green-600'; break;
                                    case 'ppt': case 'pptx': $revisedIconClass = 'fa-file-powerpoint text-orange-500'; break;
                                    case 'jpg': case 'jpeg': case 'png': case 'gif': $revisedIconClass = 'fa-file-image text-blue-500'; break;
                                    case 'zip': case 'rar': $revisedIconClass = 'fa-file-archive text-yellow-600'; break;
                                    case 'txt': $revisedIconClass = 'fa-file-alt text-gray-600'; break;
                                    default: $revisedIconClass = 'fa-file text-gray-500'; break;
                                }
                            @endphp
                            @if($revisedFilePath)
                                <div class="mb-2">
                                    <a href="{{ $revisedFileUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline mt-1 inline-flex items-center">
                                        <i class="fas {{ $revisedIconClass }} mr-2 text-lg"></i>
                                        {{ $revisedFileName }} (Diunggah pada: {{ $revisedProgress->created_at->format('d F Y H:i') }})
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Revised Document Attachments (Substansi) --}}
                @if($revisedSubstansiProgresses->isNotEmpty())
                    <div class="md:col-span-2 mt-4">
                        <span class="font-semibold text-gray-700"><i class="fas fa-file-upload mr-1"></i>Lampiran Perbaikan Substansi:</span><br>
                        @foreach($revisedSubstansiProgresses as $revisedProgress)
                            @php
                                $revisedFilePath = $revisedProgress->lampiran->lampiran ?? null;
                                $revisedFileUrl = $revisedFilePath ? asset('storage/' . $revisedFilePath) : null;
                                $revisedFileName = $revisedFilePath ? basename($revisedFilePath) : null;
                                $revisedExtension = $revisedFileName ? strtolower(pathinfo($revisedFileName, PATHINFO_EXTENSION)) : '';
                                $revisedIconClass = '';

                                switch ($revisedExtension) {
                                    case 'pdf': $revisedIconClass = 'fa-file-pdf text-red-600'; break;
                                    case 'doc': case 'docx': $revisedIconClass = 'fa-file-word text-blue-600'; break;
                                    case 'xls': case 'xlsx': $revisedIconClass = 'fa-file-excel text-green-600'; break;
                                    case 'ppt': case 'pptx': $revisedIconClass = 'fa-file-powerpoint text-orange-500'; break;
                                    case 'jpg': case 'jpeg': case 'png': case 'gif': $revisedIconClass = 'fa-file-image text-blue-500'; break;
                                    case 'zip': case 'rar': $revisedIconClass = 'fa-file-archive text-yellow-600'; break;
                                    case 'txt': $revisedIconClass = 'fa-file-alt text-gray-600'; break;
                                    default: $revisedIconClass = 'fa-file text-gray-500'; break;
                                }
                            @endphp
                            @if($revisedFilePath)
                                <div class="mb-2">
                                    <a href="{{ $revisedFileUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline mt-1 inline-flex items-center">
                                        <i class="fas {{ $revisedIconClass }} mr-2 text-lg"></i>
                                        {{ $revisedFileName }} (Diunggah pada: {{ $revisedProgress->created_at->format('d F Y H:i') }})
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- QR Code --}}
            <div class="lg:col-span-1 flex items-center justify-center p-4 bg-gray-50 border rounded-lg">
                {!! QrCode::size(250)->generate($dokumen->kode_perling ?? $dokumen->id) !!}
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        {{-- Pengumuman Publik Section (Adjusted for universal display if pengumuman exists) --}}
        @if($dokumen->pengumuman) {{-- Changed condition here --}}
            <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-blue-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-bullhorn mr-2"></i>Informasi Pengumuman Publik
                    </h3>
                    <p class="text-gray-700 text-sm mt-1">Lihat detail lengkap pengumuman publik terkait dokumen ini.</p>
                </div>
                <a href="{{ route('pengumuman.show', $dokumen->pengumuman->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-external-link-alt mr-2"></i> Lihat Pengumuman
                </a>
            </div>
            <hr class="my-4 border-gray-200">
        @endif

        {{-- Main Form for Notes, Attachment, Status Update Buttons, and now Rapat --}}
        <form action="{{ route('perling.update-status', $dokumen->id) }}" method="POST" enctype="multipart/form-data" class="w-full relative"> {{-- Added relative for button positioning --}}
            @csrf

            {{-- PENTING: Pindahkan include 'pengumuman.blade.php' ke sini, DI DALAM TAG <form> --}}
            @if($statusTerakhir === 'Administrasi Lengkap')
                @include('dashboard.pages.perling.pengumuman')
            @endif

            {{-- Import the 'rapat.blade.php' file (now inside the form) --}}
            {{-- Ini akan membuat inputan jadwal rapat disubmit bersamaan dengan form ini --}}
            @include('dashboard.pages.perling.rapat')

            {{-- Jadwal Rapat Substansi --}}
            @if($dokumen->jadwalRapat)
                <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-indigo-50 flex items-center justify-between mt-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-check mr-2"></i>Jadwal Rapat Substansi
                        </h3>
                        <p class="text-gray-700 text-sm mt-1">Klik untuk melihat detail lengkap rapat koordinasi.</p>
                    </div>
                    <button type="button" id="toggleRapatDetail" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-external-link-alt mr-2"></i> Lihat Detail Rapat
                    </button>
                </div>
                <hr class="my-4 border-gray-200">

                <div id="rapatDetailContent" class="hidden mt-4 p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Detail Rapat Terjadwal</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-base text-gray-800">
                        <div>
                            <span class="font-semibold text-gray-700"><i class="fas fa-calendar-alt mr-1"></i>Tanggal Rapat:</span>
                            <p class="mt-1">{{ \Carbon\Carbon::parse($dokumen->jadwalRapat->tanggal_rapat)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700"><i class="fas fa-clock mr-1"></i>Waktu Rapat:</span>
                            <p class="mt-1">{{ \Carbon\Carbon::parse($dokumen->jadwalRapat->waktu_rapat)->format('H:i') }} WIB</p>
                        </div>
                        <div class="md:col-span-2">
                            <span class="font-semibold text-gray-700"><i class="fas fa-map-marker-alt mr-1"></i>Ruang Rapat:</span>
                            <p class="mt-1">{{ $dokumen->jadwalRapat->ruang_rapat ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Input fields for general notes and attachments (shown conditionally) --}}
            @if(!in_array($statusTerakhir, ['Terbit', 'Diajukan', 'Administrasi Lengkap', 'Pengumuman Publik', 'Perbaikan Administrasi', 'Perbaikan Substansi']))
                <div class="mb-6 p-4 border border-gray-300 rounded-lg bg-gray-50">
                    <x-form.textarea
                        name="catatan"
                        label="Catatan (Opsional)"
                        :value="old('catatan', ($statusTerakhir === 'Pengumuman Publik' && $dokumen->jadwalRapat) ? $dokumen->jadwalRapat->catatan : '')"
                        placeholder="Tuliskan catatan tambahan..."
                        rows="4"
                    />
                    <div class="mt-4">
                        <x-form.file-upload
                            name="lampiran_file"
                            label="Upload Lampiran (Opsional)"
                        />
                    </div>
                </div>
            @endif

            {{-- Status Update Buttons (moved to bottom right and smaller) --}}
            @php
            $buttons = array(
                'Diajukan' => array('status' => 'Pemeriksaan Administrasi', 'color' => 'blue', 'icon' => 'fas fa-clipboard-check', 'text' => 'Pemeriksaan Administrasi'),
                'Pemeriksaan Administrasi' => array(
                    array('status' => 'Administrasi Lengkap', 'color' => 'green', 'icon' => 'fas fa-check-circle', 'text' => 'Administrasi Lengkap'),
                    array('status' => 'Perbaikan Administrasi', 'color' => 'red', 'icon' => 'fas fa-times-circle', 'text' => 'Perbaikan Administrasi'),
                ),
                'Administrasi Lengkap' => array('status' => 'Pengumuman Publik', 'color' => 'emerald', 'icon' => 'fas fa-bullhorn', 'text' => 'Terbitkan Pengumuman Publik'),
                'Pengumuman Publik' => array(
                    array('status' => 'Rapat Koordinasi', 'color' => 'indigo', 'icon' => 'fas fa-users', 'text' => 'Jadwalkan Rapat Koordinasi'),
                ),
                'Rapat Koordinasi' => array(
                    array('status' => 'Pemeriksaan Substansi', 'color' => 'purple', 'icon' => 'fas fa-search', 'text' => 'Pemeriksaan Substansi'),
                ),
                'Pemeriksaan Substansi' => array(
                    array('status' => 'Substansi Lengkap', 'color' => 'green', 'icon' => 'fas fa-file-invoice', 'text' => 'Substansi Lengkap'),
                    array('status' => 'Perbaikan Substansi', 'color' => 'red', 'icon' => 'fas fa-wrench', 'text' => 'Perbaikan Substansi'),
                ),
                'Revisi Substansi' => array('status' => 'Pemeriksaan Substansi', 'color' => 'blue', 'icon' => 'fas fa-undo', 'text' => 'Kembali ke Pemeriksaan Substansi'),
                'Substansi Lengkap' => array('status' => 'Proses Penerbitan', 'color' => 'yellow', 'icon' => 'fas fa-spinner', 'text' => 'Proses Penerbitan'),
                'Proses Penerbitan' => array('status' => 'Terbit', 'color' => 'green', 'icon' => 'fas fa-certificate', 'text' => 'Terbit'),
            );
            @endphp

            @if(in_array($displayStatus, ['Perbaikan Administrasi', 'Perbaikan Substansi']))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Menunggu Dokumen!</strong>
                    <span class="block sm:inline">Pengajuan sedang dalam status {{ $displayStatus }}. Menunggu lampiran perbaikan dari pemohon.</span>
                </div>
            @elseif(isset($buttons[$displayStatus]))
                <div class="flex justify-end mt-4"> {{-- Flex container to push buttons to the right --}}
                    @if(is_array($buttons[$displayStatus][0] ?? null)) {{-- Check if it's an array of buttons --}}
                        <div class="flex flex-col md:flex-row gap-2"> {{-- Smaller gap for smaller buttons --}}
                            @foreach($buttons[$displayStatus] as $button)
                                <button type="submit" name="new_status" value="{{ $button['status'] }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-md shadow-sm transition
                                    @if($button['color'] == 'purple') bg-purple-600 hover:bg-purple-700 focus:ring-purple-500
                                    @elseif($button['color'] == 'blue') bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                    @elseif($button['color'] == 'green') bg-green-600 hover:bg-green-700 focus:ring-green-500
                                    @elseif($button['color'] == 'red') bg-red-600 hover:bg-red-700 focus:ring-red-500
                                    @elseif($button['color'] == 'indigo') bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500
                                    @elseif($button['color'] == 'emerald') bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500
                                    @elseif($button['color'] == 'yellow') bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                    @elseif($button['color'] == 'teal') bg-teal-600 hover:bg-teal-700 focus:ring-teal-500
                                    @elseif($button['color'] == 'orange') bg-orange-600 hover:bg-orange-700 focus:ring-orange-500
                                    @elseif($button['color'] == 'lime') bg-lime-600 hover:bg-lime-700 focus:ring-lime-500
                                    @elseif($button['color'] == 'cyan') bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500
                                    @else bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 @endif
                                    text-white focus:outline-none focus:ring-2 focus:ring-offset-2">
                                    <i class="{{ $button['icon'] }} mr-2"></i> {{ $button['text'] }}
                                </button>
                            @endforeach
                        </div>
                    @else {{-- Single button --}}
                        <button type="submit" name="new_status" value="{{ $buttons[$displayStatus]['status'] }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-md shadow-sm transition
                            @if($buttons[$displayStatus]['color'] == 'purple') bg-purple-600 hover:bg-purple-700 focus:ring-purple-500
                            @elseif($buttons[$displayStatus]['color'] == 'blue') bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                            @elseif($buttons[$displayStatus]['color'] == 'green') bg-green-600 hover:bg-green-700 focus:ring-green-500
                            @elseif($buttons[$displayStatus]['color'] == 'red') bg-red-600 hover:bg-red-700 focus:ring-red-500
                            @elseif($buttons[$displayStatus]['color'] == 'indigo') bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500
                            @elseif($buttons[$displayStatus]['color'] == 'emerald') bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500
                            @elseif($buttons[$displayStatus]['color'] == 'yellow') bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                            @elseif($buttons[$displayStatus]['color'] == 'teal') bg-teal-600 hover:bg-teal-700 focus:ring-teal-500
                            @elseif($buttons[$displayStatus]['color'] == 'orange') bg-orange-600 hover:bg-orange-700 focus:ring-orange-500
                            @elseif($buttons[$displayStatus]['color'] == 'lime') bg-lime-600 hover:bg-lime-700 focus:ring-lime-500
                            @elseif($buttons[$displayStatus]['color'] == 'cyan') bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500
                            @else bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 @endif
                            text-white focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <i class="{{ $buttons[$displayStatus]['icon'] }} mr-2"></i> {{ $buttons[$displayStatus]['text'] }}
                        </button>
                    @endif
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleRapatDetailButton = document.getElementById('toggleRapatDetail');
        const rapatDetailContent = document.getElementById('rapatDetailContent');

        if (toggleRapatDetailButton && rapatDetailContent) {
            toggleRapatDetailButton.addEventListener('click', function () {
                rapatDetailContent.classList.toggle('hidden');
            });
        }
    });
</script>

@endsection