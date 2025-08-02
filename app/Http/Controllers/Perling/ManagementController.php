<?php

namespace App\Http\Controllers\Perling;

use App\Models\DokumenPersetujuan;
use App\Models\JenisPerling;
use App\Models\Lampiran;
use App\Models\ProgresDokumen;
use App\Models\JadwalRapat;
use App\Models\Pengumuman;
use App\Models\StatusDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Make sure to add this if not already
use App\Http\Controllers\Perling\IndexController;

class ManagementController extends IndexController
{
    public function edit($id)
    {
        $dokumen = DokumenPersetujuan::with(['lampiran', 'jenisPerling'])->findOrFail($id);
        $jenisPerlingList = JenisPerling::all();

        return view('dashboard.pages.perling.edit', compact('dokumen', 'jenisPerlingList'));
    }

    public function update(Request $request, $id)
    {
        $dokumen = DokumenPersetujuan::findOrFail($id);

        $request->validate([
            'nama_pemohon'      => 'required|string|max:255',
            'nama_usaha'        => 'required|string|max:255',
            'bidang_usaha'      => 'required|string|max:255',
            'lokasi'            => 'required|string',
            'pemrakarsa'        => 'required|string|max:255',
            'penanggung_jawab'  => 'required|string|max:255',
            'jenis_perling_id'  => 'required|exists:jenis_perling,id',
            'lampiran'          => 'nullable|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('lampiran')) {
            if ($dokumen->lampiran && $dokumen->lampiran->lampiran) {
                Storage::disk('public')->delete($dokumen->lampiran->lampiran);
                $dokumen->lampiran->delete();
            }

            $lampiranPath = $request->file('lampiran')->store('lampiran_perling', 'public');

            $lampiran = Lampiran::create([
                'lampiran' => $lampiranPath,
            ]);

            $dokumen->lampiran_id = $lampiran->id;
        }

        $dokumen->update([
            'nama_pemohon'     => $request->nama_pemohon,
            'nama_usaha'       => $request->nama_usaha,
            'bidang_usaha'     => $request->bidang_usaha,
            'lokasi'           => $request->lokasi,
            'pemrakarsa'       => $request->pemrakarsa,
            'penanggung_jawab' => $request->penanggung_jawab,
            'jenis_perling_id' => $request->jenis_perling_id,
        ]);

        $jenis = JenisPerling::find($request->jenis_perling_id);

        return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::beginTransaction(); // Add transaction for safety
        try {
            $dokumen = DokumenPersetujuan::with('lampiran', 'progresDokumen.lampiran', 'pengumuman.tanggapan')->findOrFail($id);

            // Delete main lampiran
            if ($dokumen->lampiran && $dokumen->lampiran->lampiran) {
                Storage::disk('public')->delete($dokumen->lampiran->lampiran);
                $dokumen->lampiran->delete();
            }

            // Delete progresDokumen and their lampiran
            foreach ($dokumen->progresDokumen as $progres) {
                if ($progres->lampiran) {
                    Storage::disk('public')->delete($progres->lampiran->lampiran);
                    $progres->lampiran->delete(); // Delete Lampiran record
                }
                $progres->delete(); // Delete ProgresDokumen record
            }

            // Delete JadwalRapat
            JadwalRapat::where('dokumen_id', $dokumen->id)->delete();

            // Delete Pengumuman and related Tanggapan and their lampiran
            $pengumuman = Pengumuman::where('dokumen_id', $dokumen->id)->first();
            if ($pengumuman) {
                if ($pengumuman->tanggapan) {
                    foreach ($pengumuman->tanggapan as $tanggapan) {
                        if ($tanggapan->lampiran) {
                            Storage::disk('public')->delete($tanggapan->lampiran->lampiran);
                            $tanggapan->lampiran()->delete();
                        }
                        $tanggapan->delete();
                    }
                }
                if ($pengumuman->lampiran) { // Check for lampiran on Pengumuman itself
                    $lampiran = $pengumuman->lampiran;
                    if ($lampiran->lampiran) {
                        Storage::disk('public')->delete($lampiran->lampiran);
                    }
                    $lampiran->delete();
                }
                $pengumuman->delete();
            }

            // Finally, delete the DokumenPersetujuan
            $dokumen->delete();

            DB::commit();

            $jenis = JenisPerling::find($dokumen->jenis_perling_id);
            return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $dokumen = DokumenPersetujuan::with([
            'user',
            'lampiran',
            'jenisPerling',
            // We will query progresDokumen selectively for specific needs below
            'jadwalRapat',
            'pengumuman',
        ])->findOrFail($id);

        // Get the latest progress for status determination (using query builder for efficiency)
        $latestProgress = $dokumen->progresDokumen()->with('status')->latest()->first();
        $statusTerakhir = $latestProgress?->status->nama_status ?? 'Belum Ada Status';

        // --- LOGIKA UNTUK MEMERIKSA DOKUMEN REVISI ADMINISTRASI ---
        $revisedAdministrasiProgresses = $dokumen->progresDokumen()
            ->with('status', 'lampiran') // Eager load status and lampiran
            ->whereHas('status', function ($query) {
                // We are looking for progress entries where the status *after*
                // 'Perbaikan Administrasi' was 'Diajukan', and it has a lampiran.
                // This logic is a bit tricky if 'Diajukan' also happens at the very start.
                // A more robust way would be to track the 'type' of upload.
                // For now, let's assume 'Diajukan' with a lampiran following 'Perbaikan Administrasi' is the revision.
                $query->where('nama_status', 'Diajukan');
            })
            ->whereNotNull('lampiran_id')
            ->orderByDesc('created_at')
            ->get();
        // --- AKHIR LOGIKA UNTUK DOKUMEN REVISI ADMINISTRASI ---


        // --- NEW LOGIC FOR REVISED SUBSTANSI DOCUMENTS ---
        $revisedSubstansiProgresses = $dokumen->progresDokumen()
            ->with('status', 'lampiran') // Eager load status and lampiran
            ->whereHas('status', function ($query) {
                // Looking for progress entries where the status *after*
                // 'Perbaikan Substansi' was 'Revisi Substansi', and it has a lampiran.
                $query->where('nama_status', 'Revisi Substansi');
            })
            ->whereNotNull('lampiran_id')
            ->orderByDesc('created_at')
            ->get();
        // --- END NEW LOGIC FOR REVISED SUBSTANSI DOCUMENTS ---

        $displayStatusForView = $statusTerakhir;

        return view('dashboard.pages.perling.detail', [
            'dokumen' => $dokumen,
            'statusTerakhir' => $statusTerakhir,
            'title' => 'Detail Dokumen ' . ($dokumen->jenisPerling->nama_perling ?? 'Tidak Diketahui'),
            'revisedAdministrasiProgresses' => $revisedAdministrasiProgresses, // Pass the collection
            'revisedSubstansiProgresses' => $revisedSubstansiProgresses, // Pass the new collection
            'displayStatus' => $displayStatusForView,
        ]);
    }

    public function showProgressHistory(DokumenPersetujuan $dokumen)
    {
        $progressHistory = $dokumen->progresDokumen()->with('status', 'lampiran')->latest('created_at')->get();

        $statusColors = [
            'Diajukan' => 'bg-blue-100 text-blue-800',
            'Pemeriksaan Administrasi' => 'bg-yellow-100 text-yellow-800',
            'Perbaikan Administrasi' => 'bg-red-100 text-red-800',
            'Administrasi Lengkap' => 'bg-green-100 text-green-800',
            'Pengumuman Publik' => 'bg-emerald-100 text-emerald-800',
            'Rapat Koordinasi' => 'bg-indigo-100 text-indigo-800',
            'Pemeriksaan Substansi' => 'bg-purple-100 text-purple-800',
            'Perbaikan Substansi' => 'bg-orange-100 text-orange-800',
            'Revisi Substansi' => 'bg-cyan-100 text-cyan-800', // Make sure this is consistent if you use it in blade
            'Substansi Lengkap' => 'bg-teal-100 text-teal-800',
            'Proses Penerbitan' => 'bg-cyan-100 text-cyan-800',
            'Terbit' => 'bg-lime-100 text-lime-800',
        ];

        return view('dashboard.pages.perling.proggres-history', compact('dokumen', 'progressHistory', 'statusColors'));
    }
}