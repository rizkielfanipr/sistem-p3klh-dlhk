<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use App\Models\JenisKonsultasi;
use App\Models\KonsultasiDetail;
use App\Models\TopikKonsultasi;
use App\Models\SesiKonsultasi;
use App\Models\Setting;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KonsultasiController extends Controller
{
    /**
     * Menampilkan formulir pembuatan konsultasi untuk admin berdasarkan jenis.
     *
     * @param string $jenis Nama jenis konsultasi (daring/luring).
     * @return \Illuminate\View\View
     */
    public function create($jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();
        $topik = TopikKonsultasi::all();
        $sesi = SesiKonsultasi::all();

        $setting = Setting::first();
        $tanggalTidakTersediaLuring = [];
        $tanggalTidakTersediaDaring = [];

        if ($setting) {
            if ($setting->tanggal_tidak_tersedia_konsultasi_luring) {
                $tanggalTidakTersediaLuring = collect($setting->tanggal_tidak_tersedia_konsultasi_luring)->map(function($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })->toArray();
            }
            if ($setting->tanggal_tidak_tersedia_konsultasi_daring) {
                $tanggalTidakTersediaDaring = collect($setting->tanggal_tidak_tersedia_konsultasi_daring)->map(function($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })->toArray();
            }
        }

        return view('dashboard.pages.konsultasi.create', compact('jenis', 'jenisKonsultasi', 'topik', 'sesi', 'tanggalTidakTersediaLuring', 'tanggalTidakTersediaDaring'));
    }

    /**
     * Menampilkan formulir pembuatan konsultasi untuk pengguna biasa.
     *
     * @return \Illuminate\View\View
     */
    public function createForUser()
    {
        $jenisKonsultasis = JenisKonsultasi::all()->map(function ($jenis) {
            if ($jenis->nama_jenis === 'daring') {
                $jenis->display_name = 'Konsultasi Daring';
            } elseif ($jenis->nama_jenis === 'luring') {
                $jenis->display_name = 'Konsultasi Luring';
            } else {
                $jenis->display_name = ucfirst($jenis->nama_jenis);
            }
            return $jenis;
        });

        $topiks = TopikKonsultasi::all();
        $sesis = SesiKonsultasi::all();

        $setting = Setting::first();
        $tanggalTidakTersediaLuring = [];
        $tanggalTidakTersediaDaring = [];

        if ($setting) {
            if ($setting->tanggal_tidak_tersedia_konsultasi_luring) {
                $tanggalTidakTersediaLuring = collect($setting->tanggal_tidak_tersedia_konsultasi_luring)->map(function($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })->toArray();
            }
            if ($setting->tanggal_tidak_tersedia_konsultasi_daring) {
                $tanggalTidakTersediaDaring = collect($setting->tanggal_tidak_tersedia_konsultasi_daring)->map(function($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })->toArray();
            }
        }

        return view('konsultasi.create', compact('jenisKonsultasis', 'topiks', 'sesis', 'tanggalTidakTersediaLuring', 'tanggalTidakTersediaDaring'));
    }

    /**
     * Menyimpan pengajuan konsultasi dari admin.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $jenis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();

        $rules = [
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        $isDaring = (strtolower($jenisKonsultasi->nama_jenis) === 'daring');
        if (!$isDaring) {
            $rules['tanggal_konsultasi'] = 'required|date';
            $rules['sesi_konsultasi_id'] = 'required|exists:sesi_konsultasi,id';
        }

        $request->validate($rules);

        // --- Validasi Pengaturan Konsultasi dari model Setting ---
        $setting = Setting::first();
        if ($setting) {
            // Validasi batas harian berdasarkan jenis konsultasi
            $today = Carbon::today();
            $currentConsultationsCount = 0;
            $maxDailyLimit = 0;
            $tanggalCek = null;
            $unavailableDates = [];

            if ($isDaring) {
                $maxDailyLimit = $setting->maks_konsultasi_daring_harian;
                $currentConsultationsCount = KonsultasiDetail::whereHas('konsultasi', function ($query) use ($jenisKonsultasi) {
                    $query->where('jenis_konsultasi_id', $jenisKonsultasi->id);
                })->whereDate('created_at', $today)->count();
                $tanggalCek = $today;
                $unavailableDates = $setting->tanggal_tidak_tersedia_konsultasi_daring;
            } else {
                $maxDailyLimit = $setting->maks_konsultasi_luring_harian;
                $currentConsultationsCount = KonsultasiDetail::whereHas('konsultasi', function ($query) use ($jenisKonsultasi) {
                    $query->where('jenis_konsultasi_id', $jenisKonsultasi->id);
                })->whereDate('tanggal_konsultasi', $request->tanggal_konsultasi)->count();
                $tanggalCek = Carbon::parse($request->tanggal_konsultasi);
                $unavailableDates = $setting->tanggal_tidak_tersedia_konsultasi_luring;
            }

            if ($maxDailyLimit > 0 && $currentConsultationsCount >= $maxDailyLimit) {
                $errorMessage = $isDaring
                    ? 'Batas maksimum pengajuan konsultasi daring harian telah tercapai. Mohon coba lagi besok.'
                    : 'Batas maksimum pengajuan konsultasi luring harian untuk tanggal ' . Carbon::parse($request->tanggal_konsultasi)->format('d-m-Y') . ' telah tercapai. Mohon pilih tanggal lain.';
                return back()->withInput()->with('error', $errorMessage);
            }

            // Validasi tanggal tidak tersedia
            if ($unavailableDates) {
                foreach ($unavailableDates as $item) {
                    if (Carbon::parse($item['date'])->isSameDay($tanggalCek)) {
                        $tanggalFormatted = $tanggalCek->format('d-m-Y');
                        $errorMessage = $isDaring
                            ? 'Mohon Maaf, konsultasi daring untuk tanggal ' . $tanggalFormatted . ' ditutup karena: ' . $item['reason']
                            : 'Mohon Maaf, konsultasi luring untuk tanggal ' . $tanggalFormatted . ' ditutup karena: ' . $item['reason'] . '. Sementara ajukan konsultasi daring.';
                        return back()->withInput()->with('error', $errorMessage);
                    }
                }
            }
        }
        // --- Akhir Validasi Pengaturan Konsultasi ---

        DB::beginTransaction();
        try {
            $konsultasi = Konsultasi::create([
                'user_id' => Auth::id(),
                'jenis_konsultasi_id' => $jenisKonsultasi->id,
            ]);

            $kode = $this->generateKodeKonsultasi($request->topik_id, $jenisKonsultasi->nama_jenis);

            $lampiranId = null;
            $user = Auth::user();

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $ext = $file->getClientOriginalExtension();

                $sesiDisplay = $isDaring ? 'daring' : (SesiKonsultasi::find($request->sesi_konsultasi_id)->nama_sesi ?? 'sesi');
                $tanggalForFileName = $isDaring ? now() : Carbon::parse($request->tanggal_konsultasi);

                $namaFile = $this->generateNamaFile($kode, $tanggalForFileName, $sesiDisplay, $user->nama, $ext);

                $path = $file->storeAs('lampiran_konsultasi', $namaFile, 'public');

                $lampiran = Lampiran::create(['lampiran' => $path]);
                $lampiranId = $lampiran->id;
            }

            KonsultasiDetail::create([
                'konsultasi_id' => $konsultasi->id,
                'topik_id' => $request->topik_id,
                'tanggal_konsultasi' => $isDaring ? null : $request->tanggal_konsultasi,
                'sesi_konsultasi_id' => $isDaring ? null : $request->sesi_konsultasi_id,
                'catatan_konsultasi' => $request->catatan_konsultasi,
                'status_id' => 1,
                'lampiran_id' => $lampiranId,
                'kode_konsultasi' => $kode,
            ]);

            DB::commit();

            return redirect()->route('konsultasi.jenis', $jenisKonsultasi->nama_jenis)
                ->with('success', 'Konsultasi berhasil diajukan. Kode: ' . $kode);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Konsultasi submission failed (Admin): ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan konsultasi. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan pengajuan konsultasi dari pengguna biasa.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeForUser(Request $request)
    {
        $rules = [
            'jenis_konsultasi_id' => 'required|exists:jenis_konsultasi,id',
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        $selectedJenisKonsultasi = JenisKonsultasi::find($request->jenis_konsultasi_id);
        $isDaring = false;

        if ($selectedJenisKonsultasi) {
            $isDaring = (strtolower($selectedJenisKonsultasi->nama_jenis) === 'daring');
            if (!$isDaring) {
                $rules['tanggal_konsultasi'] = 'required|date';
                $rules['sesi_konsultasi_id'] = 'required|exists:sesi_konsultasi,id';
            }
        }

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // --- Validasi Pengaturan Konsultasi dari model Setting ---
        $setting = Setting::first();
        if ($setting) {
            $today = Carbon::today();
            $currentConsultationsCount = 0;
            $maxDailyLimit = 0;
            $tanggalCek = null;
            $unavailableDates = [];

            if ($isDaring) {
                $maxDailyLimit = $setting->maks_konsultasi_daring_harian;
                $currentConsultationsCount = KonsultasiDetail::whereHas('konsultasi', function ($query) use ($selectedJenisKonsultasi) {
                    $query->where('jenis_konsultasi_id', $selectedJenisKonsultasi->id);
                })->whereDate('created_at', $today)->count();
                $tanggalCek = $today;
                $unavailableDates = $setting->tanggal_tidak_tersedia_konsultasi_daring;
            } else {
                $maxDailyLimit = $setting->maks_konsultasi_luring_harian;
                $currentConsultationsCount = KonsultasiDetail::whereHas('konsultasi', function ($query) use ($selectedJenisKonsultasi) {
                    $query->where('jenis_konsultasi_id', $selectedJenisKonsultasi->id);
                })->whereDate('tanggal_konsultasi', $request->tanggal_konsultasi)->count();
                $tanggalCek = Carbon::parse($request->tanggal_konsultasi);
                $unavailableDates = $setting->tanggal_tidak_tersedia_konsultasi_luring;
            }

            if ($maxDailyLimit > 0 && $currentConsultationsCount >= $maxDailyLimit) {
                $errorMessage = $isDaring
                    ? 'Batas maksimum pengajuan konsultasi daring harian telah tercapai. Mohon coba lagi besok.'
                    : 'Batas maksimum pengajuan konsultasi luring harian untuk tanggal ' . Carbon::parse($request->tanggal_konsultasi)->format('d-m-Y') . ' telah tercapai. Mohon pilih tanggal lain.';
                return back()->withInput()->with('error', $errorMessage);
            }

            // Validasi tanggal tidak tersedia
            if ($unavailableDates) {
                foreach ($unavailableDates as $item) {
                    if (Carbon::parse($item['date'])->isSameDay($tanggalCek)) {
                        $tanggalFormatted = $tanggalCek->format('d-m-Y');
                        $errorMessage = $isDaring
                            ? 'Mohon Maaf, konsultasi daring untuk tanggal ' . $tanggalFormatted . ' ditutup karena: ' . $item['reason']
                            : 'Mohon Maaf, konsultasi luring untuk tanggal ' . $tanggalFormatted . ' ditutup karena: ' . $item['reason'] . '. Sementara ajukan konsultasi daring.';
                        return back()->withInput()->with('error', $errorMessage);
                    }
                }
            }
        }
        // --- Akhir Validasi Pengaturan Konsultasi ---

        DB::beginTransaction();
        try {
            $konsultasi = Konsultasi::create([
                'user_id' => Auth::id(),
                'jenis_konsultasi_id' => $request->jenis_konsultasi_id,
            ]);

            $actualJenisNama = $selectedJenisKonsultasi->nama_jenis;
            $kode = $this->generateKodeKonsultasi($request->topik_id, $actualJenisNama);

            $lampiran = null;
            $lampiranId = null;
            $user = Auth::user();

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $ext = $file->getClientOriginalExtension();
                $sesiDisplay = $isDaring ? 'daring' : (SesiKonsultasi::find($request->sesi_konsultasi_id)->nama_sesi ?? 'sesi');
                $tanggalForFileName = $isDaring ? now() : Carbon::parse($request->tanggal_konsultasi);
                $namaFile = $this->generateNamaFile($kode, $tanggalForFileName, $sesiDisplay, $user->nama, $ext);

                $path = $file->storeAs('lampiran_konsultasi', $namaFile, 'public');

                $lampiran = Lampiran::create(['lampiran' => $path]);
                $lampiranId = $lampiran->id;
            }

            KonsultasiDetail::create([
                'konsultasi_id' => $konsultasi->id,
                'topik_id' => $request->topik_id,
                'tanggal_konsultasi' => $isDaring ? null : $request->tanggal_konsultasi,
                'sesi_konsultasi_id' => $isDaring ? null : $request->sesi_konsultasi_id,
                'catatan_konsultasi' => $request->catatan_konsultasi,
                'status_id' => 1,
                'lampiran_id' => $lampiranId,
                'kode_konsultasi' => $kode,
            ]);

            DB::commit();

            $lampiranPath = $lampiran ? Storage::url($lampiran->lampiran) : null;
            $namaPemohon = Auth::user()->nama;
            $jenisKonsultasiNama = $selectedJenisKonsultasi->nama_jenis;
            $namaUsaha = Auth::user()->nama_usaha ?? 'Tidak Ada';

            return redirect()->route('konsultasi.successForUser', [
                'kode_konsultasi' => $kode,
                'nama_pemohon' => $namaPemohon,
                'jenis_konsultasi' => $jenisKonsultasiNama,
                'lampiran_path' => $lampiranPath,
                'nama_usaha' => $namaUsaha,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Konsultasi submission failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan konsultasi. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman sukses setelah pengajuan konsultasi.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function successForUser(Request $request)
    {
        $request->validate([
            'kode_konsultasi' => 'required|string',
            'nama_pemohon' => 'required|string',
            'jenis_konsultasi' => 'required|string',
            'lampiran_path' => 'nullable|string',
            'nama_usaha' => 'nullable|string',
        ]);

        return view('konsultasi.succes', [
            'kode_konsultasi' => $request->query('kode_konsultasi'),
            'nama_pemohon' => $request->query('nama_pemohon'),
            'nama_usaha' => $request->query('nama_usaha'),
            'jenis_konsultasi' => $request->query('jenis_konsultasi'),
            'lampiran_path' => $request->query('lampiran_path'),
        ]);
    }

    /**
     * Menampilkan daftar konsultasi berdasarkan jenis.
     *
     * @param string $jenis
     * @return \Illuminate\View\View
     */
    public function indexByJenis($jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();

        $konsultasi = Konsultasi::where('jenis_konsultasi_id', $jenisKonsultasi->id)
            ->with(['user', 'jenisKonsultasi', 'detail.topik', 'detail.status'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dashboard.pages.konsultasi.index', compact('konsultasi', 'jenis', 'jenisKonsultasi'));
    }

    /**
     * Menghasilkan kode konsultasi unik.
     *
     * @param int $topikId
     * @param string $jenisNama
     * @return string
     */
    protected function generateKodeKonsultasi($topikId, $jenisNama)
    {
        $prefixes = [
            1 => 'PD', 2 => 'PA', 3 => 'PU', 4 => 'RS',
            5 => 'PL', 6 => 'AN', 7 => 'LN',
        ];

        $prefix = $prefixes[$topikId] ?? 'XX';
        $jenisKode = strtolower($jenisNama) === 'daring' ? '2' : '3';

        do {
            $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $kode = "$prefix-$jenisKode$randomNumber";
        } while (KonsultasiDetail::where('kode_konsultasi', $kode)->exists());

        return $kode;
    }

    /**
     * Menghasilkan nama file untuk lampiran.
     *
     * @param string $kode
     * @param \Carbon\Carbon|string $tanggal
     * @param string $sesi
     * @param string $namaUser
     * @param string $ext
     * @return string
     */
    protected function generateNamaFile($kode, $tanggal, $sesi, $namaUser, $ext)
    {
        $tanggalFormat = Carbon::parse($tanggal)->format('Ymd');
        $nama = str_replace(' ', '_', strtolower($namaUser));
        return "{$kode}-{$tanggalFormat}-{$sesi}-{$nama}.{$ext}";
    }

    /**
     * Menampilkan detail konsultasi untuk admin.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showDetail($id)
    {
        $detail = KonsultasiDetail::with([
            'topik', 'sesi', 'status', 'lampiran', 'konsultasi.user', 'konsultasi.jenisKonsultasi'
        ])->findOrFail($id);

        return view('dashboard.pages.konsultasi.detail', compact('detail'));
    }

    /**
     * Menampilkan detail konsultasi untuk pengguna.
     *
     * @param \App\Models\KonsultasiDetail $konsultasiDetail
     * @return \Illuminate\View\View
     */
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

    /**
     * Menampilkan formulir edit konsultasi untuk admin.
     *
     * @param string $jenis
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($jenis, $id)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();
        $detail = KonsultasiDetail::with(['konsultasi', 'lampiran', 'topik', 'sesi', 'status'])->findOrFail($id);
        $topik = TopikKonsultasi::all();
        $sesi = SesiKonsultasi::all();

        return view('dashboard.pages.konsultasi.edit', compact('jenis', 'jenisKonsultasi', 'detail', 'topik', 'sesi'));
    }

    /**
     * Memperbarui data konsultasi.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $jenis
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Menghapus konsultasi.
     *
     * @param string $jenis
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($jenis, $id)
    {
        $detail = KonsultasiDetail::with('lampiran', 'konsultasi')->findOrFail($id);

        $lampiran = $detail->lampiran;
        $konsultasi = $detail->konsultasi;

        DB::beginTransaction();
        try {
            DB::table('tindak_lanjut_konsultasi')->where('konsultasi_id', $konsultasi->id)->delete();
            $detail->delete();

            if ($lampiran) {
                if (!empty($lampiran->lampiran)) {
                    Storage::disk('public')->delete($lampiran->lampiran);
                }
                $lampiran->delete();
            }

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

    /**
     * Mengubah status konsultasi menjadi "Diproses".
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifikasi($id)
    {
        $detail = KonsultasiDetail::findOrFail($id);
        $detail->status_id = 2;
        $detail->save();

        return back()->with('success', 'Status diubah menjadi Diproses.');
    }

    /**
     * Menambahkan catatan tindak lanjut dan mengubah status menjadi "Selesai".
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

            $detail->status_id = 3;
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