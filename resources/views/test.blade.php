<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelayanan Persetujuan Lingkungan</title>
    
    <!-- Vite CSS and JS -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js') <!-- Untuk file JavaScript yang dibundel oleh Vite -->

    <!-- Optional: Laravel Mix for additional assets -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

</head>

<body class="font-montserrat">

    <!-- Panggil Komponen Navbar -->
    <x-navbar />
    <x-hero />
    <x-menu />
    <x-news />
    <x-faq />
    <x-footer />

</body>

</html>
