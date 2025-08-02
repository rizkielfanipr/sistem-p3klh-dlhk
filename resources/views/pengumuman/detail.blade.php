@extends('layouts.user') {{-- Menggunakan layout user --}}

@section('title', $pengumuman->nama_usaha) {{-- Menetapkan judul halaman --}}
@section('description', 'Pengumuman Publik - Saran, Tanggapan dan Masukan Masyarakat') {{-- Menetapkan deskripsi halaman --}}

@section('breadcrumb', 'Detail Pengumuman') {{-- Sesuaikan breadcrumb jika diperlukan --}}

@section('content')

<div class="bg-white p-6 rounded-lg border border-gray-200">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-bullhorn text-purple-600 mr-3"></i> {{ $pengumuman->nama_usaha }}
        </h1>
        <a href="{{ url('/') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>

    {{-- Important Notice: Announcement Duration --}}
    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 mb-6 rounded-md shadow-sm" role="alert">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg>
            </div>
            <div>
                <p class="font-bold">Penting:</p>
                <p class="text-sm">Pengumuman ini hanya akan ditampilkan selama 3x24 jam sejak tanggal publikasi untuk proses penyampaian saran, tanggapan, dan masukan dari masyarakat.</p>
            </div>
        </div>
    </div>
    {{-- End Important Notice --}}

    {{-- Image Display Section --}}
    <div class="mb-8 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
        @if ($pengumuman->image)
            <img src="{{ Storage::url($pengumuman->image) }}" alt="Cover Image of {{ $pengumuman->judul }}" 
                 class="w-full h-64 md:h-80 object-cover object-center rounded-t-lg">
        @else
            <div class="w-full h-64 md:h-80 flex items-center justify-center bg-gray-200 rounded-t-lg">
                <i class="fas fa-image text-gray-400 text-6xl"></i>
            </div>
            <div class="p-4 bg-gray-50 text-center">
                <span class="text-sm font-medium text-gray-700">Tidak ada gambar cover</span>
            </div>
        @endif
    </div>
    {{-- End Image Display Section --}}

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 md:gap-8 mb-8">

        {{-- Information Section --}}
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                {{-- Detail Item for Judul --}}
                <div class="flex items-start">
                    <i class="fas fa-heading text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Judul:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->judul }}</p>
                    </div> 
                </div>
                {{-- Detail Item for Jenis Perling --}}
                <div class="flex items-start">
                    <i class="fas fa-list-alt text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Jenis Perling:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->jenis_perling }}</p>
                    </div> 
                </div>
                {{-- Detail Item for Nama Usaha --}}
                <div class="flex items-start">
                    <i class="fas fa-building text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Nama Usaha:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->nama_usaha ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Bidang Usaha --}}
                <div class="flex items-start">
                    <i class="fas fa-industry text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Bidang Usaha:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->bidang_usaha ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Skala Besaran --}}
                <div class="flex items-start">
                    <i class="fas fa-ruler-combined text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Skala Besaran:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->skala_besaran ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Lokasi --}}
                <div class="flex items-start">
                    <i class="fas fa-map-marker-alt text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Lokasi:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->lokasi ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Pemrakarsa --}}
                <div class="flex items-start">
                    <i class="fas fa-user-tie text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Pemrakarsa:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->pemrakarsa ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Penanggung Jawab --}}
                <div class="flex items-start">
                    <i class="fas fa-user-shield text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Penanggung Jawab:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->penanggung_jawab ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Dibuat Oleh --}}
                <div class="flex items-start">
                    <i class="fas fa-user-edit text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Dibuat Oleh:</span>
                        <p class="text-gray-800 text-base break-words">{{ $pengumuman->user->name ?? 'Admin' }}</p>
                    </div>
                </div>
                {{-- Detail Item for Tanggal Publikasi --}}
                <div class="flex items-start">
                    <i class="fas fa-calendar-alt text-lg text-gray-500 mt-1 mr-3"></i>
                    <div>
                        <span class="font-semibold text-gray-700 block text-sm">Tanggal Publikasi:</span>
                        <p class="text-gray-800 text-base break-words">{{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Description and Impact --}}
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Deskripsi Usaha
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $pengumuman->deskripsi }}</p>
            </div>

            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i> Perkiraan Dampak Lingkungan
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $pengumuman->dampak }}</p>
            </div>

            {{-- Lampiran Section --}}
            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-paperclip mr-2 text-green-500"></i> Lampiran
                </h3>
                @if ($pengumuman->lampiran && $pengumuman->lampiran->lampiran)
                    @php
                        $filePath = $pengumuman->lampiran->lampiran;
                        $fileUrl = Storage::url($filePath);
                        $fileName = basename($filePath);
                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $iconClass = match ($extension) {
                            'pdf' => 'fa-file-pdf text-red-600',
                            'doc', 'docx' => 'fa-file-word text-blue-600',
                            'xls', 'xlsx' => 'fa-file-excel text-green-600',
                            'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fa-file-image text-purple-500',
                            'zip', 'rar' => 'fa-file-archive text-yellow-600',
                            'txt' => 'fa-file-alt text-gray-600',
                            default => 'fa-file text-gray-500',
                        };
                    @endphp
                    @if ($isImage)
                        <img src="{{ $fileUrl }}" alt="Lampiran Pengumuman" class="max-w-full h-auto rounded-md mt-2">
                    @else
                        <a href="{{ $fileUrl }}" target="_blank"
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 underline transition ease-in-out duration-150 mt-2">
                            <i class="fas {{ $iconClass }} mr-2 text-xl"></i>
                            Unduh Lampiran ({{ strtoupper($extension) }})
                        </a>
                    @endif
                @else
                    <p class="text-gray-600 italic">- Tidak ada lampiran -</p>
                @endif
            </div>

        </div>
    </div>

    {{-- Tanggapan Pengumuman Section --}}
    <div class="mt-6 p-6 bg-white rounded-xl border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-comments text-blue-600 mr-3 text-2xl"></i> Saran, Pendapat, dan Masukan ({{ $pengumuman->tanggapan->count() }})
        </h3>

        @forelse($pengumuman->tanggapan as $tanggapan)
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-4 transition-all duration-300">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-gray-500 text-3xl mr-4"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-lg">{{ $tanggapan->user->name ?? $tanggapan->nama ?? 'Anonim' }}</p>
                        <p class="text-gray-500 text-sm mt-1">
                            {{ \Carbon\Carbon::parse($tanggapan->created_at)->translatedFormat('d F Y, H:i') }}
                            @if($tanggapan->jenis_kelamin)
                                <span class="mx-2 text-gray-400">•</span> {{ $tanggapan->jenis_kelamin }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="space-y-2 text-sm text-gray-700 mb-4 pl-10 border-l-2 border-blue-200">
                    @if($tanggapan->nomor_hp)
                        <p class="flex items-center"><i class="fas fa-phone mr-3 text-gray-400"></i> {{ $tanggapan->nomor_hp }}</p>
                    @endif
                    @if($tanggapan->email)
                        <p class="flex items-center"><i class="fas fa-envelope mr-3 text-gray-400"></i> {{ $tanggapan->email }}</p>
                    @endif
                </div>
                <div class="p-4 bg-white rounded-md border border-gray-200 relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-full w-1 bg-blue-500"></div> {{-- Accent bar --}}
                    <p class="text-gray-800 leading-relaxed italic pl-3">{{ $tanggapan->isi_tanggapan }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-600 italic text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">Belum ada saran, pendapat, atau masukan untuk pengumuman ini.</p>
        @endforelse
    </div>

    {{-- Form untuk Menambahkan Tanggapan --}}
    <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-100">
        <h3 class="text-2xl font-bold text-blue-800 mb-6 flex items-center">
            <i class="fas fa-plus-circle text-blue-600 mr-3 text-2xl"></i> Tambahkan Saran, Pendapat, atau Masukan
        </h3>
        {{-- Removed @auth directive to allow public access --}}
        <form action="{{ route('tanggapan.store', ['id' => $pengumuman->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="pengumuman_id" value="{{ $pengumuman->id }}">
            <input type="hidden" name="tanggal_tanggapan" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    {{-- Updated value to default to 'Anonim' if user is not logged in and old('nama') is empty --}}
                    <input type="text" name="nama" id="nama" class="form-input block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out px-4 py-2" required value="{{ old('nama', Auth::user()->name ?? '') }}" placeholder="Masukkan nama Anda">
                    @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nomor_hp" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                    <input type="text" name="nomor_hp" id="nomor_hp" class="form-input block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out px-4 py-2" value="{{ old('nomor_hp') }}" placeholder="Cth: 081234567890">
                    @error('nomor_hp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" class="form-input block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out px-4 py-2" value="{{ old('email', Auth::user()->email ?? '') }}" placeholder="Cth: nama@contoh.com">
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin:</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out px-4 py-2">
                        <option value="">Pilih</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="isi_tanggapan" class="block text-sm font-medium text-gray-700 mb-1">Isi Tanggapan <span class="text-red-500">*</span></label>
                <textarea name="isi_tanggapan" id="isi_tanggapan" rows="5" class="form-textarea block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out px-4 py-2" required placeholder="Tuliskan saran, pendapat, atau masukan Anda di sini...">{{ old('isi_tanggapan') }}</textarea>
                @error('isi_tanggapan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- reCAPTCHA Checkbox --}}
            <div class="mb-6">
                {!! NoCaptcha::display() !!}
                @error('g-recaptcha-response')
                    <p class="text-red-600 text-xs mt-1">Harap centang "Saya bukan robot".</p>
                @enderror
            </div>

            <div class="text-right">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Tanggapan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    {!! NoCaptcha::renderJs() !!}
@endsection