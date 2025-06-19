@extends('dashboard.layouts.adminlayout')

@section('title', 'Daftar Pengumuman')

@section('content')
    <div>
        <x-button-add href="{{ route('pengumuman.create') }}">
            Tambah Pengumuman
        </x-button-add>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @php
        use Illuminate\Support\Str;

        $tableHeadings = ['Judul', 'Tanggal', 'Konten', 'Aksi'];
        $pengumumanProperties = ['judul', 'tanggal', 'konten'];
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
            @foreach($pengumuman as $item)
                <tr>
                    @foreach ($pengumumanProperties as $property)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($property === 'tanggal')
                                {{ \Carbon\Carbon::parse(data_get($item, $property))->format('d M Y') }}
                            @elseif ($property === 'konten')
                                {{ Str::limit(strip_tags(data_get($item, $property)), 100) }}
                            @else
                                {{ data_get($item, $property) }}
                            @endif
                        </td>
                    @endforeach
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="inline-flex items-center justify-center space-x-2">
                            <x-button-edit :href="route('pengumuman.edit', $item->id)" />
                            <x-button-delete :action="route('pengumuman.destroy', $item->id)" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>
@endsection
