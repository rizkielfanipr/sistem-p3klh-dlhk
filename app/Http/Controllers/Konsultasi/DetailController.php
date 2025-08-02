<?php

namespace App\Http\Controllers\Konsultasi;

use App\Models\KonsultasiDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Konsultasi\IndexController; 

class DetailController extends IndexController 
{

    public function showDetail($id)
    {
        $detail = KonsultasiDetail::with([
            'topik', 'sesi', 'status', 'lampiran', 'konsultasi.user', 'konsultasi.jenisKonsultasi'
        ])->findOrFail($id);

        return view('dashboard.pages.konsultasi.detail', compact('detail'));
    }

    public function showUserDetail(KonsultasiDetail $konsultasiDetail)
    {
        if (Auth::id() !== $konsultasiDetail->konsultasi->user_id) {
            abort(403); 
        }

        $konsultasiDetail->load([
            'topik', 'sesi', 'status', 'lampiran', 'konsultasi.user', 'konsultasi.jenisKonsultasi',
            'konsultasi.tindakLanjut'
        ]);

        return view('konsultasi.detail', compact('konsultasiDetail'));
    }
}
