{{-- rapat.blade.php --}}

{{-- MEETING SCHEDULE FORM FIELDS (INPUT) --}}
@if($statusTerakhir === 'Pengumuman Publik')
    <div class="mb-6 p-4 border border-gray-300 rounded-lg bg-yellow-50">
        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-calendar-plus mr-2"></i>Penjadwalan Rapat Koordinasi Substansi:
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 gap-y-4">
            @foreach ([
                'Tanggal Rapat' => ['name' => 'tanggal_rapat', 'type' => 'date', 'value' => $dokumen->jadwalRapat->tanggal_rapat ?? ''],
                'Waktu Rapat' => ['name' => 'waktu_rapat', 'type' => 'time', 'value' => $dokumen->jadwalRapat ? \Carbon\Carbon::parse($dokumen->jadwalRapat->waktu_rapat)->format('H:i') : ''],
                'Ruang Rapat' => ['name' => 'ruang_rapat', 'type' => 'text', 'value' => $dokumen->jadwalRapat->ruang_rapat ?? ''],
            ] as $label => $data)
                <x-form.input
                    name="{{ $data['name'] }}"
                    label="{{ $label }}"
                    type="{{ $data['type'] }}"
                    :value="old($data['name'], $data['value'])"
                    required
                />
            @endforeach
        </div>
    </div>
@endif