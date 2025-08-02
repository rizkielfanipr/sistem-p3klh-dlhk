@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Informasi')

@section('content')
<form action="{{ route('informasi.update', $informasi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- Cover Image Upload Field --}}
    <div class="relative w-full p-4 border border-gray-300 rounded-lg shadow-sm text-center bg-gray-50">
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2 text-left">
            Cover Image Informasi (Opsional)
        </label>
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-6 cursor-pointer hover:border-blue-400 hover:bg-gray-100 transition duration-150 ease-in-out"
             onclick="document.getElementById('image').click();">
            <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(event)">
            {{-- Image preview area --}}
            {{-- Menyesuaikan kelas awal berdasarkan keberadaan gambar --}}
            <div id="image-preview" class="w-full h-48 flex items-center justify-center rounded-md overflow-hidden relative @if (!$informasi->image) bg-gray-200 text-gray-500 @else bg-transparent @endif">
                @if ($informasi->image)
                    <img src="{{ Storage::url($informasi->image) }}" alt="Image Preview" class="absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
                    <i class="fas fa-image text-6xl hidden" id="image-icon"></i>
                @else
                    <i class="fas fa-image text-6xl" id="image-icon"></i>
                    <img src="#" alt="Image Preview" class="hidden absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
                @endif
            </div>
            <p class="text-sm text-gray-600 mt-3">Klik untuk unggah atau seret & lepas</p>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hingga 2MB</p>
        </div>
        @error('image')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror

        {{-- Checkbox to clear image --}}
        @if ($informasi->image)
            <div class="mt-2 flex items-center justify-center">
                <input type="checkbox" name="clear_image" id="clear_image" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mr-2">
                <label for="clear_image" class="text-sm text-red-600 cursor-pointer">Hapus Gambar Cover</label>
            </div>
        @endif
    </div>

    {{-- Form fields --}}
    <x-form.input name="judul" label="Judul Informasi" :value="old('judul', $informasi->judul)" required />
    <x-form.textarea name="description" label="Deskripsi Informasi" :value="old('description', $informasi->description)" required />

    {{-- Display current lampiran if exists --}}
    @if ($informasi->lampiran)
        <div class="mb-4 p-3 border border-gray-300 rounded-md bg-white shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Lampiran Saat Ini:</p>
                <a href="{{ Storage::url($informasi->lampiran->lampiran) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-semibold">
                    Lihat Lampiran
                </a>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="clear_lampiran_file" id="clear_lampiran_file" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mr-2">
                <label for="clear_lampiran_file" class="text-sm text-red-600 cursor-pointer">Hapus Lampiran</label>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">Unggah file baru di bawah untuk mengganti lampiran saat ini.</p>
    @else
        <p class="text-sm text-gray-600 mb-4">Belum ada lampiran terlampir. Anda bisa mengunggahnya di bawah.</p>
    @endif

    {{-- New Lampiran File Upload Field (using your component) --}}
    <x-form.file-upload name="lampiran_file_update" label="Unggah Lampiran Baru (Opsional)" />


    <div class="flex items-center space-x-4">
        <x-form.button variant="primary">Perbarui</x-form.button>
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
                icon.classList.add('hidden');
                previewContainer.classList.remove('bg-gray-200');
                previewContainer.classList.add('bg-transparent');
            } else {
                // Jika tidak ada file yang dipilih atau preview dihapus
                output.classList.add('hidden');
                output.src = '';
                icon.classList.remove('hidden');
                previewContainer.classList.remove('bg-transparent');
                previewContainer.classList.add('bg-gray-200');
            }
        };
        // Perbaikan typo: event.target.files.0 menjadi event.target.files[0]
        if (event.target.files && event.target.files.length > 0) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            // Logika untuk mempertahankan gambar yang sudah ada jika tidak ada file baru yang dipilih
            const existingImageSrc = document.getElementById('image-tag').getAttribute('src');
            if (existingImageSrc && existingImageSrc !== '#' && existingImageSrc !== '{{ Storage::url('') }}') { // Cek juga jika src bukan path kosong
                document.getElementById('image-tag').classList.remove('hidden');
                document.getElementById('image-icon').classList.add('hidden');
                document.getElementById('image-preview').classList.remove('bg-gray-200');
                document.getElementById('image-preview').classList.add('bg-transparent');
            } else {
                document.getElementById('image-tag').classList.add('hidden');
                document.getElementById('image-icon').classList.remove('hidden');
                document.getElementById('image-preview').classList.add('bg-gray-200');
                document.getElementById('image-preview').classList.remove('bg-transparent');
            }
        }
    }
</script>
@endsection