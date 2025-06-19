<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\PengumumanController;

// Halaman Utama
Route::get('/', fn () => view('home'))->name('home');

// Autentikasi (untuk tamu)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (memerlukan autentikasi)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (admin)
Route::get('/dashboard', fn () => view('dashboard.pages.dashboard'))->middleware(['auth', 'admin'])->name('dashboard');

// Profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// Manajemen pengguna
Route::middleware('auth')->group(function () {
    Route::get('/users/admin', [UserController::class, 'indexAdmin'])->name('users.admin');
    Route::get('/users/fo', [UserController::class, 'indexFrontOffice'])->name('users.fo');
    Route::get('/users/pengguna', [UserController::class, 'indexPengguna'])->name('users.pengguna');
    Route::get('/users/penelaah', [UserController::class, 'indexPenelaah'])->name('users.penelaah'); 
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Layanan
Route::middleware('auth')->group(function () {
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    Route::post('/layanan/store', [LayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/update/{id}', [LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/destroy/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    Route::get('/layanan/konten/{id}', [LayananController::class, 'getKonten']);
    Route::post('/layanan/upload-image', [LayananController::class, 'uploadImage'])->name('layanan.uploadImage');
});

// Pengumuman
Route::middleware('auth')->group(function () {
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/create', [PengumumanController::class, 'create'])->name('pengumuman.create');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
});

// Konsultasi (by jenis)
Route::middleware('auth')->group(function () {
    Route::get('/konsultasi/{jenis}', [KonsultasiController::class, 'indexByJenis'])->name('konsultasi.jenis');
    Route::get('/konsultasi/{jenis}/create', [KonsultasiController::class, 'create'])->name('konsultasi.create');
    Route::post('/konsultasi/{jenis}', [KonsultasiController::class, 'store'])->name('konsultasi.store');
    Route::get('/konsultasi/detail/{id}', [KonsultasiController::class, 'showDetail'])->name('konsultasi.detail.show');
    Route::get('konsultasi/{jenis}/edit/{id}', [KonsultasiController::class, 'edit'])->name('konsultasi.edit');
    Route::put('konsultasi/{jenis}/update/{id}', [KonsultasiController::class, 'update'])->name('konsultasi.update');
    Route::delete('/konsultasi/{jenis}/{id}', [KonsultasiController::class, 'destroy'])->name('konsultasi.destroy');
    Route::post('/konsultasi/verifikasi/{id}', [KonsultasiController::class, 'verifikasi'])->name('konsultasi.verifikasi');
    Route::post('/konsultasi/tindaklanjut/{id}', [KonsultasiController::class, 'tindaklanjut'])->name('konsultasi.tindaklanjut');
});
