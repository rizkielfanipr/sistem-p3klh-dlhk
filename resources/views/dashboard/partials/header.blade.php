<?php
    $username = "User"; // Misalnya dari session nanti
?>

<header class="fixed top-0 left-0 right-0 bg-white shadow px-4 py-3 flex items-center justify-between z-10">
    <!-- Tombol Hamburger (untuk mobile) -->
    <button class="text-gray-600 focus:outline-none md:hidden" @click="sidebarOpen = !sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Dropdown User -->
    <div class="flex items-center gap-3 ml-auto relative" x-data="{ dropdownOpen: false }">
        <span class="text-gray-600 hidden md:block">Hello, <?= $username; ?></span>

        <!-- Tombol Avatar -->
        <button @click="dropdownOpen = !dropdownOpen" class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white">
            <i class="fas fa-user"></i>
        </button>

        <!-- Menu Dropdown -->
        <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute top-full mt-1 right-0 w-48 bg-white shadow-lg rounded-md z-30">
            <ul class="flex flex-col p-2 text-gray-700">
                <li>
                    <a href="#" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                        <i class="fas fa-id-card"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
