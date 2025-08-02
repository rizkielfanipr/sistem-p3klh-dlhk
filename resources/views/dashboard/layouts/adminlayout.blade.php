<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false, sidebarCollapse: false, dropdownOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CDN / Eksternal --}}
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<body class="font-montserrat bg-gray-100">

    @include('dashboard.partials.sidebar')

    <!-- Overlay untuk mobile -->
    <div
        class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
    ></div>

    @include('dashboard.partials.header')

    <div
        class="flex flex-col min-h-screen transition-all duration-300 ease-in-out pt-16"
        :class="sidebarCollapse ? 'md:ml-20' : 'md:ml-64'"
    >
        <main class="flex-1 p-6 rounded-lg bg-white shadow-lg transition-all duration-300 ease-in-out">
            
            <!-- Page Title + Breadcrumb -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">@yield('title', 'Halaman')</h2>
                <nav class="text-sm text-gray-600">
                    <ul class="flex items-center space-x-2">
                        <li><a href="#" class="hover:text-blue-600">Home</a></li>
                        <li>&gt;</li>
                        <li><span class="text-blue-600">@yield('breadcrumb', 'Halaman')</span></li>
                    </ul>
                </nav>
            </div>

            <!-- Main Content -->
            @yield('content')
        </main>
    </div>

    {{-- Push script dari komponen --}}
    @stack('scripts')
@yield('scripts')
</body>
</html>
