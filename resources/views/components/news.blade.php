@props(['pengumumanItems', 'informasiItems'])

<div class="w-full flex flex-col items-center mt-52">
    <h2 class="text-2xl font-bold text-[#03346E] mb-4">Pengumuman & Informasi</h2>

    <div class="bg-gray-100 rounded-full flex items-center px-2 py-1 mb-8 space-x-2">
        <button class="tab-button px-6 py-2 rounded-full font-medium text-[#03346E] transition-colors duration-300" id="tabPengumuman" onclick="changeTab('pengumuman')">
            Pengumuman
        </button>
        <button class="tab-button px-6 py-2 rounded-full font-medium text-[#03346E] transition-colors duration-300" id="tabBerita" onclick="changeTab('informasi')">
            Informasi
        </button>
    </div>

    <div class="relative w-full max-w-6xl flex items-center justify-center px-2 sm:px-6">
        <button class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-[#03346E] text-white rounded-full w-10 h-10 flex items-center justify-center z-10 shadow-md hover:bg-[#02264E] text-base sm:w-8 sm:h-8 sm:text-sm sm:-left-2" onclick="navigateSlide('left')">
            <i class="fas fa-chevron-left"></i>
        </button>

        <button class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-[#03346E] text-white rounded-full w-10 h-10 flex items-center justify-center z-10 shadow-md hover:bg-[#02264E] text-base sm:w-8 sm:h-8 sm:text-sm sm:-right-2" onclick="navigateSlide('right')">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div id="sliderContainer" class="w-full px-4 sm:px-6"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
    let currentSwiper = null;

    const pengumumanItems = @json($pengumumanItems);
    const informasiItems = @json($informasiItems);
    const baseUrl = "{{ asset('') }}";

    function renderSlider(items, type) {
        if (currentSwiper) {
            currentSwiper.destroy(true, true);
            currentSwiper = null;
        }

        const container = document.getElementById('sliderContainer');
        const leftBtn = document.querySelector('.relative.w-full button:first-of-type');
        const rightBtn = document.querySelector('.relative.w-full button:last-of-type');

        if (items.length === 0) {
            const message = type === 'pengumuman' ? 'pengumuman' : 'informasi';
            container.innerHTML = `<p class="text-gray-600 text-center py-8">Tidak ada ${message} yang tersedia saat ini.</p>`;
            if (leftBtn) leftBtn.style.display = 'none';
            if (rightBtn) rightBtn.style.display = 'none';
            return;
        } else {
            if (leftBtn) leftBtn.style.display = 'flex';
            if (rightBtn) rightBtn.style.display = 'flex';
        }

        container.innerHTML = `
            <div class="swiper w-full">
                <div class="swiper-wrapper">
                    ${items.map(item => `
                        <div class="swiper-slide w-full sm:w-72 flex-shrink-0 transition-transform duration-300 border rounded-xl p-4 shadow-sm hover:shadow-md bg-white">
                            ${item.image ? `<img src="${item.image}" alt="${item.judul}" class="mb-4 w-full h-40 object-cover rounded-md">` : `<div class="mb-4 w-full h-40 flex items-center justify-center bg-gray-200 rounded-md text-gray-500"><i class="fas fa-image fa-3x"></i></div>`}
                            <h3 class="font-bold text-[#03346E] text-lg">${item.judul}</h3>
                            ${type === 'pengumuman' ? `
                                <p class="text-sm text-gray-600 mt-2">
                                    <i class="fas fa-briefcase mr-2"></i> Nama Usaha: ${item.nama_usaha || 'N/A'}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-briefcase mr-2"></i> Bidang Usaha: ${item.bidang_usaha || 'N/A'}
                                </p>
                            ` : ''}
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-calendar-alt mr-2"></i> Dibuat pada: ${new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </p>
                            <a href="${type === 'pengumuman' ? '{{ route('user.pengumuman.show', '') }}' : '{{ route('user.informasi.show', '') }}'}/${item.id}" class="inline-flex items-center px-3 py-1 mt-2 text-sm font-medium text-white bg-[#03346E] rounded-lg hover:bg-[#02264E] transition-colors duration-200">
                                ${type === 'pengumuman' ? '<i class="fas fa-comment-dots mr-2"></i> Berikan Tanggapan' : '<i class="fas fa-info-circle mr-2"></i> Lihat Detail'}
                            </a>
                            </div>
                    `).join('')}
                </div>
            </div>
        `;

        currentSwiper = new Swiper('.swiper', {
            loop: false,
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
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('bg-[#03346E]', 'text-white');
            btn.classList.add('text-[#03346E]');
        });

        const activeBtn = tab === 'pengumuman' ? document.getElementById('tabPengumuman') : document.getElementById('tabBerita');
        if (activeBtn) {
            activeBtn.classList.remove('text-[#03346E]');
            activeBtn.classList.add('bg-[#03346E]', 'text-white');
        }

        renderSlider(tab === 'pengumuman' ? pengumumanItems : informasiItems, tab);
    }

    function navigateSlide(direction) {
        if (currentSwiper) {
            direction === 'left' ? currentSwiper.slidePrev() : currentSwiper.slideNext();
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        changeTab('pengumuman');
    });
</script>