{{-- resources/views/perling/track_results_partial.blade.php --}}
@if ($dokumen)
    <div class="bg-white rounded-3xl p-6 md:p-10 border border-gray-100">
        {{-- Informasi Dokumen --}}
        <h2 class="text-3xl font-bold text-[#03346E] mb-8 pb-4 border-b-2 border-blue-100 flex items-center">
            <i class="fas fa-file-alt mr-3 text-[#03346E]"></i> Informasi Dokumen
        </h2>

        {{-- Seluruh Informasi Dokumen dalam satu div rounded --}}
        <div class="bg-gray-50 p-6 rounded-xl mb-10 text-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                {{-- Kode Permohonan --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Kode Permohonan</p>
                    <p class="text-xl font-mono text-gray-900 tracking-wide mb-3">{{ $dokumen->kode_perling }}</p>
                </div>

                {{-- Nama Pemohon --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Nama Pemohon</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->nama_pemohon }}</p>
                </div>

                {{-- Nama Usaha/Kegiatan --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Nama Usaha/Kegiatan</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->nama_usaha }}</p>
                </div>

                {{-- Bidang Usaha --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Bidang Usaha</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->bidang_usaha }}</p>
                </div>

                {{-- Lokasi Usaha --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Lokasi Usaha</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->lokasi }}</p>
                </div>

                {{-- Pemrakarsa --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Nama Pemrakarsa</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->pemrakarsa }}</p>
                </div> 
                
                {{-- Penanggung Jawab --}} 
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Nama Penanggung Jawab</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->penanggung_jawab }}</p>
                </div>

                {{-- Jenis Persetujuan Lingkungan --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Jenis Persetujuan Lingkungan</p>
                    <p class="text-lg text-gray-900 mb-3">{{ $dokumen->jenisPerling->nama_perling ?? '-' }}</p>
                </div>

                {{-- Lampiran Utama --}}
                <div class="md:col-span-2 lg:col-span-3 pt-3">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Lampiran Utama</p>
                    @if ($dokumen->lampiran)
                        <a href="{{ Storage::url($dokumen->lampiran) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-base font-medium transition duration-300 hover:underline">
                            <i class="fas fa-file-download mr-2 text-xl"></i> Unduh Lampiran Utama
                        </a>
                    @else
                        <p class="italic text-gray-500 text-base">Tidak ada lampiran utama.</p>
                    @endif
                </div>
            </div>
        </div>


        {{-- Progres Dokumen (Redesigned with Icons) --}}
        <h2 class="text-3xl font-bold text-[#03346E] mt-8 mb-8 pb-4 border-b-2 border-blue-100 flex items-center">
            <i class="fas fa-chart-line mr-3 text-green-500"></i> Progres Dokumen
        </h2>

        @if ($progresDokumen->isNotEmpty())
            {{-- Main container for the timeline --}}
            <div class="relative pl-0 pr-0">
                {{-- Vertical connecting line --}}
                @unless($progresDokumen->count() <= 1)
                    <div class="absolute left-6 top-0 h-full w-0.5 bg-gray-300 transform -translate-x-1/2"></div>
                @endunless

                @foreach ($progresDokumen as $progres)
                    @php
                        $statusColorClass = 'bg-gray-200 text-gray-600';
                        $ringColorClass = 'border-gray-300';
                        $iconClass = 'fas fa-circle'; // Default icon

                        switch ($progres->status_id) {
                            case 1: $statusColorClass = 'bg-blue-100 text-blue-700'; $ringColorClass = 'border-blue-400'; $iconClass = 'fas fa-paper-plane'; break; // Submitted
                            case 2: $statusColorClass = 'bg-yellow-100 text-yellow-700'; $ringColorClass = 'border-yellow-400'; $iconClass = 'fas fa-clipboard-check'; break; // Reviewed
                            case 3: $statusColorClass = 'bg-green-100 text-green-700'; $ringColorClass = 'border-green-400'; $iconClass = 'fas fa-check-circle'; break; // Approved
                            case 4: $statusColorClass = 'bg-red-100 text-red-700'; $ringColorClass = 'border-red-400'; $iconClass = 'fas fa-exclamation-triangle'; break; // Rejected/Revision Required
                            case 5: $statusColorClass = 'bg-purple-100 text-purple-700'; $ringColorClass = 'border-purple-400'; $iconClass = 'fas fa-bullhorn'; break; // Announcement
                            case 6: $statusColorClass = 'bg-indigo-100 text-indigo-700'; $ringColorClass = 'border-indigo-400'; $iconClass = 'fas fa-users'; break; // Coordination
                            case 7: $statusColorClass = 'bg-green-200 text-green-800'; $ringColorClass = 'border-green-500'; $iconClass = 'fas fa-check-double'; break; // Completed
                            case 8: $statusColorClass = 'bg-red-200 text-red-800'; $ringColorClass = 'border-red-500'; $iconClass = 'fas fa-times-circle'; break; // Cancelled
                            case 9: $statusColorClass = 'bg-orange-100 text-orange-700'; $ringColorClass = 'border-orange-400'; $iconClass = 'fas fa-cogs'; break; // Processing
                            case 10: $statusColorClass = 'bg-teal-100 text-teal-700'; $ringColorClass = 'border-teal-400'; $iconClass = 'fas fa-award'; break; // Issued
                        }
                        $isCurrent = $loop->last;
                    @endphp

                    <div class="flex items-center w-full max-w-2xl relative z-10 @unless($loop->first) mt-8 @endunless">
                        {{-- Circle with Icon (positioned to overlap the main vertical line) --}}
                        <div class="w-12 h-12 p-8 rounded-full flex items-center justify-center border-4 {{ $ringColorClass }} {{ $statusColorClass }} flex-shrink-0 -ml-6 mr-4">
                            <i class="{{ $iconClass }} text-xl"></i> {{-- Changed back to icon --}}
                        </div>

                        {{-- Horizontal Connector Line (fixed width, pushes card) --}}
                        <div class="h-0.5 bg-gray-300 w-8 flex-shrink-0"></div>

                        {{-- Progress Card --}}
                        <div class="flex-grow p-6 rounded-md border transition-all duration-300 ml-4
                            @if($isCurrent) bg-blue-50 border-blue-400 scale-[1.01] @else bg-white border-gray-200 @endif">
                            <h3 class="text-xl md:text-2xl font-bold {{ str_replace('bg-', 'text-', $statusColorClass) }} mb-2">
                                {{ $progres->statusDokumen->nama_status ?? 'Status Tidak Diketahui' }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($progres->created_at)->locale('id')->isoFormat('dddd, D MMMM [pukul] HH:mm') }} WIB
                            </p>

                            <p class="text-gray-700 mb-4 leading-relaxed">
                                <span class="font-semibold">Catatan:</span>
                                @if ($progres->catatan)
                                    {{ $progres->catatan }}
                                @else
                                    <span class="italic text-gray-500">Tidak ada catatan.</span>
                                @endif
                            </p>

                            @if ($progres->lampiran && property_exists($progres->lampiran, 'path'))
                                <a href="{{ Storage::url($progres->lampiran->path) }}" target="_blank" class="text-base text-blue-600 hover:text-blue-800 hover:underline flex items-center font-medium">
                                    <i class="fas fa-file-alt mr-2"></i> Unduh Lampiran Tahap Ini
                                </a>
                            @else
                                <p class="text-sm italic text-gray-500 flex items-center"><i class="fas fa-info-circle mr-2"></i> Tidak ada lampiran untuk tahap ini.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-6 rounded-xl flex items-start space-x-4">
                <i class="fas fa-info-circle text-3xl mt-1 text-blue-600"></i>
                <div>
                    <p class="font-bold text-xl mb-2">Informasi Progres</p>
                    <p class="text-base">Belum ada progres terbaru untuk dokumen ini. Kami akan memperbarui di sini segera setelah ada perkembangan.</p>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-6 rounded-xl flex items-start space-x-4">
        <i class="fas fa-exclamation-triangle text-3xl mt-1 text-red-600"></i>
        <div>
            <p class="font-bold text-xl mb-2">Dokumen Tidak Ditemukan</p>
            <p class="text-base">Mohon maaf, dokumen dengan kode permohonan atau nama usaha yang Anda masukkan tidak ditemukan. Pastikan informasi yang Anda berikan sudah benar dan coba lagi.</p>
        </div>
    </div>
@endif