@extends('dashboard.layouts.adminlayout')

@section('title', 'Informasi Layanan')
@section('breadcrumb', 'Informasi Layanan')
@section('content')
<div>

    @if (session('success'))
        <div class="alert alert-success mb-4 bg-green-500 text-white p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-4 bg-red-500 text-white p-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('layanan.store') }}" method="POST" id="layananForm">
        @csrf
        <div class="mb-4">
            <label for="kategori" class="block text-gray-700 font-semibold">Kategori Layanan</label>
            <select id="kategori" class="w-full p-2 border rounded" name="kategori_id">
                <option value="">Pilih Kategori</option>
                @foreach($kategoriLayanans as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ ucwords(str_replace('-', ' ', $kategori->nama_kategori)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="editor" class="block text-gray-700 font-semibold">Konten</label>
            <div id="editor" class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100" style="height: 300px;"></div>
            <input type="hidden" name="konten_layanan" id="konten_layanan">
            <input type="hidden" name="layanan_id" id="layanan_id">
        </div>

        <div class="flex space-x-2">
            <button type="submit" id="saveBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
            <button type="button" id="deleteBtn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus Data</button>
        </div>
    </form>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<script>
    // Inisialisasi Quill Editor
    const editor = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'font': [] }], [{ 'size': ['small', false, 'large', 'huge'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    // Fungsi custom untuk menangani gambar
    function imageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = async () => {
            const file = input.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const response = await fetch("{{ route('layanan.uploadImage') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        const range = editor.getSelection();
                        editor.insertEmbed(range.index, 'image', data.url);
                    } else {
                        alert('Gagal mengunggah gambar.');
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                }
            }
        };
    }

    // Override tombol gambar pada toolbar
    editor.getModule('toolbar').addHandler('image', imageHandler);

    let initialContent = ''; // Menyimpan konten awal
    let isNew = true; // Flag untuk status baru/ubah data

    function showNotification(message, isSuccess) {
        const notificationDiv = document.createElement('div');
        notificationDiv.textContent = message;
        notificationDiv.classList.add('fixed', 'bottom-10', 'right-8', 'py-2', 'px-4', 'rounded-md', 'shadow-lg', 'z-50');
        notificationDiv.style.opacity = 0.9;

        if (isSuccess) {
            notificationDiv.classList.add('bg-green-500', 'text-white');
        } else {
            notificationDiv.classList.add('bg-red-500', 'text-white');
        }

        document.body.appendChild(notificationDiv);

        setTimeout(() => {
            notificationDiv.remove();
        }, 3000);
    }

    // Update tombol simpan jika konten berubah
    function updateSaveButtonText() {
        const currentContent = editor.root.innerHTML;
        const saveButton = document.getElementById('saveBtn');
        if (currentContent !== initialContent && !isNew) {
            saveButton.textContent = 'Simpan Perubahan';
            saveButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            saveButton.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
        } else {
            saveButton.textContent = 'Simpan';
            saveButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
            saveButton.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }
    }

    editor.on('text-change', function() {
        updateSaveButtonText();
    });

    // Menyimpan konten editor ke input hidden
    document.getElementById('layananForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent form submit default
        const konten = editor.root.innerHTML;
        document.getElementById('konten_layanan').value = konten;
        const layananId = document.getElementById('layanan_id').value;
        const kategoriId = document.getElementById('kategori').value;

        let method = 'POST';
        let url = "{{ route('layanan.store') }}";

        if (layananId) {
            method = 'PUT';
            url = `/layanan/update/${layananId}`;
            let methodInput = document.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                this.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
        } else {
            const methodInput = document.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
        }

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(this)),
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server Response:', text); // Debug response
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, true);
                editor.root.innerHTML = data.konten || '';
                document.getElementById('konten_layanan').value = data.konten || '';
                document.getElementById('layanan_id').value = data.id || '';
                initialContent = data.konten || '';
                isNew = false;
                updateSaveButtonText();
                if (data.id) {
                    document.getElementById('deleteBtn').classList.remove('hidden');
                } else {
                    document.getElementById('deleteBtn').classList.add('hidden');
                }
                const kategoriSelect = document.getElementById('kategori');
                kategoriSelect.value = kategoriId;
                kategoriSelect.dispatchEvent(new Event('change'));
            } else {
                showNotification(data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error.message);
            showNotification(error.message || 'Terjadi kesalahan saat mengirim data.', false);
        });
    });

    // Event listener untuk tombol Hapus Data
    document.getElementById('deleteBtn').addEventListener('click', function() {
        const layananId = document.getElementById('layanan_id').value;
        if (confirm('Apakah Anda yakin ingin menghapus data layanan ini?')) {
            fetch(`/layanan/destroy/${layananId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || `HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(data.message, true);
                    location.reload(); // Reload halaman setelah berhasil dihapus
                } else {
                    showNotification(data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
                showNotification(error.message || 'Terjadi kesalahan saat menghapus data.', false);
            });
        }
    });

    // Mengambil data awal saat kategori berubah
    document.getElementById('kategori').addEventListener('change', function() {
        const kategoriId = this.value;
        if (kategoriId) {
            fetch(`/layanan/konten/${kategoriId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    editor.root.innerHTML = data.konten || '';
                    document.getElementById('konten_layanan').value = data.konten || '';
                    document.getElementById('layanan_id').value = data.id || '';
                    initialContent = data.konten || '';
                    isNew = !data.id; // Jika tidak ada ID, berarti ini adalah data baru
                    updateSaveButtonText();
                    if (data.id) {
                        document.getElementById('deleteBtn').classList.remove('hidden');
                        document.getElementById('saveBtn').textContent = 'Simpan Perubahan';
                        document.getElementById('saveBtn').classList.remove('bg-blue-500', 'hover:bg-blue-600');
                        document.getElementById('saveBtn').classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                    } else {
                        document.getElementById('deleteBtn').classList.add('hidden');
                        document.getElementById('saveBtn').textContent = 'Simpan';
                        document.getElementById('saveBtn').classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                        document.getElementById('saveBtn').classList.add('bg-blue-500', 'hover:bg-blue-600');
                    }
                })
                .catch(error => {
                    console.error('Error fetching konten:', error);
                    editor.root.innerHTML = '';
                    document.getElementById('konten_layanan').value = '';
                    document.getElementById('layanan_id').value = '';
                    initialContent = '';
                    isNew = true;
                    updateSaveButtonText();
                    document.getElementById('deleteBtn').classList.add('hidden');
                });
        } else {
            editor.root.innerHTML = '';
            document.getElementById('konten_layanan').value = '';
            document.getElementById('layanan_id').value = '';
            initialContent = '';
            isNew = true;
            updateSaveButtonText();
            document.getElementById('deleteBtn').classList.add('hidden');
        }
    });

    // Mengambil data awal jika ada (dari old input atau saat load pertama jika ada kategori terpilih)
    document.addEventListener('DOMContentLoaded', function() {
        const initialKategoriId = document.getElementById('kategori').value;
        if (initialKategoriId) {
            fetch(`/layanan/konten/${initialKategoriId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    editor.root.innerHTML = data.konten || '';
                    document.getElementById('konten_layanan').value = data.konten || '';
                    document.getElementById('layanan_id').value = data.id || '';
                    initialContent = data.konten || '';
                    isNew = !data.id;
                    updateSaveButtonText();
                    if (data.id) {
                        document.getElementById('deleteBtn').classList.remove('hidden');
                        document.getElementById('saveBtn').textContent = 'Simpan Perubahan';
                        document.getElementById('saveBtn').classList.remove('bg-blue-500', 'hover:bg-blue-600');
                        document.getElementById('saveBtn').classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                    } else {
                        document.getElementById('deleteBtn').classList.add('hidden');
                        document.getElementById('saveBtn').textContent = 'Simpan';
                        document.getElementById('saveBtn').classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                        document.getElementById('saveBtn').classList.add('bg-blue-500', 'hover:bg-blue-600');
                    }
                })
                .catch(error => {
                    console.error('Error fetching initial konten:', error);
                    editor.root.innerHTML = '';
                    document.getElementById('konten_layanan').value = '';
                    document.getElementById('layanan_id').value = '';
                    initialContent = '';
                    isNew = true;
                    updateSaveButtonText();
                    document.getElementById('deleteBtn').classList.add('hidden');
                });
        }
    });
</script>
@endsection