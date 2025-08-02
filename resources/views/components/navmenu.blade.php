@php
    $menuItems = [
        ['name' => 'Beranda', 'link' => '/'], // Tetap ke root
        ['name' => 'Layanan', 'link' => route('beranda.layanan')], // Menggunakan rute bernama
        ['name' => 'Informasi', 'link' => route('beranda.informasi')], // Menggunakan rute bernama
        ['name' => 'Pengumuman', 'link' => route('beranda.pengumuman')], // Menggunakan rute bernama
        ['name' => 'Kontak', 'link' => route('beranda.kontak')] // Menggunakan rute bernama
    ];
@endphp

<div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 space-x-2">
    @foreach ($menuItems as $item)
        <a href="{{ $item['link'] }}" class="text-gray-700 hover:bg-gray-100 py-2 px-4 rounded-md text-sm">{{ $item['name'] }}</a>
    @endforeach
</div>