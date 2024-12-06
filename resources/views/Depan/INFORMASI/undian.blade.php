<!DOCTYPE html>
<html lang="en">

<x-depan.head />

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