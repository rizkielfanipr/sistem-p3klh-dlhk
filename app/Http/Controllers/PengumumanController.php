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
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
        ]);

        $data = $request->only(['judul', 'konten']);
        $data['tanggal'] = Carbon::now('Asia/Jakarta');
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
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:2048',
        ]);

        $pengumuman = Pengumuman::with('lampiran')->findOrFail($id);
        $data = $request->only(['judul', 'konten']);

        try {
            DB::beginTransaction();

            if ($request->hasFile('lampiran')) {
                // Simpan lampiran baru terlebih dahulu
                $path = $request->file('lampiran')->store('lampiran_pengumuman', 'public');
                $lampiranBaru = Lampiran::create([
                    'lampiran' => $path,
                ]);

                // Update foreign key di model Pengumuman terlebih dahulu
                $data['lampiran_id'] = $lampiranBaru->id;

                // Hapus lampiran lama jika ada setelah foreign key diperbarui
                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                    $pengumuman->lampiran->delete();
                }
            } elseif ($request->input('remove_lampiran')) {
                // Handle case where user wants to remove existing attachment without uploading a new one
                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                    $pengumuman->lampiran->delete();
                    $data['lampiran_id'] = null; // Set lampiran_id to null
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

            // Hapus file dari storage jika ada
            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran->lampiran);
                $pengumuman->lampiran->delete(); // Hapus record lampiran dari database
            }

            $pengumuman->delete(); // Hapus pengumuman itu sendiri
            DB::commit();

            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pengumuman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}