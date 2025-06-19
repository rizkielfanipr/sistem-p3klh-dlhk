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
        $request->validate([
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'tanggal_konsultasi' => 'required|date',
            'sesi_konsultasi_id' => 'required|exists:sesi_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'jenis_konsultasi_id' => $jenisKonsultasi->id,
        ]);

        $lampiranId = null;
        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran_konsultasi', 'public');

            $lampiran = Lampiran::create([
                'nama_file' => $request->file('lampiran')->getClientOriginalName(),
                'path' => $path,
            ]);

            $lampiranId = $lampiran->id;
        }

        $kode = $this->generateKodeKonsultasi($request->topik_id, $jenis);

        KonsultasiDetail::create([
            'konsultasi_id' => $konsultasi->id,
            'topik_id' => $request->topik_id,
            'tanggal_konsultasi' => $request->tanggal_konsultasi,
            'sesi_konsultasi_id' => $request->sesi_konsultasi_id,
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

    public function showDetail($id)
    {
        $detail = KonsultasiDetail::with([
            'topik',
            'sesi',
            'status',
            'lampiran',
            'konsultasi.user',
            'konsultasi.jenisKonsultasi'
        ])->findOrFail($id);

        return view('dashboard.pages.konsultasi.detail', compact('detail'));
    }

    public function edit($jenis, $id)
    {
        $jenisKonsultasi = JenisKonsultasi::where('nama_jenis', $jenis)->firstOrFail();
        $detail = KonsultasiDetail::with(['konsultasi', 'lampiran'])->findOrFail($id);
        $topik = TopikKonsultasi::all();
        $sesi = SesiKonsultasi::all();

        return view('dashboard.pages.konsultasi.edit', compact('jenis', 'jenisKonsultasi', 'detail', 'topik', 'sesi'));
    }

    public function update(Request $request, $jenis, $id)
    {
        $request->validate([
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'tanggal_konsultasi' => 'required|date',
            'sesi_konsultasi_id' => 'required|exists:sesi_konsultasi,id',
            'catatan_konsultasi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        $detail = KonsultasiDetail::findOrFail($id);

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran_konsultasi', 'public');
            $lampiran = Lampiran::create([
                'nama_file' => $request->file('lampiran')->getClientOriginalName(),
                'path' => $path,
            ]);
            $detail->lampiran_id = $lampiran->id;
        }

        $detail->update([
            'topik_id' => $request->topik_id,
            'tanggal_konsultasi' => $request->tanggal_konsultasi,
            'sesi_konsultasi_id' => $request->sesi_konsultasi_id,
            'catatan_konsultasi' => $request->catatan_konsultasi,
        ]);

        return redirect()->route('konsultasi.jenis', $jenis)->with('success', 'Konsultasi berhasil diperbarui.');
    }

    public function destroy($jenis, $id)
    {
        $detail = KonsultasiDetail::findOrFail($id);

        if ($detail->lampiran) {
            \Storage::disk('public')->delete($detail->lampiran->path);
            $detail->lampiran->delete();
        }

        $detail->delete();

        $konsultasi = $detail->konsultasi;
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
