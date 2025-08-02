{{-- PUBLIC ANNOUNCEMENT FORM FIELDS (INPUT) --}}
@if($statusTerakhir === 'Administrasi Lengkap' || ($dokumen->pengumuman && $statusTerakhir === 'Pengumuman Publik'))
    <div class="mb-6 p-6 border border-blue-300 rounded-lg bg-blue-50 shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bullhorn mr-2 text-blue-600"></i>Formulir Pengumuman Publik
        </h3>
        <p class="text-gray-600 mb-6">Isi detail di bawah ini untuk menerbitkan pengumuman publik terkait dokumen.</p>

        {{-- Custom Image Upload Field (Selaras dengan field lain) --}}
        <div class="mb-6"> {{-- Added mb-6 for spacing from next section --}}
            <div class="relative w-full p-4 border border-gray-300 rounded-lg shadow-sm text-center bg-gray-50">
                <label for="image_pengumuman" class="block text-sm font-medium text-gray-700 mb-2 text-left">
                    Cover Image Pengumuman
                </label>
                <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md p-6 cursor-pointer hover:border-blue-400 hover:bg-gray-100 transition duration-150 ease-in-out"
                     onclick="document.getElementById('image_pengumuman').click();">
                    <input type="file" name="image_pengumuman" id="image_pengumuman" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewNewImage(event)">

                    {{-- Image preview area --}}
                    <div id="image-preview" class="w-full h-48 flex items-center justify-center bg-gray-200 text-gray-500 rounded-md overflow-hidden relative">
                        {{-- Display existing image or placeholder --}}
                        @if(isset($dokumen->pengumuman->image))
                            <img src="{{ asset('storage/' . $dokumen->pengumuman->image) }}" alt="Image Preview" class="absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
                            <i class="fas fa-image text-6xl hidden" id="image-icon"></i> {{-- Hidden if image exists --}}
                        @else
                            <i class="fas fa-image text-6xl" id="image-icon"></i>
                            <img src="#" alt="Image Preview" class="hidden absolute top-0 left-0 w-full h-full object-cover" id="image-tag">
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-3">Klik untuk unggah atau seret & lepas</p>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hingga 5MB</p>
                </div>
                @error('image_pengumuman') {{-- Updated error name --}}
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        {{-- End Custom Image Upload Field --}}

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            {{-- JUDUL PENGUMUMAN --}}
            <x-form.input
                name="judul_pengumuman"
                label="Judul Pengumuman"
                type="text"
                placeholder="Contoh: Pengumuman Proyek Pembangunan"
                :value="old('judul_pengumuman', $dokumen->nama_usaha . ' - ' . ($dokumen->jenisPerling->nama_perling ?? ''))"
                readonly
                required
            />
            {{-- JENIS PERLING --}}
            <x-form.input
                name="jenis_perling_pengumuman"
                label="Jenis Perling"
                type="text"
                placeholder="Contoh: UKL-UPL"
                :value="old('jenis_perling_pengumuman', $dokumen->jenisPerling->nama_perling ?? '')"
                readonly
                required
            />
            {{-- NAMA USAHA --}}
            <x-form.input
                name="nama_usaha_pengumuman"
                label="Nama Usaha"
                type="text"
                placeholder="Nama Usaha/Kegiatan yang akan diumumkan"
                :value="old('nama_usaha_pengumuman', $dokumen->nama_usaha)"
                readonly
                required
            />
            {{-- BIDANG USAHA --}}
            <x-form.input
                name="bidang_usaha_pengumuman"
                label="Bidang Usaha"
                type="text"
                placeholder="Contoh: Perumahan, Industri Manufaktur"
                :value="old('bidang_usaha_pengumuman', $dokumen->bidang_usaha ?? '')"
                readonly
                required
            />
            {{-- SKALA BESARAN --}}
            <x-form.input
                name="skala_besaran_pengumuman"
                label="Skala Besaran"
                type="text"
                placeholder="Contoh: Skala Kecil, Menengah"
                :value="old('skala_besaran_pengumuman', $dokumen->skala_besaran ?? '')"
                readonly
                required
            />
            {{-- LOKASI --}}
            <x-form.input
                name="lokasi_pengumuman"
                label="Lokasi"
                type="text"
                placeholder="Alamat lengkap lokasi"
                :value="old('lokasi_pengumuman', $dokumen->lokasi ?? '')"
                readonly
                required
            />
            {{-- PEMRAKARSA --}}
            <x-form.input
                name="pemrakarsa_pengumuman"
                label="Pemrakarsa"
                type="text"
                placeholder="Nama Pemrakarsa"
                :value="old('pemrakarsa_pengumuman', $dokumen->pemrakarsa ?? '')"
                readonly
                required
            />
            {{-- PENANGGUNG JAWAB --}}
            <x-form.input
                name="penanggung_jawab_pengumuman"
                label="Penanggung Jawab"
                type="text"
                placeholder="Nama Penanggung Jawab"
                :value="old('penanggung_jawab_pengumuman', $dokumen->penanggung_jawab ?? '')"
                readonly
                required
            />
        </div>

        <div class="mt-6">
            {{-- DESKRIPSI USAHA --}}
            <x-form.textarea
                label="Deskripsi Usaha"
                name="deskripsi_pengumuman"
                :value="old('deskripsi_pengumuman', $dokumen->deskripsi ?? '')"
                placeholder="Jelaskan secara singkat deskripsi usaha/kegiatan yang akan diumumkan."
                rows="4"
                readonly
                required
            />
        </div>

        <div class="mt-6">
            {{-- PERKIRAAN DAMPAK LINGKUNGAN (FIELD BARU) --}}
            <x-form.textarea
                label="Perkiraan Dampak Lingkungan"
                name="dampak_pengumuman"
                :value="old('dampak_pengumuman', $dokumen->pengumuman->dampak ?? '')"
                placeholder="Tuliskan perkiraan dampak lingkungan dari usaha/kegiatan ini."
                rows="4"
                required
            />
        </div>

        <div class="mt-6">
            {{-- LAMPIRAN PENGUMUMAN --}}
            <x-form.file-upload
                name="lampiran_pengumuman"
                label="Unggah File Lampiran Pengumuman (PDF, DOCX - Opsional)"
            />
            @if(isset($dokumen->pengumuman->lampiran->lampiran))
                <p class="text-sm text-gray-500 mt-2">Lampiran saat ini:
                    <a href="{{ asset('storage/' . $dokumen->pengumuman->lampiran->lampiran) }}" target="_blank" class="text-blue-600 hover:underline">
                        {{ basename($dokumen->pengumuman->lampiran->lampiran) }}
                    </a>
                </p>
            @endif
        </div>

        <script>
            function previewNewImage(event) {
                const imageTag = document.getElementById('image-tag');
                const imageIcon = document.getElementById('image-icon');
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageTag.src = e.target.result;
                        imageTag.classList.remove('hidden');
                        imageIcon.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no file is selected, revert to placeholder
                    imageTag.src = '#';
                    imageTag.classList.add('hidden');
                    imageIcon.classList.remove('hidden');
                }
            }

            // Initial check for existing image on page load
            document.addEventListener('DOMContentLoaded', function() {
                const existingImageSrc = document.getElementById('image-tag').src;
                if (existingImageSrc && existingImageSrc !== window.location.href + '#') { // Check if not just '#'
                    document.getElementById('image-icon').classList.add('hidden');
                    document.getElementById('image-tag').classList.remove('hidden');
                }
            });
        </script>
    </div>
@endif