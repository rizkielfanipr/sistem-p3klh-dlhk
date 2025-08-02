<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Konsultasi;
use App\Models\DokumenPersetujuan;
use App\Models\User; // Keep if you plan to use it directly, otherwise can be removed if only Auth::user() is used

class ProfilController extends Controller
{
    /**
     * Display the dashboard profile view.
     */
    public function index()
    {
        return view('dashboard.pages.profile.profil', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display the user's public profile page with consultation and Perling history.
     */
    public function showUserProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman profil.');
        }

        $konsultasiHistory = $user->konsultasi() // Assuming a 'konsultasi' relationship on the User model
                                  ->with(['jenisKonsultasi', 'detail.status', 'detail.topik'])
                                  ->latest() // Equivalent to orderBy('created_at', 'desc')
                                  ->get()
                                  ->map(function ($konsultasi) {
                                      $latestDetail = $konsultasi->detail->sortByDesc('created_at')->first();

                                      return (object) [
                                          'id' => $konsultasi->id, // Ini adalah ID Konsultasi (parent)
                                          'detail_konsultasi_id' => $latestDetail->id ?? null, // Ini adalah ID dari KonsultasiDetail yang terbaru
                                          'tanggal_konsultasi' => $konsultasi->created_at,
                                          'metode_konsultasi' => $konsultasi->jenisKonsultasi->nama_jenis ?? 'Tidak Diketahui',
                                          'topik_konsultasi' => $latestDetail->topik->nama_topik ?? 'Topik Tidak Diketahui',
                                          'kode_konsultasi' => $latestDetail->kode_konsultasi ?? 'N/A',
                                          'status' => $latestDetail->status->nama_status ?? 'Status Tidak Diketahui',
                                          'detail_summary' => $latestDetail->catatan_konsultasi ?? 'Tidak ada catatan ringkasan detail.', // Mengubah nama agar jelas ini ringkasan
                                      ];
                                  });

        $perlingHistory = $user->dokumenPersetujuan() // Assuming a 'dokumenPersetujuan' relationship on the User model
                               ->with(['jenisPerling', 'progresDokumen.status'])
                               ->latest() // Equivalent to orderBy('created_at', 'desc')
                               ->get()
                               ->map(function ($perling) {
                                   $latestProgres = $perling->progresDokumen->sortByDesc('created_at')->first();

                                   return (object) [
                                       'id' => $perling->id,
                                       'tanggal_pengajuan' => $perling->created_at,
                                       'nama_usaha' => $perling->nama_usaha ?? 'Tidak Diketahui',
                                       'kode_perling' => $perling->kode_perling ?? 'N/A',
                                       'status_aplikasi' => $latestProgres->status ?? null,
                                       'jenisPerling' => $perling->jenisPerling ?? null,
                                       'detail' => 'Pengajuan Perling: ' . ($perling->jenisPerling->nama_jenis ?? 'Tidak Diketahui') . ' (' . ($perling->kode_perling ?? 'N/A') . ')',
                                   ];
                               });

        return view('profile.my-profile', compact('user', 'konsultasiHistory', 'perlingHistory'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->fill($request->only(['nama', 'no_telp', 'email']));

        if ($request->boolean('hapus_foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = null;
        } elseif ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('profile_photos', 'public');
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show the form to change user password.
     */
    public function showChangePasswordForm()
    {
        return view('dashboard.pages.profile.gantipassword');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
