<?php

namespace App\Http\Controllers\Konsultasi;

use App\Models\KonsultasiDetail;
use App\Models\JenisKonsultasi;
use App\Models\TopikKonsultasi;
use App\Models\SesiKonsultasi;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Konsultasi\IndexController; 

class ManagementController extends IndexController 
{

    public function edit($jenis, $id)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();
        $detail = KonsultasiDetail::with(['konsultasi', 'lampiran', 'topik', 'sesi', 'status'])->findOrFail($id);
        $topik = TopikKonsultasi::all();
        $sesi = SesiKonsultasi::all();

        return view('dashboard.pages.konsultasi.edit', compact('jenis', 'jenisKonsultasi', 'detail', 'topik', 'sesi'));
    }

    public function update(Request $request, $jenis, $id)
    {
        $detail = KonsultasiDetail::with('lampiran')->findOrFail($id);
        $user = Auth::user();
        $kode = $detail->kode_konsultasi ?? 'kode';

        $actualJenisKonsultasi = JenisKonsultasi::find($detail->konsultasi->jenis_konsultasi_id);
        $actualJenisNama = $actualJenisKonsultasi ? $actualJenisKonsultasi->nama_jenis : 'default';

        $rules = [
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'remove_lampiran' => 'nullable|boolean',
        ];

        if (strtolower($actualJenisNama) !== 'daring') {
            $rules['tanggal_konsultasi'] = 'required|date';
            $rules['sesi_konsultasi_id'] = 'required|exists:sesi_konsultasi,id';
        }

        $request->validate($rules);

        $sesiDisplay = strtolower($actualJenisNama) === 'daring' ? 'daring' : (SesiKonsultasi::find($request->sesi_konsultasi_id)->nama_sesi ?? 'sesi');
        $tanggalForFileName = strtolower($actualJenisNama) === 'daring' ? now() : $request->tanggal_konsultasi;

        DB::beginTransaction();

        try {
            $oldLampiran = $detail->lampiran;
            $newLampiranId = $detail->lampiran_id;

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $ext = $file->getClientOriginalExtension();
                $namaFile = $this->generateNamaFile($kode, $tanggalForFileName, $sesiDisplay, $user->nama, $ext);

                $path = $file->storeAs('lampiran_konsultasi', $namaFile, 'public');

                $lampiranBaru = Lampiran::create(['lampiran' => $path]);
                $newLampiranId = $lampiranBaru->id;

                if ($oldLampiran) {
                    if (!empty($oldLampiran->lampiran) && Storage::disk('public')->exists($oldLampiran->lampiran)) {
                        Storage::disk('public')->delete($oldLampiran->lampiran);
                    }
                    $oldLampiran->delete();
                }
            } elseif ($request->input('remove_lampiran') && $oldLampiran) {
                if (!empty($oldLampiran->lampiran) && Storage::disk('public')->exists($oldLampiran->lampiran)) {
                    Storage::disk('public')->delete($oldLampiran->lampiran);
                }
                $oldLampiran->delete();
                $newLampiranId = null;
            }

            $detail->update([
                'topik_id' => $request->topik_id,
                'tanggal_konsultasi' => strtolower($actualJenisNama) === 'daring' ? null : $request->tanggal_konsultasi,
                'sesi_konsultasi_id' => strtolower($actualJenisNama) === 'daring' ? null : $request->sesi_konsultasi_id,
                'catatan_konsultasi' => $request->catatan_konsultasi,
                'lampiran_id' => $newLampiranId,
            ]);

            DB::commit();

            return redirect()->route('konsultasi.jenis', $actualJenisNama)
                ->with('success', 'Konsultasi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Konsultasi update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui konsultasi. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }

    public function destroy($jenis, $id)
    {
        $detail = KonsultasiDetail::with('lampiran', 'konsultasi')->findOrFail($id);

        $lampiran = $detail->lampiran;
        $konsultasi = $detail->konsultasi;

        DB::beginTransaction();
        try {
            // Hapus tindak lanjut terkait
            DB::table('tindak_lanjut_konsultasi')->where('konsultasi_id', $konsultasi->id)->delete();
            // Hapus detail konsultasi
            $detail->delete();

            // Hapus lampiran jika ada
            if ($lampiran) {
                if (!empty($lampiran->lampiran)) {
                    Storage::disk('public')->delete($lampiran->lampiran);
                }
                $lampiran->delete();
            }

            // Hapus entri Konsultasi utama jika tidak ada detail terkait
            if ($konsultasi && $konsultasi->detail()->count() === 0) {
                $konsultasi->delete();
            }
            DB::commit();

            return redirect()->route('konsultasi.jenis', $jenis)
                ->with('success', 'Konsultasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Konsultasi deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus konsultasi. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }
}
