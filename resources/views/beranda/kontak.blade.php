@extends('layouts.user')

@section('title', 'Kontak Kami')
@section('description', 'Hubungi kami untuk pertanyaan, masukan, atau kolaborasi.')
@section('breadcrumb', 'Kontak')

@section('content')
<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto bg-white rounded-lg p-6 lg:p-10 border border-gray-200">

        {{-- Header Section: Centered and Clean --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#03346E] tracking-tight sm:text-5xl">
                <i class="fas fa-headset text-[#03346E] mr-4"></i> Kontak Kami
            </h1>
            <p class="mt-4 text-base text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Kami siap sedia membantu Anda. Jangan ragu untuk menghubungi kami melalui informasi di bawah ini
                untuk pertanyaan, masukan, atau peluang kolaborasi.
            </p>
        </div>

        {{-- Unified Contact & Map Container --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-stretch p-6 border border-[#03346E] rounded-lg bg-blue-50/50">

            {{-- Left Section: DLHK Contact Information --}}
            <div>
                <h3 class="text-xl font-bold text-[#03346E] mb-5 border-b-2 pb-2 border-[#03346E]">
                    Dinas Lingkungan Hidup dan Kehutanan
                    <br>
                    Daerah Istimewa Yogyakarta
                    </br>
                </h3>
                <div class="space-y-4 text-gray-700 text-base">
                    <p class="flex items-start">
                        <i class="fas fa-map-marker-alt text-[#03346E] text-xl mr-3 mt-1 flex-shrink-0"></i>
                        <span>Jalan Argulobang Nomor 19, Baciro, Gondokusuman, Yogyakarta 55225</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-phone text-[#03346E] text-lg mr-3"></i>
                        <span>(0274) 588518</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-fax text-[#03346E] text-lg mr-3"></i> {{-- Added fax icon --}}
                        <span>(0274) 512447</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-[#03346E] text-lg mr-3"></i>
                        <span><a href="mailto:dlhk@jogjaprov.go.id" class="text-[#03346E] hover:underline">dlhk@jogjaprov.go.id</a></span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-globe text-[#03346E] text-lg mr-3"></i> {{-- Added globe icon for website --}}
                        <span><a href="https://dlhk.jogjaprov.go.id/" target="_blank" rel="noopener noreferrer" class="text-[#03346E] hover:underline">dlhk.jogjaprov.go.id</a></span>
                    </p>
                    <p class="flex items-center">
                        <i class="fab fa-instagram text-[#03346E] text-lg mr-3"></i>
                        <span><a href="https://www.instagram.com/dlhkdiy/" target="_blank" rel="noopener noreferrer" class="text-[#03346E] hover:underline">@dlhkdiy</a></span>
                    </p>
                    <p class="flex items-center">
                        <i class="fab fa-youtube text-[#03346E] text-lg mr-3"></i>
                        <span><a href="https://www.youtube.com/@dlhkdiy" target="_blank" rel="noopener noreferrer" class="text-[#03346E] hover:underline">dlhk diy</a></span>
                    </p>
                </div>
            </div>

            {{-- Right Section: Google Maps --}}
            <div>
                <div class="relative w-full flex-grow" style="padding-top: 100%;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126479.0525340131!2d110.31835648819009!3d-7.845736031899162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59b7e162014f%3A0x900075df7e90db72!2sDinas%20Lingkungan%20Hidup%20dan%20Kehutanan%20Daerah%20Istimewa%20Yogyakarta!5e0!3m2!1sid!2sid!4v1751940869114!5m2!1sid!2sid"
                        style="border:0; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="rounded-lg"
                    ></iframe>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection