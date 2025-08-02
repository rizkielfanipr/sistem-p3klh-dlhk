{{-- resources/views/perling/detail.blade.php --}}

@extends('layouts.user')

@section('title', 'Detail Permohonan Perling')
@section('description', 'Lihat detail lengkap permohonan Persetujuan Lingkungan Anda.')

@section('breadcrumb')
    <a href="{{ route('profil.my_profile') }}" class="text-blue-500 hover:text-blue-700">Profil Saya</a>
    <span class="mx-2">/</span>
    Detail Permohonan Perling
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg p-5 md:p-6 lg:p-8 border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <div class="mb-3 md:mb-0">
                <span class="inline-block bg-green-600 text-white text-2xl font-bold px-3 py-1.5 rounded-lg">
                    <i class="fas fa-barcode mr-2"></i>Kode: {{ $dokumen->kode_perling ?? 'N/A' }}
                </span>
            </div>
            {{-- Bagian informasi pengguna (foto, nama, email) dihapus --}}
        </div>

        <hr class="my-4 border-gray-200">

        {{-- Document Information & Attachments --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-3 text-base text-gray-800">
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
                                'Rapat Koordinasi' => 'bg-blue-200 text-blue-900',
                                'Perbaikan Substansi' => 'bg-red-200 text-red-900', // Ensure this is present
                                'Substansi Lengkap' => 'bg-green-200 text-green-900',
                                'Proses Penerbitan' => 'bg-yellow-200 text-yellow-900',
                                'Terbit' => 'bg-lime-200 text-lime-900',
                                'Dokumen Direvisi' => 'bg-purple-100 text-purple-800',
                            ];
                            $statusClass = $statusColors[$statusTerakhir] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm rounded-full {{ $statusClass }}">
                            {{ $statusTerakhir }}
                        </span>
                    </p>
                </div>

                {{-- Display "Lampiran Awal" / Lampiran Utama Dokumen --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-paperclip mr-1"></i>Lampiran Utama Dokumen:</span><br>
                    @php
                        $filePath = $dokumen->lampiran->lampiran ?? null;
                        $fileUrl = $filePath ? Storage::url($filePath) : null;
                        $fileName = $filePath ? basename($filePath) : null;
                        $extension = $fileName ? strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) : '';
                        $iconClass = match ($extension) {
                            'pdf' => 'fa-file-pdf text-red-600',
                            'doc', 'docx' => 'fa-file-word text-blue-600',
                            'xls', 'xlsx' => 'fa-file-excel text-green-600',
                            'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-blue-500',
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
                        <p class="mt-1 text-gray-500">- Tidak ada lampiran utama -</p>
                    @endif
                </div>

                {{-- Display "Lampiran Perbaikan Administrasi" (latest uploaded revision) if exists --}}
                @if($latestRevisionFile)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-700"><i class="fas fa-paperclip mr-1"></i>Lampiran Dokumen Revisi (Terakhir Diunggah):</span><br>
                        @php
                            $revisionFilePath = $latestRevisionFile->lampiran;
                            $revisionFileUrl = Storage::url($revisionFilePath);
                            $revisionFileName = basename($revisionFilePath);
                            $revisionExtension = strtolower(pathinfo($revisionFileName, PATHINFO_EXTENSION));
                            $revisionIconClass = match ($revisionExtension) {
                                'pdf' => 'fa-file-pdf text-red-600',
                                'doc', 'docx' => 'fa-file-word text-blue-600',
                                'xls', 'xlsx' => 'fa-file-excel text-green-600',
                                'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                                'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-blue-500',
                                'zip', 'rar' => 'fa-file-archive text-yellow-600',
                                'txt' => 'fa-file-alt text-gray-600',
                                default => 'fa-file text-gray-500',
                            };
                        @endphp
                        <a href="{{ $revisionFileUrl }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline mt-1 inline-flex items-center">
                            <i class="fas {{ $revisionIconClass }} mr-2 text-lg"></i>
                            {{ $revisionFileName }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- QR Code --}}
            <div class="lg:col-span-1 flex items-center justify-center p-4 bg-gray-50 border rounded-lg">
                {!! QrCode::size(250)->generate(route('user.perling.detail', $dokumen->id)) !!}
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        {{-- Section for Uploading Revision --}}
        {{-- Display if status is 'Perbaikan Administrasi' OR 'Perbaikan Substansi' --}}
        @if (in_array($statusTerakhir, ['Perbaikan Administrasi', 'Perbaikan Substansi']))
            <div class="mb-6 p-5 border border-red-400 rounded-lg bg-red-50">
                <h3 class="text-lg font-semibold text-red-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Dokumen Membutuhkan Perbaikan
                </h3>
                <p class="text-gray-700 mb-3">
                    Mohon unggah dokumen revisi Anda sesuai dengan catatan perbaikan berikut:
                </p>
                <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded-md mb-4">
                    <p class="font-semibold">Catatan Perbaikan:</p>
                    <p class="mt-1">{{ $catatanPerbaikan ?? 'Tidak ada catatan spesifik dari admin.' }}</p>
                </div>

                <form action="{{ route('user.perling.upload_revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="revised_lampiran" class="block text-sm font-medium text-gray-700 mb-1">Pilih File Revisi (PDF, DOCX, XLSX, JPG, PNG):</label>
                        <input type="file" name="revised_lampiran" id="revised_lampiran" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('revised_lampiran')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-upload mr-2"></i> Unggah Revisi
                    </button>
                </form>
            </div>
            <hr class="my-6 border-gray-200">
        @endif

        {{-- Jadwal Rapat Section --}}
        @if(in_array($statusTerakhir, ['Rapat Koordinasi', 'Substansi Lengkap', 'Perbaikan Substansi', 'Proses Penerbitan', 'Terbit', 'Dokumen Direvisi']))
            <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-blue-50"> {{-- Matched outer container style --}}
                <div class="flex items-center justify-between mb-2"> {{-- Adjusted for title and button alignment --}}
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>Jadwal Rapat Substansi
                    </h3>
                    <button id="toggleRapatDetail" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        Lihat Detail Rapat
                        <i id="rapatIcon" class="fas fa-chevron-down ml-2"></i>
                    </button>
                </div>

                <div id="rapatDetail" class="mt-4 hidden transition-all duration-300 ease-in-out transform origin-top"> {{-- Removed extra padding and border from inner div --}}
                    @if($dokumen->jadwalRapat)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-base text-gray-800 p-2"> {{-- Added a bit of padding here --}}
                            <div>
                                <span class="font-semibold text-gray-700"><i class="fas fa-calendar-day mr-2"></i>Tanggal Rapat:</span>
                                <p class="mt-1">{{ \Carbon\Carbon::parse($dokumen->jadwalRapat->tanggal_rapat)->format('d F Y') }}</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700"><i class="fas fa-clock mr-2"></i>Waktu Rapat:</span>
                                <p class="mt-1">{{ \Carbon\Carbon::parse($dokumen->jadwalRapat->waktu_rapat)->format('H:i') }} WIB</p>
                            </div>
                            <div class="md:col-span-2">
                                <span class="font-semibold text-gray-700"><i class="fas fa-map-marker-alt mr-2"></i>Ruang Rapat:</span>
                                <p class="mt-1">{{ $dokumen->jadwalRapat->ruang_rapat }}</p>
                            </div>
                            @if($dokumen->jadwalRapat->catatan)
                                <div class="md:col-span-2">
                                    <span class="font-semibold text-gray-700"><i class="fas fa-info-circle mr-2"></i>Catatan Rapat:</span>
                                    <p class="mt-1">{{ $dokumen->jadwalRapat->catatan }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-700 p-2">Jadwal rapat belum tersedia.</p> {{-- Added padding for consistency --}}
                    @endif
                </div>
            </div>
            <hr class="my-6 border-gray-200">
        @endif

        {{-- Pengumuman Publik Section --}}
        @if($dokumen->pengumuman && in_array($statusTerakhir, ['Pengumuman Publik', 'Rapat Koordinasi', 'Substansi Lengkap', 'Perbaikan Substansi', 'Proses Penerbitan', 'Terbit', 'Dokumen Direvisi']))
            <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-blue-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-bullhorn mr-2"></i>Informasi Pengumuman Publik
                    </h3>
                    <p class="text-gray-700 text-sm mt-1">Lihat detail lengkap pengumuman publik terkait dokumen ini.</p>
                </div>
                <a href="{{ route('user.pengumuman.show', $dokumen->pengumuman->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-external-link-alt mr-2"></i> Lihat Pengumuman
                </a>
            </div>
            <hr class="my-4 border-gray-200">
        @endif

        {{-- Progress History Section --}}

        <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-history mr-2"></i>Riwayat Progres Dokumen:</h3>

        @if($dokumen->progresDokumen->isEmpty())
            <div class="text-center py-8 bg-gray-50 rounded-md border border-gray-200">
                <p class="text-gray-600 text-lg font-medium">
                    <i class="fas fa-box-open text-gray-400 mr-2"></i> Belum ada riwayat progres.
                </p>
            </div>
        @else
            <div class="relative pl-6 sm:pl-12">
                <div class="absolute inset-y-0 left-2.5 sm:left-5 w-0.5 bg-gray-200"></div>

                <div class="space-y-6">
                    @foreach($dokumen->progresDokumen->sortBy('created_at') as $progres)
                        <div class="relative">
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
                                            'Perbaikan Substansi' => 'bg-orange-100 text-orange-800', // Ensured consistent naming
                                            'Substansi Lengkap' => 'bg-teal-100 text-teal-800',
                                            'Proses Penerbitan' => 'bg-cyan-100 text-cyan-800',
                                            'Terbit' => 'bg-lime-100 text-lime-800',
                                            'Dokumen Direvisi' => 'bg-purple-100 text-purple-800', // This status indicates user uploaded a revision
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

@push('scripts')
<script>
    document.getElementById('toggleRapatDetail')?.addEventListener('click', function() {
        const detailDiv = document.getElementById('rapatDetail');
        const icon = document.getElementById('rapatIcon');
        if (detailDiv.classList.contains('hidden')) {
            detailDiv.classList.remove('hidden');
            detailDiv.classList.add('opacity-0', 'scale-95'); // Add initial hidden state for animation
            setTimeout(() => {
                detailDiv.classList.remove('opacity-0', 'scale-95');
                detailDiv.classList.add('opacity-100', 'scale-100'); // Animate to visible state
            }, 10); // Small delay to allow hidden class removal to take effect
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            detailDiv.classList.remove('opacity-100', 'scale-100');
            detailDiv.classList.add('opacity-0', 'scale-95'); // Animate to hidden state
            setTimeout(() => {
                detailDiv.classList.add('hidden');
            }, 300); // Match this duration with transition-all duration
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });
</script>
@endpush
@endsection