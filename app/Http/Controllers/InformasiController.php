<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\JenisInformasi;
use App\Models\Lampiran;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InformasiController extends Controller
{
    public function pengumuman()
    {
        $informasi = Informasi::whereHas('jenisInformasi', function ($query) {
            $query->where('nama_jenis', 'pengumuman');
        })->with('jenisInformasi', 'lampiran', 'user')->get();

        return view('dashboard.pages.informasi.index', compact('informasi'));
    }

    public function publikasi()
    {
        $informasi = Informasi::whereHas('jenisInformasi', function ($query) {
            $query->where('nama_jenis', 'publikasi');
        })->with('jenisInformasi', 'lampiran', 'user')->get();

        return view('dashboard.pages.informasi.index', compact('informasi'));
    }

    public function create()
    {
        $jenisInformasi = JenisInformasi::all();
        $users = User::all();
        return view('dashboard.pages.informasi.create', compact('users', 'jenisInformasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_informasi_id' => 'required|exists:jenis_informasi,id',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
        ]);

        $data = $request->only(['jenis_informasi_id', 'judul', 'konten', 'user_id']);
        $data['tanggal'] = Carbon::now('Asia/Jakarta');
        $data['user_id'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('lampiran', $filename, 'public');

                $lampiran = Lampiran::create([
                    'lampiran' => 'storage/' . $path
                ]);

                $data['lampiran_id'] = $lampiran->id;
            }

            Informasi::create($data);
            DB::commit();

            switch ($request->jenis_informasi_id) {
                case 1:
                    return redirect()->route('informasi.publikasi')->with('success', 'Publikasi berhasil ditambahkan.');
                case 2:
                    return redirect()->route('informasi.pengumuman')->with('success', 'Pengumuman berhasil ditambahkan.');
                default:
                    return redirect()->route('home')->with('error', 'Jenis Informasi tidak valid.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating informasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan informasi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $informasi = Informasi::with('lampiran')->findOrFail($id);
        $jenisInformasi = JenisInformasi::all();
        return view('dashboard.pages.informasi.edit', compact('informasi', 'jenisInformasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_informasi_id' => 'required|exists:jenis_informasi,id',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
        ]);

        $informasi = Informasi::findOrFail($id);
        $data = $request->only(['jenis_informasi_id', 'judul', 'konten']);

        try {
            DB::beginTransaction();

            // Menyimpan lampiran baru jika ada
            if ($request->hasFile('lampiran')) {
                // Menghapus lampiran lama jika ada
                if ($informasi->lampiran) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $informasi->lampiran->lampiran));
                    $informasi->lampiran->delete();
                }

                $file = $request->file('lampiran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('lampiran', $filename, 'public');

                $lampiran = Lampiran::create([
                    'lampiran' => 'storage/' . $path,
                ]);

                $data['lampiran_id'] = $lampiran->id;
            } else {
                // Jika tidak ada lampiran baru, menggunakan lampiran lama
                $data['lampiran_id'] = $informasi->lampiran_id;
            }

            // Update informasi
            $informasi->update($data);
            DB::commit();

            // Redirect sesuai jenis informasi
            switch ($request->jenis_informasi_id) {
                case 1:
                    return redirect()->route('informasi.publikasi')->with('success', 'Publikasi berhasil diperbarui.');
                case 2:
                    return redirect()->route('informasi.pengumuman')->with('success', 'Pengumuman berhasil diperbarui.');
                default:
                    return redirect()->route('home')->with('error', 'Jenis Informasi tidak valid.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating informasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui informasi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $informasi = Informasi::findOrFail($id);
        $jenisInformasiId = $informasi->jenis_informasi_id;

        try {
            DB::beginTransaction();

            if ($informasi->lampiran) {
                Storage::disk('public')->delete(str_replace('storage/', '', $informasi->lampiran->lampiran));
                $informasi->lampiran->delete();
            }

            $informasi->delete();
            DB::commit();

            switch ($jenisInformasiId) {
                case 1:
                    return redirect()->route('informasi.publikasi')->with('success', 'Publikasi berhasil dihapus.');
                case 2:
                    return redirect()->route('informasi.pengumuman')->with('success', 'Pengumuman berhasil dihapus.');
                default:
                    return redirect()->route('home')->with('error', 'Jenis Informasi tidak valid.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting informasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus informasi: ' . $e->getMessage());
        }
    }
}
