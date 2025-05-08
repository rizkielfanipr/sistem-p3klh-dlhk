<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\KategoriLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    // Menampilkan semua layanan
    public function index()
    {
        $kategoriLayanans = KategoriLayanan::all();
        return view('dashboard.pages.layanan.layanan', compact('kategoriLayanans'));
    }

    // Menyimpan layanan baru
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_layanan,id',
            'konten_layanan' => 'required',
        ]);

        // Cek apakah sudah ada layanan dengan kategori yang sama
        $existingLayanan = Layanan::where('kategori_id', $request->kategori_id)->first();

        if ($existingLayanan) {
            return response()->json(['success' => false, 'message' => 'Layanan pada kategori ini sudah ada.'], 409); // Status 409 Conflict
        }

        // Menambahkan user_id yang diambil dari user yang sedang login
        $layanan = new Layanan();
        $layanan->kategori_id = $request->kategori_id;
        $layanan->konten_layanan = $request->konten_layanan;
        $layanan->user_id = auth()->user()->id;
        $layanan->save();

        return response()->json(['success' => true, 'message' => 'Layanan berhasil ditambahkan', 'id' => $layanan->id, 'konten' => $layanan->konten_layanan]);
    }

    // Menampilkan form untuk mengedit layanan (tidak digunakan pada implementasi ini)
    public function edit($id)
    {
        // Implementasi ini menggunakan form yang sama untuk tambah dan edit
    }

    // Memperbarui layanan
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_layanan,id',
            'konten_layanan' => 'required',
        ]);

        $layanan = Layanan::findOrFail($id);
        $layanan->kategori_id = $request->kategori_id;
        $layanan->konten_layanan = $request->konten_layanan;
        $layanan->save();

        return response()->json(['success' => true, 'message' => 'Layanan berhasil diperbarui', 'id' => $layanan->id, 'konten' => $layanan->konten_layanan]);
    }

    // Menghapus layanan
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->delete();

        // Mengembalikan ID kategori untuk di-refresh di JavaScript
        return Response::json(['success' => true, 'message' => 'Layanan berhasil dihapus', 'kategori_id' => $layanan->kategori_id]);
    }

    // Mendapatkan konten layanan berdasarkan kategori
    public function getKonten($id)
    {
        $layanan = Layanan::where('kategori_id', $id)->first();
        return response()->json(['konten' => $layanan ? $layanan->konten_layanan : '', 'id' => $layanan ? $layanan->id : null]);
    }

    // Menangani upload gambar
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/images', 'public');
            $url = asset('storage/' . $path);

            return response()->json(['success' => true, 'url' => $url]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar.']);
    }
}
