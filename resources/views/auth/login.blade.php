@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="relative w-full h-screen overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E]">
    @include('components.pattern')

    <div class="absolute inset-0 bg-black/10 z-10"></div>

    <div class="absolute inset-0 flex items-center justify-center z-20">
        <div class="w-96 max-w-full mx-auto bg-white p-6 rounded-lg shadow-lg">
            <div class="flex justify-start mb-4">
                <img src="{{ asset('logo-dlhk.png') }}" alt="Logo DLHK" class="h-10">
            </div>

            <h2 class="text-xl font-semibold text-left py-4">Masuk Akun</h2>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                @component('components.input', ['name' => 'email', 'label' => 'Email', 'type' => 'email']) @endcomponent

                <!-- Kolom password dengan link lupa password di sebelah kanan -->
                <div class="mb-6 relative">
                    @component('components.password', ['name' => 'password', 'label' => 'Password']) @endcomponent
                    <div class="absolute right-0 top-0 text-xs mb-2 mr-1">
                        <a href="#" class="text-blue-900">Lupa Password?</a>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="w-full py-2 px-4 bg-blue-900 text-white font-semibold rounded-md shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                        Login
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm">Belum Punya Akun? <a href="{{ route('register') }}" class="text-blue-900">Daftar Akun</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
