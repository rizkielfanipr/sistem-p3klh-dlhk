<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PerlingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
// Halaman Utama
// =======================
Route::get('/', fn () => view('home'))->name('home');

// =======================
// Autentikasi (Guest)
// =======================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// =======================
// Logout
// =======================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =======================
// Dashboard (Admin Only)
// =======================
Route::get('/dashboard', fn () => view('dashboard.pages.dashboard'))
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

// =======================
// Profil Pengguna
// =======================
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/profil/ganti-password', [ProfilController::class, 'showChangePasswordForm'])->name('profil.password.form');
    Route::post('/profil/ganti-password', [ProfilController::class, 'changePassword'])->name('profil.password.update');
});

// =======================
// Manajemen Pengguna
// =======================
Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    Route::get('/admin', [UserController::class, 'indexAdmin'])->name('admin');
    Route::get('/fo', [UserController::class, 'indexFrontOffice'])->name('fo');
    Route::get('/pengguna', [UserController::class, 'indexPengguna'])->name('pengguna');
    Route::get('/penelaah', [UserController::class, 'indexPenelaah'])->name('penelaah');

    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// =======================
// Layanan
// =======================
Route::middleware('auth')->prefix('layanan')->name('layanan.')->group(function () {
    Route::get('/', [LayananController::class, 'index'])->name('index');
    Route::post('/store', [LayananController::class, 'store'])->name('store');
    Route::put('/update/{id}', [LayananController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [LayananController::class, 'destroy'])->name('destroy');
    Route::get('/konten/{id}', [LayananController::class, 'getKonten']);
    Route::post('/upload-image', [LayananController::class, 'uploadImage'])->name('uploadImage');
});

// =======================
// Pengumuman
// =======================
Route::middleware('auth')->prefix('pengumuman')->name('pengumuman.')->group(function () {
    Route::get('/', [PengumumanController::class, 'index'])->name('index');
    Route::get('/create', [PengumumanController::class, 'create'])->name('create');
    Route::post('/', [PengumumanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [PengumumanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');
    Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
});

// =======================
// Konsultasi
// =======================
Route::middleware('auth')->prefix('konsultasi')->name('konsultasi.')->group(function () {
    Route::get('/{jenis}', [KonsultasiController::class, 'indexByJenis'])->name('jenis');
    Route::get('/{jenis}/create', [KonsultasiController::class, 'create'])->name('create');
    Route::post('/{jenis}', [KonsultasiController::class, 'store'])->name('store');

    Route::get('/detail/{id}', [KonsultasiController::class, 'showDetail'])->name('detail.show');

    Route::get('/{jenis}/edit/{id}', [KonsultasiController::class, 'edit'])->name('edit');
    Route::put('/{jenis}/update/{id}', [KonsultasiController::class, 'update'])->name('update');
    Route::delete('/{jenis}/{id}', [KonsultasiController::class, 'destroy'])->name('destroy');

    Route::post('/verifikasi/{id}', [KonsultasiController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/tindaklanjut/{id}', [KonsultasiController::class, 'tindaklanjut'])->name('tindaklanjut');
});

// =======================
// Dokumen Persetujuan Lingkungan (Perling)
// =======================
Route::middleware('auth')->prefix('perling')->name('perling.')->group(function () {
    // Index berdasarkan jenis
    Route::get('amdal', [PerlingController::class, 'indexAmdal'])->name('amdal');
    Route::get('uklupl', [PerlingController::class, 'indexUKLUPL'])->name('uklupl');
    Route::get('delh', [PerlingController::class, 'indexDELH'])->name('delh');
    Route::get('dplh', [PerlingController::class, 'indexDPLH'])->name('dplh');

    // Tambah dan simpan
    Route::get('create', [PerlingController::class, 'create'])->name('create');
    Route::post('store', [PerlingController::class, 'store'])->name('store');

    // Edit, update, dan hapus
    Route::get('{id}/edit', [PerlingController::class, 'edit'])->name('edit');
    Route::put('{id}/update', [PerlingController::class, 'update'])->name('update');
    Route::delete('{id}', [PerlingController::class, 'destroy'])->name('destroy');
});
