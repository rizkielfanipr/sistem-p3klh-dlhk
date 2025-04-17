<nav class="bg-white border-b fixed w-full z-50 font-montserrat" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative h-20 flex items-center justify-between">

        <!-- Kiri: Logo -->
        <div class="flex items-center space-x-4">
            <a href="#">
                <img src="{{ asset('logo-dlhk.png') }}" alt="Logo" class="h-12 w-auto">
            </a>
        </div>

        <!-- Tengah: Menu Navigasi (Desktop) -->
        <x-navmenu />

        <!-- Kanan: Konsultasi (Desktop) -->
        <div class="hidden md:block">
            <a href="#" class="bg-[#03346E] text-white px-4 py-2 rounded-lg hover:border hover:border-blue-700 transition duration-300 text-sm flex items-center gap-2">
                <i class="fas fa-phone"></i> Konsultasi
            </a>
        </div>

        <!-- Hamburger Button (Mobile) -->
        <div class="md:hidden">
            <button @click="open = !open" class="text-gray-700 focus:outline-none">
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div x-show="open" x-transition class="md:hidden bg-white border-t px-4 pt-2 pb-4 space-y-2">
        <x-navmenu-mobile />
    </div>
</nav>
