<!DOCTYPE html>
<html lang="en" x-data="{ dropdownOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Halaman Pengguna')</title>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CDN / Eksternal --}}
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>
<body class="flex flex-col min-h-screen text-gray-900">

    @include('components.navbar')

    {{-- We'll add some top padding to the main content below to avoid overlap --}}
    @include('components.page-header', [
        'title' => View::hasSection('title') ? trim($__env->yieldContent('title')) : 'Judul Halaman',
        'description' => View::hasSection('description') ? trim($__env->yieldContent('description')) : 'Deskripsi halaman.'
    ])

    <div class="flex-grow">
        <main class="container mx-auto px-4 py-8 mt-16"> {{-- Added mt-16 to push content below a fixed navbar --}}
            {{-- Breadcrumb Section --}}
            @hasSection('breadcrumb')
                <nav class="bg-gray-50 p-3 rounded-lg shadow-sm mb-6 text-sm text-gray-700" aria-label="breadcrumb">
                    <ol class="list-none p-0 inline-flex">
                        <li><a href="{{ url('/') }}" class="text-blue-500 hover:text-blue-700">Home</a></li>
                        <span class="mx-2">/</span>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Push script dari komponen --}}
    @stack('scripts')
    @yield('scripts')

    @include('components.footer')
</body>
</html>
