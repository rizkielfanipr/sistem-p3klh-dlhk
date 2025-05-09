@extends('dashboard.layouts.adminlayout')

@section('title', 'Detail Diskusi Forum')

@section('content')
    <div class="bg-gray-100 border border-gray-300 p-6 rounded-lg">
        <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
            <div class="flex items-start mb-4">
                <img src="{{ asset('storage/' . ($forum->user->foto ?? 'images/default-profile.png')) }}" alt="Foto Profil" class="w-10 h-10 rounded-full mr-3">
                <div class="flex flex-col">
                    <div class="font-semibold text-gray-800 text-sm">{{ $forum->user->nama }}</div>
                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($forum->created_at)->format('d-m-Y H:i') }}</div>
                </div>
            </div>
            <h2 class="text-md font-semibold mb-2 text-gray-800">{{ $forum->judul_diskusi }}</h2>

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

            <p class="text-xs text-gray-700 mb-4 leading-relaxed">{{ $forum->uraian_diskusi }}</p>

            @if ($forum->lampiran)
                @php $extension = pathinfo($forum->lampiran->lampiran, PATHINFO_EXTENSION); @endphp
                <div class="mt-4">
                    <span class="text-xs font-semibold text-gray-700">Lampiran:</span>
                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ asset('storage/' . $forum->lampiran->lampiran) }}" alt="Lampiran" class="mt-2 max-w-xs border rounded">
                    @else
                        <a href="{{ asset('storage/' . $forum->lampiran->lampiran) }}" class="text-blue-600 underline mt-2 inline-block" target="_blank">
                            Lihat Lampiran ({{ strtoupper($extension) }})
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Balasan Konsultasi</h3>

            @forelse ($forum->balasan as $balasan)
                <div class="bg-white border border-gray-300 rounded-lg p-4 mb-4 relative">
                    <div class="absolute top-4 right-4">
                        @if(Auth::id() === $balasan->user_id)
                            <form action="{{ route('balasan.delete', $balasan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus balasan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded text-sm">
                                    <i class="fas fa-trash text-xs mr-1"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="flex items-start mb-4">
                        <img src="{{ asset('storage/' . ($balasan->user->foto ?? 'images/default-profile.png')) }}" alt="Foto Profil" class="w-10 h-10 rounded-full mr-3">
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-800 text-sm">{{ $balasan->user->nama }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($balasan->created_at)->format('d-m-Y H:i') }}</div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-700 mb-4">{{ $balasan->balasan_diskusi }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-600">Belum ada balasan untuk diskusi ini.</p>
            @endforelse
        </div>

        <div class="mt-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Tambah Balasan</h4>
            <form action="{{ route('balasan.create') }}" method="POST">
                @csrf
                <input type="hidden" name="forum_diskusi_id" value="{{ $forum->id }}">
                <textarea name="balasan_diskusi" class="w-full border rounded-lg p-4 mb-2 text-sm" rows="4" placeholder="Tulis balasan Anda..." required></textarea>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-xs">Kirim</button>
            </form>
        </div>
    </div>
@endsection