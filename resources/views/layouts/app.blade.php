<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pelayanan Persetujuan Lingkungan')</title>
    
    <!-- Vite CSS and JS -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @stack('scripts')


    <!-- Optional: Laravel Mix for additional assets -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>

<body class="font-montserrat">

    @yield('content')

</body>

</html>
