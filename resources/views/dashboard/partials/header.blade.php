<header class="fixed top-0 left-0 right-0 bg-white border border-b-gray-300 px-4 py-3 flex items-center justify-between z-10">
    <button class="text-gray-600 focus:outline-none md:hidden" @click="sidebarOpen = !sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>

    <div class="flex items-center gap-3 ml-auto relative" x-data="{ dropdownOpen: false }">
        <span class="text-gray-600 hidden md:block">
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
            {{ $greeting }},
            {{ Auth::check() ? Auth::user()->nama : 'User' }}
        </span>

        <button @click="dropdownOpen = !dropdownOpen" class="w-8 h-8 rounded-full overflow-hidden border border-gray-300 bg-gray-200 flex items-center justify-center">
            @if(Auth::check() && Auth::user()->foto)
                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto Profil" class="object-cover w-full h-full">
            @else
                <i class="fas fa-user text-gray-600"></i>
            @endif
        </button>

        @if(Auth::check())
            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute top-full mt-1 right-0 w-48 bg-white shadow-lg rounded-md z-30">
                <ul class="flex flex-col p-2 text-gray-700">
                    <li>
                        <a href="{{ route('profil') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-id-card"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</header>
