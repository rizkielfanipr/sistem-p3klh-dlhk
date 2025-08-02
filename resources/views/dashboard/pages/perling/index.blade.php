@extends('dashboard.layouts.adminlayout')

@section('title', $title)
@section('breadcrumb', 'Daftar Perling')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <x-button-add href="{{ route('perling.create') }}" class="mb-4 md:mb-0">
            Tambah {{ $buttonText }}
        </x-button-add>

        {{-- Search input --}}
        <x-table.search-input id="searchInput" onkeyup="filterPerlingTable()" placeholder="Cari dokumen perling..." />
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        $tableHeadings = [
            'Kode Perling',
            'Nama Pemohon',
            'Nama Usaha',
            'Jenis Perling',
            'Tanggal Submit',
            'Status Terakhir',
            'Aksi',
        ];
    @endphp

    {{-- Use x-table.data-table component --}}
    <x-table.data-table :headings="$tableHeadings" bodyId="perlingTableBody" searchId="searchInput" filterFunction="filterPerlingTable">
        @forelse ($dokumenList as $dokumen)
            @php
                $statusTerakhir = $dokumen->progresDokumen
                    ->sortByDesc('created_at')
                    ->first()?->status->nama_status
                    ?? 'Belum Ada Status';
            @endphp

            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $dokumen->kode_perling }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $dokumen->nama_pemohon }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $dokumen->nama_usaha }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $dokumen->jenisPerling->nama_perling }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $dokumen->created_at->format('d-m-Y H:i') }}
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="inline-block px-3 py-1 text-sm rounded-full
                        @switch($statusTerakhir)
                            @case('Diajukan')                   bg-blue-100   text-blue-800   @break
                            @case('Pemeriksaan Administrasi')   bg-yellow-100 text-yellow-800 @break
                            @case('Administrasi Lengkap')       bg-green-100  text-green-800  @break
                            @case('Perbaikan Administrasi')     bg-red-100    text-red-800    @break
                            @case('Pengumuman Publik')          bg-purple-100 text-purple-800 @break
                            @case('Rapat Koordinasi')           bg-indigo-100 text-indigo-800 @break
                            @case('Substansi Lengkap')          bg-green-100  text-green-800  @break
                            @case('Perbaikan Substansi')        bg-red-200    text-red-900    @break
                            @case('Proses Penerbitan')          bg-yellow-200 text-yellow-900 @break
                            @case('Terbit')                     bg-green-200  text-green-900  @break
                            @default                            bg-gray-100   text-gray-800
                        @endswitch">
                        {{ $statusTerakhir }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center text-sm font-medium">
                    <div class="inline-flex items-center justify-center space-x-3">
                        <a href="{{ route('perling.detail', $dokumen->id) }}"
                           class="text-blue-600 hover:text-blue-900"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('perling.edit', $dokumen->id) }}"
                           class="text-green-600 hover:text-green-900"
                           title="Edit Dokumen">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('perling.destroy', $dokumen->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-900"
                                    title="Hapus Dokumen">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            {{-- "Tidak ada data" message is now handled by x-table.data-table --}}
        @endforelse
    </x-table.data-table>
@endsection

@push('scripts')
<script>
    function filterPerlingTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("perlingTableBody"); // Ensure this ID matches your table body ID
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            tr[i].style.display = "none"; // Hide all rows initially

            // Loop through all cells in the current row
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ""; // Show the row if a match is found
                        break; // No need to check other cells in this row
                    }
                }
            }
        }

        // Handle "No data found" message
        var visibleRows = 0;
        for (i = 0; i < tr.length; i++) {
            if (tr[i].style.display !== "none") {
                visibleRows++;
            }
        }

        var noDataRow = document.getElementById("noDataRow");
        if (visibleRows === 0) {
            if (!noDataRow) {
                var newRow = table.insertRow();
                newRow.id = "noDataRow";
                var cell = newRow.insertCell();
                cell.colSpan = {{ count($tableHeadings) }}; // Span across all columns
                cell.className = "px-6 py-4 text-center text-sm text-gray-500";
                cell.textContent = "Tidak ada dokumen {{ strtolower($buttonText) }} yang ditemukan.";
            } else {
                noDataRow.style.display = "";
            }
        } else {
            if (noDataRow) {
                noDataRow.style.display = "none";
            }
        }
    }

    // Call the filter function initially to handle the "no data" message if the table is empty on load
    document.addEventListener('DOMContentLoaded', filterPerlingTable);
</script>
@endpush