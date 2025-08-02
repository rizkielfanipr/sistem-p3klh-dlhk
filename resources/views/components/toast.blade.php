@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Sukses!</strong>
    <span class="block sm:inline">{{ session('success') }}</span>
    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.196l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 9.5l-2.652-2.651a1.2 1.2 0 0 1 1.697-1.697L10 7.803l2.651-2.652a1.2 1.2 0 0 1 1.697 1.697L11.696 9.5l2.652 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
    </span>
</div>
@endif

@if (session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Error!</strong>
    <span class="block sm:inline">{{ session('error') }}</span>
    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.196l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 9.5l-2.652-2.651a1.2 1.2 0 0 1 1.697-1.697L10 7.803l2.651-2.652a1.2 1.2 0 0 1 1.697 1.697L11.696 9.5l2.652 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
    </span>
</div>
@endif

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Validasi Error!</strong>
    <ul class="mt-2 list-disc list-inside">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.196l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 9.5l-2.652-2.651a1.2 1.2 0 0 1 1.697-1.697L10 7.803l2.651-2.652a1.2 1.2 0 0 1 1.697 1.697L11.696 9.5l2.652 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
    </span>
</div>
@endif
