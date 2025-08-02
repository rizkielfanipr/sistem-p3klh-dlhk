<tr class="border-b border-gray-200">
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->nama_usaha }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->bidang_usaha }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->lokasi }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->pemrakarsa }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
        <div class="inline-flex items-center justify-center space-x-3">
            <a href="{{ route('pengumuman.show', $item->id) }}"
               class="text-blue-600 hover:text-blue-900"
               title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>

            <a href="{{ route('pengumuman.edit', $item->id) }}"
               class="text-green-600 hover:text-green-900"
               title="Edit Pengumuman">
                <i class="fas fa-edit"></i>
            </a>

            <form action="{{ route('pengumuman.destroy', $item->id) }}"
                  method="POST"
                  class="inline-block"
                  onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-red-600 hover:text-red-900"
                        title="Hapus Pengumuman">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>