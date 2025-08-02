<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login DAN emailnya belum diverifikasi
        if (Auth::check() && is_null(Auth::user()->email_verified_at)) {
            // Jika email belum diverifikasi, logout pengguna
            Auth::logout();

            // Arahkan kembali ke halaman login dengan pesan error
            return redirect()->route('login')->with('error', 'Email Anda belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu.');
        }

        // Lanjutkan request jika email sudah diverifikasi atau pengguna tidak login
        return $next($request);
    }
}
