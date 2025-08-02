<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SettingsController extends Controller
{

    public function showSettingsForm()
    {
        $setting = Setting::firstOrCreate([]);
        return view('dashboard.pages.settings', compact('setting'));
    }

    public function saveSettings(Request $request)
    {
        $rules = [
            'maks_konsultasi_daring_harian' => 'required|integer|min:0',
            'maks_konsultasi_luring_harian' => 'required|integer|min:0',
            'tanggal_tidak_tersedia_konsultasi_luring' => 'nullable|array',
            'tanggal_tidak_tersedia_konsultasi_luring.*.date' => 'required_with:tanggal_tidak_tersedia_konsultasi_luring|date_format:Y-m-d',
            'tanggal_tidak_tersedia_konsultasi_luring.*.reason' => 'required_with:tanggal_tidak_tersedia_konsultasi_luring|string|max:255',
            'tanggal_tidak_tersedia_konsultasi_daring' => 'nullable|array',
            'tanggal_tidak_tersedia_konsultasi_daring.*.date' => 'required_with:tanggal_tidak_tersedia_konsultasi_daring|date_format:Y-m-d',
            'tanggal_tidak_tersedia_konsultasi_daring.*.reason' => 'required_with:tanggal_tidak_tersedia_konsultasi_daring|string|max:255',
            'maks_perling_harian' => 'required|integer|min:0', // New rule for perling daily limit
            'tanggal_tidak_tersedia_perling' => 'nullable|array', // New rule for perling unavailable dates
            'tanggal_tidak_tersedia_perling.*.date' => 'required_with:tanggal_tidak_tersedia_perling|date_format:Y-m-d',
            'tanggal_tidak_tersedia_perling.*.reason' => 'required_with:tanggal_tidak_tersedia_perling|string|max:255',
        ];

        $messages = [
            'maks_konsultasi_daring_harian.required' => 'Batas maksimum konsultasi daring harian wajib diisi.',
            'maks_konsultasi_daring_harian.integer' => 'Batas maksimum konsultasi daring harian harus berupa angka.',
            'maks_konsultasi_daring_harian.min' => 'Batas maksimum konsultasi daring harian tidak boleh kurang dari 0.',
            'maks_konsultasi_luring_harian.required' => 'Batas maksimum konsultasi luring harian wajib diisi.',
            'maks_konsultasi_luring_harian.integer' => 'Batas maksimum konsultasi luring harian harus berupa angka.',
            'maks_konsultasi_luring_harian.min' => 'Batas maksimum konsultasi luring harian tidak boleh kurang dari 0.',
            'tanggal_tidak_tersedia_konsultasi_luring.*.date.required_with' => 'Tanggal tidak tersedia (Luring) wajib diisi.',
            'tanggal_tidak_tersedia_konsultasi_luring.*.date.date_format' => 'Format tanggal tidak tersedia (Luring) harus YYYY-MM-DD.',
            'tanggal_tidak_tersedia_konsultasi_luring.*.reason.required_with' => 'Alasan tanggal tidak tersedia (Luring) wajib diisi.',
            'tanggal_tidak_tersedia_konsultasi_luring.*.reason.string' => 'Alasan tanggal tidak tersedia (Luring) harus berupa teks.',
            'tanggal_tidak_tersedia_konsultasi_luring.*.reason.max' => 'Alasan tanggal tidak tersedia (Luring) maksimal 255 karakter.',
            'tanggal_tidak_tersedia_konsultasi_daring.*.date.required_with' => 'Tanggal tidak tersedia (Daring) wajib diisi.',
            'tanggal_tidak_tersedia_konsultasi_daring.*.date.date_format' => 'Format tanggal tidak tersedia (Daring) harus YYYY-MM-DD.',
            'tanggal_tidak_tersedia_konsultasi_daring.*.reason.required_with' => 'Alasan tanggal tidak tersedia (Daring) wajib diisi.',
            'tanggal_tidak_tersedia_konsultasi_daring.*.reason.string' => 'Alasan tanggal tidak tersedia (Daring) harus berupa teks.',
            'tanggal_tidak_tersedia_konsultasi_daring.*.reason.max' => 'Alasan tanggal tidak tersedia (Daring) maksimal 255 karakter.',
            'maks_perling_harian.required' => 'Batas maksimum pengajuan perling harian wajib diisi.', // New message
            'maks_perling_harian.integer' => 'Batas maksimum pengajuan perling harian harus berupa angka.', // New message
            'maks_perling_harian.min' => 'Batas maksimum pengajuan perling harian tidak boleh kurang dari 0.', // New message
            'tanggal_tidak_tersedia_perling.*.date.required_with' => 'Tanggal tidak tersedia (Perling) wajib diisi.', // New message
            'tanggal_tidak_tersedia_perling.*.date.date_format' => 'Format tanggal tidak tersedia (Perling) harus YYYY-MM-DD.', // New message
            'tanggal_tidak_tersedia_perling.*.reason.required_with' => 'Alasan tanggal tidak tersedia (Perling) wajib diisi.', // New message
            'tanggal_tidak_tersedia_perling.*.reason.string' => 'Alasan tanggal tidak tersedia (Perling) harus berupa teks.', // New message
            'tanggal_tidak_tersedia_perling.*.reason.max' => 'Alasan tanggal tidak tersedia (Perling) maksimal 255 karakter.', // New message
        ];

        $request->validate($rules, $messages);

        try {
            $setting = Setting::firstOrCreate([]);
            $setting->maks_konsultasi_daring_harian = $request->input('maks_konsultasi_daring_harian');
            $setting->maks_konsultasi_luring_harian = $request->input('maks_konsultasi_luring_harian');

            $tanggalTidakTersediaLuring = collect($request->input('tanggal_tidak_tersedia_konsultasi_luring'))
                ->filter(function ($item) {
                    return !empty($item['date']) && !empty($item['reason']);
                })
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })
                ->values()
                ->toArray();

            $tanggalTidakTersediaDaring = collect($request->input('tanggal_tidak_tersedia_konsultasi_daring'))
                ->filter(function ($item) {
                    return !empty($item['date']) && !empty($item['reason']);
                })
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })
                ->values()
                ->toArray();

            // New: Process tanggal_tidak_tersedia_perling
            $tanggalTidakTersediaPerling = collect($request->input('tanggal_tidak_tersedia_perling'))
                ->filter(function ($item) {
                    return !empty($item['date']) && !empty($item['reason']);
                })
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['date'])->format('Y-m-d'),
                        'reason' => $item['reason']
                    ];
                })
                ->values()
                ->toArray();

            $setting->tanggal_tidak_tersedia_konsultasi_luring = $tanggalTidakTersediaLuring;
            $setting->tanggal_tidak_tersedia_konsultasi_daring = $tanggalTidakTersediaDaring;
            $setting->maks_perling_harian = $request->input('maks_perling_harian'); // New: Save perling daily limit
            $setting->tanggal_tidak_tersedia_perling = $tanggalTidakTersediaPerling; // New: Save perling unavailable dates
            $setting->save();

            return back()->with('success', 'Pengaturan konsultasi berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Failed to save consultation settings: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan. Mohon coba lagi. Detail: ' . $e->getMessage());
        }
    }
}
