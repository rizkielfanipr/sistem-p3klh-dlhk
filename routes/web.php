<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\BalasanDiskusiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan semua rute untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam sebuah grup yang
| menggunakan middleware 'web'. Nikmati membangun aplikasi Anda!
|
*/

// Rute Halaman Utama (Home)
Route::get('/', function () {
    return view('home');
})->name('home');

// Grup Rute Autentikasi
Route::group(['middleware' => ['guest']], function () {
    // Menampilkan formulir login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Memproses pengiriman formulir login
    Route::post('/login', [AuthController::class, 'login']);
    // Menampilkan formulir registrasi
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    // Memproses pengiriman formulir registrasi
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Logout (Membutuhkan autentikasi)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Dashboard (Membutuhkan autentikasi dan peran admin)
Route::get('/dashboard', function () {
    return view('dashboard.pages.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

// Grup Rute Profil (Membutuhkan autentikasi)
Route::middleware('auth')->group(function () {
    // Menampilkan halaman profil pengguna yang sedang login
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    // Memproses pembaruan informasi profil pengguna
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// Grup Rute Manajemen Pengguna (Membutuhkan autentikasi)
Route::middleware(['auth'])->group(function () {
    // Menampilkan daftar pengguna dengan peran admin
    Route::get('/users/admin', [UserController::class, 'indexAdmin'])->name('users.admin');
    // Menampilkan daftar pengguna dengan peran front office
    Route::get('/users/fo', [UserController::class, 'indexFrontOffice'])->name('users.fo');
    // Menampilkan daftar pengguna dengan peran pengguna biasa
    Route::get('/users/pengguna', [UserController::class, 'indexPengguna'])->name('users.pengguna');

    // Menampilkan formulir untuk membuat pengguna baru
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    // Menyimpan data pengguna baru ke database
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Menampilkan formulir untuk mengedit informasi pengguna tertentu
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Memperbarui informasi pengguna tertentu di database
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    // Menghapus pengguna tertentu dari database
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Grup Rute Manajemen Layanan (Membutuhkan autentikasi)
Route::middleware(['auth'])->group(function () {
    // Menampilkan daftar semua layanan
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    // Menyimpan layanan baru ke database
    Route::post('/layanan/store', [LayananController::class, 'store'])->name('layanan.store');
    // Memperbarui informasi layanan tertentu di database
    Route::put('/layanan/update/{id}', [LayananController::class, 'update'])->name('layanan.update');
    // Menghapus layanan tertentu dari database
    Route::delete('/layanan/destroy/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    // Mendapatkan konten layanan berdasarkan ID kategori (kemungkinan untuk AJAX)
    Route::get('/layanan/konten/{id}', [LayananController::class, 'getKonten']);
    // Mengunggah gambar melalui Quill editor untuk konten layanan
    Route::post('/layanan/upload-image', [LayananController::class, 'uploadImage'])->name('layanan.uploadImage');
});

// Grup Rute Manajemen Informasi (Membutuhkan autentikasi)
Route::middleware(['auth'])->group(function () {
    // Menampilkan formulir untuk membuat informasi baru
    Route::get('/informasi/create', [InformasiController::class, 'create'])->name('informasi.create');
    // Menyimpan informasi baru ke database
    Route::post('/informasi', [InformasiController::class, 'store'])->name('informasi.store');
    // Menampilkan formulir untuk mengedit informasi tertentu
    Route::get('/informasi/{id}/edit', [InformasiController::class, 'edit'])->name('informasi.edit');
    // Memperbarui informasi tertentu di database
    Route::put('/informasi/{id}', [InformasiController::class, 'update'])->name('informasi.update');
    // Menghapus informasi tertentu dari database
    Route::delete('/informasi/{id}', [InformasiController::class, 'destroy'])->name('informasi.destroy');
    // Menampilkan daftar pengumuman
    Route::get('/informasi/pengumuman', [InformasiController::class, 'pengumuman'])->name('informasi.pengumuman');
    // Menampilkan daftar publikasi
    Route::get('/informasi/publikasi', [InformasiController::class, 'publikasi'])->name('informasi.publikasi');
});

// Grup Rute Forum (Membutuhkan autentikasi dan peran admin)
Route::middleware(['auth', 'admin'])->group(function () {
    // Menampilkan daftar topik forum
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    // Menampilkan formulir untuk membuat topik forum baru
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    // Menyimpan topik forum baru ke database
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    // Menampilkan detail topik forum tertentu
    Route::get('/forum/{forum}', [ForumController::class, 'show'])->name('forum.show');
    // Menampilkan formulir untuk mengedit topik forum tertentu
    Route::get('/forum/{forum}/edit', [ForumController::class, 'edit'])->name('forum.edit');
    // Memperbarui informasi topik forum tertentu di database
    Route::put('/forum/{forum}', [ForumController::class, 'update'])->name('forum.update');
    // Menghapus topik forum tertentu dari database
    Route::delete('/forum/{forum}', [ForumController::class, 'destroy'])->name('forum.destroy');
});

// Grup Rute Balasan Diskusi (Membutuhkan autentikasi)
Route::middleware(['auth'])->group(function () {
    // Menambah balasan diskusi
    Route::post('/balasan/create', [BalasanDiskusiController::class, 'create'])->name('balasan.create');
    // Menghapus balasan diskusi
    Route::delete('/balasan/{id}', [BalasanDiskusiController::class, 'delete'])->name('balasan.delete');
    // Mengambil balasan berdasarkan ID forum diskusi
    Route::get('/balasan/forum/{forum_diskusi_id}', [BalasanDiskusiController::class, 'getByForumDiskusiId'])->name('balasan.getByForum');
});
