<?php

namespace App\Http\Controllers;

use App\Models\DokumenPersetujuan;
use App\Models\JenisPerling;
use App\Models\Lampiran;
use App\Models\ProgresDokumen;
use App\Models\StatusDokumen;
use App\Models\JadwalRapat;
use App\Models\Pengumuman;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PerlingController extends Controller
{
    public function indexAmdal()
    {
        return $this->indexByJenisPerling('AMDAL', 'Daftar Permohonan AMDAL', 'AMDAL');
    }

    public function indexUKLUPL()
    {
        return $this->indexByJenisPerling('UKL-UPL', 'Daftar Permohonan UKL-UPL', 'UKL-UPL');
    }

    public function indexDELH()
    {
        return $this->indexByJenisPerling('DELH', 'Daftar Permohonan DELH', 'DELH');
    }

    public function indexDPLH()
    {
        return $this->indexByJenisPerling('DPLH', 'Daftar Permohonan DPLH', 'DPLH');
    }

    protected function indexByJenisPerling($namaPerling, $title, $buttonText)
    {
        $jenis = JenisPerling::where('nama_perling', $namaPerling)->firstOrFail();

        $dokumenList = DokumenPersetujuan::with(['user', 'lampiran', 'jenisPerling'])
            ->where('jenis_perling_id', $jenis->id)
            ->latest()
            ->get();

        return view('dashboard.pages.perling.index', [
            'dokumenList' => $dokumenList,
            'title' => $title,
            'buttonText' => $buttonText,
        ]);
    }

    public function create()
    {
        $jenisPerlingList = JenisPerling::all();
        $setting = Setting::first();
        $tanggalTidakTersediaPerling = [];

        if ($setting && $setting->tanggal_tidak_tersedia_perling) {
            $tanggalTidakTersediaPerling = collect($setting->tanggal_tidak_tersedia_perling)->map(function($item) {
                return [
                    'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                    'reason' => $item['reason']
                ];
            })->toArray();
        }

        return view('dashboard.pages.perling.create', compact('jenisPerlingList', 'tanggalTidakTersediaPerling'));
    }

    public function createForUser()
    {
        $jenisPerlingList = JenisPerling::all();
        $setting = Setting::first();
        $tanggalTidakTersediaPerling = [];

        if ($setting && $setting->tanggal_tidak_tersedia_perling) {
            $tanggalTidakTersediaPerling = collect($setting->tanggal_tidak_tersedia_perling)->map(function($item) {
                return [
                    'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                    'reason' => $item['reason']
                ];
            })->toArray();
        }

        return view('perling.create', compact('jenisPerlingList', 'tanggalTidakTersediaPerling'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_pemohon'      => 'required|string|max:255',
            'nama_usaha'        => 'required|string|max:255',
            'bidang_usaha'      => 'required|string|max:255',
            'lokasi'            => 'required|string',
            'pemrakarsa'        => 'required|string|max:255',
            'penanggung_jawab'  => 'required|string|max:255',
            'jenis_perling_id'  => 'required|exists:jenis_perling,id',
            'lampiran'          => 'required|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // --- Validasi Pengaturan Perling dari model Setting ---
        $setting = Setting::first();
        if ($setting) {
            $today = Carbon::today();
            $currentPerlingCount = DokumenPersetujuan::whereDate('created_at', $today)->count();
            $maxDailyPerlingLimit = $setting->maks_perling_harian;

            if ($maxDailyPerlingLimit > 0 && $currentPerlingCount >= $maxDailyPerlingLimit) {
                return back()->withInput()->with('error', 'Batas maksimum pengajuan dokumen perling harian telah tercapai. Mohon coba lagi besok.');
            }

            // Validasi tanggal tidak tersedia perling
            if ($setting->tanggal_tidak_tersedia_perling) {
                $submissionDate = Carbon::today();
                foreach ($setting->tanggal_tidak_tersedia_perling as $item) {
                    if (Carbon::parse($item['date'])->isSameDay($submissionDate)) {
                        $tanggalFormatted = $submissionDate->format('d-m-Y');
                        return back()->withInput()->with('error', 'Mohon Maaf, pengajuan dokumen perling ditutup untuk tanggal ' . $tanggalFormatted . ' karena: ' . $item['reason']);
                    }
                }
            }
        }
        // --- Akhir Validasi Pengaturan Perling ---

        DB::beginTransaction();
        try {
            $lampiranPath = $request->file('lampiran')->store('lampiran_perling', 'public');

            $lampiran = Lampiran::create([
                'lampiran' => $lampiranPath,
            ]);

            $jenis = JenisPerling::findOrFail($request->jenis_perling_id);

            $prefix = match (strtoupper($jenis->nama_perling)) {
                'AMDAL'   => 'AM-',
                'UKL-UPL' => 'UK-',
                'DELH'    => 'DE-',
                'DPLH'    => 'DP-',
                default   => 'XX-',
            };

            do {
                $randomNumber = random_int(10000, 99999);
                $kode_perling = $prefix . $randomNumber;
            } while (DokumenPersetujuan::where('kode_perling', $kode_perling)->exists());

            $dokumen = DokumenPersetujuan::create([
                'user_id'           => Auth::id(),
                'nama_pemohon'      => $request->nama_pemohon,
                'nama_usaha'        => $request->nama_usaha,
                'bidang_usaha'      => $request->bidang_usaha,
                'lokasi'            => $request->lokasi,
                'pemrakarsa'        => $request->pemrakarsa,
                'penanggung_jawab'  => $request->penanggung_jawab,
                'jenis_perling_id'  => $request->jenis_perling_id,
                'lampiran_id'       => $lampiran->id,
                'kode_perling'      => $kode_perling,
            ]);

            $statusDiajukan = StatusDokumen::where('nama_status', 'Diajukan')->first();

            if ($statusDiajukan) {
                ProgresDokumen::create([
                    'dokumen_id' => $dokumen->id,
                    'status_id'  => $statusDiajukan->id,
                    'catatan'    => 'Dokumen telah diajukan oleh pemohon.',
                    'tanggal'    => now(),
                ]);
            }
            DB::commit();

            return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan dokumen: ' . $e->getMessage());
        }
    }

    public function storeForUser(Request $request)
    {
        $rules = [
            'nama_pemohon'      => 'required|string|max:255',
            'nama_usaha'        => 'required|string|max:255',
            'bidang_usaha'      => 'required|string|max:255',
            'lokasi'            => 'required|string',
            'pemrakarsa'        => 'required|string|max:255',
            'penanggung_jawab'  => 'required|string|max:255',
            'jenis_perling_id'  => 'required|exists:jenis_perling,id',
            'lampiran'          => 'required|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // --- Validasi Pengaturan Perling dari model Setting ---
        $setting = Setting::first();
        if ($setting) {
            $today = Carbon::today();
            $currentPerlingCount = DokumenPersetujuan::whereDate('created_at', $today)->count();
            $maxDailyPerlingLimit = $setting->maks_perling_harian;

            if ($maxDailyPerlingLimit > 0 && $currentPerlingCount >= $maxDailyPerlingLimit) {
                return back()->withInput()->with('error', 'Batas maksimum pengajuan dokumen perling harian telah tercapai. Mohon coba lagi besok.');
            }

            // Validasi tanggal tidak tersedia perling
            if ($setting->tanggal_tidak_tersedia_perling) {
                $submissionDate = Carbon::today();
                foreach ($setting->tanggal_tidak_tersedia_perling as $item) {
                    if (Carbon::parse($item['date'])->isSameDay($submissionDate)) {
                        $tanggalFormatted = $submissionDate->format('d-m-Y');
                        return back()->withInput()->with('error', 'Mohon Maaf, pengajuan dokumen perling ditutup untuk tanggal ' . $tanggalFormatted . ' karena: ' . $item['reason']);
                    }
                }
            }
        }
        // --- Akhir Validasi Pengaturan Perling ---

        DB::beginTransaction();
        try {
            // Handle file upload
            $lampiranPath = $request->file('lampiran')->store('lampiran_perling', 'public');

            // Create Lampiran record
            $lampiran = Lampiran::create([
                'lampiran' => $lampiranPath,
            ]);

            // Get JenisPerling to generate prefix for kode_perling
            $jenis = JenisPerling::findOrFail($request->jenis_perling_id);

            $prefix = match (strtoupper($jenis->nama_perling)) {
                'AMDAL'   => 'AM-',
                'UKL-UPL' => 'UK-',
                'DELH'    => 'DE-',
                'DPLH'    => 'DP-',
                default   => 'XX-',
            };

            // Generate unique kode_perling
            do {
                $randomNumber = random_int(10000, 99999);
                $kode_perling = $prefix . $randomNumber;
            } while (DokumenPersetujuan::where('kode_perling', $kode_perling)->exists());

            // Create DokumenPersetujuan record
            $dokumen = DokumenPersetujuan::create([
                'user_id'           => Auth::id(), // Assign current authenticated user
                'nama_pemohon'      => $request->nama_pemohon,
                'nama_usaha'        => $request->nama_usaha,
                'bidang_usaha'      => $request->bidang_usaha,
                'lokasi'            => $request->lokasi,
                'pemrakarsa'        => $request->pemrakarsa,
                'penanggung_jawab'  => $request->penanggung_jawab,
                'jenis_perling_id'  => $request->jenis_perling_id,
                'lampiran_id'       => $lampiran->id,
                'kode_perling'      => $kode_perling,
            ]);

            // Record initial status as "Diajukan"
            $statusDiajukan = StatusDokumen::where('nama_status', 'Diajukan')->first();

            if ($statusDiajukan) {
                ProgresDokumen::create([
                    'dokumen_id' => $dokumen->id,
                    'status_id'  => $statusDiajukan->id,
                    'catatan'    => 'Dokumen telah diajukan oleh pemohon.',
                    'tanggal'    => now(),
                ]);
            }
            DB::commit();

            // Redirect to a success page, passing the newly created document details
            return redirect()->route('perling.success', [
                'kode_perling'  => $dokumen->kode_perling,
                'nama_pemohon'  => $dokumen->nama_pemohon,
                'nama_usaha'    => $dokumen->nama_usaha,
                'jenis_perling' => $jenis->nama_perling,
                'lampiran_path' => Storage::url($lampiran->lampiran), // Get public URL for preview
            ])->with('success', 'Permohonan Anda berhasil diajukan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan dokumen: ' . $e->getMessage());
        }
    }

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
        $dokumen = DokumenPersetujuan::with('lampiran')->findOrFail($id);

        if ($dokumen->lampiran && $dokumen->lampiran->lampiran) {
            Storage::disk('public')->delete($dokumen->lampiran->lampiran);
            $dokumen->lampiran->delete();
        }

        // Hapus juga progres dokumen yang terkait
        ProgresDokumen::where('dokumen_id', $dokumen->id)->delete();
        // Hapus juga jadwal rapat yang terkait
        JadwalRapat::where('dokumen_id', $dokumen->id)->delete();
        // Hapus juga pengumuman dan tanggapan terkait (jika ada)
        $pengumuman = Pengumuman::where('dokumen_id', $dokumen->id)->first();
        if ($pengumuman) {
            // Hapus tanggapan terkait pengumuman
            if ($pengumuman->tanggapan) {
                foreach ($pengumuman->tanggapan as $tanggapan) {
                    if ($tanggapan->lampiran) {
                        Storage::disk('public')->delete($tanggapan->lampiran);
                    }
                    $tanggapan->delete();
                }
            }
            // Hapus lampiran pengumuman (jika ada)
            if ($pengumuman->lampiran) {
                $lampiran = $pengumuman->lampiran;
                if ($lampiran && $lampiran->lampiran) {
                    Storage::disk('public')->delete($lampiran->lampiran);
                }
                $lampiran->delete();
            }
            $pengumuman->delete();
        }

        $dokumen->delete();

        $jenis = JenisPerling::find($dokumen->jenis_perling_id);

        return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil dihapus.');
    }

    private function redirectToJenis($namaPerling, $message)
    {
        switch (strtoupper($namaPerling)) {
            case 'UKL-UPL':
                return redirect()->route('perling.uklupl')->with('success', $message);
            case 'AMDAL':
                return redirect()->route('perling.amdal')->with('success', $message);
            case 'DELH':
                return redirect()->route('perling.delh')->with('success', $message);
            case 'DPLH':
                return redirect()->route('perling.dplh')->with('success', $message);
            default:
                return redirect()->route('dashboard')->with('success', $message);
        }
    }

    public function show($id)
    {
        $dokumen = DokumenPersetujuan::with([
            'user',
            'lampiran',
            'jenisPerling',
            'progresDokumen.status',
            'jadwalRapat',
            'pengumuman',
            'pengumuman.tanggapan'
        ])->findOrFail($id);

        $statusTerakhir = $dokumen->progresDokumen->sortByDesc('created_at')->first()?->status->nama_status ?? 'Belum Ada Status';

        return view('dashboard.pages.perling.detail', [
            'dokumen' => $dokumen,
            'statusTerakhir' => $statusTerakhir,
            'title' => 'Detail Dokumen ' . $dokumen->jenisPerling->nama_perling,
        ]);
    }

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
            'jadwalRapat',
            'pengumuman',
            'pengumuman.tanggapan'
        ]);

        $statusTerakhir = $dokumenPersetujuan->progresDokumen->sortByDesc('created_at')->first()?->status->nama_status ?? 'Belum Ada Status';

        // Check if the latest status is 'Perbaikan Administrasi'
        $needsRevisionUpload = ($statusTerakhir === 'Perbaikan Administrasi');

        return view('perling.detail', [
            'dokumen' => $dokumenPersetujuan,
            'statusTerakhir' => $statusTerakhir,
            'needsRevisionUpload' => $needsRevisionUpload, // Pass this flag to the view
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $dokumen = DokumenPersetujuan::findOrFail($id);
        $newStatus = $request->input('new_status');
        $catatan = $request->input('catatan');
        $lampiranFile = $request->file('lampiran_file');

        $rules = [
            'new_status'    => 'required|string',
            'catatan'       => 'nullable|string|max:1000',
            'lampiran_file' => 'nullable|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ];
        $messages = [
            'lampiran_file.mimes' => 'Format file lampiran tidak didukung. Gunakan PDF, DOCX, DOC, XLSX, JPG, JPEG, atau PNG.',
            'lampiran_file.max'   => 'Ukuran file lampiran tidak boleh melebihi 2 MB.',
        ];

        if ($newStatus === 'Pengumuman Publik') {
            $rules = array_merge($rules, [
                'judul_pengumuman'            => 'required|string|max:255', // Added
                'jenis_perling_pengumuman'    => 'required|string|max:255', // Added
                'nama_usaha_pengumuman'       => 'required|string|max:255',
                'bidang_usaha_pengumuman'     => 'required|string|max:255',
                'skala_besaran_pengumuman'    => 'required|string|max:255',
                'lokasi_pengumuman'           => 'required|string|max:255',
                'pemrakarsa_pengumuman'       => 'required|string|max:255',
                'penanggung_jawab_pengumuman' => 'required|string|max:255',
                'deskripsi_pengumuman'        => 'required|string',
                'dampak_pengumuman'           => 'required|string',
                'lampiran_pengumuman'         => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
            $messages = array_merge($messages, [
                'judul_pengumuman.required'           => 'Judul Pengumuman harus diisi.', // Added
                'jenis_perling_pengumuman.required'   => 'Jenis Perling untuk pengumuman harus diisi.', // Added
                'nama_usaha_pengumuman.required'      => 'Nama Usaha untuk pengumuman harus diisi.',
                'bidang_usaha_pengumuman.required'    => 'Bidang Usaha untuk pengumuman harus diisi.',
                'skala_besaran_pengumuman.required'   => 'Skala Besaran untuk pengumuman harus diisi.',
                'lokasi_pengumuman.required'          => 'Lokasi untuk pengumuman harus diisi.',
                'pemrakarsa_pengumuman.required'      => 'Pemrakarsa untuk pengumuman harus diisi.',
                'penanggung_jawab_pengumuman.required' => 'Penanggung Jawab untuk pengumuman harus diisi.',
                'deskripsi_pengumuman.required'       => 'Deskripsi Usaha untuk pengumuman harus diisi.',
                'dampak_pengumuman.required'          => 'Perkiraan Dampak Lingkungan untuk pengumuman harus diisi.',
                'lampiran_pengumuman.mimes'           => 'Format file lampiran pengumuman tidak didukung. Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
                'lampiran_pengumuman.max'             => 'Ukuran file lampiran pengumuman tidak boleh melebihi 5 MB.',
            ]);
        }
        else if ($newStatus === 'Rapat Koordinasi') {
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
            if ($newStatus === 'Pengumuman Publik') {
                $lampiranPengumumanId = null;
                if ($request->hasFile('lampiran_pengumuman')) {
                    $lampiranPath = $request->file('lampiran_pengumuman')->store('pengumuman_lampiran', 'public');
                    $newLampiran = Lampiran::create(['lampiran' => $lampiranPath]);
                    $lampiranPengumumanId = $newLampiran->id;
                }

                $existingPengumuman = Pengumuman::where('dokumen_id', $dokumen->id)->first();

                // Handle old lampiran deletion if a new one is uploaded for existing announcement
                if ($existingPengumuman && $lampiranPengumumanId && $existingPengumuman->lampiran_id) {
                    $oldLampiran = Lampiran::find($existingPengumuman->lampiran_id);
                    if ($oldLampiran && $oldLampiran->lampiran) {
                        Storage::disk('public')->delete($oldLampiran->lampiran);
                        $oldLampiran->delete();
                    }
                }

                $pengumumanData = [
                    'judul'               => $request->judul_pengumuman, // Added
                    'jenis_perling'       => $request->jenis_perling_pengumuman, // Added
                    'nama_usaha'          => $request->nama_usaha_pengumuman,
                    'bidang_usaha'        => $request->bidang_usaha_pengumuman,
                    'skala_besaran'       => $request->skala_besaran_pengumuman,
                    'lokasi'              => $request->lokasi_pengumuman,
                    'pemrakarsa'          => $request->pemrakarsa_pengumuman,
                    'penanggung_jawab'    => $request->penanggung_jawab_pengumuman,
                    'deskripsi'           => $request->deskripsi_pengumuman,
                    'dampak'              => $request->dampak_pengumuman,
                    'tanggal_publikasi'   => now(),
                    'user_id'             => $dokumen->user_id, // Ensure user_id is set
                    // Only set lampiran_id if a new file was uploaded, otherwise keep existing
                    'lampiran_id'         => $lampiranPengumumanId ?: ($existingPengumuman->lampiran_id ?? null),
                ];

                Pengumuman::updateOrCreate(
                    ['dokumen_id' => $dokumen->id],
                    $pengumumanData
                );
            }

            $statusToUpdate = StatusDokumen::where('nama_status', $newStatus)->first();

            if (!$statusToUpdate) {
                throw new \Exception('Status "' . $newStatus . '" tidak ditemukan.');
            }

            $lampiranProgresId = null;
            if ($lampiranFile) {
                $path = $lampiranFile->store('lampiran_progres', 'public');
                $lampiran = Lampiran::create([
                    'lampiran' => $path,
                ]);
                $lampiranProgresId = $lampiran->id;
            }

            ProgresDokumen::create([
                'dokumen_id'  => $dokumen->id,
                'status_id'   => $statusToUpdate->id,
                'catatan'     => ($newStatus === 'Pengumuman Publik' && empty($catatan)) ? 'Pengumuman publik telah diterbitkan.' : $catatan,
                'lampiran_id' => $lampiranProgresId,
                'tanggal'     => now(),
            ]);

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

    /**
     * Handle the upload of a revised document by the user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DokumenPersetujuan $dokumenPersetujuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadRevision(Request $request, DokumenPersetujuan $dokumenPersetujuan)
    {
        if (Auth::id() !== $dokumenPersetujuan->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $latestStatus = $dokumenPersetujuan->progresDokumen->sortByDesc('created_at')->first()?->status->nama_status;

        if ($latestStatus !== 'Perbaikan Administrasi') {
            return back()->with('error', 'Dokumen tidak dalam status perbaikan administrasi.');
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
            // Delete old lampiran if exists
            if ($dokumenPersetujuan->lampiran && $dokumenPersetujuan->lampiran->lampiran) {
                Storage::disk('public')->delete($dokumenPersetujuan->lampiran->lampiran);
                $dokumenPersetujuan->lampiran->delete();
            }

            // Store new lampiran
            $newLampiranPath = $request->file('revised_lampiran')->store('lampiran_perling', 'public');
            $newLampiran = Lampiran::create(['lampiran' => $newLampiranPath]);

            // Update dokumen persetujuan with new lampiran_id
            $dokumenPersetujuan->update(['lampiran_id' => $newLampiran->id]);

            // Create a new progress entry for "Dokumen Direvisi" or similar
            $statusDirevisi = StatusDokumen::where('nama_status', 'Dokumen Direvisi')->first();

            if (!$statusDirevisi) {
                // If "Dokumen Direvisi" status doesn't exist, create it or use a default one
                // For demonstration, let's assume it exists or create it on the fly (less ideal for production)
                // In a real application, you'd ensure this status exists in your database.
                $statusDirevisi = StatusDokumen::firstOrCreate(
                    ['nama_status' => 'Dokumen Direvisi'],
                    ['deskripsi' => 'Dokumen telah direvisi oleh pemohon dan diajukan kembali.']
                );
            }

            ProgresDokumen::create([
                'dokumen_id'  => $dokumenPersetujuan->id,
                'status_id'   => $statusDirevisi->id,
                'catatan'     => 'Pemohon telah mengunggah dokumen revisi administrasi.',
                'lampiran_id' => $newLampiran->id, // Link the new lampiran to this progress entry
                'tanggal'     => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Dokumen revisi berhasil diunggah dan diajukan ulang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengunggah dokumen revisi: ' . $e->getMessage());
        }
    }


    public function track(Request $request)
    {
        $searchQuery = $request->input('search');
        $dokumen = null;
        $progresDokumen = collect();

        if ($searchQuery) {
            $dokumen = DokumenPersetujuan::where('kode_perling', $searchQuery)
                                         ->orWhere('nama_usaha', 'like', '%' . $searchQuery . '%')
                                         ->first();

            if ($dokumen) {
                $progresDokumen = $dokumen->progresDokumen()->with('status', 'lampiran')->get();
            }
        }

        return view('perling.track_results_partial', compact('dokumen', 'progresDokumen'))->render();
    }

    public function successForUser(Request $request)
    {
        return view('perling.succes', [
            'kode_perling'  => $request->query('kode_perling'),
            'nama_pemohon'  => $request->query('nama_pemohon'),
            'nama_usaha'    => $request->query('nama_usaha'),
            'jenis_perling' => $request->query('jenis_perling'),
            'lampiran_path' => $request->query('lampiran_path'),
        ]);
    }
}