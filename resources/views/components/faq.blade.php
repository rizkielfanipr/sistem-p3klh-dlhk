<?php
$faqs = [
  [
    "question" => "Apakah persetujuan teknis bisa paralel dengan persetujuan lingkungan?",
    "answer" => "Persetujuan teknis disusun dan diselesaikan terlebih dahulu karena menjadi prasyarat pengajuan persetujuan lingkungan."
  ],
  [
    "question" => "Usaha Kegiatan apa saja yang menjadi kewenangan Gubernur dalam memproses persetujuan lingkungan?",
    "answer" => "Rencana Usaha Kegiatan yang tercantum dalam Pergub DIY Nomor 116 Tahun 2021 dan diperbarui dengan Pergub DIY Nomor 38 Tahun 2022, serta Rencana Usaha Kegiatan lain yang menjadi kewenangan Kabupaten/Kota, dan kewenangan pusat yang telah dilimpahkan kepada Gubernur/DLHK melalui surat resmi pelimpahan atau pelimpahan yang terdaftar di sistem AMDALNet. Untuk informasi lebih lanjut, silakan merujuk langsung ke Pergub tersebut."
  ],
  [
    "question" => "Apakah pemohon, pelaku usaha, pemrakarsa dapat menyusun dokumen lingkungan sendiri?",
    "answer" => "Pemohon, pelaku usaha, pemrakarsa dapat menyusun dokumen lingkungan UKL UPL sendiri tanpa perlu memiliki sertifikat kompetensi penyusun. Namun, jika dokumen yang disusun adalah AMDAL, maka penyusun harus memiliki sertifikat kompetensi penyusun AMDAL dan minimal terdiri dari tiga orang penyusun yang bersertifikat, yaitu satu ketua dan dua anggota, ditambah tenaga ahli lain sesuai dengan rencana usaha kegiatan."
  ]
];
?>

<div class="w-full max-w-6xl mx-auto mt-12 px-4 sm:px-6 py-6 border border-[#03346E]/20 rounded-lg">
  <h2 class="text-xl sm:text-2xl font-bold text-[#03346E] mb-6">Pertanyaan Umum (FAQ)</h2>

  <?php foreach ($faqs as $faq): ?>
    <div class="faq-item mb-4">
      <button class="faq-title w-full text-left text-sm sm:text-base md:text-lg font-medium text-[#03346E] bg-white border border-gray-200 rounded-lg px-4 py-3 flex items-center justify-between transition-colors duration-300">
        <span><?= $faq['question'] ?></span>
        <i class="fas fa-chevron-down transition-transform duration-300"></i>
      </button>
      <div class="faq-content hidden text-xs sm:text-sm text-gray-700 bg-white border border-gray-200 rounded-lg mt-2 px-4 py-3">
        <?= $faq['answer'] ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- JavaScript FAQ Toggle -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
      const button = item.querySelector('.faq-title');
      const content = item.querySelector('.faq-content');
      const icon = button.querySelector('i');

      button.addEventListener('click', function () {
        const isOpen = !content.classList.contains('hidden');

        document.querySelectorAll('.faq-content').forEach(c => c.classList.add('hidden'));
        document.querySelectorAll('.faq-title i').forEach(i => i.classList.replace('fa-chevron-up', 'fa-chevron-down'));

        if (!isOpen) {
          content.classList.remove('hidden');
          icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        }
      });
    });
  });
</script>
