<!DOCTYPE html>
<html lang="en">

<x-depan.head />


<body class="index-page">

<x-depan.navbar />

  <main class="main">

    <x-depan.heroundian :daftarundian="$daftarundian"/>

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
 <script>
        document.addEventListener("DOMContentLoaded", function () {
            var elem = document.querySelector('.carousel');
            var flkty = new Flickity(elem, {
                wrapAround: true,
                freeScroll: false,
                contain: true,
                pageDots: false,
                autoPlay: 3500,
                prevNextButtons: false,
                cellAlign: 'center'
            });
        });

    </script>

<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        var elem = document.querySelector('.undian-carousel');
                        var flkty = new Flickity(elem, {
                            wrapAround: true,
                            freeScroll: false,
                            contain: true,
                            pageDots: false,
                            autoPlay: 3500,
                            prevNextButtons: false,
                            cellAlign: 'center'
                        });
                    });

                </script>
</body>

</html>