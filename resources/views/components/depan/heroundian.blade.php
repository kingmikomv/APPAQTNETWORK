<section id="hero" class="hero section accent-background">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                <h1>Undian Hadiah</h1>
                <p>Selain menyediakan koneksi internet, AQT Network juga ada Undian Hadiah yang dilakukan setiap bulan.
                    Pasang sekarang untuk mendapatkan undian hadiah.</p>
                <div class="d-flex">
                    <a href="#undian" class="btn-get-started">Get Started</a>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img">
                @if (!empty($daftarundian) && is_array($daftarundian))
                <div class="container text-center">
                <div class="coverflow-gallery">
                    <div class="undian-carousel">
                        @foreach ($daftarundian as $row)
                            @if (!empty($row['foto_pemenang']))
                                <div class="undian-carousel-cell">
                                    <div class="undian-winner-card">
                                        <div class="undian-winner-image">

                                            <img src="{{ url('http://localhost:8000/undian/pemenang/' . $row['foto_pemenang']) }}"
                                                alt="Foto Pemenang">
                                       
                                        </div>
                                        <div class="undian-winner-info">
                                            <h5>{{ $row['pemenang'] }}</h5>
                                            <p><strong>Kode:</strong> {{ $row['kode_undian'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                            <img src="{{ asset('assets/img/undian.png')}}" class="img-fluid animated" alt="" style="width: 200em">
                                        @endif
                        @endforeach
                    </div>
                </div>
                </div>

               

                @else
                <img src="{{ asset('assets/img/undian.png')}}" class="img-fluid animated" alt="" style="width: 200em">
                @endif


            </div>
        </div>
    </div>
</section>
