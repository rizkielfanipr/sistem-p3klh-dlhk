@extends('dashboard.layouts.adminlayout')

@section('title', 'Dashboard')

@section('content')
@section('breadcrumb', 'Dashboard')

<!-- Statistik Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
    @php
        $cards = [
            ['bg' => 'bg-blue-500', 'icon' => 'fas fa-concierge-bell', 'title' => 'Jumlah Layanan', 'count' => 45, 'color' => 'bg-blue-600'],
            ['bg' => 'bg-yellow-500', 'icon' => 'fas fa-bullhorn', 'title' => 'Jumlah Pengumuman', 'count' => 12, 'color' => 'bg-yellow-600'],
            ['bg' => 'bg-green-500', 'icon' => 'fas fa-newspaper', 'title' => 'Jumlah Publikasi', 'count' => 33, 'color' => 'bg-green-600'],
            ['bg' => 'bg-purple-500', 'icon' => 'fas fa-users', 'title' => 'Jumlah Pengguna', 'count' => 120, 'color' => 'bg-purple-600'],
            ['bg' => 'bg-teal-500', 'icon' => 'fas fa-comments', 'title' => 'Jumlah Konsultasi', 'count' => 8, 'color' => 'bg-teal-600'],
            ['bg' => 'bg-red-500', 'icon' => 'fas fa-users', 'title' => 'Jumlah Forum Diskusi', 'count' => 5, 'color' => 'bg-red-600']
        ];
    @endphp

    @foreach($cards as $card)
    <div class="{{ $card['bg'] }} text-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <i class="{{ $card['icon'] }} text-3xl"></i>
            <h3 class="text-xl font-semibold mt-2">{{ $card['title'] }}</h3>
            <p class="text-2xl">{{ $card['count'] }}</p>
        </div>
        <div class="{{ $card['color'] }} p-4 rounded-full">
            <i class="{{ $card['icon'] }} text-2xl"></i>
        </div>
    </div>
    @endforeach
</div>
@endsection
