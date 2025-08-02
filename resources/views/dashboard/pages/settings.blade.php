@extends('dashboard.layouts.adminlayout') {{-- Pastikan ini mengarah ke layout yang benar --}}

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="container mx-auto max-w-7xl">

    {{-- Notifikasi Sukses (tetap hijau) --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded-lg mb-3 text-sm" role="alert">
            <div class="flex items-center">
                <div class="mr-2 text-green-500"><i class="fas fa-check-circle"></i></div>
                <div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error (tetap merah) --}}
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg mb-3 text-sm" role="alert">
            <div class="flex items-center">
                <div class="mr-2 text-red-500"><i class="fas fa-times-circle"></i></div>
                <div>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Validasi Error Laravel (tetap merah) --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg mb-3 text-sm" role="alert">
            <ul class="list-disc pl-4 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
        <form action="{{ route('settings.save') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- Kolom Kiri: Pengaturan Konsultasi --}}
                <div>
                    <h2 class="text-xl font-bold mb-4 pb-1 border-b" style="color: #03346E; border-color: #03346E;"> {{-- Warna sesuai hex --}}
                        <i class="fas fa-comments mr-2"></i> Pengaturan Konsultasi
                    </h2>

                    {{-- Input untuk Maksimum Konsultasi Daring Harian --}}
                    <div class="mb-4">
                        <label for="maks_konsultasi_daring_harian" class="block text-gray-700 text-sm font-medium mb-1">
                            Maks. Konsultasi Daring Harian
                            <span class="text-gray-500 text-xs">(0 = Tidak Terbatas)</span>:
                        </label>
                        <input type="number" name="maks_konsultasi_daring_harian" id="maks_konsultasi_daring_harian"
                               class="form-input w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80" {{-- Fokus border dengan opacity --}}
                               style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);" {{-- Warna fokus ring lebih transparan --}}
                               value="{{ old('maks_konsultasi_daring_harian', $setting->maks_konsultasi_daring_harian ?? 0) }}" min="0"
                               placeholder="Contoh: 10">
                    </div>

                    {{-- Input untuk Maksimum Konsultasi Luring Harian --}}
                    <div class="mb-4">
                        <label for="maks_konsultasi_luring_harian" class="block text-gray-700 text-sm font-medium mb-1">
                            Maks. Konsultasi Luring Harian
                            <span class="text-gray-500 text-xs">(0 = Tidak Terbatas)</span>:
                        </label>
                        <input type="number" name="maks_konsultasi_luring_harian" id="maks_konsultasi_luring_harian"
                               class="form-input w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                               style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                               value="{{ old('maks_konsultasi_luring_harian', $setting->maks_konsultasi_luring_harian ?? 0) }}" min="0"
                               placeholder="Contoh: 5">
                    </div>

                    {{-- Section untuk Tanggal Tidak Tersedia Konsultasi Luring --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            Tanggal Tidak Tersedia Luring:
                        </label>
                        <div id="luring-unavailable-dates-container" class="space-y-2 p-2 bg-gray-50 rounded-md border border-gray-200">
                            @forelse ($setting->tanggal_tidak_tersedia_konsultasi_luring ?? [] as $index => $item)
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-1 sm:space-y-0 sm:space-x-2 p-2 bg-white rounded-md border border-gray-200 unavailable-date-item">
                                    <input type="date" name="tanggal_tidak_tersedia_konsultasi_luring[{{ $index }}][date]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           value="{{ old('tanggal_tidak_tersedia_konsultasi_luring.' . $index . '.date', $item['date'] ?? '') }}">
                                    <input type="text" name="tanggal_tidak_tersedia_konsultasi_luring[{{ $index }}][reason]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           placeholder="Alasan tidak tersedia"
                                           value="{{ old('tanggal_tidak_tersedia_konsultasi_luring.' . $index . '.reason', $item['reason'] ?? '') }}">
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-1.5 px-2 rounded-md remove-date-btn flex-shrink-0">
                                        <i class="fas fa-times-circle"></i> Hapus
                                    </button>
                                </div>
                            @empty
                                {{-- Warna untuk pesan kosong, disesuaikan agar lebih netral atau tetap pakai biru terang yang konsisten --}}
                                <div class="text-blue-700 text-xs italic p-2 bg-blue-50 rounded-md border border-blue-200 flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> Belum ada tanggal tidak tersedia.
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-luring-unavailable-date" class="mt-3 inline-flex items-center px-3 py-1.5 text-white text-sm font-semibold rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm"
                                style="background-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.4);"> {{-- Warna tombol sesuai hex, hover dan focus ring disesuaikan --}}
                            <i class="fas fa-plus-circle mr-2"></i> Tambah
                        </button>
                    </div>

                    {{-- Section untuk Tanggal Tidak Tersedia Konsultasi Daring --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            Tanggal Tidak Tersedia Daring:
                        </label>
                        <div id="daring-unavailable-dates-container" class="space-y-2 p-2 bg-gray-50 rounded-md border border-gray-200">
                            @forelse ($setting->tanggal_tidak_tersedia_konsultasi_daring ?? [] as $index => $item)
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-1 sm:space-y-0 sm:space-x-2 p-2 bg-white rounded-md border border-gray-200 unavailable-date-item">
                                    <input type="date" name="tanggal_tidak_tersedia_konsultasi_daring[{{ $index }}][date]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           value="{{ old('tanggal_tidak_tersedia_konsultasi_daring.' . $index . '.date', $item['date'] ?? '') }}">
                                    <input type="text" name="tanggal_tidak_tersedia_konsultasi_daring[{{ $index }}][reason]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           placeholder="Alasan tidak tersedia"
                                           value="{{ old('tanggal_tidak_tersedia_konsultasi_daring.' . $index . '.reason', $item['reason'] ?? '') }}">
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-1.5 px-2 rounded-md remove-date-btn flex-shrink-0">
                                        <i class="fas fa-times-circle"></i> Hapus
                                    </button>
                                </div>
                            @empty
                                <div class="text-blue-700 text-xs italic p-2 bg-blue-50 rounded-md border border-blue-200 flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> Belum ada tanggal tidak tersedia.
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-daring-unavailable-date" class="mt-3 inline-flex items-center px-3 py-1.5 text-white text-sm font-semibold rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm"
                                style="background-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.4);">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah
                        </button>
                    </div>
                </div>

                {{-- Kolom Kanan: Pengaturan Perling --}}
                <div>
                    <h2 class="text-xl font-bold mb-4 pb-1 border-b" style="color: #03346E; border-color: #03346E;">
                        <i class="fas fa-file-alt mr-2"></i> Pengaturan Perling
                    </h2>

                    {{-- Input untuk Maksimum Pengajuan Perling Harian --}}
                    <div class="mb-4">
                        <label for="maks_perling_harian" class="block text-gray-700 text-sm font-medium mb-1">
                            Maks. Pengajuan Perling Harian
                            <span class="text-gray-500 text-xs">(0 = Tidak Terbatas)</span>:
                        </label>
                        <input type="number" name="maks_perling_harian" id="maks_perling_harian"
                               class="form-input w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                               style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                               value="{{ old('maks_perling_harian', $setting->maks_perling_harian ?? 0) }}" min="0"
                               placeholder="Contoh: 20">
                    </div>

                    {{-- Section untuk Tanggal Tidak Tersedia Perling --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            Tanggal Tidak Tersedia Perling:
                        </label>
                        <div id="perling-unavailable-dates-container" class="space-y-2 p-2 bg-gray-50 rounded-md border border-gray-200">
                            @forelse ($setting->tanggal_tidak_tersedia_perling ?? [] as $index => $item)
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-1 sm:space-y-0 sm:space-x-2 p-2 bg-white rounded-md border border-gray-200 unavailable-date-item">
                                    <input type="date" name="tanggal_tidak_tersedia_perling[{{ $index }}][date]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           value="{{ old('tanggal_tidak_tersedia_perling.' . $index . '.date', $item['date'] ?? '') }}">
                                    <input type="text" name="tanggal_tidak_tersedia_perling[{{ $index }}][reason]"
                                           class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                                           style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                                           placeholder="Alasan tidak tersedia"
                                           value="{{ old('tanggal_tidak_tersedia_perling.' . $index . '.reason', $item['reason'] ?? '') }}">
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-1.5 px-2 rounded-md remove-date-btn flex-shrink-0">
                                        <i class="fas fa-times-circle"></i> Hapus
                                    </button>
                                </div>
                            @empty
                                <div class="text-blue-700 text-xs italic p-2 bg-blue-50 rounded-md border border-blue-200 flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> Belum ada tanggal tidak tersedia.
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-perling-unavailable-date" class="mt-3 inline-flex items-center px-3 py-1.5 text-white text-sm font-semibold rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm"
                                style="background-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.4);">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah
                        </button>
                    </div>
                </div>
            </div> {{-- End of grid --}}

            <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm">
                    <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to add a new date-reason pair
        function addDatePair(containerId, inputName, type) {
            const container = document.getElementById(containerId);
            const index = container.querySelectorAll('.unavailable-date-item').length;

            // Remove the "empty state" message if it exists
            const emptyStateDiv = container.querySelector('.text-blue-700.italic'); // Keep blue for now
            if (emptyStateDiv) {
                emptyStateDiv.remove();
            }

            const newItem = document.createElement('div');
            newItem.classList.add('flex', 'flex-col', 'sm:flex-row', 'items-stretch', 'sm:items-center', 'space-y-1', 'sm:space-y-0', 'sm:space-x-2', 'p-2', 'bg-white', 'rounded-md', 'border', 'border-gray-200', 'unavailable-date-item');
            newItem.innerHTML = `
                <input type="date" name="${inputName}[${index}][date]"
                             class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                             style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);">
                <input type="text" name="${inputName}[${index}][reason]"
                             class="form-input flex-1 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:border-opacity-80"
                             style="focus-border-color: #03346E; focus-ring-color: rgba(3, 52, 110, 0.2);"
                             placeholder="Alasan tidak tersedia">
                <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-1.5 px-2 rounded-md remove-date-btn flex-shrink-0">
                    <i class="fas fa-times-circle"></i> Hapus
                </button>
            `;
            container.appendChild(newItem);
        }

        // Get references to the "Tambah Tanggal" buttons by their IDs
        const addLuringBtn = document.getElementById('add-luring-unavailable-date');
        const addDaringBtn = document.getElementById('add-daring-unavailable-date');
        const addPerlingBtn = document.getElementById('add-perling-unavailable-date');

        // Attach event listeners to the "Tambah Tanggal" buttons
        if (addLuringBtn) {
            addLuringBtn.addEventListener('click', () => {
                addDatePair('luring-unavailable-dates-container', 'tanggal_tidak_tersedia_konsultasi_luring', 'konsultasi luring');
            });
        }
        if (addDaringBtn) {
            addDaringBtn.addEventListener('click', () => {
                addDatePair('daring-unavailable-dates-container', 'tanggal_tidak_tersedia_konsultasi_daring', 'konsultasi daring');
            });
        }
        if (addPerlingBtn) {
            addPerlingBtn.addEventListener('click', () => {
                addDatePair('perling-unavailable-dates-container', 'tanggal_tidak_tersedia_perling', 'pengajuan perling');
            });
        }

        // Event delegation for removing date-reason pairs (works for all containers)
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-date-btn')) {
                const itemToRemove = e.target.closest('.unavailable-date-item');
                const container = itemToRemove.parentElement;
                itemToRemove.remove();

                // Re-index remaining items to maintain proper array keys in Laravel
                container.querySelectorAll('.unavailable-date-item').forEach((item, newIndex) => {
                    // Update the name attribute for both date and reason inputs
                    const dateInput = item.querySelector('input[type="date"]');
                    const reasonInput = item.querySelector('input[type="text"]');
                    
                    if (dateInput) {
                        dateInput.name = dateInput.name.replace(/\[\d+\]/, `[${newIndex}]`);
                    }
                    if (reasonInput) {
                        reasonInput.name = reasonInput.name.replace(/\[\d+\]/, `[${newIndex}]`);
                    }
                });

                // If no items left, re-add the empty state message
                if (container.querySelectorAll('.unavailable-date-item').length === 0) {
                    let message = 'Belum ada tanggal tidak tersedia.';
                    
                    // Keeping the blue Tailwind classes for the empty state message as they provide good contrast and neutrality,
                    // unless you have a specific hex for this subtle info text.
                    const emptyStateDiv = document.createElement('div');
                    emptyStateDiv.classList.add('text-blue-700', 'text-xs', 'italic', 'p-2', 'bg-blue-50', 'rounded-md', 'border', 'border-blue-200', 'flex', 'items-center', 'justify-center');
                    emptyStateDiv.innerHTML = `<i class="fas fa-info-circle mr-1 text-blue-500"></i> ${message}`;
                    container.appendChild(emptyStateDiv);
                }
            }
        });
    });
</script>
@endsection