@extends('dashboard.layouts.adminlayout')

@section('title', 'Daftar Informasi')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <x-button-add href="{{ route('informasi.create') }}" class="mb-4 md:mb-0">
            Tambah Informasi
        </x-button-add>

        <x-table.search-input id="searchInput" onkeyup="filterInformasiTable()" placeholder="Cari informasi..." />
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        $tableHeadings = ['Judul', 'Deskripsi', 'Tanggal Dibuat', 'Aksi'];
    @endphp

    <x-table.data-table :headings="$tableHeadings" bodyId="informasiTableBody" searchId="searchInput" filterFunction="filterInformasiTable">
        @forelse($informasi as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $item->judul }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ Str::limit($item->description, 50) }} {{-- Limit description for table view --}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->created_at->format('d M Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('informasi.show', $item->id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('informasi.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('informasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus informasi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            {{-- "Tidak ada data" message is handled by x-table.data-table --}}
        @endforelse
    </x-table.data-table>

    <div class="mt-4">
        {{ $informasi->links() }}
    </div>

@endsection

@section('scripts')
<script>
    function filterInformasiTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("informasiTableBody");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            let rowMatches = false;
            // Loop through all table data cells (td) in the current row
            for (j = 0; j < tr[i].getElementsByTagName("td").length - 1; j++) { // Exclude the last column (Aksi)
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowMatches = true;
                        break; // No need to check other cells in this row
                    }
                }
            }
            if (rowMatches) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
</script>
@endsection