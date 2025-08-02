<?php

namespace App\Http\Controllers\Konsultasi;

use App\Models\KonsultasiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Konsultasi\IndexController; 

class ActionController extends IndexController 
{

    public function verifikasi($id)
    {
        $detail = KonsultasiDetail::findOrFail($id);
        $detail->status_id = 2; // ID status 'Diproses'
        $detail->save();

        return back()->with('success', 'Status diubah menjadi Diproses.');
    }

    public function tindaklanjut(Request $request, $id)
    {
        $request->validate([
            'catatan_tindaklanjut' => 'required|string|max:1000',
        ]);

        $detail = KonsultasiDetail::findOrFail($id);

        DB::beginTransaction();
        try {
            DB::table('tindak_lanjut_konsultasi')->insert([
                'konsultasi_id' => $detail->konsultasi_id,
                'catatan_tindaklanjut' => $request->catatan_tindaklanjut,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detail->status_id = 3; // ID status 'Selesai'
            $detail->save();
            DB::commit();

            return back()->with('success', 'Catatan tersimpan dan status diubah menjadi Selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tindak lanjut failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan tindak lanjut. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }
}
