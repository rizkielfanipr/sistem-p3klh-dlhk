<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TanggapanPengumuman;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TanggapanPengumumanController extends Controller
{
    public function store(Request $request, $pengumuman_id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'isi_tanggapan' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        try {
            TanggapanPengumuman::create([
                'pengumuman_id' => $pengumuman_id,
                'nama' => $request->nama,
                'nomor_hp' => $request->nomor_hp,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'isi_tanggapan' => $request->isi_tanggapan,
                'tanggal_tanggapan' => Carbon::now()->toDateString(),
            ]);

            return redirect()->back()->with('success', 'Tanggapan berhasil dikirim.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan tanggapan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan tanggapan.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $tanggapan = TanggapanPengumuman::findOrFail($id);
            $tanggapan->delete();

            return redirect()->back()->with('success', 'Tanggapan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus tanggapan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus tanggapan.');
        }
    }
}
