<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Role; // Pastikan Anda memiliki model Role ini dan mengimpornya

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Daftar nama peran yang diizinkan (e.g., 'Admin', 'Front Office', 'Penelaah')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah pengguna sudah login.
        // Seharusnya middleware 'auth' sudah menangani ini,
        // jadi bagian ini mungkin tidak akan pernah terpicu jika 'auth' ada di depannya.
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        // Pastikan pengguna memiliki relasi 'role' yang dimuat atau akses langsung role_id
        // Asumsi kolom role_id ada di tabel users atau relasi 'role' sudah didefinisikan di User model
        $userRoleId = $user->role_id;

        // Dapatkan ID peran untuk 'Admin' dari database secara dinamis.
        $adminRole = Role::where('nama_role', 'Admin')->first();
        $adminRoleId = $adminRole ? $adminRole->id : null;

        // Jika pengguna adalah Admin, izinkan akses ke mana saja.
        if ($adminRoleId && $userRoleId === $adminRoleId) {
            return $next($request);
        }

        // Kumpulkan ID peran yang diizinkan untuk rute ini berdasarkan nama peran ($roles)
        $allowedRoleIds = [];
        if (!empty($roles)) {
            $dbRoles = Role::whereIn('nama_role', $roles)->pluck('id')->toArray();
            $allowedRoleIds = $dbRoles;
        }

        // Periksa apakah ID peran pengguna saat ini ada dalam daftar ID peran yang diizinkan.
        if (in_array($userRoleId, $allowedRoleIds)) {
            return $next($request);
        }

        // Jika pengguna tidak memiliki peran yang diizinkan dan bukan admin,
        // berikan respon 403 Forbidden.
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}