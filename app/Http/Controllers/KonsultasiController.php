<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use App\Models\JenisKonsultasi;
use App\Models\KonsultasiDetail;
use App\Models\TopikKonsultasi;
use App\Models\SesiKonsultasi;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class KonsultasiController extends Controller
{
    public function create($jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();
        $topik = TopikKonsultasi::all();
        $sesi = SesiKonsultasi::all();

        return view('dashboard.pages.konsultasi.create', compact('jenis', 'jenisKonsultasi', 'topik', 'sesi'));
    }

    public function store(Request $request, $jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();

        $rules = [
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        if (strtolower($jenis) !== 'daring') {
            $rules['tanggal_konsultasi'] = 'required|date';
            $rules['sesi_konsultasi_id'] = 'required|exists:sesi_konsultasi,id';
        }

        $request->validate($rules);

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'jenis_konsultasi_id' => $jenisKonsultasi->id,
        ]);

        $kode = $this->generateKodeKonsultasi($request->topik_id, $jenis);
        $lampiranId = null;
        $user = Auth::user();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $ext = $file->getClientOriginalExtension();
            $sesi = strtolower($jenis) === 'daring' ? 'daring' : (SesiKonsultasi::find($request->sesi_konsultasi_id)->sesi ?? 'sesi');
            $tanggal = strtolower($jenis) === 'daring' ? now() : $request->tanggal_konsultasi;
            $namaFile = $this->generateNamaFile($kode, $tanggal, $sesi, $user->nama, $ext);

            $path = $file->storeAs('lampiran_konsultasi', $namaFile, 'public');

            $lampiran = Lampiran::create(['lampiran' => $path]);
            $lampiranId = $lampiran->id;
        }

        KonsultasiDetail::create([
            'konsultasi_id' => $konsultasi->id,
            'topik_id' => $request->topik_id,
            'tanggal_konsultasi' => strtolower($jenis) === 'daring' ? null : $request->tanggal_konsultasi,
            'sesi_konsultasi_id' => strtolower($jenis) === 'daring' ? null : $request->sesi_konsultasi_id,
            'catatan_konsultasi' => $request->catatan_konsultasi,
            'status_id' => 1,
            'lampiran_id' => $lampiranId,
            'kode_konsultasi' => $kode,
        ]);

        return redirect()->route('konsultasi.jenis', $jenis)
            ->with('success', 'Konsultasi berhasil diajukan. Kode: ' . $kode);
    }

    public function indexByJenis($jenis)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();

        $konsultasi = Konsultasi::where('jenis_konsultasi_id', $jenisKonsultasi->id)
            ->with(['user', 'jenisKonsultasi', 'detail.topik', 'detail.status'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dashboard.pages.konsultasi.index', compact('konsultasi', 'jenis', 'jenisKonsultasi'));
    }

    protected function generateKodeKonsultasi($topikId, $jenisNama)
    {
        $prefixes = [
            1 => 'PD',
            2 => 'PA',
            3 => 'PU',
            4 => 'RS',
            5 => 'PL',
            6 => 'AN',
            7 => 'LN',
        ];

        $prefix = $prefixes[$topikId] ?? 'XX';
        $jenisKode = strtolower($jenisNama) === 'daring' ? '2' : '3';

        do {
            $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $kode = "$prefix-$jenisKode$randomNumber";
        } while (KonsultasiDetail::where('kode_konsultasi', $kode)->exists());

        return $kode;
    }

    protected function generateNamaFile($kode, $tanggal, $sesi, $namaUser, $ext)
    {
        $tanggalFormat = Carbon::parse($tanggal)->format('Ymd');
        $nama = str_replace(' ', '_', strtolower($namaUser));
        return "{$kode}-{$tanggalFormat}-{$sesi}-{$nama}.{$ext}";
    }

    public function showDetail($id)
    {
        $detail = KonsultasiDetail::with([
            'topik', 'sesi', 'status', 'lampiran', 'konsultasi.user', 'konsultasi.jenisKonsultasi'
        ])->findOrFail($id);

        return view('dashboard.pages.konsultasi.detail', compact('detail'));
    }

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

    $rules = [
        'topik_id' => 'required|exists:topik_konsultasi,id',
        'catatan_konsultasi' => 'nullable|string',
        'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ];

    if (strtolower($jenis) !== 'daring') {
        $rules['tanggal_konsultasi'] = 'required|date';
        $rules['sesi_konsultasi_id'] = 'required|exists:sesi_konsultasi,id';
    }

    $request->validate($rules);

    $sesi = strtolower($jenis) === 'daring' ? 'daring' : (SesiKonsultasi::find($request->sesi_konsultasi_id)->sesi ?? 'sesi');
    $tanggal = strtolower($jenis) === 'daring' ? now() : $request->tanggal_konsultasi;

    if ($request->hasFile('lampiran')) {
        $file = $request->file('lampiran');
        $ext = $file->getClientOriginalExtension();
        $namaFile = $this->generateNamaFile($kode, $tanggal, $sesi, $user->nama, $ext);

        $path = $file->storeAs('lampiran_konsultasi', $namaFile, 'public');

        // Simpan lampiran baru terlebih dahulu
        $lampiranBaru = Lampiran::create(['lampiran' => $path]);

        // Update relasi di detail dulu sebelum menghapus lampiran lama
        $detail->lampiran_id = $lampiranBaru->id;
        $detail->save();

        // Hapus file dan record lampiran lama setelah tidak digunakan
        if ($detail->lampiran && $detail->lampiran->id !== $lampiranBaru->id) {
            if (!empty($detail->lampiran->lampiran)) {
                Storage::disk('public')->delete($detail->lampiran->lampiran);
            }
            $detail->lampiran->delete();
        }
    }

    // Update data lainnya
    $detail->update([
        'topik_id' => $request->topik_id,
        'tanggal_konsultasi' => strtolower($jenis) === 'daring' ? null : $request->tanggal_konsultasi,
        'sesi_konsultasi_id' => strtolower($jenis) === 'daring' ? null : $request->sesi_konsultasi_id,
        'catatan_konsultasi' => $request->catatan_konsultasi,
    ]);

    return redirect()->route('konsultasi.jenis', $jenis)
        ->with('success', 'Konsultasi berhasil diperbarui.');
}


    public function destroy($jenis, $id)
    {
        $detail = KonsultasiDetail::with('lampiran', 'konsultasi')->findOrFail($id);

        $lampiran = $detail->lampiran;
        $konsultasi = $detail->konsultasi;

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

        return redirect()->route('konsultasi.jenis', $jenis)
            ->with('success', 'Konsultasi berhasil dihapus.');
    }

    public function verifikasi($id)
    {
        $detail = KonsultasiDetail::findOrFail($id);
        $detail->status_id = 2;
        $detail->save();

        return back()->with('success', 'Status diubah menjadi Diproses.');
    }

    public function tindaklanjut(Request $request, $id)
    {
        $request->validate([
            'catatan_tindaklanjut' => 'required|string|max:1000',
        ]);

        $detail = KonsultasiDetail::findOrFail($id);

        DB::table('tindak_lanjut_konsultasi')->insert([
            'konsultasi_id' => $detail->konsultasi_id,
            'catatan_tindaklanjut' => $request->catatan_tindaklanjut,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $detail->status_id = 3;
        $detail->save();

        return back()->with('success', 'Catatan tersimpan dan status diubah menjadi Selesai.');
    }
}
