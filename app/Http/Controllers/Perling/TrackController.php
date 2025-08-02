<?php

namespace App\Http\Controllers\Perling;

use App\Models\DokumenPersetujuan;
use Illuminate\Http\Request;
use App\Http\Controllers\Perling\IndexController;

class TrackController extends IndexController
{

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

        // Return a partial view for AJAX requests, or a full view if not
        return view('perling.track_results_partial', compact('dokumen', 'progresDokumen'))->render();
    }
}
