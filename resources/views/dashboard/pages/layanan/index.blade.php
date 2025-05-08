@extends('dashboard.layouts.adminlayout')

@section('title', 'Layanan')
@section('breadcrumb', 'Layanan')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Daftar Layanan</h1>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="alert alert-success mb-4 bg-green-500 text-white p-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tombol Tambah Layanan -->
        <button class="bg-blue-500 text-white py-2 px-4 rounded-md mb-4" data-toggle="modal" data-target="#tambahLayananModal">
            Tambah Layanan
        </button>

        <!-- Tabel Layanan -->
        <table class="min-w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b text-left text-sm text-gray-700">ID</th>
                    <th class="py-2 px-4 border-b text-left text-sm text-gray-700">Kategori Layanan</th>
                    <th class="py-2 px-4 border-b text-left text-sm text-gray-700">Konten Layanan</th>
                    <th class="py-2 px-4 border-b text-left text-sm text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($layanans as $layanan)
                    <tr class="border-b">
                        <td class="py-2 px-4 text-sm">{{ $layanan->id }}</td>
                        <td class="py-2 px-4 text-sm">{{ $layanan->kategoriLayanan->nama_kategori }}</td>
                        <td class="py-2 px-4 text-sm">{{ $layanan->konten_layanan }}</td>
                        <td class="py-2 px-4 text-sm">
                            <!-- Tombol Edit -->
                            <button class="bg-yellow-500 text-white py-1 px-3 rounded-md mr-2" data-toggle="modal" data-target="#editLayananModal{{ $layanan->id }}">
                                Edit
                            </button>

                            <!-- Form Hapus -->
                            <form action="{{ route('layanan.destroy', $layanan->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-md">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Layanan -->
                    <div class="modal fade" id="editLayananModal{{ $layanan->id }}" tabindex="-1" role="dialog" aria-labelledby="editLayananLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editLayananLabel">Edit Layanan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('layanan.update', $layanan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label for="kategori_id" class="block text-sm font-semibold text-gray-700">Kategori Layanan</label>
                                            <select name="kategori_id" id="kategori_id" class="form-control mt-1 block w-full border border-gray-300 rounded-md p-2">
                                                @foreach ($kategoriLayanans as $kategori)
                                                    <option value="{{ $kategori->id }}" {{ $layanan->kategori_id == $kategori->id ? 'selected' : '' }}>
                                                        {{ $kategori->nama_kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="konten_layanan" class="block text-sm font-semibold text-gray-700">Konten Layanan</label>
                                            <textarea name="konten_layanan" id="konten_layanan" class="form-control mt-1 block w-full border border-gray-300 rounded-md p-2" rows="5">{{ $layanan->konten_layanan }}</textarea>
                                        </div>

                                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md mt-3">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>

        <!-- Modal Tambah Layanan -->
        <div class="modal fade" id="tambahLayananModal" tabindex="-1" role="dialog" aria-labelledby="tambahLayananLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahLayananLabel">Tambah Layanan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('layanan.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="kategori_id" class="block text-sm font-semibold text-gray-700">Kategori Layanan</label>
                                <select name="kategori_id" id="kategori_id" class="form-control mt-1 block w-full border border-gray-300 rounded-md p-2">
                                    @foreach ($kategoriLayanans as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="konten_layanan" class="block text-sm font-semibold text-gray-700">Konten Layanan</label>
                                <textarea name="konten_layanan" id="konten_layanan" class="form-control mt-1 block w-full border border-gray-300 rounded-md p-2" rows="5"></textarea>
                            </div>

                            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>  
@endsection
