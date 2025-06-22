<?php

namespace App\Http\Controllers;

use App\Models\DokumenPersetujuan;
use App\Models\JenisPerling;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerlingController extends Controller
{
    public function indexAmdal()
    {
        return $this->indexByJenisPerling('AMDAL', 'Daftar Dokumen AMDAL', 'AMDAL');
    }

    public function indexUKLUPL()
    {
        return $this->indexByJenisPerling('UKL-UPL', 'Daftar Dokumen UKL-UPL', 'UKL-UPL');
    }

    public function indexDELH()
    {
        return $this->indexByJenisPerling('DELH', 'Daftar Dokumen DELH', 'DELH');
    }

    public function indexDPLH()
    {
        return $this->indexByJenisPerling('DPLH', 'Daftar Dokumen DPLH', 'DPLH');
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
        return view('dashboard.pages.perling.create', compact('jenisPerlingList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemohon'     => 'required|string|max:255',
            'nama_usaha'       => 'required|string|max:255',
            'alamat_usaha'     => 'required|string',
            'jenis_perling_id' => 'required|exists:jenis_perling,id',
            'lampiran'         => 'required|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
        ]);

        $lampiranPath = $request->file('lampiran')->store('lampiran_perling', 'public');

        $lampiran = Lampiran::create([
            'lampiran' => $lampiranPath,
        ]);

        $dokumen = DokumenPersetujuan::create([
            'user_id'           => Auth::id(),
            'nama_pemohon'      => $request->nama_pemohon,
            'nama_usaha'        => $request->nama_usaha,
            'alamat_usaha'      => $request->alamat_usaha,
            'jenis_perling_id'  => $request->jenis_perling_id,
            'lampiran_id'       => $lampiran->id,
        ]);

        $jenis = JenisPerling::find($request->jenis_perling_id);

        return $this->redirectToJenis($jenis->nama_perling, 'Dokumen berhasil ditambahkan.');
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
            'nama_pemohon'     => 'required|string|max:255',
            'nama_usaha'       => 'required|string|max:255',
            'alamat_usaha'     => 'required|string',
            'jenis_perling_id' => 'required|exists:jenis_perling,id',
            'lampiran'         => 'nullable|file|mimes:pdf,docx,doc,xlsx,jpg,jpeg,png|max:2048',
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
            'nama_pemohon'      => $request->nama_pemohon,
            'nama_usaha'        => $request->nama_usaha,
            'alamat_usaha'      => $request->alamat_usaha,
            'jenis_perling_id'  => $request->jenis_perling_id,
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
}
