<?php

namespace App\Http\Controllers;

use App\Models\ForumDiskusi;
use App\Models\Lampiran;
use App\Models\TopikKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class ForumController extends Controller
{
    public function index()
    {
        $forums = ForumDiskusi::with(['topik', 'user'])->get();
        return view('dashboard.pages.forum.index', compact('forums'));
    }

    public function show($id)
    {
        $forum = ForumDiskusi::with(['balasan.user', 'topik', 'user', 'lampiran'])->findOrFail($id);
        return view('dashboard.pages.forum.show', compact('forum'));
    }

    public function create()
    {
        $topikKonsultasi = TopikKonsultasi::all();
        return view('dashboard.pages.forum.create', compact('topikKonsultasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_diskusi' => 'required|string|max:255',
            'uraian_diskusi' => 'required|string',
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $lampiranId = null;

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $path = $file->store('lampiran_diskusi', 'public');

            $lampiran = Lampiran::create([
                'lampiran' => $path
            ]);

            $lampiranId = $lampiran->id;
        }

        ForumDiskusi::create([
            'judul_diskusi' => $request->judul_diskusi,
            'uraian_diskusi' => $request->uraian_diskusi,
            'topik_id' => $request->topik_id,
            'tanggal_diskusi' => now(),
            'lampiran_id' => $lampiranId,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('forum.index')->with('success', 'Diskusi berhasil dibuat.');
    }

    public function edit($id)
    {
        $forum = ForumDiskusi::with('lampiran')->findOrFail($id);
        $topikKonsultasi = TopikKonsultasi::all();
        return view('dashboard.pages.forum.edit', compact('forum', 'topikKonsultasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_diskusi' => 'required|string|max:255',
            'uraian_diskusi' => 'required|string',
            'topik_id' => 'required|exists:topik_konsultasi,id',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $forum = ForumDiskusi::findOrFail($id);
        $lampiranId = $forum->lampiran_id; // Inisialisasi dengan ID lampiran yang sudah ada

        // Update file jika ada yang baru
        if ($request->hasFile('lampiran')) {
            // Hapus lampiran lama jika ada
            if ($forum->lampiran) {
                Storage::disk('public')->delete($forum->lampiran->lampiran);
                Lampiran::destroy($forum->lampiran_id);
                $lampiranId = null; // Reset lampiran_id karena lampiran lama dihapus
            }

            $file = $request->file('lampiran');
            $path = $file->store('lampiran_diskusi', 'public');

            $lampiran = Lampiran::create([
                'lampiran' => $path
            ]);

            $lampiranId = $lampiran->id;
        }

        $forum->update([
            'judul_diskusi' => $request->judul_diskusi,
            'uraian_diskusi' => $request->uraian_diskusi,
            'topik_id' => $request->topik_id,
            'lampiran_id' => $lampiranId,
        ]);

        return redirect()->route('forum.index')->with('success', 'Diskusi berhasil diperbarui.');
    }

    public function destroy($id)
{
    $forum = ForumDiskusi::findOrFail($id);

    // Hapus lampiran dari storage dan database jika ada
    if ($forum->lampiran_id) {
        $lampiran = $forum->lampiran;
        if ($lampiran && \Storage::disk('public')->exists($lampiran->lampiran)) {
            \Storage::disk('public')->delete($lampiran->lampiran);
        }
        $lampiran->delete();
    }

    $forum->delete();

    return redirect()->route('forum.index')->with('success', 'Diskusi berhasil dihapus.');
}
}