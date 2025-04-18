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
</head>

<body class="font-montserrat">

    <!-- Panggil Komponen Navbar -->
    <x-navbar />
    <x-hero />
    <x-menu />

</body>

</html>
