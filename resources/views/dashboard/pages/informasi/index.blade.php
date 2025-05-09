@extends('dashboard.layouts.adminlayout')

@section('title')
    @if (request()->routeIs('informasi.pengumuman'))
        Daftar Pengumuman
    @elseif (request()->routeIs('informasi.publikasi'))
        Daftar Publikasi
    @endif
@endsection

@section('content')
    <div>
        @if (request()->routeIs('informasi.pengumuman'))
            <x-button-add href="{{ route('informasi.create') }}?jenis=pengumuman">
                Tambah Pengumuman
            </x-button-add>
        @elseif (request()->routeIs('informasi.publikasi'))
            <x-button-add href="{{ route('informasi.create') }}?jenis=publikasi">
                Tambah Publikasi
            </x-button-add>
        @endif
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        $tableHeadings = ['Judul', 'Jenis Informasi', 'Tanggal', 'Aksi'];
        $informasiProperties = ['judul', 'jenisInformasi.nama_jenis', 'tanggal'];
    @endphp

    <x-table>
        <x-slot name="head">
            @foreach ($tableHeadings as $heading)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $heading }}
                </th>
            @endforeach
        </x-slot>
        <x-slot name="body">
            @foreach($informasi as $item)
                <tr>
                    @foreach ($informasiProperties as $property)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($property === 'tanggal')
                                {{ \Carbon\Carbon::parse(data_get($item, $property))->format('d M Y') }}
                            @else
                                {{ data_get($item, $property) }}
                            @endif
                        </td>
                    @endforeach
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-2">
                            <x-button-edit :href="route('informasi.edit', $item->id)" />
                            <x-button-delete :action="route('informasi.destroy', $item->id)" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>
@endsection
