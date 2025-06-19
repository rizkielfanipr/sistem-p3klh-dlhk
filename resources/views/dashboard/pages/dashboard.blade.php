@extends('dashboard.layouts.adminlayout')

@section('title', 'Dashboard')

@section('content')
@section('breadcrumb', 'Dashboard')

<!-- Statistik Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5 gap-4">
    @php
        $cards = [
            ['bg' => 'bg-blue-500', 'icon' => 'fas fa-concierge-bell', 'title' => 'Jumlah Layanan', 'count' => 45, 'color' => 'bg-blue-600'],
            ['bg' => 'bg-yellow-500', 'icon' => 'fas fa-bullhorn', 'title' => 'Jumlah Pengumuman', 'count' => 12, 'color' => 'bg-yellow-600'],
            ['bg' => 'bg-purple-500', 'icon' => 'fas fa-users', 'title' => 'Jumlah Pengguna', 'count' => 120, 'color' => 'bg-purple-600'],
            ['bg' => 'bg-teal-500', 'icon' => 'fas fa-comments', 'title' => 'Jumlah Konsultasi', 'count' => 8, 'color' => 'bg-teal-600'],
            ['bg' => 'bg-green-500', 'icon' => 'fas fa-file-signature', 'title' => 'Pengajuan Perling', 'count' => 20, 'color' => 'bg-green-600'],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="{{ $card['bg'] }} text-white p-4 rounded-lg shadow-sm flex items-center justify-between space-x-4">
        <div>
            <i class="{{ $card['icon'] }} text-2xl"></i>
            <h3 class="text-base font-semibold mt-1">{{ $card['title'] }}</h3>
            <p class="text-xl font-bold">{{ $card['count'] }}</p>
        </div>
        <div class="{{ $card['color'] }} p-3 rounded-full">
            <i class="{{ $card['icon'] }} text-xl"></i>
        </div>
    </div>
    @endforeach
</div>
@endsection
