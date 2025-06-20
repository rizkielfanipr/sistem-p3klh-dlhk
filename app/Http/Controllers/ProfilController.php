<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.pages.profile.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update data
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;

        // Hapus foto jika diminta
        if ($request->has('hapus_foto') && $request->hapus_foto == 1) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = null;
        }

        // Upload dan simpan foto baru
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path; // Hanya simpan path relatif ke 'storage'
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showChangePasswordForm()
{
    return view('dashboard.pages.profile.gantipassword');
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors(['current_password' => 'Password lama salah.']);
    }

    $user = Auth::user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password berhasil diperbarui.');
}
}
