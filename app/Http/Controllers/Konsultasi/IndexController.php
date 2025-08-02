<?php

namespace App\Http\Controllers\Konsultasi;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\JenisKonsultasi;
use App\Models\KonsultasiDetail;
use App\Models\TopikKonsultasi;
use Carbon\Carbon;

class IndexController extends Controller
{

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

    protected function generateNamaFile($kode, $tanggal, $sesi, $namaUser, $ext)
    {
        $tanggalFormat = Carbon::parse($tanggal)->format('Ymd');
        $nama = str_replace(' ', '_', strtolower($namaUser));
        return "{$kode}-{$tanggalFormat}-{$sesi}-{$nama}.{$ext}";
    }
}
