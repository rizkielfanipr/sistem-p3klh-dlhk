<form action="{{ $action }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?')">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="inline-flex items-center p-2 bg-red-500 hover:bg-red-700 text-white text-xs font-semibold rounded">
        <i class="fas fa-trash text-white text-md"></i>
    </button>
</form>