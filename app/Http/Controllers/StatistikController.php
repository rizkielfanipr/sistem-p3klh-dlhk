<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Konsultasi;
use App\Models\DokumenPersetujuan;

use Illuminate\Http\Request;

class StatistikController extends Controller
{
    public function index()
    {
        $jumlahLayanan = Layanan::count();
        $jumlahPengumuman = Pengumuman::count();
        $jumlahPengguna = User::count();
        $jumlahKonsultasi = Konsultasi::count();
        $jumlahPengajuanPerling = DokumenPersetujuan::count();

        $cards = [
            ['icon' => 'fas fa-concierge-bell', 'title' => 'Jumlah Layanan', 'count' => $jumlahLayanan],
            ['icon' => 'fas fa-bullhorn', 'title' => 'Jumlah Pengumuman', 'count' => $jumlahPengumuman],
            ['icon' => 'fas fa-users', 'title' => 'Jumlah Pengguna', 'count' => $jumlahPengguna],
            ['icon' => 'fas fa-comments', 'title' => 'Jumlah Konsultasi', 'count' => $jumlahKonsultasi],
            ['icon' => 'fas fa-file-signature', 'title' => 'Pengajuan Perling', 'count' => $jumlahPengajuanPerling],
        ];

        return view('dashboard.pages.dashboard', compact('cards'));
    }
}