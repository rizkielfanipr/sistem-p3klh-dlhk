<div class="relative w-full h-screen overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E]">
    <!-- Memanggil komponen pattern -->
    @include('components.pattern')

    <!-- Overlay gelap tambahan -->
    <div class="absolute inset-0 bg-black/10 z-10"></div>

    <!-- Konten -->
    <div class="relative z-20 flex items-center justify-center h-full px-4 md:px-20 text-center">
        <div class="text-white space-y-6 max-w-3xl">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold leading-tight">
                Sistem Pelayanan Persetujuan Lingkungan
                Dinas Lingkungan Hidup dan Kehutanan
                Daerah Istimewa Yogyakarta
            </h1>
            <p class="text-base sm:text-sm md:text-md font-light">
                Informasi Pelayanan dan Proses pengurusan persetujuan lingkungan<br class="hidden md:block" />
                (AMDAL, UKL UPL, SPPL, DELH, DPLH)
            </p>
        </div>
    </div>
</div>
