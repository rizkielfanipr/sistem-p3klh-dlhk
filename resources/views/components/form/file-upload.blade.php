@props(['name', 'label'])

<div class="mb-6">
    {{-- Label --}}
    <label for="{{ $name }}" class="block text-lg font-semibold text-gray-800 mb-3">
        {{ $label }}
    </label>

    {{-- Drop Area --}}
    <div
        id="drop-area"
        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-6 bg-white hover:border-blue-400 transition cursor-pointer text-center"
        onclick="document.getElementById('{{ $name }}').click();"
        ondragover="event.preventDefault(); this.classList.add('border-blue-400')"
        ondragleave="this.classList.remove('border-blue-400')"
        ondrop="handleDrop(event)"
    >
        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <p class="text-gray-600 text-sm">Drag and drop or <span class="text-blue-600 underline">browse</span> your files (PDF/Image)</p>
    </div>

    {{-- Hidden File Input --}}
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $name }}"
        class="hidden"
        accept="application/pdf,image/png,image/jpeg,image/jpg"
        onchange="handleFileUpload(event)"
    >

    {{-- File Info --}}
    <div id="file-info" class="mt-4 hidden">
        <div class="flex items-center gap-3">
            <div id="file-icon" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded text-xl text-blue-600">
                <!-- Icon dinamis -->
            </div>
            <div class="w-full">
                <p id="file-name" class="text-sm font-medium text-gray-700 truncate"></p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div id="upload-progress" class="bg-blue-500 h-2 rounded-full transition-all duration-300 ease-in-out" style="width: 0%"></div>
                </div>
                <p id="upload-status" class="text-xs text-blue-500 mt-1">Uploading... <span id="progress-text">0%</span></p>
            </div>

            {{-- Delete Button --}}
            <button
                onclick="resetFileUpload()"
                type="button"
                class="text-red-500 hover:text-red-700 transition"
                title="Hapus Lampiran"
            >
                <i class="fa-solid fa-trash text-lg"></i>
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    <div id="upload-success" class="mt-4 hidden flex items-center text-green-600 text-sm font-medium">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        File uploaded successfully.
    </div>

    {{-- Error --}}
    <x-form.error :name="$name" />
</div>

@once
    @push('scripts')
        <script>
            function handleDrop(e) {
                e.preventDefault();
                const files = e.dataTransfer.files;
                if (files.length) {
                    document.getElementById('{{ $name }}').files = files;
                    handleFileUpload({ target: { files } });
                }
            }

            function handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('File tidak didukung. Hanya PDF dan gambar yang diperbolehkan.');
                    return;
                }

                const icon = document.getElementById('file-icon');
                const info = document.getElementById('file-info');
                const nameEl = document.getElementById('file-name');
                const progressBar = document.getElementById('upload-progress');
                const progressText = document.getElementById('progress-text');
                const status = document.getElementById('upload-status');
                const successMessage = document.getElementById('upload-success');

                // Set icon sesuai file type
                if (file.type === 'application/pdf') {
                    icon.innerHTML = `<i class="fa-solid fa-file-pdf text-red-600"></i>`;
                } else {
                    icon.innerHTML = `<i class="fa-solid fa-file-image text-blue-500"></i>`;
                }

                nameEl.textContent = file.name;
                info.classList.remove('hidden');
                successMessage.classList.add('hidden');

                // Simulasi upload dengan progress
                let uploaded = 0;
                const total = file.size;
                const interval = setInterval(() => {
                    uploaded += total * 0.05;
                    const percent = Math.min(100, Math.round((uploaded / total) * 100));
                    progressBar.style.width = percent + '%';
                    progressText.textContent = percent + '%';

                    if (percent >= 100) {
                        clearInterval(interval);
                        status.textContent = 'Upload complete';
                        successMessage.classList.remove('hidden');
                    }
                }, 100);
            }

            function resetFileUpload() {
                const fileInput = document.getElementById('{{ $name }}');
                const fileInfo = document.getElementById('file-info');
                const fileName = document.getElementById('file-name');
                const progressBar = document.getElementById('upload-progress');
                const progressText = document.getElementById('progress-text');
                const status = document.getElementById('upload-status');
                const successMessage = document.getElementById('upload-success');
                const fileIcon = document.getElementById('file-icon');

                fileInput.value = '';
                fileInfo.classList.add('hidden');
                successMessage.classList.add('hidden');

                fileName.textContent = '';
                progressBar.style.width = '0%';
                progressText.textContent = '0%';
                status.textContent = 'Uploading...';
                fileIcon.innerHTML = '';
            }
        </script>
    @endpush
@endonce
