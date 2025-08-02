<?php

namespace App\Http\Controllers\Perling;

use App\Models\DokumenPersetujuan;
use App\Models\Lampiran;
use App\Models\ProgresDokumen;
use App\Models\StatusDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Perling\IndexController;

class UserDetailController extends IndexController
{
    public function showUserDetail(DokumenPersetujuan $dokumenPersetujuan)
    {
        if (Auth::id() !== $dokumenPersetujuan->user_id) {
            abort(403);
        }

        $dokumenPersetujuan->load([
            'user',
            'lampiran',
            'jenisPerling',
            'progresDokumen.status',
            'progresDokumen.lampiran',
            'jadwalRapat',
            'pengumuman',
            'pengumuman.tanggapan'
        ]);

        $statusTerakhir = $dokumenPersetujuan->progresDokumen->sortByDesc('created_at')->first()?->status->nama_status ?? 'Belum Ada Status';

        $needsRevisionUpload = in_array($statusTerakhir, ['Perbaikan Administrasi', 'Perbaikan Substansi']);

        $catatanPerbaikan = null;
        if ($needsRevisionUpload) {
            $lastRevisionProgress = $dokumenPersetujuan->progresDokumen
                                    ->whereIn('status.nama_status', ['Perbaikan Administrasi', 'Perbaikan Substansi'])
                                    ->sortByDesc('created_at')
                                    ->first();
            $catatanPerbaikan = $lastRevisionProgress->catatan ?? 'Tidak ada catatan spesifik dari admin.';
        }

        // Mencari lampiran revisi terakhir yang diunggah oleh pengguna
        // Ini akan mencari entri progres dengan lampiran terbaru yang terkait dengan status
        // yang dihasilkan dari pengunggahan revisi oleh pengguna.
        $latestUserUploadedProgres = $dokumenPersetujuan->progresDokumen
                                    ->whereIn('status.nama_status', ['Diajukan', 'Revisi Substansi']) // Status yang dicapai setelah user mengunggah revisi
                                    ->sortByDesc('created_at')
                                    ->first();

        $latestRevisionFile = $latestUserUploadedProgres->lampiran ?? null;

        // Jika tidak ada revisi yang diunggah oleh pengguna, tampilkan lampiran dokumen awal
        if (!$latestRevisionFile && $dokumenPersetujuan->lampiran) {
             $latestRevisionFile = $dokumenPersetujuan->lampiran;
        }


        return view('perling.detail', [
            'dokumen' => $dokumenPersetujuan,
            'statusTerakhir' => $statusTerakhir,
            'needsRevisionUpload' => $needsRevisionUpload,
            'catatanPerbaikan' => $catatanPerbaikan,
            'latestRevisionFile' => $latestRevisionFile,
        ]);
    }

    public function uploadRevision(Request $request, DokumenPersetujuan $dokumenPersetujuan)
    {
        if (Auth::id() !== $dokumenPersetujuan->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $latestStatus = $dokumenPersetujuan->progresDokumen->sortByDesc('created_at')->first()?->status->nama_status;

        if (!in_array($latestStatus, ['Perbaikan Administrasi', 'Perbaikan Substansi'])) {
            return back()->with('error', 'Dokumen tidak dalam status perbaikan.');
        }

        $request->validate([
            'revised_lampiran' => 'required|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ], [
            'revised_lampiran.required' => 'Mohon unggah file perbaikan.',
            'revised_lampiran.mimes'    => 'Format file tidak didukung. Gunakan PDF, DOCX, DOC, XLSX, JPG, JPEG, atau PNG.',
            'revised_lampiran.max'      => 'Ukuran file tidak boleh melebihi 2 MB.',
        ]);

        DB::beginTransaction();
        try {
            $newLampiranPath = $request->file('revised_lampiran')->store('lampiran_perling', 'public');
            $newLampiran = Lampiran::create(['lampiran' => $newLampiranPath]);

            $nextStatusName = '';
            $catatan = '';

            if ($latestStatus === 'Perbaikan Administrasi') {
                $nextStatusName = 'Diajukan'; // Kembali ke Diajukan untuk pemeriksaan administrasi ulang
                $catatan = 'Pemohon telah mengunggah dokumen revisi administrasi. Dokumen sekarang berstatus diajukan kembali.';
            } elseif ($latestStatus === 'Perbaikan Substansi') {
                $nextStatusName = 'Revisi Substansi'; // Sesuai permintaan: setelah Perbaikan Substansi, statusnya jadi Revisi Substansi
                $catatan = 'Pemohon telah mengunggah dokumen revisi substansi. Dokumen sekarang berstatus revisi substansi, menunggu pemeriksaan.';
            }

            $nextStatus = StatusDokumen::where('nama_status', $nextStatusName)->first();
            if (!$nextStatus) {
                // Ini seharusnya tidak terjadi jika status sudah di-seed di database
                $nextStatus = StatusDokumen::firstOrCreate(
                    ['nama_status' => $nextStatusName],
                    ['deskripsi' => 'Status otomatis setelah pemohon mengunggah revisi.']
                );
            }

            ProgresDokumen::create([
                'dokumen_id'  => $dokumenPersetujuan->id,
                'status_id'   => $nextStatus->id,
                'catatan'     => $catatan,
                'lampiran_id' => $newLampiran->id,
                'tanggal'     => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Dokumen revisi berhasil diunggah dan status diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengunggah dokumen revisi: ' . $e->getMessage());
        }
    }
}