<?php
$menus = [
    [
        'label' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt text-blue-500',
        'route' => 'dashboard',
        'roles' => ['admin', 'front_office', 'penelaah'], // Allowed roles for Dashboard
    ],
    [
        'label' => 'Layanan',
        'icon' => 'fas fa-concierge-bell text-green-500',
        'route' => 'layanan.index',
        'roles' => ['admin'], // Allowed roles for Layanan
    ],
    [
        'label' => 'Pengumuman',
        'icon' => 'fas fa-bullhorn text-yellow-500',
        'route' => 'pengumuman.index',
        'roles' => ['admin', 'penelaah'], // Allowed roles for Pengumuman (Admin side)
    ],
    [
        'label' => 'Informasi',
        'icon' => 'fas fa-info-circle text-orange-500',
        'route' => 'informasi.index',
        'roles' => ['admin'], // Allowed roles for Informasi
    ],
    [
        'label' => 'Pengguna',
        'icon' => 'fas fa-users text-red-500',
        'roles' => ['admin'], // Allowed roles for main Pengguna menu
        'sub' => [
            ['label' => 'Admin', 'route' => 'users.admin', 'icon' => 'fas fa-user-shield text-blue-500', 'roles' => ['admin']],
            ['label' => 'Front Office', 'route' => 'users.fo', 'icon' => 'fas fa-user-tie text-green-500', 'roles' => ['admin']],
            ['label' => 'Pengguna', 'route' => 'users.pengguna', 'icon' => 'fas fa-user text-gray-500', 'roles' => ['admin']],
            ['label' => 'Penelaah', 'route' => 'users.penelaah', 'icon' => 'fas fa-user-cog text-purple-500', 'roles' => ['admin']],
        ]
    ],
    [
        'label' => 'Konsultasi',
        'icon' => 'fas fa-comments text-pink-500',
        'roles' => ['admin', 'front_office', 'penelaah'], // Allowed roles for main Konsultasi menu
        'sub' => [
            [
                'label' => 'Konsultasi Daring',
                'route' => 'konsultasi.jenis',
                'params' => ['jenis' => 'daring'],
                'icon' => 'fas fa-laptop text-blue-500',
                'roles' => ['admin', 'front_office', 'penelaah'],
            ],
            [
                'label' => 'Konsultasi Luring',
                'route' => 'konsultasi.jenis',
                'params' => ['jenis' => 'luring'],
                'icon' => 'fas fa-phone-alt text-green-500',
                'roles' => ['admin', 'front_office', 'penelaah'],
            ],
        ]
    ],
    [
        'label' => 'Perling',
        'icon' => 'fas fa-file-signature text-indigo-500',
        'roles' => ['admin', 'penelaah'], // Allowed roles for main Perling menu
        'sub' => [
            ['label' => 'AMDAL', 'route' => 'perling.amdal', 'icon' => 'fas fa-leaf text-green-600', 'roles' => ['admin', 'penelaah']],
            ['label' => 'UKL-UPL', 'route' => 'perling.uklupl', 'icon' => 'fas fa-recycle text-blue-600', 'roles' => ['admin', 'penelaah']],
            ['label' => 'DELH', 'route' => 'perling.delh', 'icon' => 'fas fa-water text-purple-600', 'roles' => ['admin', 'penelaah']],
            ['label' => 'DPLH', 'route' => 'perling.dplh', 'icon' => 'fas fa-tree text-emerald-600', 'roles' => ['admin', 'penelaah']],
        ]
    ],
    [
        'label' => 'Settings',
        'icon' => 'fas fa-cog text-gray-500',
        'route' => 'settings.form',
        'roles' => ['admin'], // Allowed roles for Settings
    ]
];
?>

<aside class="fixed inset-y-0 left-0 z-30 bg-white text-black transition-all duration-300 ease-in-out border border-gray-300"
    :class="{ 'w-64': !sidebarCollapse, 'w-20': sidebarCollapse, '-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    @click.outside="sidebarOpen = false">

    <div class="p-4 flex items-center justify-between border-b border-gray-300">
        <i x-show="!sidebarCollapse"></i>
        <button @click="sidebarCollapse = !sidebarCollapse" class="text-black md:block hidden">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="p-4 space-y-2" x-data="{
        openPengguna: false,
        openKonsultasi: false,
        openPerling: false,
        // Add more state variables for other sub-menus if needed
    }">
        <?php
        $user = Auth::user(); // Get the authenticated user
        $userRoleId = $user ? $user->role_id : null;

        // Helper function to check if user has access to a menu item
        function hasAccess($menuRoles, $userRoleId) {
            if ($userRoleId === 1) return true; // Admin (ID 1) has access to everything
            if (empty($menuRoles)) return true; // If no roles are specified, it's public/open

            $roleMap = [
                1 => 'admin',
                2 => 'front_office',
                3 => 'pengguna',
                4 => 'penelaah',
            ];

            $userRoleName = $roleMap[$userRoleId] ?? null;

            return in_array($userRoleName, $menuRoles);
        }
        ?>

        <?php foreach ($menus as $menu): ?>
            <?php if (hasAccess($menu['roles'] ?? [], $userRoleId)): // Check access for main menu item ?>
                <?php if (isset($menu['sub'])): ?>
                    <?php
                        $key = strtolower(str_replace(' ', '', $menu['label']));
                        // Check if at least one submenu item is accessible
                        $hasAccessibleSubmenu = false;
                        foreach ($menu['sub'] as $submenu) {
                            if (hasAccess($submenu['roles'] ?? [], $userRoleId)) {
                                $hasAccessibleSubmenu = true;
                                break;
                            }
                        }
                    ?>
                    <?php if ($hasAccessibleSubmenu): // Only show main menu if at least one submenu is accessible ?>
                        <button @click="open<?= ucfirst($key); ?> = !open<?= ucfirst($key); ?>"
                            class="w-full text-left flex items-center gap-3 px-4 py-2 my-1 hover:bg-gray-200 rounded">
                            <i class="<?= $menu['icon']; ?>"></i>
                            <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                            <i :class="{'rotate-180': open<?= ucfirst($key); ?>}" class="fas fa-chevron-down ml-auto transition-transform"
                                x-show="!sidebarCollapse"></i>
                        </button>
                        <div x-show="open<?= ucfirst($key); ?>" x-transition class="mt-2 pl-6 space-y-1" x-cloak>
                            <?php foreach ($menu['sub'] as $submenu): ?>
                                <?php if (hasAccess($submenu['roles'] ?? [], $userRoleId)): // Check access for submenu item ?>
                                    <?php
                                        $url = !empty($submenu['route'])
                                            ? (isset($submenu['params']) ? route($submenu['route'], $submenu['params']) : route($submenu['route']))
                                            : '#';
                                    ?>
                                    <a href="<?= $url; ?>" class="flex items-center text-sm gap-2 py-1 my-1 hover:text-gray-500">
                                        <i class="<?= $submenu['icon'] ?? 'fas fa-circle text-xs'; ?>"></i>
                                        <span x-show="!sidebarCollapse"><?= $submenu['label']; ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $url = isset($menu['params']) ? route($menu['route'], $menu['params']) : route($menu['route']); ?>
                    <a href="<?= $url; ?>" class="flex items-center gap-3 px-4 py-2 my-1 hover:bg-gray-200 rounded">
                        <i class="<?= $menu['icon']; ?>"></i>
                        <span x-show="!sidebarCollapse"><?= $menu['label']; ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>