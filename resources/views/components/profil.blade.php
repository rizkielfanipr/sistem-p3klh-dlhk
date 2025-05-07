@extends('dashboard.layouts.adminlayout')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl bg-white border border-gray-200 rounded-lg p-6 mt-6">

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col items-center gap-4 mb-6">
        <img id="foto-profil" src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/default_profile.png') }}" alt="Foto Profil"
            class="w-20 h-20 rounded-full object-cover border border-gray-300">
        <div class="flex gap-2 mt-2">
            <label for="foto" class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-600 text-sm">Pilih Foto</label>
            <button id="hapus-foto" type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm" onclick="hapusFoto()">Hapus Foto</button>
        </div>
    </div>

    <form id="form-profil" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <label for="nama" class="block">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" id="nama" class="w-full p-2 border border-gray-300 rounded" required>
                @error('nama')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="no_telp" class="block">No. Telepon</label>
                <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" id="no_telp" class="w-full p-2 border border-gray-300 rounded">
                @error('no_telp')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <input type="file" name="foto" id="foto" class="hidden" accept="image/*">
        </div>
        
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 text-sm">Simpan Perubahan</button>
    </form>
</div>

<script>
function hapusFoto() {
    const img = document.getElementById('foto-profil');
    const inputFoto = document.getElementById('foto');
    const form = document.getElementById('form-profil');

    // Set foto profil menjadi default
    img.src = "{{ asset('images/default_profile.png') }}";
    // Clear input file
    inputFoto.value = '';

    // Tambahkan input hidden untuk menandakan penghapusan foto
    const inputHapusFoto = document.createElement('input');
    inputHapusFoto.type = 'hidden';
    inputHapusFoto.name = 'hapus_foto'; // Nama input ini akan kita cek di controller
    inputHapusFoto.value = '1';
    form.appendChild(inputHapusFoto);
}

// Tambahkan event listener untuk input file
document.getElementById('foto').addEventListener('change', function(e) {
    const img = document.getElementById('foto-profil');
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            img.src = event.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        img.src = "{{ asset('images/default_profile.png') }}";
    }
});
</script>
@endsection
