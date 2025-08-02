<nav class="bg-white border-b fixed w-full z-50 font-montserrat" x-data="{ open: false, userMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative h-20 flex items-center justify-between">

        <div class="flex items-center space-x-4">
            <a href="#">
                <img src="{{ asset('logo-dlhk.png') }}" alt="Logo" class="h-12 w-auto">
            </a>
        </div>

        <x-navmenu />

        <div class="hidden md:block relative">
            @auth
                @php
                    $hour = now()->timezone('Asia/Jakarta')->format('H');
                    if ($hour >= 5 && $hour < 12) {
                        $greeting = 'Selamat Pagi';
                    } elseif ($hour >= 11 && $hour < 15) {
                        $greeting = 'Selamat Siang';
                    } elseif ($hour >= 15 && $hour < 19) {
                        $greeting = 'Selamat Sore';
                    } else {
                        $greeting = 'Selamat Malam';
                    }
                @endphp
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 focus:outline-none">
                    <div class="flex flex-col items-end">
                        <span class="text-xs text-gray-500">{{ $greeting }},</span>
                        <span class="text-base font-medium text-gray-700">{{ Auth::user()->nama }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-300 bg-gray-200 flex items-center justify-center">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto Profil" class="object-cover w-full h-full">
                        @else
                            <i class="fas fa-user text-gray-600 text-lg"></i>
                        @endif
                    </div>
                </button>

                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50">
                    <ul class="flex flex-col p-2 text-gray-700">
                        <li>
                            {{-- Route untuk Profil Saya ditambahkan --}}
                            <a href="{{ route('profil.my_profile') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                                <i class="fas fa-user"></i> Profil Saya
                            </a>
                        </li>
                        {{-- Menu "Riwayat" Dihilangkan --}}
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="bg-[#03346E] text-white px-4 py-2 rounded-lg hover:border hover:border-blue-700 transition duration-300 text-sm flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>

        <div class="md:hidden">
            <button @click="open = !open" class="text-gray-700 focus:outline-none">
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="md:hidden bg-white border-t px-4 pt-2 pb-4 space-y-2">
        <x-navmenu-mobile />

        <div class="border-t pt-2 mt-2">
            @auth
                @php
                    $hour = now()->timezone('Asia/Jakarta')->format('H');
                    if ($hour >= 5 && $hour < 12) {
                        $greeting = 'Selamat Pagi';
                    } elseif ($hour >= 11 && $hour < 15) {
                        $greeting = 'Selamat Siang';
                    } elseif ($hour >= 15 && $hour < 19) {
                        $greeting = 'Selamat Sore';
                    } else {
                        $greeting = 'Selamat Malam';
                    }
                @endphp
                <div class="flex items-center space-x-2 py-2">
                    <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-300 bg-gray-200 flex items-center justify-center">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto Profil" class="object-cover w-full h-full">
                        @else
                            <i class="fas fa-user text-gray-600 text-base"></i>
                        @endif
                    </div>
                    <div class="flex flex-col items-start">
                        <span class="text-xs text-gray-500">{{ $greeting }},</span>
                        <span class="text-base font-medium text-gray-700">{{ Auth::user()->nama }}</span>
                    </div>
                </div>
                {{-- Route untuk Profil Saya ditambahkan --}}
                <a href="{{ route('profil.my_profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user"></i> Profil Saya
                </a>
                {{-- Menu "Riwayat" Dihilangkan --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-[#03346E] text-white px-4 py-2 rounded-lg hover:border hover:border-blue-700 transition duration-300 text-sm flex items-center gap-2 w-full text-center">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>
    </div>
</nav>