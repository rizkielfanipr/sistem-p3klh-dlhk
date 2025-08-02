@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="relative w-full h-screen overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E] flex items-center justify-center">
    {{-- Include the background pattern component --}}
    @include('components.pattern')

    <div class="container text-center bg-white p-12 rounded-xl shadow-lg border border-grey-200 max-w-md w-11/12 relative z-10">
        <div class="text-red-500 text-7xl mb-5">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="text-red-600 text-5xl mb-4 font-bold">403</h1>
        <h2 class="text-2xl mb-6 text-gray-800">Unauthorized</h2>
        <p class="text-lg mb-8 leading-relaxed text-gray-700">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>

        <?php
            // Ensure Auth facade is available
            use Illuminate\Support\Facades\Auth;

            // Default redirection route
            $redirectRoute = 'home';

            // Check if a user is authenticated
            if (Auth::check()) {
                $user = Auth::user();
                $userRoleId = $user->role_id; // Get the user's role ID

                // Define role IDs for clarity
                $adminRoleId = 1;
                $frontOfficeRoleId = 2;
                $penelaahRoleId = 4;
                $penggunaRoleId = 3; // Role ID for 'Pengguna'

                // If the user is Admin, Front Office, or Penelaah, redirect to dashboard
                if (in_array($userRoleId, [$adminRoleId, $frontOfficeRoleId, $penelaahRoleId])) {
                    $redirectRoute = 'dashboard';
                }
                // Otherwise (e.g., 'Pengguna' role or any other unlisted role), default to 'home'
            }
        ?>

        <a href="{{ route($redirectRoute) }}" class="inline-block px-8 py-3 bg-[#03346E] text-white font-semibold rounded-lg shadow-md transition duration-300 ease-in-out hover:bg-[#022A59] hover:scale-105">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection