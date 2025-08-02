@extends('layouts.user')

@section('title', 'Layanan Kami')
@section('description', 'Daftar lengkap layanan yang kami sediakan.')
@section('breadcrumb', 'Layanan')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-[#03346E] mb-2 md:mb-0">
            <i class="fas fa-tools text-[#03346E] mr-3"></i> Layanan Kami
        </h1>
    </div>

    <?php
    $services = [
        [
            'slug' => 'penapisan-dokling',
            'icon' => 'fas fa-file-invoice',
            'title' => 'Penapisan DOKLING',
            'description' => 'Proses awal untuk menentukan kewajiban dokumen lingkungan.'
        ],
        [
            'slug' => 'penilaian-amdal',
            'icon' => 'fas fa-clipboard-list',
            'title' => 'Penilaian AMDAL',
            'description' => 'Evaluasi mendalam dampak lingkungan dari rencana usaha besar.'
        ],
        [
            'slug' => 'pemeriksaan-ukl-upl',
            'icon' => 'fas fa-tasks',
            'title' => 'Pemeriksaan UKL UPL',
            'description' => 'Analisis upaya pengelolaan dan pemantauan lingkungan.'
        ],
        [
            'slug' => 'penilaian-delh',
            'icon' => 'fas fa-book-reader',
            'title' => 'Penilaian DELH',
            'description' => 'Evaluasi dokumen lingkungan hidup bagi kegiatan yang sudah berjalan.'
        ],
        [
            'slug' => 'penilaian-dplh',
            'icon' => 'fas fa-book-open',
            'title' => 'Penilaian DPLH',
            'description' => 'Penilaian detail pengelolaan lingkungan hidup yang telah dilakukan.'
        ],
        [
            'slug' => 'peraturan-regulasi',
            'icon' => 'fas fa-gavel',
            'title' => 'Peraturan & Regulasi',
            'description' => 'Informasi terkait hukum dan ketentuan lingkungan yang berlaku.'
        ],
        [
            'url' => '/ajukan-konsultasi',
            'icon' => 'fas fa-comments',
            'title' => 'Ajukan Konsultasi',
            'description' => 'Dapatkan panduan dan saran ahli untuk kebutuhan lingkungan Anda.'
        ],
        [
            'url' => '/ajukan-perling',
            'icon' => 'fas fa-leaf',
            'title' => 'Ajukan Perling',
            'description' => 'Ajukan permohonan persetujuan lingkungan untuk usaha Anda.'
        ]
    ];
    ?>

    <section class="py-8">
        <div class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($services as $service)
                    <a href="{{ isset($service['slug']) ? route('layanan.detail', ['slug' => $service['slug']]) : url($service['url']) }}"
                       class="service-card group relative overflow-hidden bg-white border border-gray-200 rounded-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1"> {{-- Removed shadow classes here --}}
                        <div class="p-6 flex flex-col items-center text-center">
                            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-[#03346E] text-white text-3xl mb-4 transition-all duration-300"> {{-- Changed gradient to solid color, removed shadow --}}
                                <i class="{{ $service['icon'] }}"></i>
                            </div>
                            <h3 class="text-lg font-bold text-[#03346E] mb-2 group-hover:text-[#03346E] transition-colors duration-300">{{ $service['title'] }}</h3> {{-- Changed hover color to main brand color --}}
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $service['description'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection