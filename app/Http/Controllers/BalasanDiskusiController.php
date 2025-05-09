<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BalasanDiskusi;
use Illuminate\Support\Facades\Auth;

class BalasanDiskusiController extends Controller
{
    // Fungsi untuk menambahkan balasan
    public function create(Request $request)
    {
        $validated = $request->validate([
            'balasan_diskusi' => 'required|string',
            'forum_diskusi_id' => 'required|exists:forum_diskusi,id',
        ]);

        $validated['user_id'] = Auth::id();

        $balasan = BalasanDiskusi::create($validated);

        return redirect()->back()->with('success', 'Balasan berhasil ditambahkan');
    }

    // Fungsi untuk menghapus balasan berdasarkan ID
    public function delete($id)
    {
        $balasan = BalasanDiskusi::findOrFail($id);
        $forumId = $balasan->forum_diskusi_id;
        $balasan->delete();

    return redirect()->route('forum.show', ['forum' => $forumId])->with('success', 'Balasan berhasil dihapus.');
    }


    // Fungsi untuk mengambil balasan berdasarkan forum_diskusi_id
    public function getByForumDiskusiId($forum_diskusi_id)
    {
        $balasan = BalasanDiskusi::where('forum_diskusi_id', $forum_diskusi_id)->get();

        if ($balasan->isEmpty()) {
            return response()->json(['message' => 'Tidak ada balasan untuk forum ini'], 404);
        }

        return response()->json(['message' => 'Data balasan berhasil diambil', 'data' => $balasan]);
    }
}
