@extends('dashboard.layouts.adminlayout')

@section('title')
    Daftar Konsultasi {{ ucfirst($jenis) }}
@endsection
@section('breadcrumb', 'Daftar Konsultasi')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <x-button-add href="{{ route('konsultasi.create', ['jenis' => $jenis]) }}" class="mb-4 md:mb-0">
            Tambah Konsultasi {{ ucfirst($jenis) }}
        </x-button-add>

        {{-- Search input --}}
        <x-table.search-input id="searchInput" onkeyup="filterKonsultasiTable()" placeholder="Cari konsultasi..." />
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        $tableHeadings = ['Kode Konsultasi', 'Nama', 'Jenis Konsultasi', 'Topik', 'Status', 'Tanggal', 'Aksi'];
    @endphp

    {{-- Use x-table.data-table component --}}
    <x-table.data-table :headings="$tableHeadings" bodyId="konsultasiTableBody" searchId="searchInput" filterFunction="filterKonsultasiTable">
        @forelse($konsultasi as $item)
            @php $detail = $item->detail->first(); @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $detail?->kode_konsultasi ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->user->nama ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 capitalize">{{ $item->jenisKonsultasi->nama_jenis ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $detail?->topik->nama_topik ?? '-' }}</td>
                <td class="px-6 py-4 text-sm">
                    @php
                        $status = strtolower($detail?->status->nama_status ?? '');
                        $bgColor = match($status) {
                            'diajukan' => 'bg-blue-100 text-blue-800',
                            'diproses' => 'bg-yellow-100 text-yellow-800',
                            'selesai'  => 'bg-green-100 text-green-800',
                            default    => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bgColor }}">
                        {{ $detail?->status->nama_status ?? '-' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                </td>
                <td class="px-6 py-4 text-center text-sm font-medium">
                    <div class="inline-flex items-center justify-center space-x-3">
                        @if($detail)
                            <a href="{{ route('konsultasi.detail.show', $detail->id) }}"
                               class="text-blue-600 hover:text-blue-900"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('konsultasi.edit', ['jenis' => $jenis, 'konsultasi' => $detail->id]) }}"
                               class="text-green-600 hover:text-green-900"
                               title="Edit Konsultasi">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('konsultasi.destroy', ['jenis' => $jenis, 'konsultasi' => $detail->id]) }}"
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus konsultasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus Konsultasi">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 italic">Belum ada detail</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            {{-- "Tidak ada data" message is now handled by x-table.data-table --}}
        @endforelse
    </x-table.data-table>

    <div class="mt-4">
        {{ $konsultasi->links() }}
    </div>
@endsection

@push('scripts')
<script>
    function filterKonsultasiTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("konsultasiTableBody"); // Ensure this ID matches your table body ID
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
                cell.textContent = "Tidak ada data konsultasi yang ditemukan.";
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
    document.addEventListener('DOMContentLoaded', filterKonsultasiTable);
</script>
@endpush