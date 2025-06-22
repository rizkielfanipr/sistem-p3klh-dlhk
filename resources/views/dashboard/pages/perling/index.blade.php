@extends('dashboard.layouts.adminlayout')

@section('title', $title)

@section('content')
<div>
    <x-button-add href="{{ route('perling.create') }}">
        Tambah {{ $buttonText }}
    </x-button-add>
</div>

@if(session('success'))
    <x-alert type="success" :message="session('success')" />
@endif

@if(session('error'))
    <x-alert type="error" :message="session('error')" />
@endif

@php
    $tableHeadings = ['Nama Pemohon', 'Nama Usaha', 'Alamat Usaha', 'Jenis Perling', 'Tanggal Submit', 'Aksi'];
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
        @forelse($dokumenList as $dokumen)
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $dokumen->nama_pemohon }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $dokumen->nama_usaha }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $dokumen->alamat_usaha }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $dokumen->jenisPerling->nama_perling }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $dokumen->created_at->format('d-m-Y H:i') }}</td>
                <td class="px-6 py-4 text-center text-sm font-medium">
                    <div class="inline-flex items-center justify-center space-x-2">
                        <x-button-edit :href="route('perling.edit', $dokumen->id)" />
                        <x-button-delete :action="route('perling.destroy', $dokumen->id)" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    Tidak ada dokumen {{ strtolower($buttonText) }} yang ditemukan.
                </td>
            </tr>
        @endforelse
    </x-slot>
</x-table>
@endsection
