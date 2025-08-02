@php
    $menuItems = [
        ['name' => 'Beranda', 'link' => '#beranda'],
        ['name' => 'Layanan', 'link' => '#layanan'],
        ['name' => 'Informasi', 'link' => '#publikasi'],
        ['name' => 'Pengumuman', 'link' => '#pengumuman'],
        ['name' => 'Kontak', 'link' => '#kontak'],
        ['name' => 'Konsultasi', 'link' => '#', 'is_button' => true]
    ];
@endphp

<div x-show="open" x-transition class="md:hidden bg-white border-t px-4 pt-2 pb-4 space-y-2">
    @foreach ($menuItems as $item)
        @if (isset($item['is_button']) && $item['is_button'])
            <a href="{{ $item['link'] }}" class="flex text-center justify-center bg-[#03346E] text-white px-4 py-2 rounded-lg text-sm gap-2 hover:border hover:border-blue-700 transition duration-300">
                <i class="fas fa-phone"></i> {{ $item['name'] }}
            </a>
        @else
            <a href="{{ $item['link'] }}" class="flex text-center justify-center text-gray-700 hover:bg-gray-100 text-sm py-2 px-3 rounded-md">
                {{ $item['name'] }}
            </a>
        @endif
    @endforeach
</div>
