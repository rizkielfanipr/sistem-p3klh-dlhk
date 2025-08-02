<?php

namespace App\Http\Controllers\Perling;

use App\Models\DokumenPersetujuan;
use App\Models\Lampiran;
use App\Models\ProgresDokumen;
use App\Models\StatusDokumen;
use App\Models\JadwalRapat;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Perling\IndexController;

class StatusController extends IndexController
{
    public function updateStatus(Request $request, $id)
    {
        $dokumen = DokumenPersetujuan::findOrFail($id);
        $newStatus = $request->input('new_status');
        $catatan = $request->input('catatan');
        $lampiranFile = $request->file('lampiran_file'); // This is for progres_dokumen lampiran

        $rules = [
            'new_status'    => 'required|string',
            'catatan'       => 'nullable|string|max:1000',
            'lampiran_file' => 'nullable|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048', // For progres_dokumen
        ];
        $messages = [
            'lampiran_file.mimes' => 'Format file lampiran tidak didukung. Gunakan PDF, DOCX, DOC, XLSX, JPG, JPEG, atau PNG.',
            'lampiran_file.max'   => 'Ukuran file lampiran tidak boleh melebihi 2 MB.',
        ];

        // --- Validasi tambahan berdasarkan newStatus ---
        if ($newStatus === 'Pengumuman Publik') {
            $rules = array_merge($rules, [
                'judul_pengumuman'              => 'required|string|max:255',
                'jenis_perling_pengumuman'      => 'required|string|max:255',
                'nama_usaha_pengumuman'         => 'required|string|max:255',
                'bidang_usaha_pengumuman'       => 'required|string|max:255',
                'skala_besaran_pengumuman'      => 'required|string|max:255',
                'lokasi_pengumuman'             => 'required|string|max:255',
                'pemrakarsa_pengumuman'         => 'required|string|max:255',
                'penanggung_jawab_pengumuman'   => 'required|string|max:255',
                'deskripsi_pengumuman'          => 'required|string',
                'dampak_pengumuman'             => 'required|string',
                'lampiran_pengumuman'           => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'image_pengumuman'              => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Add validation for image
            ]);
            $messages = array_merge($messages, [
                'judul_pengumuman.required'             => 'Judul Pengumuman harus diisi.',
                'jenis_perling_pengumuman.required'     => 'Jenis Perling untuk pengumuman harus diisi.',
                'nama_usaha_pengumuman.required'        => 'Nama Usaha untuk pengumuman harus diisi.',
                'bidang_usaha_pengumuman.required'      => 'Bidang Usaha untuk pengumuman harus diisi.',
                'skala_besaran_pengumuman.required'     => 'Skala Besaran untuk pengumuman harus diisi.',
                'lokasi_pengumuman.required'            => 'Lokasi untuk pengumuman harus diisi.',
                'pemrakarsa_pengumuman.required'        => 'Pemrakarsa untuk pengumuman harus diisi.',
                'penanggung_jawab_pengumuman.required'  => 'Penanggung Jawab untuk pengumuman harus diisi.',
                'deskripsi_pengumuman.required'         => 'Deskripsi Usaha untuk pengumuman harus diisi.',
                'dampak_pengumuman.required'            => 'Perkiraan Dampak Lingkungan untuk pengumuman harus diisi.',
                'lampiran_pengumuman.mimes'             => 'Format file lampiran pengumuman tidak didukung. Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
                'lampiran_pengumuman.max'               => 'Ukuran file lampiran pengumuman tidak boleh melebihi 5 MB.',
                'image_pengumuman.image'                => 'File cover image harus berupa gambar.', // Message for image
                'image_pengumuman.mimes'                => 'Format cover image tidak didukung. Gunakan JPEG, PNG, JPG, atau GIF.', // Message for image
                'image_pengumuman.max'                  => 'Ukuran cover image tidak boleh melebihi 5 MB.', // Message for image
            ]);
        } else if ($newStatus === 'Rapat Koordinasi') {
            $rules = array_merge($rules, [
                'tanggal_rapat' => 'required|date',
                'waktu_rapat'   => 'required|date_format:H:i',
                'ruang_rapat'   => 'required|string|max:255',
            ]);
            $messages = array_merge($messages, [
                'tanggal_rapat.required' => 'Tanggal rapat harus diisi.',
                'waktu_rapat.required'   => 'Waktu rapat harus diisi.',
                'ruang_rapat.required'   => 'Ruang rapat harus diisi.',
            ]);
        }

        try {
            $request->validate($rules, $messages);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // --- Logika untuk Pengumuman Publik ---
            if ($newStatus === 'Pengumuman Publik') {
                $lampiranPengumumanId = null;
                $imagePengumumanPath = null;

                // Handle Lampiran Pengumuman
                if ($request->hasFile('lampiran_pengumuman')) {
                    $lampiranPath = $request->file('lampiran_pengumuman')->store('pengumuman_lampiran', 'public');
                    $newLampiran = Lampiran::create(['lampiran' => $lampiranPath]);
                    $lampiranPengumumanId = $newLampiran->id;
                }

                // Handle Image Pengumuman
                if ($request->hasFile('image_pengumuman')) {
                    $imagePengumumanPath = $request->file('image_pengumuman')->store('pengumuman_images', 'public');
                }

                $existingPengumuman = Pengumuman::where('dokumen_id', $dokumen->id)->first();

                // If there's a new pengumuman attachment and an old one, delete the old attachment
                if ($existingPengumuman && $lampiranPengumumanId && $existingPengumuman->lampiran_id) {
                    $oldLampiran = Lampiran::find($existingPengumuman->lampiran_id);
                    if ($oldLampiran && $oldLampiran->lampiran) {
                        Storage::disk('public')->delete($oldLampiran->lampiran);
                        $oldLampiran->delete();
                    }
                }

                // If there's a new pengumuman image and an old one, delete the old image
                if ($existingPengumuman && $imagePengumumanPath && $existingPengumuman->image) {
                    Storage::disk('public')->delete($existingPengumuman->image);
                }

                $pengumumanData = [
                    'judul'               => $request->judul_pengumuman,
                    'jenis_perling'       => $request->jenis_perling_pengumuman,
                    'nama_usaha'          => $request->nama_usaha_pengumuman,
                    'bidang_usaha'        => $request->bidang_usaha_pengumuman,
                    'skala_besaran'       => $request->skala_besaran_pengumuman,
                    'lokasi'              => $request->lokasi_pengumuman,
                    'pemrakarsa'          => $request->pemrakarsa_pengumuman,
                    'penanggung_jawab'    => $request->penanggung_jawab_pengumuman,
                    'deskripsi'           => $request->deskripsi_pengumuman,
                    'dampak'              => $request->dampak_pengumuman,
                    'tanggal_publikasi'   => now(),
                    'user_id'             => $dokumen->user_id,
                    'dokumen_id'          => $dokumen->id,
                ];

                // Update lampiran_id if a new lampiran was uploaded, else keep the old one
                if ($lampiranPengumumanId) {
                    $pengumumanData['lampiran_id'] = $lampiranPengumumanId;
                } elseif ($existingPengumuman && $existingPengumuman->lampiran_id) {
                    $pengumumanData['lampiran_id'] = $existingPengumuman->lampiran_id;
                } else {
                    $pengumumanData['lampiran_id'] = null;
                }

                // Update image path if a new image was uploaded, else keep the old one
                if ($imagePengumumanPath) {
                    $pengumumanData['image'] = $imagePengumumanPath;
                } elseif ($existingPengumuman && $existingPengumuman->image) {
                    $pengumumanData['image'] = $existingPengumuman->image;
                } else {
                    $pengumumanData['image'] = null;
                }


                Pengumuman::updateOrCreate(
                    ['dokumen_id' => $dokumen->id],
                    $pengumumanData
                );
            }

            // --- Temukan status yang akan diperbarui ---
            $statusToUpdate = StatusDokumen::where('nama_status', $newStatus)->first();

            // --- Jika status 'Dokumen Direvisi', set the actual status to 'Pemeriksaan Administrasi' ---
            if ($newStatus === 'Dokumen Direvisi') {
                $statusToUpdate = StatusDokumen::firstOrCreate(
                    ['nama_status' => 'Pemeriksaan Administrasi'],
                    ['deskripsi' => 'Dokumen sedang dalam tahap pemeriksaan administrasi awal.']
                );
                // Also, ensure "Dokumen Direvisi" exists for reference, if needed for history
                StatusDokumen::firstOrCreate(
                    ['nama_status' => 'Dokumen Direvisi'],
                    ['deskripsi' => 'Dokumen telah direvisi oleh pemohon dan menunggu pemeriksaan kembali.']
                );
            } elseif (!$statusToUpdate) {
                throw new \Exception('Status "' . $newStatus . '" tidak ditemukan.');
            }

            // --- Unggah Lampiran Progres ---
            $lampiranProgresId = null;
            if ($lampiranFile) {
                $path = $lampiranFile->store('lampiran_progres', 'public');
                $lampiran = Lampiran::create([
                    'lampiran' => $path,
                ]);
                $lampiranProgresId = $lampiran->id;
            }

            // --- Buat Progres Dokumen Baru ---
            ProgresDokumen::create([
                'dokumen_id'    => $dokumen->id,
                'status_id'     => $statusToUpdate->id,
                'catatan'       => ($newStatus === 'Pengumuman Publik' && empty($catatan)) ? 'Pengumuman publik telah diterbitkan.' : $catatan,
                'lampiran_id'   => $lampiranProgresId, // Lampiran spesifik untuk progres ini
                'tanggal'       => now(),
            ]);

            // --- Logika untuk Jadwal Rapat ---
            if ($newStatus === 'Rapat Koordinasi') {
                JadwalRapat::updateOrCreate(
                    ['dokumen_id' => $dokumen->id],
                    [
                        'tanggal_rapat' => $request->tanggal_rapat,
                        'waktu_rapat'   => $request->waktu_rapat,
                        'ruang_rapat'   => $request->ruang_rapat,
                        'catatan'       => $catatan,
                    ]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui menjadi "' . $newStatus . '".');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status dokumen: ' . $e->getMessage());
        }
    }
}