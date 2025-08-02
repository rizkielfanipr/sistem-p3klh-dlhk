@extends('dashboard.layouts.adminlayout')

@section('title', 'Tambah Informasi')

@section('content')
<form action="{{ route('informasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    {{-- Cover Image Upload Field --}}
    <div class="relative w-full p-4 border border-gray-300 rounded-lg shadow-sm text-center bg-gray-50">
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2 text-left">
            Gambar Cover Informasi (Opsional)
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
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hingga 2MB</p>
        </div>
        @error('image')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Form fields: Judul and Description --}}
    <x-form.input name="judul" label="Judul Informasi" required />
    <x-form.textarea name="description" label="Deskripsi Informasi" required />

    {{-- Use your x-form.file-upload component for Lampiran --}}
    <x-form.file-upload name="lampiran_file" label="File Lampiran (Opsional)" />


    <div class="flex items-center space-x-4">
        <x-form.button variant="primary">Simpan</x-form.button>
        <x-form.button variant="secondary" href="{{ route('informasi.index') }}">Batal</x-form.button>
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

            if (reader.result) {
                output.src = reader.result;
                output.classList.remove('hidden');
                icon.classList.add('hidden'); // Sembunyikan ikon ketika gambar ditampilkan
                previewContainer.classList.remove('bg-gray-200');
                previewContainer.classList.add('bg-transparent');
            } else {
                // Jika tidak ada file yang dipilih atau preview dihapus
                output.classList.add('hidden');
                output.src = ''; // Hapus sumber gambar
                icon.classList.remove('hidden');
                previewContainer.classList.remove('bg-transparent');
                previewContainer.classList.add('bg-gray-200');
            }
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            // Picu onload dengan null untuk mengatur ulang pratinjau jika file tidak dipilih
            reader.onload(null);
        }
    }

    // Fungsi updateFileName ini seharusnya menjadi bagian dari komponen x-form.file-upload Anda.
    // Jika tidak, Anda mungkin perlu memastikan komponen x-form.file-upload Anda menangani tampilan nama file.
    // Untuk saat ini, saya tetap mempertahankannya jika komponen Anda bergantung padanya atau Anda ingin mengintegrasikannya di sana.
    // Jika x-form.file-upload sudah menanganinya, Anda bisa menghapus fungsi ini.
    function updateFileName(event, elementId) {
        const fileNameDisplay = document.getElementById(elementId);
        if (event.target.files.length > 0) {
            fileNameDisplay.textContent = event.target.files[0].name;
        } else {
            fileNameDisplay.textContent = 'Pilih file atau seret & lepas di sini';
        }
    }
</script>
@endsection