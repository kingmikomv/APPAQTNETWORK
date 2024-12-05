<section id="undian" class="services section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Undian Hadiah</h2>
        <p>Informasi Undian dan Pemenang Undian ada disini</p>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="row justify-content-center gy-4">
            <!-- Pusatkan Formulir -->

            <div class="col-lg-12 col-md-8" data-aos="fade-up" data-aos-delay="150">
                <div class="service-item">
                    <table class="table" id="untianTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Undian</th>
                                <th>Site / Lokasi</th>
                                <th>Hadiah Undian</th>
                                <th>Tanggal Dimulai</th>
                                <th>Pemenang Undian</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($daftarundian as $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $row->unique_undian }}</td>
                                <td>{{ $row->site }}</td>
                                <td>{{ $row->hadiah }}</td>
                                <td>{{ $row->tanggal }}</td>
                                <td>{{ $row->pemenang ?? 'Belum Diundi' }}</td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- End Service Item -->

        </div><!-- End Row -->

    </div><!-- End Container -->

</section><!-- End Section -->
