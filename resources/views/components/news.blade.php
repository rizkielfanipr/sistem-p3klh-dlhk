<!-- resources/views/components/news.blade.php -->
@php
    $pengumumanItems = [
        ['image' => 'images/pengumuman1.jpg', 'title' => 'Pengumuman Libur Nasional', 'excerpt' => 'Pemerintah mengumumkan libur nasional pada tanggal...'],
        ['image' => 'images/pengumuman2.jpg', 'title' => 'Perubahan Jam Kerja', 'excerpt' => 'Jam kerja pegawai berubah selama bulan Ramadan...'],
        ['image' => 'images/pengumuman3.jpg', 'title' => 'Pemadaman Listrik', 'excerpt' => 'Akan ada pemadaman listrik di wilayah tertentu...'],
        ['image' => 'images/pengumuman4.jpg', 'title' => 'Pendaftaran Beasiswa', 'excerpt' => 'Pendaftaran beasiswa dibuka hingga akhir bulan ini...'],
        ['image' => 'images/pengumuman5.jpg', 'title' => 'Kegiatan Sosialisasi', 'excerpt' => 'Akan ada kegiatan sosialisasi mengenai...'],
        ['image' => 'images/pengumuman6.jpg', 'title' => 'Penerimaan Karyawan Baru', 'excerpt' => 'Kami membuka lowongan untuk posisi...']
    ];

    $beritaItems = [
        ['image' => 'images/berita1.jpg', 'title' => 'Berita A', 'excerpt' => 'Ini adalah berita terbaru A...'],
        ['image' => 'images/berita2.jpg', 'title' => 'Berita B', 'excerpt' => 'Berita penting seputar kebijakan terbaru...'],
        ['image' => 'images/berita3.jpg', 'title' => 'Berita C', 'excerpt' => 'Informasi umum untuk masyarakat...'],
        ['image' => 'images/berita4.jpg', 'title' => 'Berita D', 'excerpt' => 'Berita terkini mengenai lingkungan...'],
        ['image' => 'images/berita5.jpg', 'title' => 'Berita E', 'excerpt' => 'Update terbaru dari Dinas Lingkungan Hidup...'],
        ['image' => 'images/berita6.jpg', 'title' => 'Berita F', 'excerpt' => 'Kegiatan terbaru yang dilakukan oleh...']
    ];
@endphp

<div class="w-full flex flex-col items-center mt-52">
    <!-- Judul -->
    <h2 class="text-2xl font-bold text-[#03346E] mb-4">Pengumuman & Berita</h2>

    <!-- Tab Navigasi -->
    <div class="bg-gray-100 rounded-full flex items-center px-2 py-1 mb-8 space-x-2">
        <button class="tab-button px-6 py-2 rounded-full font-medium text-[#03346E] transition-colors duration-300" id="tabPengumuman" onclick="changeTab('pengumuman')">
            Pengumuman
        </button>
        <button class="tab-button px-6 py-2 rounded-full font-medium text-[#03346E] transition-colors duration-300" id="tabBerita" onclick="changeTab('berita')">
            Berita
        </button>
    </div>

    <!-- Kontainer Slider -->
    <div class="relative w-full max-w-6xl flex items-center justify-center px-2 sm:px-6">
        <!-- Tombol Navigasi Kiri -->
        <button class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-[#03346E] text-white rounded-full w-10 h-10 flex items-center justify-center z-10 shadow-md hover:bg-[#02264E] text-base sm:w-8 sm:h-8 sm:text-sm sm:-left-2" onclick="navigateSlide('left')">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- Tombol Navigasi Kanan -->
        <button class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-[#03346E] text-white rounded-full w-10 h-10 flex items-center justify-center z-10 shadow-md hover:bg-[#02264E] text-base sm:w-8 sm:h-8 sm:text-sm sm:-right-2" onclick="navigateSlide('right')">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Kontainer Dinamis -->
        <div id="sliderContainer" class="w-full px-4 sm:px-6"></div>
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
    let currentSwiper = null;

    const pengumumanItems = @json($pengumumanItems);
    const beritaItems = @json($beritaItems);

    function renderSlider(items) {
        const container = document.getElementById('sliderContainer');
        container.innerHTML = `
            <div class="swiper w-full">
                <div class="swiper-wrapper">
                    ${items.map(item => `
                        <div class="swiper-slide w-full sm:w-72 flex-shrink-0 transition-transform duration-300 border rounded-xl p-4 shadow-sm hover:shadow-md bg-white">
                            <img src="${asset(item.image)}" alt="${item.title}" class="mb-4 w-full h-40 object-cover rounded-md">
                            <h3 class="font-bold text-[#03346E] text-lg">${item.title}</h3>
                            <p class="text-sm text-gray-600 mt-2">${item.excerpt}</p>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        currentSwiper = new Swiper('.swiper', {
            loop: true,
            spaceBetween: 16,
            slidesPerView: 3,
            breakpoints: {
                1280: { slidesPerView: 3 },
                1024: { slidesPerView: 2 },
                640: { slidesPerView: 1 },
                0: { slidesPerView: 1 }
            }
        });
    }

    function changeTab(tab) {
        // Reset semua tab
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('bg-[#03346E]', 'text-white');
            btn.classList.add('text-[#03346E]');
        });

        // Aktifkan tab sesuai pilihan
        const activeBtn = tab === 'pengumuman' ? document.getElementById('tabPengumuman') : document.getElementById('tabBerita');
        activeBtn.classList.remove('text-[#03346E]');
        activeBtn.classList.add('bg-[#03346E]', 'text-white');

        // Render konten slider
        renderSlider(tab === 'pengumuman' ? pengumumanItems : beritaItems);
    }

    function navigateSlide(direction) {
        if (currentSwiper) {
            direction === 'left' ? currentSwiper.slidePrev() : currentSwiper.slideNext();
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        changeTab('pengumuman');
    });

    // Laravel asset helper
    function asset(path) {
        return `{{ asset('') }}` + path;
    }
</script>
