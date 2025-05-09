@extends('dashboard.layouts.adminlayout')

@section('title', 'Forum Diskusi')

@section('content')
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <x-button-add href="{{ route('forum.create') }}">
        Tambah Diskusi Baru
    </x-button-add>

    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($forums as $forum)
                <div class="bg-white border border-gray-300 rounded-lg p-4 flex flex-col justify-between">
                    <div class="flex items-start mb-4">
                        @php
                            $fotoPath = $forum->user->foto
                                ? asset('storage/' . $forum->user->foto)
                                : asset('images/default-profile.png');
                        @endphp
                        <img src="{{ $fotoPath }}" alt="Foto Profil" class="w-10 h-10 rounded-full mr-3">

                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-800 text-sm">{{ $forum->user->nama }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($forum->created_at)->format('d-m-Y H:i') }}</div>
                        </div>
                    </div>

                    <h2 class="text-md font-semibold mb-2 text-gray-800">{{ \Str::limit($forum->judul_diskusi, 25) }}</h2>

                    @php
                        $topikColor = match($forum->topik->nama_topik) {
                            'Penapisan Dokling' => 'bg-blue-200',
                            'Penilaian AMDAL' => 'bg-green-200',
                            'Pemeriksaan UKL UPL' => 'bg-yellow-200',
                            'Registrasi SPPL' => 'bg-teal-200',
                            'Penilaian DELH & DPLH' => 'bg-indigo-200',
                            'AMDALNET' => 'bg-purple-200',
                            'Lain-Lain' => 'bg-pink-200',
                            default => 'bg-gray-200',
                        };
                    @endphp

                    <div class="text-xs text-gray-600 mb-2 {{ $topikColor }} max-w-fit p-2 rounded-md">
                        {{ $forum->topik->nama_topik }}
                    </div>

                    <p class="text-xs text-gray-700 mb-4 leading-relaxed">
                        {{ \Str::limit($forum->uraian_diskusi, 50) }}
                    </p>

                    <div class="flex justify-between items-center mt-auto">
                        <div class="flex space-x-1">
                            <a href="{{ route('forum.edit', $forum->id) }}" class="bg-yellow-500 text-white p-1 w-8 h-8 flex items-center justify-center rounded hover:bg-yellow-600">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('forum.destroy', $forum->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus forum ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white p-1 w-8 h-8 flex items-center justify-center rounded hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            <a href="{{ route('forum.show', $forum->id) }}" class="bg-blue-500 text-white p-1 w-8 h-8 flex items-center justify-center rounded hover:bg-blue-600">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection