<?php

namespace App\Http\Controllers\Perling;

use App\Http\Controllers\Controller;
use App\Models\DokumenPersetujuan;
use App\Models\JenisPerling;

class IndexController extends Controller
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

    protected function redirectToJenis($namaPerling, $message)
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
