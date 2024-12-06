<!DOCTYPE html>
<html lang="en">

<x-depan.head />
<style>
  .gallery-section {
  background-color: #f8f9fa; /* Background terang */
  padding: 40px 0;
}

.gallery-wrapper {
  display: flex;
  gap: 10px; /* Jarak antar gambar */
}

.gallery-photo img {
  display: block;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, filter 0.3s ease, opacity 0.3s ease;
}

/* Foto Utama */
.main-photo img {
  width: 200px; /* Ukuran lebih besar untuk foto utama */
  height: auto;
  filter: blur(0);
  opacity: 1;
  transform: scale(1.05);
}

/* Foto Blurred */
.blurred-photo img {
  width: 150px;
  height: auto;
  filter: blur(8px);
  opacity: 0.5;
  transform: scale(0.9);
}

/* Efek Hover */
.blurred-photo:hover img {
  filter: blur(2px);
  opacity: 0.8;
  transform: scale(1);
}

.main-photo:hover img {
  transform: scale(1.1);
}

</style>

<body class="index-page">

<x-depan.navbar />

  <main class="main">

    <x-depan.heroundian />

    <x-depan.undian :daftarundian="$daftarundian"/>
    
   
   <!-- /Faq Section -->

    
    <!-- Contact Section -->
    <x-depan.kontak />
  <!-- /Contact Section -->

  </main>

 <x-depan.footer />

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
 <x-depan.script />

</body>

</html>