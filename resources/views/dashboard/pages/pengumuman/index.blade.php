@extends('dashboard.layouts.adminlayout')

@section('title', 'Daftar Pengumuman')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <x-button-add href="{{ route('pengumuman.create') }}" class="mb-4 md:mb-0">
            Tambah Pengumuman
        </x-button-add>

        <x-table.search-input id="searchInput" onkeyup="filterPengumumanTable()" placeholder="Cari pengumuman..." />
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        // Tambahkan 'Status' ke dalam array heading tabel
        $tableHeadings = ['Nama Usaha', 'Bidang Usaha', 'Lokasi', 'Pemrakarsa', 'Tanggal', 'Status', 'Aksi'];
    @endphp

    {{-- Perubahan di sini: Gunakan x-table.data-table --}}
    <x-table.data-table :headings="$tableHeadings" bodyId="pengumumanTableBody" searchId="searchInput" filterFunction="filterPengumumanTable">
        @forelse($pengumuman as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $item->nama_usaha }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->bidang_usaha }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->lokasi }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->pemrakarsa }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->created_at->format('d M Y') }}
                </td>
                {{-- Kolom Status baru --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @php
                        $statusColorClass = $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColorClass }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('pengumuman.show', $item->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('pengumuman.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('pengumuman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            {{-- Pesan "Tidak ada data" sekarang ditangani oleh x-table.data-table --}}
        @endforelse
    </x-table.data-table>

    {{-- Added pagination (if you're using it) --}}
    {{-- <div class="mt-4">
        {{ $pengumuman->links() }}
    </div> --}}

@endsection

{{-- Tidak perlu lagi @push('scripts') di sini karena sudah di dalam komponen data-table --}}