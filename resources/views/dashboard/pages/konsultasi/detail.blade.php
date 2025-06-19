@extends('dashboard.layouts.adminlayout')

@section('title', 'Detail Konsultasi')

@section('content')
    <div class="bg-white shadow-md rounded-2xl p-6 mb-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Konsultasi</h1>
            <p class="text-lg font-semibold text-blue-600 mt-2 tracking-widest">Kode: {{ $detail->kode_konsultasi }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div>
                <span class="font-semibold">Nama Pengguna:</span>
                <p>{{ $detail->konsultasi->user->nama ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Email:</span>
                <p>{{ $detail->konsultasi->user->email ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Jenis Konsultasi:</span>
                <p>{{ $detail->konsultasi->jenisKonsultasi->nama_jenis ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Topik:</span>
                <p>{{ $detail->topik->nama_topik ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Tanggal Konsultasi:</span>
                <p>{{ $detail->tanggal_konsultasi }}</p>
            </div>
            <div>
                <span class="font-semibold">Sesi:</span>
                <p>{{ $detail->sesi->nama_sesi ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Status:</span>
                <p>{{ $detail->status->nama_status ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Catatan:</span>
                <p>{{ $detail->catatan_konsultasi ?? '-' }}</p>
            </div>
            <div>
                <span class="font-semibold">Tanggal Dibuat:</span>
                <p>{{ $detail->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <span class="font-semibold">Tanggal Diubah:</span>
                <p>{{ $detail->updated_at->format('d M Y H:i') }}</p>
            </div>
            <div class="md:col-span-2">
                <span class="font-semibold">Lampiran:</span><br>
                @if($detail->lampiran)
                    <a href="{{ asset('storage/' . $detail->lampiran->path) }}"
                       target="_blank"
                       class="text-blue-600 hover:text-blue-800 underline">
                        {{ $detail->lampiran->nama_file }}
                    </a>
                @else
                    <p>-</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tombol Verifikasi Kehadiran (Luring) atau Proses Konsultasi (Daring) --}}
    @if($detail->status_id == 1)
        <form action="{{ route('konsultasi.verifikasi', $detail->id) }}" method="POST" class="mb-6 text-center">
            @csrf
            @if(strtolower($detail->konsultasi->jenisKonsultasi->nama_jenis) === 'luring')
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-xl transition">
                    Verifikasi Kehadiran
                </button>
            @elseif(strtolower($detail->konsultasi->jenisKonsultasi->nama_jenis) === 'daring')
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-xl transition">
                    Proses Konsultasi
                </button>
            @endif
        </form>
    @endif

    {{-- Form Tindak Lanjut --}}
    @if($detail->status_id == 2)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <form action="{{ route('konsultasi.tindaklanjut', $detail->id) }}" method="POST">
                @csrf
                <label for="catatan_tindaklanjut" class="block font-medium mb-2 text-gray-700">Catatan Tindak Lanjut:</label>
                <textarea name="catatan_tindaklanjut" id="catatan_tindaklanjut"
                          class="w-full border border-blue-300 p-2 rounded-lg" rows="4" required></textarea>

                <button type="submit"
                        class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-semibold">
                    Simpan Tindak Lanjut & Tandai Selesai
                </button>
            </form>
        </div>
    @endif

    {{-- Tampilkan Catatan Tindak Lanjut jika status selesai --}}
    @if($detail->status_id == 3)
        @php
            $tindaklanjut = DB::table('tindak_lanjut_konsultasi')->where('konsultasi_id', $detail->konsultasi_id)->latest()->first();
        @endphp

        @if($tindaklanjut)
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Catatan Tindak Lanjut</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $tindaklanjut->catatan_tindaklanjut }}</p>
                <p class="text-xs text-gray-500 mt-2">Dibuat: {{ \Carbon\Carbon::parse($tindaklanjut->created_at)->format('d M Y H:i') }}</p>
            </div>
        @endif
    @endif

    <div class="text-center">
        <a href="{{ url()->previous() }}"
           class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-xl transition">
            ← Kembali
        </a>
    </div>
@endsection
