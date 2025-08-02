<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('lampiran', 'user')->latest()->get();
        return view('dashboard.pages.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('dashboard.pages.pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'skala_besaran' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'pemrakarsa' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'dampak' => 'required|string',
            'judul' => 'required|string|max:255',
            'jenis_perling' => 'required|string|max:255',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048', // Added validation for image
        ]);

        $data = $request->only([
            'nama_usaha', 'bidang_usaha', 'skala_besaran', 'lokasi', 'pemrakarsa',
            'penanggung_jawab', 'deskripsi', 'dampak',
            'judul', 'jenis_perling'
        ]);
        $data['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $path = $file->store('lampiran_pengumuman', 'public');

                $lampiran = Lampiran::create([
                    'lampiran' => $path
                ]);

                $data['lampiran_id'] = $lampiran->id;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('pengumuman_images', 'public');
                $data['image'] = $imagePath;
            }

            Pengumuman::create($data);
            DB::commit();

            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pengumuman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $pengumuman = Pengumuman::with('lampiran')->findOrFail($id);
        return view('dashboard.pages.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'skala_besaran' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'pemrakarsa' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'dampak' => 'required|string',
            'judul' => 'required|string|max:255',
            'jenis_perling' => 'required|string|max:255',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048', // Added validation for image
        ]);

        $pengumuman = Pengumuman::with('lampiran')->findOrFail($id);

        $data = $request->only([
            'nama_usaha', 'bidang_usaha', 'skala_besaran', 'lokasi', 'pemrakarsa',
            'penanggung_jawab', 'deskripsi', 'dampak',
            'judul', 'jenis_perling'
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('lampiran')) {
                $path = $request->file('lampiran')->store('lampiran_pengumuman', 'public');
                $lampiranBaru = Lampiran::create(['lampiran' => $path]);
                $data['lampiran_id'] = $lampiranBaru->id;

                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                    $pengumuman->lampiran->delete();
                }
            } elseif ($request->input('remove_lampiran')) {
                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                    $pengumuman->lampiran->delete();
                    $data['lampiran_id'] = null;
                }
            }

            // Handle image update
            if ($request->hasFile('image')) {
                if ($pengumuman->image) {
                    Storage::disk('public')->delete($pengumuman->image);
                }
                $imagePath = $request->file('image')->store('pengumuman_images', 'public');
                $data['image'] = $imagePath;
            } elseif ($request->input('remove_image')) { // Add a hidden field 'remove_image' in the form if you want to allow removal without new upload
                if ($pengumuman->image) {
                    Storage::disk('public')->delete($pengumuman->image);
                    $data['image'] = null;
                }
            }

            $pengumuman->update($data);
            DB::commit();

            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pengumuman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::with('lampiran')->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                $pengumuman->lampiran->delete();
            }

            // Delete associated image
            if ($pengumuman->image) {
                Storage::disk('public')->delete($pengumuman->image);
            }

            $pengumuman->delete();
            DB::commit();

            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pengumuman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::with(['user', 'lampiran', 'tanggapan'])->findOrFail($id);

        return view('dashboard.pages.pengumuman.detail', [
            'pengumuman' => $pengumuman,
            'title' => 'Detail Pengumuman',
        ]);
    }

    public function showDetailUser($id)
    {
        $pengumuman = Pengumuman::with(['user', 'lampiran', 'tanggapan'])->findOrFail($id);

        return view('pengumuman.detail', [
            'pengumuman' => $pengumuman,
            'title' => 'Detail Pengumuman',
        ]);
    }
}