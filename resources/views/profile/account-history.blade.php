<div class="bg-white p-8 rounded-xl border border-gray-200">
    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Riwayat Akun</h3>

    <div class="flex border-b border-gray-200 mb-6">
        <button id="tab-konsultasi"
                class="py-3 px-6 text-lg font-medium border-b-2 focus:outline-none transition-colors duration-200 border-blue-500 text-blue-600">
            Riwayat Konsultasi
        </button>
        <button id="tab-perling"
                class="py-3 px-6 text-lg font-medium border-b-2 focus:outline-none transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Riwayat Permohonan Perling
        </button>
    </div>

    {{-- Konten Tab Konsultasi --}}
    <div id="content-konsultasi" class="tab-content">
        <h4 class="text-xl font-semibold text-gray-700 mb-4">Daftar Riwayat Konsultasi Anda</h4>
        @if($konsultasiHistory->isEmpty())
            <div class="bg-gray-50 p-6 rounded-lg text-center text-gray-500">
                <i class="fas fa-history text-4xl mb-3 text-gray-400"></i>
                <p class="text-lg">Belum ada riwayat konsultasi.</p>
                <p class="text-sm mt-2">Mulai konsultasi pertama Anda sekarang!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($konsultasiHistory as $konsultasi)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 relative">
                        <div class="mb-3 sm:mb-0 pr-24">
                            <p class="text-sm text-gray-500">
                                Tanggal Dibuat: {{ $konsultasi->tanggal_konsultasi->format('d M Y H:i') }}
                            </p>
                            <h5 class="text-lg font-semibold text-gray-800">
                                Kode Konsultasi: <span class="text-blue-600">{{ $konsultasi->kode_konsultasi }}</span>
                            </h5>
                            <span class="inline-block mt-2 py-1 px-3 rounded-full text-xs font-semibold
                                @if($konsultasi->status == 'Selesai') bg-green-200 text-green-800
                                @elseif($konsultasi->status == 'Menunggu' || $konsultasi->status == 'Diajukan') bg-yellow-200 text-yellow-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ $konsultasi->status }}
                            </span>
                            <p class="text-base text-gray-700 mt-2">
                                Jenis Konsultasi: <span class="font-medium">{{ ucfirst($konsultasi->metode_konsultasi) }}</span>
                            </p>
                            <p class="text-base text-gray-700">
                                Topik Konsultasi: <span class="font-medium">{{ $konsultasi->topik_konsultasi }}</span>
                            </p>
                        </div>

                        <div class="absolute top-5 right-5 flex flex-col items-end gap-2">
                            {{-- Menggunakan detail_konsultasi_id untuk QR Code --}}
                            {!! QrCode::size(80)->generate(route('user.konsultasi.detail', $konsultasi->detail_konsultasi_id)) !!} {{-- Menggunakan rute baru --}}
                            {{-- Mengubah button menjadi anchor tag yang mengarah ke halaman detail konsultasi --}}
                            <a href="{{ route('user.konsultasi.detail', $konsultasi->detail_konsultasi_id) }}" {{-- Menggunakan rute baru --}}
                               class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                Lihat Detail <i class="fas fa-chevron-right text-xs ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Konten Tab Perling --}}
    <div id="content-perling" class="tab-content hidden">
        <h4 class="text-xl font-semibold text-gray-700 mb-4">Daftar Riwayat Pengajuan Perling Anda</h4>
        @if($perlingHistory->isEmpty())
            <div class="bg-gray-50 p-6 rounded-lg text-center text-gray-500">
                <i class="fas fa-tree text-4xl mb-3 text-gray-400"></i>
                <p class="text-lg">Belum ada riwayat permohonan Perling.</p>
                <p class="text-sm mt-2">Ajukan permohonan Perling pertama Anda sekarang!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($perlingHistory as $perling)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 relative">
                        <div class="mb-3 sm:mb-0 pr-24">
                            <p class="text-sm text-gray-500">Tanggal Pengajuan: {{ $perling->tanggal_pengajuan->format('d M Y H:i') }}</p>
                            <h5 class="text-lg font-semibold text-gray-800">
                                Kode Perling: <span class="text-blue-600">{{ $perling->kode_perling ?? 'N/A' }}</span>
                            </h5>
                            <span class="inline-block mt-2 py-1 px-3 rounded-full text-xs font-semibold
                                @if($perling->status_aplikasi && $perling->status_aplikasi->nama_status == 'Disetujui') bg-green-200 text-green-800
                                @elseif($perling->status_aplikasi && ($perling->status_aplikasi->nama_status == 'Diajukan' || $perling->status_aplikasi->nama_status == 'Menunggu' || $perling->status_aplikasi->nama_status == 'Rapat Koordinasi' || $perling->status_aplikasi->nama_status == 'Terbit')) bg-yellow-200 text-yellow-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ $perling->status_aplikasi->nama_status ?? 'Tidak Diketahui' }}
                            </span>
                            <p class="text-base text-gray-700 mt-2">
                                Nama Usaha: <span class="font-medium">{{ $perling->nama_usaha ?? 'Tidak Diketahui' }}</span>
                            </p>
                            <p class="text-base text-gray-700">
                                Jenis Perling: <span class="font-medium">{{ $perling->jenisPerling->nama_perling ?? 'Tidak Diketahui' }}</span>
                            </p>
                        </div>
                        <div class="absolute top-5 right-5 flex flex-col items-end gap-2">
                            {!! QrCode::size(90)->generate(route('user.perling.detail', $perling->id)) !!} {{-- Menggunakan rute baru --}}
                            {{-- Mengubah button menjadi anchor tag yang mengarah ke halaman detail perling --}}
                            <a href="{{ route('user.perling.detail', $perling->id) }}" {{-- Menggunakan rute baru --}}
                               class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                Lihat Detail <i class="fas fa-chevron-right text-xs ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal Detail Perling dihapus dari sini --}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Logika Pengalihan Tab ---
        const tabKonsultasi = document.getElementById('tab-konsultasi');
        const tabPerling = document.getElementById('tab-perling');
        const contentKonsultasi = document.getElementById('content-konsultasi');
        const contentPerling = document.getElementById('content-perling');

        function showTab(tabId) {
            if (tabId === 'konsultasi') {
                tabKonsultasi.classList.add('border-blue-500', 'text-blue-600');
                tabKonsultasi.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                contentKonsultasi.classList.remove('hidden');

                tabPerling.classList.remove('border-blue-500', 'text-blue-600');
                tabPerling.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                contentPerling.classList.add('hidden');
            } else if (tabId === 'perling') {
                tabPerling.classList.add('border-blue-500', 'text-blue-600');
                tabPerling.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                contentPerling.classList.remove('hidden');

                tabKonsultasi.classList.remove('border-blue-500', 'text-blue-600');
                tabKonsultasi.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                contentKonsultasi.classList.add('hidden');
            }
        }

        tabKonsultasi.addEventListener('click', function() {
            showTab('konsultasi');
        });

        tabPerling.addEventListener('click', function() {
            showTab('perling');
        });

        // Pastikan tab yang benar ditampilkan saat halaman dimuat (Konsultasi secara default)
        showTab('konsultasi');
    });
</script>
