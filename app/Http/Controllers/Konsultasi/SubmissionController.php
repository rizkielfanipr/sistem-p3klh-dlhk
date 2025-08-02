<?php

namespace App\Http\Controllers\Konsultasi;

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
use App\Http\Controllers\Konsultasi\IndexController; 

class SubmissionController extends IndexController 
{

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

        // Validasi pengaturan konsultasi dari model Setting
        $setting = Setting::first();
        if ($setting) {
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
                'status_id' => 1, // Status 'Diajukan'
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

        // Validasi pengaturan konsultasi dari model Setting
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
                'status_id' => 1, // Status 'Diajukan'
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
}
