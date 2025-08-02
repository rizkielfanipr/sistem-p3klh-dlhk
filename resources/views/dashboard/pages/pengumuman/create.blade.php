@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Pengumuman')

@section('content')
<form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    {{-- Custom Image Upload Field (Selaras dengan field lain) --}}
    <div class="relative w-full p-4 border border-gray-300 rounded-lg shadow-sm text-center bg-gray-50">
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2 text-left">
            Cover Image Pengumuman
        </label>
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-6 cursor-pointer hover:border-blue-400 hover:bg-gray-100 transition duration-150 ease-in-out"
             onclick="document.getElementById('image').click();">
            <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(event)">
            {{-- Image preview area --}}
            <div id="image-preview" class="w-full h-48 flex items-center justify-center bg-gray-200 text-gray-500 rounded-md overflow-hidden relative">
                <i class="fas fa-image text-6xl" id="image-icon"></i>
                <img src="#" alt="Image Preview" class="hidden absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
            </div>
            <p class="text-sm text-gray-600 mt-3">Klik untuk unggah atau seret & lepas</p>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hingga 5MB</p>
        </div>
        @error('image')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Existing form fields --}}
    <x-form.input name="judul" label="Judul Pengumuman" required />
    <x-form.input name="nama_usaha" label="Nama Usaha" required />
    <x-form.input name="bidang_usaha" label="Bidang Usaha" required />
    <x-form.input name="skala_besaran" label="Skala Besaran" required />
    <x-form.input name="lokasi" label="Lokasi" required />
    <x-form.input name="pemrakarsa" label="Pemrakarsa" required />
    <x-form.input name="penanggung_jawab" label="Penanggung Jawab" required />
    <x-form.textarea name="deskripsi" label="Deskripsi Usaha" required />
    <x-form.textarea name="dampak" label="Perkiraan Dampak Lingkungan" required />
    <x-form.input name="jenis_perling" label="Jenis Perling" required />
    <x-form.file-upload name="lampiran" label="File Lampiran (Opsional)" />

    <div class="flex items-center space-x-4">
        <x-form.button variant="primary">Simpan</x-form.button>
        <x-form.button variant="secondary" href="{{ route('pengumuman.index') }}">Batal</x-form.button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-tag');
            const icon = document.getElementById('image-icon');
            const previewContainer = document.getElementById('image-preview');

            output.src = reader.result;
            output.classList.remove('hidden');
            icon.classList.add('hidden'); // Sembunyikan ikon ketika gambar ditampilkan

            // Ensure the container background is transparent if an image is loaded
            previewContainer.classList.remove('bg-gray-200');
            previewContainer.classList.add('bg-transparent');
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            // Jika tidak ada file yang dipilih, kembalikan ke ikon dan latar belakang default
            const output = document.getElementById('image-tag');
            const icon = document.getElementById('image-icon');
            const previewContainer = document.getElementById('image-preview');

            output.classList.add('hidden');
            output.src = ''; // Bersihkan sumber gambar
            icon.classList.remove('hidden');

            previewContainer.classList.remove('bg-transparent');
            previewContainer.classList.add('bg-gray-200');
        }
    }
</script>
@endsection