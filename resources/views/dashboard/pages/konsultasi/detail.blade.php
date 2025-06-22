@extends('dashboard.layouts.adminlayout')

@section('title', 'Detail Konsultasi')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-8 rounded-lg border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div class="mb-4 md:mb-0">
                <span class="inline-block bg-blue-500 text-white text-3xl font-bold px-4 py-2 rounded-lg shadow-md">
                    <i class="fas fa-barcode mr-2"></i>Kode: {{ $detail->kode_konsultasi }}
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <img src="{{ $detail->konsultasi->user->foto ? asset('storage/' . $detail->konsultasi->user->foto) : asset('default-profile.png') }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-300 shadow-md" alt="Foto Profil">
                <div>
                    <div class="font-semibold text-lg text-gray-900">
                        <i class="fas fa-user mr-1"></i>{{ $detail->konsultasi->user->nama ?? 'Nama Pengguna Tidak Tersedia' }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        <i class="fas fa-envelope mr-1"></i>{{ $detail->konsultasi->user->email ?? 'Email Tidak Tersedia' }}
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Detail Kolom --}}
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-base text-gray-800">

                @php
                    $isLuring = strtolower($detail->konsultasi->jenisKonsultasi->nama_jenis ?? '') === 'luring';
                    $fields = [
                        'Jenis Konsultasi' => $isLuring ? 'Konsultasi Luring' : 'Konsultasi Daring',
                        'Topik' => $detail->topik->nama_topik ?? '-',
                        'Tanggal Dibuat' => $detail->created_at->format('d F Y H:i'),
                    ];

                    if ($isLuring) {
                        $fields['Tanggal Konsultasi'] = \Carbon\Carbon::parse($detail->tanggal_konsultasi)->format('d F Y');
                        $fields['Sesi'] = $detail->sesi->nama_sesi ?? '-';
                    }
                @endphp

                @foreach ($fields as $label => $value)
                    <div>
                        <span class="font-semibold text-gray-700"><i class="fas fa-circle-info mr-1"></i>{{ $label }}:</span>
                        <p class="mt-1">{{ $value }}</p>
                    </div>
                @endforeach

                {{-- Status --}}
                <div>
                    <span class="font-semibold text-gray-700"><i class="fas fa-flag mr-1"></i>Status:</span>
                    <p class="mt-1">
                        @php
                            $statusName = strtolower($detail->status->nama_status ?? '');
                            $statusClass = match($statusName) {
                                'diajukan' => 'bg-blue-100 text-blue-800',
                                'diproses' => 'bg-yellow-100 text-yellow-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'batal' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($detail->status->nama_status ?? '-') }}
                        </span>
                    </p>
                </div>

                {{-- Catatan --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-sticky-note mr-1"></i>Catatan:</span>
                    <div class="mt-1 text-gray-800 leading-relaxed">
                        {!! $detail->catatan_konsultasi !!}
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="md:col-span-2">
                    <span class="font-semibold text-gray-700"><i class="fas fa-paperclip mr-1"></i>Lampiran:</span><br>
                    @php
                        $filePath = $detail->lampiran->lampiran ?? null;
                        $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
                        $fileName = $filePath ? basename($filePath) : null;
                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
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

            {{-- QR Code --}}
            <div class="lg:col-span-1 flex items-center justify-center p-4 bg-gray-50 border rounded-lg">
                {!! QrCode::size(250)->generate($detail->kode_konsultasi) !!}
            </div>
        </div>

        {{-- Tombol Aksi --}}
        @if($detail->status_id == 1)
            <div class="mt-8 flex justify-end">
                <form action="{{ route('konsultasi.verifikasi', $detail->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ strtolower($detail->konsultasi->jenisKonsultasi->nama_jenis) === 'luring' ? 'Verifikasi Kehadiran' : 'Proses Konsultasi' }}
                    </button>
                </form>
            </div>
        @endif

        {{-- Form Tindak Lanjut --}}
        @if($detail->status_id == 2)
            <div class="border border-blue-200 rounded-lg p-6 mt-8">
                <h2 class="text-lg font-semibold text-blue-800 mb-4"><i class="fas fa-edit mr-2"></i>Form Catatan Tindak Lanjut</h2>
                <form action="{{ route('konsultasi.tindaklanjut', $detail->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <x-quill-editor name="catatan_tindaklanjut" id="catatan_tindaklanjut"
                                        placeholder="Masukkan catatan tindak lanjut..." height="250px" />
                    </div>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md">
                        <i class="fas fa-save mr-2"></i>Simpan Tindak Lanjut & Tandai Selesai
                    </button>
                </form>
            </div>
        @endif

        {{-- Tampilkan Tindak Lanjut --}}
        @if($detail->status_id == 3)
            @php
                $tindaklanjut = DB::table('tindak_lanjut_konsultasi')
                    ->where('konsultasi_id', $detail->konsultasi_id)
                    ->latest()
                    ->first();
            @endphp

            @if($tindaklanjut)
                <div class="bg-gray-100 border border-gray-200 rounded-lg p-6 mt-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-comment-dots mr-2"></i>Catatan Tindak Lanjut</h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! $tindaklanjut->catatan_tindaklanjut !!}
                    </div>
                    <p class="text-sm text-gray-500 mt-4"><i class="fas fa-calendar-alt mr-1"></i>Dibuat pada: {{ \Carbon\Carbon::parse($tindaklanjut->created_at)->format('d F Y H:i') }}</p>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
