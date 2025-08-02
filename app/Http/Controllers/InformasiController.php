<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InformasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Memuat relasi 'lampiran' untuk setiap informasi
        $informasi = Informasi::with('lampiran')->latest()->paginate(10);
        return view('dashboard.pages.informasi.index', compact('informasi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.pages.informasi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'judul'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Validasi file lampiran
            'lampiran_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar,txt|max:10240',
        ]);

        $imagePath = null;
        // Penanganan upload gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/informasi_images');
        }

        $lampiranId = null;
        // Penanganan upload lampiran
        if ($request->hasFile('lampiran_file')) {
            $file = $request->file('lampiran_file');
            $filePath = $file->store('public/lampiran_diskusi'); // Simpan file ke storage

            // Buat entri baru di tabel 'lampiran'
            // Perhatikan: Hanya kolom 'lampiran' yang akan disimpan sesuai model Anda.
            // Data seperti nama_file, ukuran_file, tipe_file, kategori TIDAK AKAN disimpan.
            $newLampiran = Lampiran::create([
                'lampiran'    => $filePath, // Simpan path file ke kolom 'lampiran'
            ]);
            $lampiranId = $newLampiran->id;
        }

        // Buat entri baru di tabel 'informasi'
        Informasi::create([
            'judul'         => $request->judul,
            'description'   => $request->description,
            'image'         => $imagePath,
            'lampiran_id'   => $lampiranId,
        ]);

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function show(Informasi $informasi)
    {
        $informasi->load('lampiran'); // Pastikan relasi lampiran dimuat
        return view('dashboard.pages.informasi.show', compact('informasi'));
    }

     public function showDetailUser($id)
    {
        // Mengambil data Informasi berdasarkan ID, memuat relasi 'lampiran'
        // 'user' dan 'tanggapan' mungkin tidak ada di model Informasi jika itu hanya untuk pengumuman.
        // Jika Informasi Anda tidak memiliki relasi ini, hapus dari with().
        $informasi = Informasi::with(['lampiran'])->findOrFail($id);

        return view('informasi.detail', [
            'informasi' => $informasi,
            'title' => 'Detail Informasi',
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Informasi $informasi)
    {
        $informasi->load('lampiran'); // Pastikan relasi lampiran dimuat untuk form edit
        return view('dashboard.pages.informasi.edit', compact('informasi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Informasi $informasi)
    {
        // Validasi input dari request
        $request->validate([
            'judul'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Validasi file lampiran baru
            'lampiran_file_update' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar,txt|max:10240',
        ]);

        $imagePath = $informasi->image;
        // Penanganan update gambar
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($informasi->image) {
                Storage::delete($informasi->image);
            }
            $imagePath = $request->file('image')->store('public/informasi_images');
        } elseif ($request->input('clear_image')) { // Jika ada checkbox untuk menghapus gambar
            if ($informasi->image) {
                Storage::delete($informasi->image);
            }
            $imagePath = null;
        }

        $lampiranId = $informasi->lampiran_id;
        // Penanganan update lampiran
        if ($request->hasFile('lampiran_file_update')) {
            // Hapus lampiran lama jika ada
            if ($informasi->lampiran) {
                if ($informasi->lampiran->lampiran) { // Akses kolom 'lampiran' di model Lampiran untuk path fisik
                    Storage::delete($informasi->lampiran->lampiran);
                }
                $informasi->lampiran->delete(); // Hapus entri lampiran dari database
            }

            $file = $request->file('lampiran_file_update');
            $filePath = $file->store('public/lampiran_diskusi');

            // Buat entri lampiran baru
            // Perhatikan: Hanya kolom 'lampiran' yang akan disimpan sesuai model Anda.
            $newLampiran = Lampiran::create([
                'lampiran'    => $filePath, // Simpan path file ke kolom 'lampiran'
            ]);
            $lampiranId = $newLampiran->id;
        } elseif ($request->input('clear_lampiran_file')) { // Jika ada checkbox untuk menghapus lampiran
            if ($informasi->lampiran) {
                if ($informasi->lampiran->lampiran) { // Akses kolom 'lampiran' di model Lampiran
                    Storage::delete($informasi->lampiran->lampiran);
                }
                $informasi->lampiran->delete();
            }
            $lampiranId = null;
        }

        // Perbarui entri informasi
        $informasi->update([
            'judul'         => $request->judul,
            'description'   => $request->description,
            'image'         => $imagePath,
            'lampiran_id'   => $lampiranId,
        ]);

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Informasi $informasi)
    {
        // Hapus gambar jika ada
        if ($informasi->image) {
            Storage::delete($informasi->image);
        }

        // Hapus lampiran jika ada
        if ($informasi->lampiran) {
            if ($informasi->lampiran->lampiran) { // Akses kolom 'lampiran' di model Lampiran
                Storage::delete($informasi->lampiran->lampiran);
            }
            $informasi->lampiran->delete(); // Hapus entri lampiran dari database
        }

        // Hapus entri informasi dari database
        $informasi->delete();

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil dihapus!');
    }
}