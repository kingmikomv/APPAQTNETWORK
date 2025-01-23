

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
                                            <td>Total Coin</td>
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
                                            <td>{{$mb->total_coin}} Coin</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                      Option
                                                    </button>
                                                    <div class="dropdown-menu">
                                                      <a class="dropdown-item" href="{{route('daftarvpn', ['unique_id' => $mb->unique_id])}}"><i class="fas fa-eye"></i> Daftar VPN</a>
                                                      <a class="dropdown-item" href="{{route('daftarmikrotik', ['unique_id' => $mb->unique_id])}}"><i class="fas fa-eye"></i> Daftar MikroTik</a>
                                                      <a class="dropdown-item" href="#" data-toggle="modal" data-target="#sendCoinModal" data-id="{{ $mb->id }}" data-name="{{ $mb->name }}">
                                                        <i class="fas fa-arrow-right"></i> Kirim Coin
                                                    </a>                                                      
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
        <div class="modal fade" id="sendCoinModal" tabindex="-1" role="dialog" aria-labelledby="sendCoinModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('send.coin') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="sendCoinModalLabel">Kirim Coin</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="userId">
                            <div class="form-group">
                                <label for="recipientName">Nama</label>
                                <input type="text" class="form-control" id="recipientName" readonly>
                            </div>
                            <div class="form-group">
                                <label for="coinAmount">Jumlah Coin</label>
                                <input type="number" class="form-control" name="coin_amount" id="coinAmount" required min="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
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

        $('#sendCoinModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var userId = button.data('id');
    var userName = button.data('name');

    var modal = $(this);
    modal.find('#userId').val(userId);
    modal.find('#recipientName').val(userName);
});

    });

</script>

@if (session('success'))
              <script>
                  Swal.fire({
                      icon: 'success',
                      title: '{{ session('success') }}',
                      showConfirmButton: true
                  });
              </script>
          @elseif (session('error'))
              <script>
                  Swal.fire({
                      icon: 'error',
                      title: '{{ session('error') }}',
                      showConfirmButton: true
                  });
              </script>
          @endif