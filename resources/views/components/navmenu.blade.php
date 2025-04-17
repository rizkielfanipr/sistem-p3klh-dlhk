@php
    $menuItems = [
        ['name' => 'Beranda', 'link' => '#beranda'],
        ['name' => 'Layanan', 'link' => '#layanan'],
        ['name' => 'Publikasi', 'link' => '#publikasi'],
        ['name' => 'Pengumuman', 'link' => '#pengumuman'],
        ['name' => 'Kontak', 'link' => '#kontak']
    ];
@endphp

<div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 space-x-2">
    @foreach ($menuItems as $item)
        <a href="{{ $item['link'] }}" class="text-gray-700 hover:bg-gray-100 py-2 px-4 rounded-md text-sm">{{ $item['name'] }}</a>
    @endforeach
</div>
