@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="relative w-full h-screen overflow-hidden bg-gradient-to-r from-[#011F4B] to-[#03346E]">
    @include('components.pattern')

    <div class="absolute inset-0 bg-black/10 z-10"></div>

    <div class="absolute inset-0 flex items-center justify-center z-20">
        <div class="w-96 max-w-full mx-auto bg-white p-6 rounded-lg shadow-lg">
            <div class="flex justify-start mb-4">
                <img src="{{ asset('logo-dlhk.png') }}" alt="Logo DLHK" class="h-10">
            </div>

            <h2 class="text-xl font-semibold text-left py-4">Verifikasi Email Anda</h2>

            {{-- Include Toast Component for success/error messages --}}
            @include('components.toast')

            <p class="text-gray-600 text-center mb-4">
                Kami telah mengirimkan kode verifikasi 6 digit ke email Anda (<strong>{{ Auth::user()->email ?? 'email@example.com' }}</strong>).
                Silakan masukkan kode tersebut di bawah ini untuk memverifikasi akun Anda.
            </p>

            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf
                <div class="mb-4">
                    <label for="verification_code" class="block text-gray-700 text-sm font-medium mb-2">Kode Verifikasi</label>
                    <input type="text" id="verification_code" name="verification_code" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('verification_code') border-red-500 @enderror" required autofocus maxlength="6" pattern="\d{6}" title="Kode harus 6 digit angka">
                    @error('verification_code')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-200 w-full">
                        Verifikasi Email
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">Tidak menerima kode?</p>
                <form method="POST" action="{{ route('verification.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none">
                        Kirim Ulang Kode
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection