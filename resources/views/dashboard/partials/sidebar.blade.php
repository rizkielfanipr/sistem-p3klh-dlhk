<?php
$menus = [
    [
        'label' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'route' => 'dashboard', // Pastikan route ini ada di web.php
    ],
    [
        'label' => 'Layanan',
        'icon' => 'fas fa-concierge-bell',
        'sub' => [
            ['label' => 'Penapisan Dokling', 'route' => 'penapisan-dokling'], 
            ['label' => 'Layanan 2', 'route' => '#'], // Link kosong tetap menggunakan '#'
            ['label' => 'Layanan 3', 'route' => '#'], 
            ['label' => 'Layanan 4', 'route' => '#'], 
            ['label' => 'Layanan 5', 'route' => '#'], 
            ['label' => 'Layanan 6', 'route' => '#']
        ]
    ],
    [
        'label' => 'Pengumuman',
        'icon' => 'fas fa-bullhorn',
        'route' => '#', // Link kosong tetap menggunakan '#'
    ],
    [
        'label' => 'Pengguna',
        'icon' => 'fas fa-users',
        'sub' => [
            ['label' => 'Admin', 'route' => 'users.admin'], // Rute untuk daftar admin
            ['label' => 'Front Office', 'route' => 'users.fo'], // Rute untuk daftar front office
            ['label' => 'Pengguna', 'route' => 'users.pengguna'], // Rute untuk daftar pengguna
        ]
    ],
    [
        'label' => 'Publikasi',
        'icon' => 'fas fa-newspaper',
        'route' => '#', // Link kosong tetap menggunakan '#'
    ],
    [
        'label' => 'Konsultasi',
        'icon' => 'fas fa-comments',
        'route' => '#', // Link kosong tetap menggunakan '#'
    ],
    [
        'label' => 'Forum Diskusi',
        'icon' => 'fas fa-users',
        'route' => '#', // Link kosong tetap menggunakan '#'
    ]
];
?>

<aside
    class="fixed inset-y-0 left-0 z-30 bg-gray-800 text-white transition-all duration-300 ease-in-out"
    :class="{
        'w-64': !sidebarCollapse,
        'w-20': sidebarCollapse,
        '-translate-x-full md:translate-x-0': !sidebarOpen,
        'translate-x-0': sidebarOpen
    }"
    @click.outside="sidebarOpen = false"
>
    <div class="p-4 flex items-center justify-between border-b border-gray-700">
        <span x-show="!sidebarCollapse" class="text-xl font-semibold">Menu</span>
        <button @click="sidebarCollapse = !sidebarCollapse" class="text-white md:block hidden">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="p-4" x-data="{ openLayanan: false, openPengguna: false }">
        <?php foreach ($menus as $menu): ?>
            <?php if (isset($menu['sub'])): ?>
                <?php $key = strtolower(str_replace(' ', '', $menu['label'])); ?>
                <button
                    @click="open<?= ucfirst($key); ?> = !open<?= ucfirst($key); ?>"
                    class="w-full text-left flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded"
                >
                    <i class="<?= $menu['icon']; ?>"></i>
                    <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                    <i :class="{'rotate-180': open<?= ucfirst($key); ?>}" class="fas fa-chevron-down ml-auto transition-transform" x-show="!sidebarCollapse"></i>
                </button>
                <div x-show="open<?= ucfirst($key); ?>" x-transition class="mt-2 pl-6 space-y-2" x-cloak>
                    <?php foreach ($menu['sub'] as $submenu): ?>
                        <a href="<?= $submenu['route'] !== '#' ? route($submenu['route']) : '#'; ?>" class="flex items-center text-sm gap-2 py-1 hover:text-gray-300">
                            <i class="fas fa-circle text-xs"></i>
                            <span x-show="!sidebarCollapse"><?= $submenu['label']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <a href="<?= $menu['route'] !== '#' ? route($menu['route']) : '#'; ?>" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                    <i class="<?= $menu['icon']; ?>"></i>
                    <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>
