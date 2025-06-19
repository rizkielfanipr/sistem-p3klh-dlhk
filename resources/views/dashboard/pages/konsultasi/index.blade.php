@extends('dashboard.layouts.adminlayout')

@section('title')
    Daftar Konsultasi {{ ucfirst($jenis) }}
@endsection

@section('content')
    <div>
        <x-button-add href="{{ route('konsultasi.create', ['jenis' => $jenis]) }}">
            Tambah Konsultasi {{ ucfirst($jenis) }}
        </x-button-add>
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

    <x-table>
        <x-slot name="head">
            <tr>
                @foreach ($tableHeadings as $heading)
                    <th class="px-6 py-3 text-{{ $heading === 'Aksi' ? 'center' : 'left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $heading }}
                    </th>
                @endforeach
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($konsultasi as $item)
                @php $detail = $item->detail->first(); @endphp
                <tr>
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
                                <!-- Tombol Lihat -->
                                <a href="{{ route('konsultasi.detail.show', $detail->id) }}"
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Tombol Edit -->
                                <a href="{{ route('konsultasi.edit', ['jenis' => $jenis, 'id' => $detail->id]) }}"
                                   class="text-green-600 hover:text-green-900"
                                   title="Edit Konsultasi">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('konsultasi.destroy', ['jenis' => $jenis, 'id' => $detail->id]) }}"
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
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-center" colspan="{{ count($tableHeadings) }}">
                        Tidak ada data konsultasi untuk jenis ini.
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-table>

    <div class="mt-4">
        {{ $konsultasi->links() }}
    </div>
@endsection
