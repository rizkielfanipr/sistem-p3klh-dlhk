@extends('dashboard.layouts.adminlayout')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-full bg-white border-gray-200 rounded-lg p-6 mt-6">

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-8">
        <!-- Foto Profil dan Tombol -->
        <div class="w-1/4 flex flex-col items-center gap-4">
            <img id="foto-profil" 
                 src="{{ $user->foto && file_exists(public_path('storage/' . $user->foto)) ? asset('storage/' . $user->foto) : asset('images/default_profile.png') }}" 
                 alt="Foto Profil"
                 class="w-full h-full max-w-xs max-h-xs rounded-lg object-cover border border-gray-300">
            
            <div class="flex gap-2 w-full">
                <label for="foto" class="w-full bg-transparent text-gray-700 px-4 py-2 rounded cursor-pointer hover:bg-gray-100 text-sm text-center border border-dashed border-gray-400">
                    Update Photo
                </label>
                <button id="hapus-foto" type="button" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm text-center">
                    Hapus Foto
                </button>
            </div>
        </div>

        <!-- Form Data Pengguna -->
        <div class="w-3/4">
            <form id="form-profil" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6 text-sm mb-6">
                    <!-- Field Nama -->
                    <div>
                        <label for="nama" class="block">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" id="nama" class="w-full p-3 border border-gray-300 rounded" required>
                        @error('nama')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Field No. Telepon -->
                    <div>
                        <label for="no_telp" class="block">No. Telepon</label>
                        <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" id="no_telp" class="w-full p-3 border border-gray-300 rounded">
                        @error('no_telp')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" id="email" class="w-full p-3 border border-gray-300 rounded" required>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="file" name="foto" id="foto" class="hidden" accept="image/*">
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="w-1/2 bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600 text-sm">Simpan</button>
                    <a href="{{ route('profil.password.form') }}" class="w-1/2 bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600 text-sm text-center flex items-center justify-center">Ganti Password</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.getElementById('hapus-foto').addEventListener('click', function() {
    hapusFoto();
});

function hapusFoto() {
    const img = document.getElementById('foto-profil');
    const inputFoto = document.getElementById('foto');
    const form = document.getElementById('form-profil');

    // Set foto profil menjadi default
    img.src = "{{ asset('images/default_profile.png') }}";
    
    // Clear input file
    inputFoto.value = '';

    // Tambahkan input hidden untuk menandakan penghapusan foto
    let inputHapusFoto = document.querySelector('input[name="hapus_foto"]');
    if (!inputHapusFoto) {
        inputHapusFoto = document.createElement('input');
        inputHapusFoto.type = 'hidden';
        inputHapusFoto.name = 'hapus_foto';
        inputHapusFoto.value = '1';
        form.appendChild(inputHapusFoto);
    }
}

// Preview gambar saat upload
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