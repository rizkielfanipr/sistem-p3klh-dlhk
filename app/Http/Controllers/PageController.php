<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumuman;
use App\Models\Informasi;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PageController extends Controller
{
    /**
     * Menampilkan halaman Beranda.
     */
    public function beranda()
    {
        // Di sini Anda bisa mengambil data untuk halaman beranda, contoh:
        // $latestNews = News::latest()->take(3)->get();
        return view('home'); // Ganti 'welcome' dengan view Beranda Anda
    }

    /**
     * Menampilkan halaman Layanan.
     */
    public function layanan()
    {
        // Contoh: Ambil daftar kategori layanan dari database
        // $kategoriLayanan = KategoriLayanan::all();
        return view('beranda.layanan'); // Akan me-render view pages/layanan.blade.php
    }

    /**
     * Menampilkan halaman Informasi/Publikasi.
     */
    public function informasi()
    {
        // Contoh: Ambil daftar publikasi/artikel
        // $publikasi = Publikasi::latest()->paginate(10);
        return view('beranda.informasi'); // Akan me-render view pages/informasi.blade.php
    }

    /**
     * Menampilkan halaman Pengumuman.
     */
    public function pengumuman()
    {
        // Contoh: Ambil daftar pengumuman
        // $pengumuman = Pengumuman::latest()->paginate(10);
        return view('beranda.pengumuman'); // Akan me-render view pages/pengumuman.blade.php
    }

    /**
     * Menampilkan halaman Kontak.
     */
    public function kontak()
    {
        return view('beranda.kontak'); // Akan me-render view pages/kontak.blade.php
    }

    public function showAllPengumuman()
    {
        // Mengambil semua pengumuman terbaru dari database.
        // Jika Anda memiliki kolom 'is_active' di tabel 'pengumuman'
        // dan ingin hanya menampilkan yang aktif, gunakan:
        // $rawPengumumanItems = Pengumuman::where('is_active', true)->latest()->get();
        // Jika tidak ada kolom 'is_active', ambil semua:
        $rawPengumumanItems = Pengumuman::latest()->get();

        // Memformat data agar sesuai dengan struktur yang diharapkan di view
        $pengumumanItems = $rawPengumumanItems->map(function($item) {
            $imagePath = asset('images/default_pengumuman.jpg'); // Gambar default jika tidak ada gambar atau lampiran

            // Prioritaskan kolom 'image' baru untuk cover
            if ($item->image) {
                // Gunakan Storage::url() untuk mendapatkan URL publik gambar yang diunggah
                $imagePath = Storage::url($item->image);
            }
            // Fallback ke lampiran jika itu adalah file gambar
            elseif ($item->lampiran && $item->lampiran->lampiran) {
                $extension = pathinfo($item->lampiran->lampiran, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagePath = Storage::url($item->lampiran->lampiran);
                }
            }

            return [
                'id' => $item->id,
                'image' => $imagePath,
                'judul' => $item->judul,
                'nama_usaha' => $item->nama_usaha,
                'bidang_usaha' => $item->bidang_usaha,
                'created_at' => $item->created_at, // Ini akan menjadi instance Carbon
            ];
        });

        // Kirim data yang sudah diformat ke view beranda.pengumuman
        return view('beranda.pengumuman', compact('pengumumanItems'));
    }

     public function showAllInformasi() // Nama fungsi bisa disesuaikan
    {
        // Ambil semua data Informasi, urutkan berdasarkan terbaru
        // Dan map untuk mendapatkan format yang sama dengan pengumumanItems
        $informasiItems = Informasi::latest()->get()->map(function($item) {
            $imagePath = 'images/default_informasi.jpg'; // Path default jika tidak ada gambar

            if ($item->image) {
                $imagePath = Storage::url($item->image);
            } elseif ($item->lampiran && $item->lampiran->lampiran) {
                // Jika ada lampiran dan lampiran tersebut adalah gambar
                $extension = pathinfo($item->lampiran->lampiran, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagePath = Storage::url($item->lampiran->lampiran);
                }
            }

            return [
                'id' => $item->id,
                'image' => $imagePath,
                'judul' => $item->judul,
                'created_at' => $item->created_at,
                'excerpt' => $item->description, // Asumsi ada kolom 'description' untuk ringkasan
            ];
        });

        return view('beranda.informasi', compact('informasiItems'));
    }

}