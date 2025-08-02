@extends('dashboard.layouts.adminlayout')

@section('title', 'Edit Pengumuman')

@section('content')
<form action="{{ route('pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- Custom Image Upload Field (Selaras dengan field lain) --}}
    <div class="relative w-full p-4 border border-gray-300 rounded-lg shadow-sm bg-gray-50">
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2 text-left">
            Cover Image (Opsional)
        </label>
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-6 cursor-pointer hover:border-blue-400 hover:bg-gray-100 transition duration-150 ease-in-out"
             onclick="document.getElementById('image').click();">
            <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(event)">
            
            {{-- Image preview area --}}
            <div id="image-preview" class="w-full h-48 flex items-center justify-center rounded-md overflow-hidden relative
                 @if($pengumuman->image) bg-transparent @else bg-gray-200 @endif">
                
                @if ($pengumuman->image)
                    <img src="{{ Storage::url($pengumuman->image) }}" alt="Current Pengumuman Image" 
                         class="w-full h-full object-cover" id="image-tag">
                    <i class="fas fa-image text-6xl hidden" id="image-icon"></i> {{-- Hidden if image exists --}}
                @else
                    <i class="fas fa-image text-6xl" id="image-icon"></i>
                    <img src="#" alt="Image Preview" class="hidden absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
                @endif
            </div>
            <p class="text-sm text-gray-600 mt-3">Klik untuk unggah atau seret & lepas</p>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hingga 5MB</p>
        </div>
        @error('image')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror

        {{-- Option to remove current image --}}
        @if ($pengumuman->image)
            <div class="mt-4 text-left">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remove_image" value="1" class="form-checkbox text-red-600">
                    <span class="ml-2 text-sm text-red-600">Hapus gambar saat ini</span>
                </label>
            </div>
        @endif
    </div>

    ---

    {{-- Existing form fields --}}
    <x-form.input name="judul" label="Judul Pengumuman" required :value="$pengumuman->judul" />
    <x-form.input name="nama_usaha" label="Nama Usaha" required :value="$pengumuman->nama_usaha" />
    <x-form.input name="bidang_usaha" label="Bidang Usaha" required :value="$pengumuman->bidang_usaha" />
    <x-form.input name="skala_besaran" label="Skala Besaran" required :value="$pengumuman->skala_besaran" />
    <x-form.input name="lokasi" label="Lokasi" required :value="$pengumuman->lokasi" />
    <x-form.input name="pemrakarsa" label="Pemrakarsa" required :value="$pengumuman->pemrakarsa" />
    <x-form.input name="penanggung_jawab" label="Penanggung Jawab" required :value="$pengumuman->penanggung_jawab" />
    <x-form.textarea name="deskripsi" label="Deskripsi Usaha" required :value="$pengumuman->deskripsi" />
    <x-form.textarea name="dampak" label="Perkiraan Dampak Lingkungan" required :value="$pengumuman->dampak" />
    <x-form.input name="jenis_perling" label="Jenis Perling" required :value="$pengumuman->jenis_perling" />

    {{-- Existing Lampiran Upload Section --}}
    <div class="mb-4">
        <x-form.file-upload name="lampiran" label="Ganti File Lampiran (Opsional)" />

        @php
            $lampiranPath = optional($pengumuman->lampiran)->lampiran;
        @endphp

        @if ($lampiranPath && Storage::disk('public')->exists($lampiranPath))
            <div class="text-sm text-gray-600 mt-2">
                <span class="font-semibold">Lampiran saat ini:</span>
                <a href="{{ Storage::url($lampiranPath) }}" target="_blank" class="text-blue-600 underline">
                    {{ basename($lampiranPath) }}
                </a>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remove_lampiran" value="1" class="form-checkbox text-red-600">
                        <span class="ml-2 text-sm text-red-600">Hapus lampiran</span>
                    </label>
                </div>
            </div>
        @else
            <div class="text-sm text-red-600 mt-2">Lampiran sebelumnya tidak ditemukan di server.</div>
        @endif
    </div>
    {{-- End Existing Lampiran Upload Section --}}

    <div class="flex items-center space-x-4">
        <x-form.button variant="primary">Update</x-form.button>
        <x-form.button variant="secondary" href="{{ route('pengumuman.index') }}" type="button">Batal</x-form.button>
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
            icon.classList.add('hidden'); // Hide the icon when image is displayed

            // Ensure the container background is transparent if an image is loaded
            previewContainer.classList.remove('bg-gray-200');
            previewContainer.classList.add('bg-transparent');
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
            // Uncheck "Hapus gambar saat ini" if a new image is selected
            const removeImageCheckbox = document.querySelector('input[name="remove_image"]');
            if (removeImageCheckbox) {
                removeImageCheckbox.checked = false;
            }
        } else {
            // If no new file is selected, revert to current image or icon
            const output = document.getElementById('image-tag');
            const icon = document.getElementById('image-icon');
            const previewContainer = document.getElementById('image-preview');
            const currentImagePath = "{{ $pengumuman->image ? Storage::url($pengumuman->image) : '' }}";

            if (currentImagePath) {
                // If there's an existing image and no new file, show the existing image
                output.src = currentImagePath;
                output.classList.remove('hidden');
                icon.classList.add('hidden');
                previewContainer.classList.remove('bg-gray-200');
                previewContainer.classList.add('bg-transparent');
            } else {
                // If no existing image and no new file, show the icon
                output.classList.add('hidden');
                output.src = ''; // Clear source
                icon.classList.remove('hidden');
                previewContainer.classList.remove('bg-transparent');
                previewContainer.classList.add('bg-gray-200');
            }
        }
    }

    // Call previewImage on page load to display the existing image correctly
    document.addEventListener('DOMContentLoaded', function() {
        const imageTag = document.getElementById('image-tag');
        const icon = document.getElementById('image-icon');
        const previewContainer = document.getElementById('image-preview');
        const currentImagePath = "{{ $pengumuman->image ? Storage::url($pengumuman->image) : '' }}";

        if (currentImagePath) {
            imageTag.src = currentImagePath;
            imageTag.classList.remove('hidden');
            icon.classList.add('hidden');
            previewContainer.classList.remove('bg-gray-200');
            previewContainer.classList.add('bg-transparent');
        }
    });
</script>
@endsection