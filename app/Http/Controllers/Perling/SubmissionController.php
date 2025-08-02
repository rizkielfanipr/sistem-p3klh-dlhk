<?php

namespace App\Http\Controllers\Perling;

use App\Models\DokumenPersetujuan;
use App\Models\JenisPerling;
use App\Models\Lampiran;
use App\Models\ProgresDokumen;
use App\Models\StatusDokumen;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Http\Controllers\Perling\IndexController; 

class SubmissionController extends IndexController 
{

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
        return $this->handleStore($request, false); 
    }

    public function storeForUser(Request $request)
    {
        return $this->handleStore($request, true); 
    }

    protected function handleStore(Request $request, bool $isUserSubmission)
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

        $setting = Setting::first();
        if ($setting) {
            $today = Carbon::today();
            $currentPerlingCount = DokumenPersetujuan::whereDate('created_at', $today)->count();
            $maxDailyPerlingLimit = $setting->maks_perling_harian;

            if ($maxDailyPerlingLimit > 0 && $currentPerlingCount >= $maxDailyPerlingLimit) {
                return back()->withInput()->with('error', 'Batas maksimum pengajuan dokumen perling harian telah tercapai. Mohon coba lagi besok.');
            }

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

            if ($isUserSubmission) {
                return redirect()->route('perling.success', [
                    'kode_perling'  => $dokumen->kode_perling,
                    'nama_pemohon'  => $dokumen->nama_pemohon,
                    'nama_usaha'    => $dokumen->nama_usaha,
                    'jenis_perling' => $jenis->nama_perling,
                    'lampiran_path' => Storage::url($lampiran->lampiran),
                ])->with('success', 'Permohonan Anda berhasil diajukan!');
            } else {
                return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil ditambahkan.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan dokumen: ' . $e->getMessage());
        }
    }

    public function successForUser(Request $request)
    {
        return view('perling.success', [
            'kode_perling'  => $request->query('kode_perling'),
            'nama_pemohon'  => $request->query('nama_pemohon'),
            'nama_usaha'    => $request->query('nama_usaha'),
            'jenis_perling' => $request->query('jenis_perling'),
            'lampiran_path' => $request->query('lampiran_path'),
        ]);
    }
}
