

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

                   

                    
                </div>
                <!-- END OF CENTER CONTENT -->
            </section>
            
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
