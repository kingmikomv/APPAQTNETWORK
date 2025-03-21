<section id="undian" class="services section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-danger">🎉 Daftar Undian Berhadiah 🎊</h2>
            <p class="text-muted">Siapa yang beruntung kali ini? Lihat daftar undian di bawah ini!</p>
        </div>
    </div><!-- End Section Title -->

    <div class="container py-5">
        <div class="row g-4">
            @php $no = 1; @endphp
            @if (!empty($daftarundian) && is_array($daftarundian))
            @foreach ($daftarundian as $row)
            @php $modalId = 'modalPemenang' . $no; @endphp
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-4 p-3 bg-light position-relative">
                    <span class="position-absolute top-0 start-50 translate-middle badge bg-danger">
                        #{{ $no++ }}
                    </span>
                    <img src="{{ 'https://biller.aqtnetwork.my.id/' . $row['foto_undian'] }}" 
    class="card-img-top rounded-3"
    alt="Undian Image" 
    style="width: 200px; height: 200px; object-fit: cover; display: block; margin: auto;">

                    <div class="card-body text-center">
                        <h5 class="fw-bold text-primary">{{ $row['nama_undian'] }}</h5>
                        <p class="mb-2"><strong>Kode:</strong> {{ $row['kode_undian'] }}</p>
                        <p class="text-muted"><strong>Tanggal Kocok:</strong> {{ $row['tanggal_kocok'] }}</p>

                        @if (!empty($row['pemenang']))
                        <div class="alert alert-success py-2 fw-bold">🎉 Pemenang: {{ $row['pemenang'] }}</div>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                            🏆 Lihat Foto Pemenang
                        </button>

                        <!-- Modal Bootstrap -->
                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1"
                            aria-labelledby="modalLabel{{ $modalId }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="modalLabel{{ $modalId }}">Foto Pemenang:
                                            {{ $row['pemenang'] }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        @if (!empty($row['foto_pemenang']))
                                        <img src="{{ 'https://biller.aqtnetwork.my.id/undian/pemenang/' . $row['foto_pemenang'] }}"
                                            class="rounded-3 img-fluid" alt="Foto Pemenang">
                                        @else
                                        <div class="alert alert-warning fw-bold">🏆 PEMENANG BELUM MENGAMBIL HADIAH
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning py-2">Belum Diundi</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="col-12 text-center">
                <div class="alert alert-danger fw-bold">🚨 Tidak ada data undian tersedia</div>
            </div>
            @endif
        </div>
    </div>

    @if (!empty($daftarundian) && is_array($daftarundian))
    <div class="container mt-5 text-center">
        <h3 class="fw-bold text-primary">🎉 Pemenang Undian 🎉</h3>
        <div class="coverflow-gallery mt-4">
            <div class="carousel">
                @foreach ($daftarundian as $row)
                @if (!empty($row['foto_pemenang']))
                <div class="carousel-cell">
                    <div class="winner-card">
                        <div class="winner-image">
                            <img src="{{ url('https://biller.aqtnetwork.my.id/undian/pemenang/' . $row['foto_pemenang']) }}"
                                alt="Foto Pemenang">
                        </div>
                        <div class="winner-info">
                            <h5>{{ $row['pemenang'] }}</h5>
                            <p><strong>Kode:</strong> {{ $row['kode_undian'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    

   
    @endif












</section><!-- End Section -->
