@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Diskusi Baru')

@section('content')
    <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Topik Diskusi --}}
        <x-form.select
            name="topik_id"
            :options="$topikKonsultasi->pluck('nama_topik', 'id')"
            label="Topik Diskusi"
            required
        />

        {{-- Judul Diskusi --}}
        <x-form.input
            name="judul_diskusi"
            label="Judul Diskusi"
            value="{{ old('judul_diskusi') }}"
            required
        />

        {{-- Uraian Diskusi --}}
        <x-form.textarea
            name="uraian_diskusi"
            label="Uraian Diskusi"
            value="{{ old('uraian_diskusi') }}"
            required
        />

        {{-- Lampiran --}} 
        <x-form.file-upload
            name="lampiran"
            label="Lampiran (Opsional)"
        />

        {{-- Tombol Aksi --}}
        <div class="flex items-center space-x-4">
            <x-form.button>Simpan Diskusi</x-form.button>
            <x-form.button href="{{ route('forum.index') }}">Batal</x-form.button>
        </div>
    </form>

    {{-- Preview lampiran gambar (jika dibutuhkan) --}}
    <script>
        document.getElementById('lampiran')?.addEventListener('change', function (event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
            }
        });
    </script>
@endsection