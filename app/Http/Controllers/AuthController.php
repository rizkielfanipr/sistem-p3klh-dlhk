<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menampilkan form register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi kredensial login
        $credentials = $request->only('email', 'password');

        // Cek login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Ambil pengguna yang sedang login
            $user = Auth::user();

            // Pengecekan role dan redirect berdasarkan role
            if ($user->role->nama_role === 'Admin' || $user->role->nama_role === 'Front Office') {
                return redirect()->route('dashboard');  // Redirect ke dashboard untuk Admin dan Front Office
            } else {
                return redirect()->route('home');  // Redirect ke home untuk Pengguna
            }
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses register
    public function register(Request $request)
    {
        // Validasi input form register
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        // Membuat user baru
        $user = User::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => \App\Models\Role::where('nama_role', 'Pengguna')->value('id'), // Ambil ID role 'Pengguna'
        ]);

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Redirect ke halaman berdasarkan role
        return redirect()->route('home');  // Pengguna akan diarahkan ke halaman home setelah registrasi
    }

    // Proses logout
    public function logout(Request $request)
    {
        // Logout dan invalidate session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect ke halaman login
        return redirect('/login');
    }
}
