<?php
$services = [
    [
        'slug' => 'penapisan-dokling', // Still uses slug for routing
        'icon' => 'fas fa-file-invoice',
        'title' => 'Penapisan DOKLING'
    ],
    [
        'slug' => 'penilaian-amdal', // Still uses slug for routing
        'icon' => 'fas fa-clipboard-list',
        'title' => 'Penilaian AMDAL'
    ],
    [
        'slug' => 'pemeriksaan-ukl-upl', // Still uses slug for routing
        'icon' => 'fas fa-tasks',
        'title' => 'Pemeriksaan UKL UPL'
    ],
    [
        'slug' => 'penilaian-delh', // Still uses slug for routing
        'icon' => 'fas fa-book-reader',
        'title' => 'Penilaian DELH'
    ],
    [
        'slug' => 'penilaian-dplh', // Still uses slug for routing
        'icon' => 'fas fa-book-open',
        'title' => 'Penilaian DPLH'
    ],
    [
        'slug' => 'peraturan-regulasi', // Still uses slug for routing
        'icon' => 'fas fa-gavel',
        'title' => 'Peraturan & Regulasi'
    ],
    [
        'url' => '/ajukan-konsultasi', // Direct URL for Ajukan Konsultasi
        'icon' => 'fas fa-comments',
        'title' => 'Ajukan Konsultasi'
    ],
    [
        'url' => '/ajukan-perling', // Direct URL for Ajukan Perling
        'icon' => 'fas fa-leaf',
        'title' => 'Ajukan Perling'
    ]
];
?>

<section class="relative bg-white h-[550px] md:h-[100px]">
  <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2
              w-11/12 md:w-4/5 lg:w-3/4 bg-white rounded-lg border border-gray-300
              p-4 md:p-6 z-10">
    <h2 class="text-xl md:text-2xl font-bold text-center text-gray-800 mb-6">
      Informasi Persetujuan Lingkungan
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php foreach ($services as $service): ?>
        <a href="{{ isset($service['slug']) ? route('layanan.detail', ['slug' => $service['slug']]) : url($service['url']) }}"
           class="border rounded-lg p-4 text-center hover:shadow transition block flex flex-col items-center justify-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-[#03346E] text-white text-xl mb-2">
                <i class="<?= $service['icon']; ?>"></i>
            </div>
            <h3 class="text-sm text-[#03346E]"><?= $service['title']; ?></h3>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>