@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Informasi')

@section('content')
    <div>
        <form
            action="{{ route('informasi.update', $informasi->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Jenis Informasi --}}
            <x-form.select
                name="jenis_informasi_id"
                :options="$jenisInformasi->pluck('nama_jenis', 'id')"
                label="Jenis Informasi"
                required
                :selected="$informasi->jenis_informasi_id"
            />

            {{-- Judul --}}
            <x-form.input
                name="judul"
                label="Judul Informasi"
                required
                :value="$informasi->judul"
            />

            {{-- Konten --}}
            <x-form.textarea
                name="konten"
                label="Isi Informasi"
                required>{{ $informasi->konten }}
            </x-form.textarea>

            {{-- Lampiran --}}
            <div>
                <x-form.label for="lampiran" value="File Lampiran" />
                <input type="file" name="lampiran" id="lampiran"
                            class="appearance-none border border-gray-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <div class="mt-2">
                    @if ($informasi->lampiran)
                        <img id="preview" src="{{ asset('/' . $informasi->lampiran->lampiran) }}" alt="Preview" class="h-20 rounded">
                    @else
                        <img id="preview" src="#" alt="Preview" class="hidden h-20 rounded">
                    @endif
                </div>
                <x-form.error name="lampiran" />
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-start space-x-4">
                <x-form.button>Update</x-form.button>
                <x-form.button type="button" href="{{ route('informasi.pengumuman') }}">Batal</x-form.button>
            </div>
        </form>
    </div>

    {{-- Preview gambar --}}
    <script>
        document.getElementById('lampiran').addEventListener('change', function (event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }

                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
            }
        });
    </script>
@endsection