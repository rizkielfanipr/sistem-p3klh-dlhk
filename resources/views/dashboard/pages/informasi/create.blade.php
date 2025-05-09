@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Informasi')

@section('content')
    <form 
        action="{{ route('informasi.store') }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="space-y-4">
        @csrf

        {{-- Jenis Informasi --}}
        <x-form.select
            name="jenis_informasi_id"
            :options="$jenisInformasi->pluck('nama_jenis', 'id')"
            label="Jenis Informasi"
            required
        />

        {{-- Judul --}}
        <x-form.input 
            name="judul" 
            label="Judul Informasi" 
            required 
        />

        {{-- Konten --}}
        <x-form.textarea 
            name="konten" 
            label="Isi Informasi" 
            required 
        />

        {{-- Lampiran --}}
        <x-form.file-upload 
            name="lampiran" 
            label="File Lampiran" 
        />

        {{-- Tombol Aksi --}}
        <div class="flex items-center space-x-4">
            <x-form.button>Simpan</x-form.button>
            <x-form.button href="{{ route('informasi.pengumuman') }}">Batal</x-form.button>
        </div>
    </form>

    {{-- Preview gambar (jika dibutuhkan) --}}
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