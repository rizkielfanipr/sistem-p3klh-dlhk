{{-- resources/views/components/page-header.blade.php --}}
@php
    $title = $title ?? 'Judul Halaman';
    $description = $description ?? 'Deskripsi halaman';
@endphp

{{-- Mengembalikan ukuran ke non-full-screen sambil mempertahankan gradien dan padding --}}
<div class="relative w-full overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E] py-12 pt-32">
    <!-- Pola Background SVG telah dihapus di sini -->

    <!-- Content -->
    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4 leading-tight">
            {{ $title }}
        </h2>
        <p class="inline-block bg-white text-[#001F3F] rounded-full px-4 py-1 text-xs sm:text-base shadow-lg">
            {{ $description }}
        </p>
    </div>
</div>
