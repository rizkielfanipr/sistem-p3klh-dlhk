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
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('lampiran', $filename, 'public');

                $lampiran = Lampiran::create([
                    'lampiran' => 'storage/' . $path
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

        $pengumuman = Pengumuman::findOrFail($id);
        $data = $request->only(['judul', 'konten']);

        try {
            DB::beginTransaction();

            if ($request->hasFile('lampiran')) {
                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $pengumuman->lampiran->lampiran));
                    $pengumuman->lampiran->delete();
                }

                $file = $request->file('lampiran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('lampiran', $filename, 'public');

                $lampiran = Lampiran::create([
                    'lampiran' => 'storage/' . $path,
                ]);

                $data['lampiran_id'] = $lampiran->id;
            } else {
                $data['lampiran_id'] = $pengumuman->lampiran_id;
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

        // Simpan referensi lampiran sebelum pengumuman dihapus
        $lampiran = $pengumuman->lampiran;

        // Hapus pengumuman terlebih dahulu
        $pengumuman->delete();

        // Hapus lampiran jika ada
        if ($lampiran) {
            Storage::disk('public')->delete(str_replace('storage/', '', $lampiran->lampiran));
            $lampiran->delete();
        }

        DB::commit();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting pengumuman: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
