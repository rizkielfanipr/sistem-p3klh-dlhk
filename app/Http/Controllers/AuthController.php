<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Make sure Role model is imported
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // This check should only apply to 'Pengguna' if that's the only role needing email verification before full access
            if ($user->role_id === 3 && $user->email_verified_at === null) {
                return redirect()->route('verification.notice')->with('info', 'Silakan verifikasi email Anda untuk melanjutkan.');
            }

            // Check user's role name for redirection
            if ($user->role->nama_role === 'Admin' ||
                $user->role->nama_role === 'Front Office' ||
                $user->role->nama_role === 'Penelaah') { // <-- ADD THIS CONDITION FOR PENELAAH
                return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $user->nama . '!');
            } elseif ($user->role->nama_role === 'Pengguna') {
                return redirect()->route('home')->with('success', 'Selamat datang kembali!');
            } else {
                // If a user logs in but their role is not explicitly handled for redirection
                // (e.g., a new role not yet configured), they will fall here.
                Auth::logout();
                return redirect()->route('login')->with('error', 'Akun Anda tidak memiliki akses yang valid.');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah. Silakan coba lagi.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $penggunaRoleId = Role::where('nama_role', 'Pengguna')->value('id');
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $penggunaRoleId,
            'email_verified_at' => null,
            'verification_code' => $verificationCode,
        ]);

        Auth::login($user);

        if ($user->role_id === $penggunaRoleId) {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->nama));
            return redirect()->route('verification.notice')->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk kode verifikasi.');
        }

        return redirect()->route('home')->with('success', 'Akun Anda berhasil didaftarkan!');
    }

    public function showVerificationForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->email_verified_at !== null) {
            // Check if 'Pengguna' role, if verified, redirect to home.
            // Other roles might have different post-verification landing pages.
            if ($user->role_id === 3) { // Assuming 3 is Pengguna role_id
                return redirect()->route('home')->with('success', 'Email Anda sudah diverifikasi.');
            } else {
                return redirect()->route('dashboard')->with('success', 'Email Anda sudah diverifikasi.');
            }
        }

        if ($user->role_id !== 3) {
            // For Admin, FO, Penelaah, if they somehow land here, redirect them to dashboard.
            return redirect()->route('dashboard')->with('info', 'Verifikasi email tidak diperlukan untuk peran Anda.');
        }

        return view('auth.verify');
    }

    public function resendVerificationCode(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role_id !== 3) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('home')->with('info', 'Email Anda sudah diverifikasi.');
        }

        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->verification_code = $verificationCode;
        $user->save();

        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->nama));

        return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ($user->verification_code === $request->verification_code) {
            $user->email_verified_at = Carbon::now();
            $user->verification_code = null;
            $user->save();

            // Redirect based on role after successful verification
            if ($user->role_id === 3) { // Pengguna
                return redirect()->route('home')->with('success', 'Email Anda berhasil diverifikasi!');
            } else { // Admin, Front Office, Penelaah (who might have been redirected here for some reason)
                return redirect()->route('dashboard')->with('success', 'Email Anda berhasil diverifikasi!');
            }
        }

        return back()->withErrors([
            'verification_code' => 'Kode verifikasi salah. Silakan coba lagi.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}