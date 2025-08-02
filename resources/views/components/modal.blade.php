{{-- resources/views/components/modal.blade.php --}}

@props([
    'id' => 'myModal',      // ID unik untuk modal ini
    'show' => false,        // Kontrol visibilitas awal modal (boolean)
    'maxWidth' => '2xl',    // Ukuran modal (sm, md, lg, xl, 2xl, etc.)
    'closeable' => true,    // Bisa ditutup dengan Esc atau klik backdrop
    'title' => '',          // Judul modal
])

<div
    id="{{ $id }}"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 transition-all duration-300 ease-in-out"
    style="display: {{ $show ? 'block' : 'none' }};"
    tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-modal="true"
    data-closeable="{{ $closeable ? 'true' : 'false' }}" {{-- Tambahkan data attribute untuk JS Escape key --}}
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"
        onclick="window.closeModal__{{ $id }}('{{ $id }}', {{ $closeable ? 'true' : 'false' }})"
        style="opacity: {{ $show ? '1' : '0' }};"
    ></div>

    {{-- Modal Content --}}
    <div
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all duration-300 ease-in-out sm:w-full sm:mx-auto
        {{ match($maxWidth) {
            'sm' => 'sm:max-w-sm',
            'md' => 'sm:max-w-md',
            'lg' => 'sm:max-w-lg',
            'xl' => 'sm:max-w-xl',
            '2xl' => 'sm:max-w-2xl',
            '3xl' => 'sm:max-w-3xl',
            '4xl' => 'sm:max-w-4xl',
            '5xl' => 'sm:max-w-5xl',
            '6xl' => 'sm:max-w-6xl',
            '7xl' => 'sm:max-w-7xl',
            default => 'sm:max-w-2xl',
        } }}"
        style="
            transform: {{ $show ? 'translateY(0) scale(1)' : 'translateY(4vh) scale(0.95)' }};
            opacity: {{ $show ? '1' : '0' }};
            margin-top: {{ $show ? 'auto' : '0' }};
            margin-bottom: auto;
        "
    >
        <div class="px-6 py-4">
            <div class="text-lg font-semibold text-gray-900 flex justify-between items-center">
                <span id="{{ $id }}Label">{{ $title }}</span>
                <button type="button" onclick="window.closeModal__{{ $id }}('{{ $id }}')" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mt-4 text-gray-700">
                {{ $slot }}
            </div>
        </div>

        @if (isset($footer))
            <div class="px-6 py-4 bg-gray-100 text-right">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    // Pastikan fungsi-fungsi ini unik untuk setiap modal
    // Kita gunakan window.openModal__ID_MODAL dan window.closeModal__ID_MODAL
    // untuk menghindari konflik nama fungsi jika ada banyak modal di halaman yang sama.

    // Fungsi untuk membuka modal ini
    window.openModal__{{ $id }} = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'block';
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                modal.querySelector('.bg-opacity-75').style.opacity = '1';
                modal.querySelector('.transform').style.transform = 'translateY(0) scale(1)';
                modal.querySelector('.transform').style.opacity = '1';
                const firstFocusable = modal.querySelector('a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }, 10);
        }
    };

    // Fungsi untuk menutup modal ini
    window.closeModal__{{ $id }} = function(id, closeable = true) {
        if (!closeable && event && event.currentTarget.className.includes('bg-opacity-75')) {
             return; // Jika diklik backdrop dan tidak closeable, jangan tutup
        }
        const modal = document.getElementById(id);
        if (modal) {
            modal.querySelector('.bg-opacity-75').style.opacity = '0';
            modal.querySelector('.transform').style.transform = 'translateY(4vh) scale(0.95)';
            modal.querySelector('.transform').style.opacity = '0';

            setTimeout(() => {
                modal.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }
    };

    // Inisialisasi awal untuk transisi yang benar jika modal sudah 'show'
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('{{ $id }}');
        if (modal) {
            if (modal.style.display === 'block') {
                document.body.classList.add('overflow-hidden');
                modal.querySelector('.bg-opacity-75').style.opacity = '1';
                modal.querySelector('.transform').style.transform = 'translateY(0) scale(1)';
                modal.querySelector('.transform').style.opacity = '1';
            } else {
                modal.querySelector('.bg-opacity-75').style.opacity = '0';
                modal.querySelector('.transform').style.transform = 'translateY(4vh) scale(0.95)';
                modal.querySelector('.transform').style.opacity = '0';
            }
        }
    });

    // Event listener untuk tombol Escape, hanya berlaku untuk modal ini
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('{{ $id }}');
        if (event.key === 'Escape' && modal && modal.style.display === 'block') {
            const closeable = modal.dataset.closeable === 'true';
            if (closeable) {
                window.closeModal__{{ $id }}('{{ $id }}', true);
            }
        }
    });
</script>