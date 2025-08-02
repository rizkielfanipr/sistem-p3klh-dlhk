<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Informasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil data Pengumuman (TETAP SAMA)
        $pengumumanItems = Pengumuman::latest()->get()->map(function($item) {
            $imagePath = 'images/default_pengumuman.jpg';

            if ($item->image) {
                $imagePath = Storage::url($item->image);
            } elseif ($item->lampiran && $item->lampiran->lampiran) {
                $extension = pathinfo($item->lampiran->lampiran, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagePath = Storage::url($item->lampiran->lampiran);
                }
            }

            return [
                'id' => $item->id,
                'image' => $imagePath,
                'judul' => $item->judul,
                'nama_usaha' => $item->nama_usaha ?? null,
                'jenis_perling' => $item->jenis_perling ?? null,
                'bidang_usaha' => $item->bidang_usaha ?? null,
                'created_at' => $item->created_at,
                'excerpt' => $item->deskripsi,
            ];
        });

        // Ambil data Informasi (BARIS NAMA_USAHA DAN BIDANG_USAHA DIHAPUS)
        $informasiItems = Informasi::latest()->get()->map(function($item) {
            $imagePath = 'images/default_informasi.jpg';

            if ($item->image) {
                $imagePath = Storage::url($item->image);
            } elseif ($item->lampiran && $item->lampiran->lampiran) {
                $extension = pathinfo($item->lampiran->lampiran, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagePath = Storage::url($item->lampiran->lampiran);
                }
            }

            return [
                'id' => $item->id,
                'image' => $imagePath,
                'judul' => $item->judul,
                // Baris 'nama_usaha' dan 'bidang_usaha' DIHAPUS DI SINI
                'created_at' => $item->created_at,
                'excerpt' => $item->description,
            ];
        });

        // Kirim kedua koleksi ke view 'home'
        return view('home', compact('pengumumanItems', 'informasiItems'));
    }
}