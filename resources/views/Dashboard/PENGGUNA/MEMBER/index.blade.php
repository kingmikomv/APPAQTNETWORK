

<x-dcore.head />
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row"> <!-- Remove gutter space between columns -->
                    <!-- Welcome Card -->
                    <div class="col-lg-12"> <!-- Full width column -->
                        <div class="card">
                            <div class="card-body text-center table-responsive">
                                <table class="table" id="tableMember">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama Member</td>
                                            <td>Role</td>
                                            <td>Jumlah VPN</td>
                                            <td>Jumlah Mikrotik</td>
                                            <td>OPT</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($members as $mb)
                                          
                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$mb->name}}</td>
                                            <td>{{$mb->role}}</td>
                                            <td>{{$mb->vpn}}</td>
                                            <td>{{$mb->mikrotik}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                      Option
                                                    </button>
                                                    <div class="dropdown-menu">
                                                      <a class="dropdown-item" href="{{route('daftarvpn', ['unique_id' => $mb->unique_id])}}"><i class="fas fa-eye"></i> Daftar VPN</a>
                                                      <a class="dropdown-item" href="{{route('daftarmikrotik', ['unique_id' => $mb->unique_id])}}"><i class="fas fa-eye"></i> Daftar MikroTik</a>
                                                      <a class="dropdown-item" href="#"><i class="fas fa-trash"></i> Hapus Akun</a>
                                                    </div>
                                                  </div>
                                            </td>
                                        </tr>

                                          
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12"> <!-- Full width column -->
                        <div class="card">
                            <div class="card-body text-center table-responsive">
                                <table class="table" id="tableMember2">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>ID Pembeli</td>
                                            <td>Tanggal Pemesanan</td>
                                            <td>Status Bukti</td>
                                            <td>Total Pesanan</td>
                                            <td>Total Harga</td>
                                            <td>OPT</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($port2 as $pt)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $pt['pembelian_id'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pt['created_at'])->format('d M Y - H:i:s') }}</td>
                                            <td>
                                                @if($pt['status_pembelian'] == 2 && $pt['bukti'] !== null)
                                                Bukti Sudah Dikirim
                                                @elseif($pt['status_pembelian'] == 3 && $pt['bukti'] !== null)
                                                Lunas
                                                @else
                                                Bukti Belum Dikirim
                                                @endif
                                            </td>
                                            <td>{{ $pt['total_count'] }}</td>
                                            <td>Rp. {{ number_format($pt['total_price'], 0, ',', '.') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Option
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" 
                                                           data-toggle="modal" 
                                                           data-target="#modalBuktiPembayaran" 
                                                           data-bukti="{{ isset($pt['bukti']) && $pt['bukti'] ? asset('payment_proofs/' . $pt['bukti']) : '' }}">
                                                            <i class="fas fa-eye"></i> Lihat Bukti Pembayaran
                                                        </a>
                                                        <a class="dropdown-item acc-button" href="#" 
                                                        data-pembelian-id="{{ $pt['pembelian_id'] }}" 
                                                        data-route="{{ route('acc', ['pembelianId' => ':id']) }}">
                                                        <i class="fas fa-eye"></i> ACC
                                                     </a>
                                                     
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>

                    
                </div>
                <!-- END OF CENTER CONTENT -->
            </section>
            
        </div>



        <div class="modal fade" id="modalBuktiPembayaran" tabindex="-1" role="dialog" aria-labelledby="modalBuktiPembayaranLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBuktiPembayaranLabel">Bukti Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <!-- Gambar -->
                        <img id="buktiPembayaranImage" src="" alt="Bukti Pembayaran" class="img-fluid d-none">
                        <!-- Pesan jika tidak ada bukti -->
                        <p id="buktiPembayaranMessage" class="d-none">Belum Ada Bukti Pembayaran</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        







        <x-dcore.footer />
    </div>
</div>
<x-dcore.script />
<script>
    $(document).ready(function () {
        // Initialize DataTable with options
        $('#tableMember').DataTable();
        $('#tableMember2').DataTable();



        $('#modalBuktiPembayaran').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button yang diklik
            var bukti = button.data('bukti'); // Ambil data-bukti
            
            var modal = $(this);
            var image = modal.find('#buktiPembayaranImage');
            var message = modal.find('#buktiPembayaranMessage');

            if (bukti) {
                // Jika ada bukti pembayaran
                image.attr('src', bukti).removeClass('d-none');
                message.addClass('d-none');
            } else {
                // Jika tidak ada bukti pembayaran
                image.addClass('d-none');
                message.removeClass('d-none');
            }
        });
    });

</script>
<script>
    $(document).on('click', '.acc-button', function (e) {
        e.preventDefault();
        let pembelianId = $(this).data('pembelian-id');
        let route = $(this).data('route').replace(':id', pembelianId);

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin meng-ACC pembelian ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ACC',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Lakukan AJAX request ke backend
                $.ajax({
                    url: route,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            location.reload(); // Refresh halaman untuk memperbarui data
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
</script>
