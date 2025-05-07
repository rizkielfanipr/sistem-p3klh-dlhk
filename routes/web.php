<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard.pages.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

// Profil
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// Manajemen User (Rute-rute yang diproteksi oleh AdminMiddleware) - Middleware Dihilangkan
Route::middleware(['auth'])->group(function () { // Memastikan hanya user yang login yang bisa akses
    Route::get('/users/admin', [UserController::class, 'indexAdmin'])->name('users.admin');
    Route::get('/users/fo', [UserController::class, 'indexFrontOffice'])->name('users.fo');
    Route::get('/users/pengguna', [UserController::class, 'indexPengguna'])->name('users.pengguna');

    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Contoh rute lain
Route::get('/dashboard/penapisan-dokling', function () {
    return view('dashboard.pages.layanan.dokling');
})->middleware('auth')->name('penapisan-dokling');