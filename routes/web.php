<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\TanggapanPengumumanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatistikController;

// Impor semua controller Perling
use App\Http\Controllers\Perling\IndexController as PerlingIndexController;
use App\Http\Controllers\Perling\SubmissionController as PerlingSubmissionController;
use App\Http\Controllers\Perling\ManagementController as PerlingManagementController;
use App\Http\Controllers\Perling\StatusController as PerlingStatusController;
use App\Http\Controllers\Perling\UserDetailController as PerlingUserDetailController;
use App\Http\Controllers\Perling\TrackController as PerlingTrackController;

// Impor semua controller Konsultasi yang baru
use App\Http\Controllers\Konsultasi\IndexController as KonsultasiIndexController;
use App\Http\Controllers\Konsultasi\SubmissionController as KonsultasiSubmissionController;
use App\Http\Controllers\Konsultasi\ManagementController as KonsultasiManagementController;
use App\Http\Controllers\Konsultasi\DetailController as KonsultasiDetailController;
use App\Http\Controllers\Konsultasi\ActionController as KonsultasiActionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dan semuanya akan ditetapkan ke grup
| middleware "web". Buat sesuatu yang hebat!
|
*/

// =======================================================================
// Halaman Utama
// =======================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// =======================================================================
// Autentikasi (Hanya untuk Tamu)
// =======================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// =======================================================================
// Logout
// =======================================================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =======================================================================
// Rute Verifikasi Email (Ini harus bisa diakses bahkan jika belum verified)
// =======================================================================
Route::middleware('auth')->group(function () { // Hanya pastikan pengguna login untuk mengakses ini
    Route::get('/email/verify', [AuthController::class, 'showVerificationForm'])->name('verification.notice');
    Route::post('/email/verify', [AuthController::class, 'verifyCode'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationCode'])->name('verification.resend');
});


// =======================================================================
// Rute yang Membutuhkan Autentikasi DAN Verifikasi Email
// Middleware 'verified' akan mengarahkan ke 'verification.notice' jika belum diverifikasi.
// =======================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Rute Pengaturan - Admin Only
    Route::middleware('role:Admin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'showSettingsForm'])->name('settings.form');
        Route::post('/settings', [SettingsController::class, 'saveSettings'])->name('settings.save');
    });

    // Dashboard - Front Office, Penelaah, Admin
    Route::middleware('role:Front Office,Penelaah,Admin')->group(function () {
        Route::get('/dashboard', [StatistikController::class, 'index'])->name('dashboard');
    });

    // ===================================================================
    // Profil Pengguna - Admin, Front Office, Penelaah, Pengguna
    // Diatur untuk diakses oleh semua role yang disebutkan
    // ===================================================================
    Route::middleware('role:Admin,Front Office,Penelaah,Pengguna')->group(function () {
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');

        Route::prefix('profil-saya')->name('profil.')->group(function () {
            Route::get('/', [ProfilController::class, 'showUserProfile'])->name('my_profile');
            Route::post('/update', [ProfilController::class, 'update'])->name('update');

            Route::get('/ganti-password', [ProfilController::class, 'showChangePasswordForm'])->name('password.form');
            Route::post('/ganti-password', [ProfilController::class, 'changePassword'])->name('password.update');

            // Rute AJAX baru untuk mengambil detail konsultasi
            Route::get('/konsultasi-detail/{konsultasiDetail}', [ProfilController::class, 'getKonsultasiDetailJson'])->name('konsultasi.detail.json');
        });
    });


    // ===================================================================
    // Manajemen Pengguna - Admin Only
    // ===================================================================
    Route::middleware('role:Admin')->prefix('users')->name('users.')->group(function () {
        // Indeks berdasarkan role
        Route::get('/admin', [UserController::class, 'indexAdmin'])->name('admin');
        Route::get('/fo', [UserController::class, 'indexFrontOffice'])->name('fo');
        Route::get('/pengguna', [UserController::class, 'indexPengguna'])->name('pengguna');
        Route::get('/penelaah', [UserController::class, 'indexPenelaah'])->name('penelaah');

        // CRUD Umum
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // ===================================================================
    // Layanan - Admin Only
    // ===================================================================
    Route::middleware('role:Admin')->prefix('layanan')->name('layanan.')->group(function () {
        Route::get('/', [LayananController::class, 'index'])->name('index');
        Route::post('/store', [LayananController::class, 'store'])->name('store');
        Route::put('/update/{id}', [LayananController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [LayananController::class, 'destroy'])->name('destroy');
        Route::get('/konten/{id}', [LayananController::class, 'getKonten']);
        Route::post('/upload-image', [LayananController::class, 'uploadImage'])->name('uploadImage');
    });

    // ===================================================================
    // Pengumuman (Sisi Admin) - Admin Only
    // ===================================================================
    Route::middleware('role:Admin')->prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/', [PengumumanController::class, 'index'])->name('index');
        Route::get('/create', [PengumumanController::class, 'create'])->name('create');
        Route::post('/', [PengumumanController::class, 'store'])->name('store');
        Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
        Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
        Route::delete('/{pengumuman}', [PengumumanController::class, 'destroy'])->name('destroy');
        Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
    });

    // Informasi (Sisi Admin) - Admin Only
    // ===================================================================
    Route::middleware('role:Admin')->prefix('informasi')->name('informasi.')->group(function () {
        Route::get('/', [InformasiController::class, 'index'])->name('index');
        Route::get('/create', [InformasiController::class, 'create'])->name('create');
        Route::post('/', [InformasiController::class, 'store'])->name('store');
        Route::get('/{informasi}/edit', [InformasiController::class, 'edit'])->name('edit');
        Route::put('/{informasi}', [InformasiController::class, 'update'])->name('update');
        Route::delete('/{informasi}', [InformasiController::class, 'destroy'])->name('destroy');
        Route::get('/{informasi}', [InformasiController::class, 'show'])->name('show');
    });

    // ===================================================================
    // Dokumen Persetujuan Lingkungan (Perling) - Sisi Admin - Penelaah, Admin
    // ===================================================================
    Route::middleware('role:Penelaah,Admin')->prefix('perling')->name('perling.')->group(function () {
        // Rute indeks untuk berbagai jenis Perling
        Route::get('amdal', [PerlingIndexController::class, 'indexAmdal'])->name('amdal');
        Route::get('uklupl', [PerlingIndexController::class, 'indexUKLUPL'])->name('uklupl');
        Route::get('delh', [PerlingIndexController::class, 'indexDELH'])->name('delh');
        Route::get('dplh', [PerlingIndexController::class, 'indexDPLH'])->name('dplh');

        // Rute buat/simpan dokumen
        Route::get('create', [PerlingSubmissionController::class, 'create'])->name('create');
        Route::post('store', [PerlingSubmissionController::class, 'store'])->name('store');

        // Rute tampil, edit, perbarui, hapus dokumen
        Route::get('{dokumen_persetujuan}', [PerlingManagementController::class, 'show'])->name('detail');
        Route::get('{dokumen_persetujuan}/edit', [PerlingManagementController::class, 'edit'])->name('edit');
        Route::put('{dokumen_persetujuan}/update', [PerlingManagementController::class, 'update'])->name('update');
        Route::delete('{dokumen_persetujuan}', [PerlingManagementController::class, 'destroy'])->name('destroy');
        Route::get('{dokumen}/progress-history', [PerlingManagementController::class, 'showProgressHistory'])->name('progress_history');

        // Rute pembaruan status
        Route::post('{dokumen_persetujuan}/update-status', [PerlingStatusController::class, 'updateStatus'])->name('update-status');
    });

    // ===================================================================
    // Konsultasi (Sisi Admin) - Front Office, Penelaah, Admin
    // ===================================================================
    Route::middleware('role:Front Office,Penelaah,Admin')->prefix('konsultasi')->name('konsultasi.')->group(function () {
        // Detail konsultasi
        Route::get('/detail/{konsultasi}', [KonsultasiDetailController::class, 'showDetail'])->name('detail.show');

        // Indeks berdasarkan jenis
        Route::get('/{jenis}', [KonsultasiIndexController::class, 'indexByJenis'])->name('jenis');
        Route::get('/{jenis}/create', [KonsultasiSubmissionController::class, 'create'])->name('create');
        Route::post('/{jenis}', [KonsultasiSubmissionController::class, 'store'])->name('store');
        Route::get('/{jenis}/edit/{konsultasi}', [KonsultasiManagementController::class, 'edit'])->name('edit');
        Route::put('/{jenis}/update/{konsultasi}', [KonsultasiManagementController::class, 'update'])->name('update');
        Route::delete('/{jenis}/{konsultasi}', [KonsultasiManagementController::class, 'destroy'])->name('destroy');

        // Verifikasi dan tindak lanjut
        Route::post('/verifikasi/{konsultasi}', [KonsultasiActionController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/tindaklanjut/{konsultasi}', [KonsultasiActionController::class, 'tindaklanjut'])->name('tindaklanjut');
    });

    // ===================================================================
    // Tanggapan Pengumuman (Sisi Admin - Hapus) - Admin Only
    // ===================================================================
    Route::middleware('role:Admin')->delete('/tanggapan/{id}', [TanggapanPengumumanController::class, 'destroy'])->name('tanggapan.destroy');

    // ===================================================================
    // Rute Menghadap Pengguna (Untuk Semua Role: Admin, FO, Penelaah, Pengguna)
    // ===================================================================
    Route::middleware('role:Admin,Front Office,Penelaah,Pengguna')->prefix('user')->name('user.')->group(function () {
        // Detail Perling untuk pengguna
        Route::get('perling/{dokumenPersetujuan}', [PerlingUserDetailController::class, 'showUserDetail'])->name('perling.detail');
        // Rute untuk mengunggah revisi oleh pengguna
        Route::post('perling/{dokumenPersetujuan}/upload-revision', [PerlingUserDetailController::class, 'uploadRevision'])->name('perling.upload_revision');
        // Rute untuk menampilkan halaman revisi (jika ada, perlu diimplementasikan di PerlingUserDetailController)
        // Route::get('perling/{dokumenPersetujuan}/revision', [PerlingUserDetailController::class, 'showRevision'])->name('perling.revision');

        // Detail Konsultasi untuk pengguna
        Route::get('konsultasi/{konsultasiDetail}', [KonsultasiDetailController::class, 'showUserDetail'])->name('konsultasi.detail');
    });

}); // Akhir dari grup middleware ['auth', 'verified']

// =======================================================================
// Rute Publik (Tidak Membutuhkan Autentikasi)
// =======================================================================

// Detail Pengumuman untuk pengguna
Route::get('/user/pengumuman/{id}', [PengumumanController::class, 'showDetailUser'])->name('user.pengumuman.show');

Route::get('/user/informasi/{id}', [InformasiController::class, 'showDetailUser'])->name('user.informasi.show');

// Tanggapan Pengumuman (Simpan - Karena bisa diakses publik)
Route::post('/pengumuman/{id}/tanggapan', [TanggapanPengumumanController::class, 'store'])->name('tanggapan.store');

// Detail Layanan (Publik)
Route::get('/layanan/{slug}', [LayananController::class, 'showByCategorySlug'])->name('layanan.detail');

// Rute untuk melacak permohonan (publik tanpa login)
Route::get('/track-permohonan', [PerlingTrackController::class, 'track'])->name('perling.track');

// Rute pengajuan dokumen Perling oleh pengguna
Route::get('ajukan-perling', [PerlingSubmissionController::class, 'createForUser'])->name('perling.ajukan');
Route::post('ajukan-perling', [PerlingSubmissionController::class, 'storeForUser'])->name('perling.storeForUser');
Route::get('perling-success', [PerlingSubmissionController::class, 'successForUser'])->name('perling.success');

// Rute pengajuan dokumen Konsultasi oleh pengguna
Route::get('ajukan-konsultasi', [KonsultasiSubmissionController::class, 'createForUser'])->name('konsultasi.ajukan');
Route::post('ajukan-konsultasi', [KonsultasiSubmissionController::class, 'storeForUser'])->name('konsultasi.storeForUser');
Route::get('konsultasi-success', [KonsultasiSubmissionController::class, 'successForUser'])->name('konsultasi.successForUser');

// =======================================================================
// Rute Halaman Publik (PageController)
// =======================================================================
Route::prefix('beranda')->group(function () {
    Route::get('/layanan', [PageController::class, 'layanan'])->name('beranda.layanan');
    Route::get('/informasi', [PageController::class, 'ShowAllInformasi'])->name('beranda.informasi');
    Route::get('/pengumuman', [PageController::class, 'ShowAllPengumuman'])->name('beranda.pengumuman');
    Route::get('/kontak', [PageController::class, 'kontak'])->name('beranda.kontak');
});

// =======================================================================
// Rute Unauthorized (403)
// =======================================================================
Route::get('/unauthorized', function () {
    return view('errors.403');
})->name('unauthorized');