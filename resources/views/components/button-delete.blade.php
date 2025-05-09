<form action="{{ $action }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?')">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-700 text-white text-xs font-semibold rounded">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m3.453-9l-.346 9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Delete
    </button>
</form>