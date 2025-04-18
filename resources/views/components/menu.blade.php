<?php
$services = [
    [
        'url' => '/penapisan-dokling',
        'icon' => '🧾',
        'title' => 'Penapisan DOKLING'
    ],
    [
        'url' => '/penilaian-amdal',
        'icon' => '📑',
        'title' => 'Penilaian AMDAL'
    ],
    [
        'url' => '/pemeriksaan-ukl-upl',
        'icon' => '📋',
        'title' => 'Pemeriksaan UKL UPL'
    ],
    [
        'url' => '/registrasi-sppl',
        'icon' => '📝',
        'title' => 'Registrasi SPPL'
    ],
    [
        'url' => '/penilaian-delh-dplh',
        'icon' => '📚',
        'title' => 'Penilaian DELH & DPLH'
    ],
    [
        'url' => '/submit-dokumen',
        'icon' => '📤',
        'title' => 'Submit Dokumen & Surat'
    ],
    [
        'url' => '/amdalnet',
        'icon' => '🌐',
        'title' => 'AMDALNET'
    ],
    [
        'url' => '/konsultasi',
        'icon' => '💬',
        'title' => 'Konsultasi'
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
        <a href="<?= $service['url']; ?>" class="border rounded-lg p-10 text-center hover:shadow transition block aspect-w-1 aspect-h-1">
          <div class="text-2xl mb-2"><?= $service['icon']; ?></div> <!-- Reduced icon size -->
          <h3 class="font-semibold text-xs"><?= $service['title']; ?></h3> <!-- Reduced text size -->
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
