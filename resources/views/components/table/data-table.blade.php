<x-table>
    <x-slot name="head">
        <tr>
            @foreach ($headings as $heading)
                <th class="px-6 py-3 text-{{ $heading === 'Aksi' ? 'center' : 'left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $heading }}
                </th>
            @endforeach
        </tr>
    </x-slot>

    <x-slot name="body">
        <tbody id="{{ $bodyId }}">
            {{ $slot }} {{-- This slot will contain the actual table rows --}}
        </tbody>
        <tr id="noResultsRow" class="hidden"> {{-- Hidden by default, shown by JS --}}
            <td class="px-6 py-4 whitespace-nowrap text-center" colspan="{{ count($headings) }}">
                Tidak ada data.
            </td>
        </tr>
    </x-slot>
</x-table>

@push('scripts')
<script>
    function {{ $filterFunction }}() {
        let input = document.getElementById('{{ $searchId }}');
        let filter = input.value.toLowerCase();

        let tableBody = document.getElementById('{{ $bodyId }}');
        let tr = tableBody.getElementsByTagName('tr');

        let foundResults = false;
        let noResultsRow = document.getElementById('noResultsRow'); // Moved here for scope

        for (let i = 0; i < tr.length; i++) {
            // Skip the noResultsRow itself if it's within the main tr collection
            if (tr[i].id === 'noResultsRow') {
                continue;
            }

            let displayRow = false;
            // Get all cells except the last one (Aksi column)
            let tds = Array.from(tr[i].children).slice(0, -1);

            for (let j = 0; j < tds.length; j++) {
                let cellText = tds[j].textContent || tds[j].innerText;
                if (cellText.toLowerCase().includes(filter)) {
                    displayRow = true;
                    break;
                }
            }

            if (displayRow) {
                tr[i].style.display = '';
                foundResults = true;
            } else {
                tr[i].style.display = 'none';
            }
        }

        if (noResultsRow) {
            if (!foundResults && filter !== '') {
                noResultsRow.children[0].textContent = 'Tidak ada yang cocok dengan pencarian Anda.';
                noResultsRow.style.display = '';
            } else if (!foundResults && filter === '') {
                noResultsRow.children[0].textContent = 'Tidak ada data.';
                noResultsRow.style.display = '';
            } else {
                noResultsRow.style.display = 'none';
            }
        }
    }

    // Call the filter function on DOMContentLoaded to set initial state
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure the initial "Tidak ada data." message is shown if the table is empty from the start
        const tableBody = document.getElementById('{{ $bodyId }}');
        const noResultsRow = document.getElementById('noResultsRow');

        if (tableBody.children.length === 0 || (tableBody.children.length === 1 && tableBody.children[0].id === 'noResultsRow')) {
             noResultsRow.children[0].textContent = 'Tidak ada data.';
             noResultsRow.style.display = '';
        } else {
            // Hide the noResultsRow if there are actual data rows
            noResultsRow.style.display = 'none';
        }

        // Call the filter function in case there's an initial search query or state to be handled
        {{ $filterFunction }}();
    });
</script>
@endpush