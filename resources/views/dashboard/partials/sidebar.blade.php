<?php
$menus = [
    [
        'label' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt text-blue-500',
        'route' => 'dashboard',
    ],
    [
        'label' => 'Layanan',
        'icon' => 'fas fa-concierge-bell text-green-500',
        'route' => 'layanan.index',  // Menghilangkan submenu dan menggantinya langsung dengan route
    ],
    [
        'label' => 'Pengumuman',
        'icon' => 'fas fa-bullhorn text-yellow-500',
        'route' => 'informasi.pengumuman',
    ],
    [
        'label' => 'Pengguna',
        'icon' => 'fas fa-users text-red-500',
        'sub' => [
            ['label' => 'Admin', 'route' => 'users.admin', 'icon' => 'fas fa-user-shield text-blue-500'],
            ['label' => 'Front Office', 'route' => 'users.fo', 'icon' => 'fas fa-user-tie text-green-500'],
            ['label' => 'Pengguna', 'route' => 'users.pengguna', 'icon' => 'fas fa-user text-gray-500'],
        ]
    ],
    [
        'label' => 'Publikasi',
        'icon' => 'fas fa-newspaper text-purple-500',
        'route' => 'informasi.publikasi',
    ],
    [
        'label' => 'Konsultasi',
        'icon' => 'fas fa-comments text-pink-500',
        'sub' => [
            ['label' => 'Konsultasi Daring', 'route' => '#', 'icon' => 'fas fa-laptop text-blue-500'],  // Sub-menu Konsultasi Daring
            ['label' => 'Konsultasi Luring', 'route' => '#', 'icon' => 'fas fa-phone-alt text-green-500'],  // Sub-menu Konsultasi Luring
        ]
    ],
    [
        'label' => 'Forum Diskusi',
        'icon' => 'fas fa-users text-indigo-500',
        'route' => 'forum.index',
    ]
];
?>

<aside class="fixed inset-y-0 left-0 z-30 bg-white text-black transition-all duration-300 ease-in-out border border-gray-300" :class="{ 'w-64': !sidebarCollapse, 'w-20': sidebarCollapse, '-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }" @click.outside="sidebarOpen = false">
    <div class="p-4 flex items-center justify-between border-b border-gray-300">
        <span x-show="!sidebarCollapse" class="text-xl font-semibold">P3KLH</span>
        <button @click="sidebarCollapse = !sidebarCollapse" class="text-black md:block hidden">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="p-4 space-y-2" x-data="{ openPengguna: false, openKonsultasi: false }">
        <?php foreach ($menus as $menu): ?>
            <?php if (isset($menu['sub'])): ?>
                <?php $key = strtolower(str_replace(' ', '', $menu['label'])); ?>
                <button @click="open<?= ucfirst($key); ?> = !open<?= ucfirst($key); ?>" class="w-full text-left flex items-center gap-3 px-4 py-2 my-1 hover:bg-gray-200 rounded">
                    <i class="<?= $menu['icon']; ?>"></i>
                    <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                    <i :class="{'rotate-180': open<?= ucfirst($key); ?>}" class="fas fa-chevron-down ml-auto transition-transform" x-show="!sidebarCollapse"></i>
                </button>
                <div x-show="open<?= ucfirst($key); ?>" x-transition class="mt-2 pl-6 space-y-1" x-cloak>
                    <?php foreach ($menu['sub'] as $submenu): ?>
                        <a href="<?= $submenu['route'] !== '#' ? route($submenu['route']) : '#'; ?>" class="flex items-center text-sm gap-2 py-1 my-1 hover:text-gray-500">
                            <i class="<?= $submenu['icon'] ?? 'fas fa-circle text-xs'; ?>"></i>
                            <span x-show="!sidebarCollapse"><?= $submenu['label']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <a href="<?= $menu['route'] !== '#' ? route($menu['route']) : '#'; ?>" class="flex items-center gap-3 px-4 py-2 my-1 hover:bg-gray-200 rounded">
                    <i class="<?= $menu['icon']; ?>"></i>
                    <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>
