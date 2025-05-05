@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="relative w-full h-screen overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E]">
    @include('components.pattern')

    <div class="absolute inset-0 bg-black/10 z-10"></div>

    <div class="absolute inset-0 flex items-center justify-center z-20">
        <div class="w-96 max-w-full mx-auto bg-white p-6 rounded-lg shadow-lg">
            <div class="flex justify-start mb-4">
                <img src="{{ asset('logo-dlhk.png') }}" alt="Logo DLHK" class="h-10">
            </div>

            <h2 class="text-xl font-semibold text-left py-4">Daftar Akun</h2>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <!-- Input untuk Nama -->
                @component('components.input', ['name' => 'nama', 'label' => 'Nama', 'type' => 'text']) @endcomponent

                <!-- Input untuk Nomor Telepon -->
                @component('components.input', ['name' => 'no_telp', 'label' => 'Nomor Telepon', 'type' => 'text']) @endcomponent

                <!-- Input untuk Email -->
                @component('components.input', ['name' => 'email', 'label' => 'Email', 'type' => 'email']) @endcomponent

                <!-- Input untuk Password -->
                @component('components.password', ['name' => 'password', 'label' => 'Password']) @endcomponent

                <!-- Input untuk Konfirmasi Password -->
                @component('components.password', ['name' => 'password_confirmation', 'label' => 'Konfirmasi Password']) @endcomponent

                <div class="flex items-center justify-between">
                    <button type="submit" class="w-full py-2 px-4 bg-blue-900 text-white font-semibold rounded-md shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm">Sudah Punya Akun? <a href="{{ route('login') }}" class="text-blue-900">Masuk</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
