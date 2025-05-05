<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class ProfilController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();
        return view('components.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input yang diterima, termasuk file foto
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file foto
        ]);

        // Update nama dan no_telp
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;

        // Proses upload dan update foto jika ada
        if ($request->has('hapus_foto') && $request->hapus_foto == 1) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists($user->foto)) {
                Storage::delete($user->foto);
            }
            $user->foto = null; // Set kolom foto di database menjadi null
        } else if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists($user->foto)) {
                Storage::delete($user->foto);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('profile_photos', 'public'); // Simpan di direktori storage/app/public/profile_photos
            $user->foto = $path; // Simpan path ke database
        }


        // Simpan perubahan
        $user->save();

        // Kembalikan ke halaman profil dengan pesan sukses
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
