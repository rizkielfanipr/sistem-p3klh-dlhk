<?php
// Social media links
$socialLinks = [
    'instagram' => 'https://instagram.com',
    'x' => 'https://x.com',
    'facebook' => 'https://facebook.com',
    'youtube' => 'https://youtube.com'
];

// Current year
$currentYear = date("Y");
?>

<footer class="border-t border-gray-300 py-14 mt-20">
    <div class="max-w-screen-xl mx-auto flex flex-col sm:flex-row justify-between items-center px-4 sm:px-20">
        <!-- Logo Section -->
        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
            <img src="{{ asset('logo-dlhk.png') }}" alt="Logo" class="h-12">
        </div>
        
        <!-- Social Media Icons Section -->
        <div class="flex space-x-6 flex-wrap justify-center sm:justify-start">
            <?php foreach ($socialLinks as $platform => $url): ?>
                <a href="<?php echo $url; ?>" target="_blank" class="text-gray-600 hover:text-blue-600 mb-2 sm:mb-0">
                    <i class="fab fa-<?php echo $platform; ?> fa-lg"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer Text Section -->
    <div class="text-center text-gray-600 mt-6">
        <p>&copy; <?php echo $currentYear; ?> Dinas Lingkungan Hidup dan Kehutanan Daerah Istimewa Yogyakarta</p>
    </div>
</footer>
