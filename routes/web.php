<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\InformasiController;

/*
|---------------------------------------------------------------------------
| Web Routes
|---------------------------------------------------------------------------
|
| Tempat untuk mendefinisikan semua rute aplikasi web. Rute ini
| dimuat oleh RouteServiceProvider dan dikelompokkan dengan middleware 'web'.
|
*/

// Home Route
Route::get('/', function () {
    return view('home');
})->name('home');

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Dashboard (Hanya untuk admin)
Route::get('/dashboard', function () {
    return view('dashboard.pages.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

// Rute Profil (Harus Login)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// Rute Manajemen User (Harus Login)
Route::middleware(['auth'])->group(function () {
    // Menampilkan daftar pengguna berdasarkan peran
    Route::get('/users/admin', [UserController::class, 'indexAdmin'])->name('users.admin');
    Route::get('/users/fo', [UserController::class, 'indexFrontOffice'])->name('users.fo');
    Route::get('/users/pengguna', [UserController::class, 'indexPengguna'])->name('users.pengguna');

    // Operasi CRUD User
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Rute Manajemen Layanan (Harus Login)
Route::middleware(['auth'])->group(function () {
    // Menampilkan semua layanan
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');

    // Menambah layanan
    Route::post('/layanan/store', [LayananController::class, 'store'])->name('layanan.store');

    // Mengedit layanan
    Route::put('/layanan/update/{id}', [LayananController::class, 'update'])->name('layanan.update');

    // Menghapus layanan
    Route::delete('/layanan/destroy/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');

    // Mendapatkan konten layanan berdasarkan kategori
    Route::get('/layanan/konten/{id}', [LayananController::class, 'getKonten']);

    // Upload gambar melalui Quill editor
    Route::post('/layanan/upload-image', [LayananController::class, 'uploadImage'])->name('layanan.uploadImage');
});

// Rute Manajemen Informasi (Harus Login)
Route::middleware(['auth'])->group(function () {
    
    // Menampilkan form untuk membuat informasi baru
    Route::get('/informasi/create', [InformasiController::class, 'create'])->name('informasi.create');

    // Menyimpan informasi baru
    Route::post('/informasi', [InformasiController::class, 'store'])->name('informasi.store');

    // Menampilkan form untuk mengedit informasi
    Route::get('/informasi/{id}/edit', [InformasiController::class, 'edit'])->name('informasi.edit');

    // Mengupdate informasi
    Route::put('/informasi/{id}', [InformasiController::class, 'update'])->name('informasi.update');

    // Menghapus informasi
    Route::delete('/informasi/{id}', [InformasiController::class, 'destroy'])->name('informasi.destroy');

    Route::get('/informasi/pengumuman', [InformasiController::class, 'pengumuman'])->name('informasi.pengumuman');
Route::get('/informasi/publikasi', [InformasiController::class, 'publikasi'])->name('informasi.publikasi');
});
