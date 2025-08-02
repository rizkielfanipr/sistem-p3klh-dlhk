@extends('dashboard.layouts.adminlayout')

@section('title', $title)
@section('breadcrumb', 'Pengguna')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <x-button-add href="{{ route('users.create') }}" class="mb-4 md:mb-0">
            Tambah {{ $buttonText }}
        </x-button-add>

        {{-- Search input --}}
        <x-table.search-input id="searchInput" onkeyup="filterUsersTable()" placeholder="Cari pengguna..." />
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        $tableHeadings = ['Nama', 'Email', 'Role', 'No. Telepon', 'Aksi'];
        $userProperties = ['nama', 'email', 'role.nama_role', 'no_telp'];
    @endphp

    {{-- Use x-table.data-table component --}}
    <x-table.data-table :headings="$tableHeadings" bodyId="usersTableBody" searchId="searchInput" filterFunction="filterUsersTable">
        @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                @foreach ($userProperties as $property)
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ data_get($user, $property) }}
                    </td>
                @endforeach
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <div class="inline-flex items-center justify-center space-x-2">
                        <x-button-edit :href="route('users.edit', $user->id)" />
                        <x-button-delete :action="route('users.destroy', $user->id)" />
                    </div>
                </td>
            </tr>
        @empty
            {{-- "Tidak ada data" message is now handled by x-table.data-table and JS --}}
        @endforelse
    </x-table.data-table>
@endsection

@push('scripts')
<script>
    function filterUsersTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("usersTableBody"); // Ensure this ID matches your table body ID
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
                cell.textContent = "Tidak ada pengguna yang ditemukan.";
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
    document.addEventListener('DOMContentLoaded', filterUsersTable);
</script>
@endpush