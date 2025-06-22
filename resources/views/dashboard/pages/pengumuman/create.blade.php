@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Pengumuman')

@section('content')
    <form 
        action="{{ route('pengumuman.store') }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="space-y-4">
        @csrf

        {{-- Judul --}}
        <x-form.input 
            name="judul" 
            label="Judul Pengumuman" 
            required 
        />

        {{-- Konten --}}
        <x-form.textarea 
            name="konten" 
            label="Isi Pengumuman" 
            required 
        />

        {{-- Lampiran --}}
        <x-form.file-upload 
            name="lampiran" 
            label="File Lampiran" 
        />

        {{-- Tombol Aksi --}}
        <div class="flex items-center space-x-4">
            {{-- Tombol Simpan --}}
            <x-form.button variant="primary">Simpan</x-form.button>

            {{-- Tombol Batal --}}
            <x-form.button variant="secondary" href="{{ route('pengumuman.index') }}">Batal</x-form.button>
        </div>
    </form>

    {{-- Preview gambar (jika diinginkan) --}}
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
