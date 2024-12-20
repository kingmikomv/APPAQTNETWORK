<!-- your-view.blade.php -->
<x-dcore.head />
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <x-dcore.modal />

        <div class="main-content">
            <section class="section">
                <!-- MAIN CONTENT -->
                <div class="row">
                    <!-- Pemberitahuan Section -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-info-circle"></i> Pemberitahuan</h4>
                            </div>
                            <div class="card-body">
                                <p style="font-size: 20px;">Pada halaman ini berfungsi sebagai halaman penambahan data
                                    OLT EPON HA7304</p>
                                <hr>
                                <p class="mb-0" style="font-size: 20px;">Jika OLT anda tidak mempunyai IP Public
                                    silahkan tambahkan disini, dan jika anda bingung silahkan kunjungi link youtube kami
                                    untuk pemasangan VPN pada OLT anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-info-circle"></i> Daftar Port VPN</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                                            data-target="#exampleModal">
                                            <i class="fas fa-shopping-cart"></i> Beli Port
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                                            data-target="#bayarDisini">
                                            <i class="fas fa-shopping-cart"></i> Bayar Disini
                                        </button>
                                    </div>
                                </div>

                                <table class="table" id="oltTable2">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>ID Pembelian</td>
                                            <td>Tanggal Pemesanan</td>
                                            <td>Status Pembelian</td>
                                            <td>Status Port</td>
                                            <td>Port</td>
                                            <td>Option</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($port as $ports)


                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$ports->pembelian_id}}</td>
                                            <td>{{$ports->created_at->format('d M Y - H:i:s')}}</td>
                                            <td>
                                                @if($ports->status_pembelian == 1)
                                                Lunas
                                                @elseif($ports->status_pembelian == 2)
                                                Pembayaran Sedang Di Cek
                                                @else
                                                Belum Di Bayar
                                                @endif
                                            </td>
                                            <td>
                                                @if($ports->status_port == 1)
                                                Running
                                                @elseif($ports->status_port == 2)
                                                Pembayaran Sedang Di Cek
                                                @else
                                                Belum Di Bayar
                                                @endif
                                            </td>
                                            <td>
                                                @if($ports->port == null)
                                                ******
                                                @else
                                                {{$ports->port ?? 'Belum Di Bayar'}}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        Dropdown button
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">Action</a>
                                                        <a class="dropdown-item" href="#">Another action</a>
                                                        <a class="dropdown-item" href="#">Something else here</a>
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
                    <!-- Form to Add VPN -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tambah OLT</h4>
                            </div>
                            <div class="card-body">

                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#addModalOlt">
                                Tambah OLT
                            </button>
                            <table class="table mt-2" id="oltTable3">
                                <thead>
                                  <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama OLT</th>
                                    <th scope="col">IP OLT:PORT OLT</th>
                                    <th scope="col">PORT VPN</th>
                                    <th scope="col">Akses Cepat</th>
                                    <th scope="col">Action</th>

                                  </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($olts as $olt)


                                  <tr>
                                    <th scope="row">{{$no++}}</th>
                                    <td>{{$olt->site}}</td>
                                    <td>{{$olt->ipolt}}:{{$olt->portolt}}</td>
                                    <td>{{$olt->portvpn}}</td>
                                    <td>
                                        <a href="http://id-1.aqtnetwork.my.id:{{ $olt->portvpn }}" target="_blank" class="btn btn-primary">Akses Cepat</a>
                                    </td>
                                    <td>
                                        asd
                                    </td>

                                  </tr>
                                  
                                  @endforeach
                                </tbody>
                              </table>

                            </div>
                        </div>
                    </div>

                    <!-- Data OLT Section -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data VPN OLT</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <!-- Button trigger modal -->
                                   

                                    <table class="table mt-2" id="oltTable">
                                        <thead>
                                          <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Nama Akun</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Password</th>
                                            <th scope="col">IP Address</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($oltvpn as $vpnolt)


                                          <tr>
                                            <th scope="row">{{$no++}}</th>
                                            <td>{{$vpnolt->namaakun}}</td>
                                            <td>{{$vpnolt->username}}</td>
                                            <td>{{$vpnolt->password}}</td>
                                            <td>{{$vpnolt->ipaddress}}</td>

                                          </tr>
                                          
                                          @endforeach
                                        </tbody>
                                      </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END MAIN CONTENT -->
            </section>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Beli Port</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('beliport') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>ID Pembeli</label>
                                <input type="text" name="nama" class="form-control"
                                    value="{{ auth()->user()->unique_id }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Pembelian Port</label>
                                <select class="form-control" name="banyaknya" required>
                                    <option disabled selected value="">Pilih Banyaknya</option>
                                    @for ($i = 1; $i <= 5; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Checkout!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        <div class="modal fade" id="bayarDisini" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Bayar Disini</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('bayar') }}" method="get">

                        <div class="modal-body">
                            <div class="form-group">
                                <label>ID Pembelian</label>
                                <select class="form-control" name="pembelian_id" required>
                                    <option disabled selected value="">Pilih ID Pembayaran</option>
                                    @foreach ($port2 as $ports2)
                                    @if($ports2->status_pembelian != 1 && $ports2->status_pembelian != 2)
                                    <option value="{{ $ports2->pembelian_id }}">
                                        {{ $ports2->pembelian_id }}
                                    </option>
                                    @endif
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Checkout!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>





        <div class="modal fade" id="addModalOlt" tabindex="-1" role="dialog" aria-labelledby="addVpnModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVpnModalLabel">Tambah OLT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="vpnForm" action="{{ route('tambaholt') }}" method="post">
                        @csrf
                    <div class="modal-body">
                      
                            <div class="form-group">
                                <label for="namaAkun">Nama OLT</label>
                                <input type="text" class="form-control" placeholder="Nama Akun" name="site" id="namaAkunInput">
                            </div>
        
                            <div class="form-group">
                                <label for="username">IP OLT</label>
                                <input type="text" class="form-control" placeholder="IP OLT (Cth. 192.168.xxx.xxx)" name="ipolt">
                            </div>
        
                            <div class="form-group">
                                <label for="password">Port OLT</label>
                                <input type="text" class="form-control" placeholder="Port OLT (Cth. 80, 8080)" name="portolt">
                            </div>
                            <div class="form-group">
                                <label for="password">IP Address VPN</label>
                                <input type="text" class="form-control" placeholder="IP Address VPN" name="ipvpn">
                            </div>
                            <div class="form-group">
                                <label for="password">Port VPN</label>
                                <input type="text" class="form-control" placeholder="Port VPN" name="portvpn">
                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="Buat VPN">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
                </div>
            </div>
        </div>





        <x-dcore.footer />
    </div>
</div>


<x-dcore.script />
<!-- DataTables Script -->
<script>
    $(document).ready(function () {
        $('#oltTable').DataTable();
        $('#oltTable2').DataTable();
        $('#oltTable3').DataTable();

    });

</script>

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '{{ session("success") }}',
        showConfirmButton: true
    });

</script>
@elseif (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '{{ session("error") }}',
        showConfirmButton: true
    });

</script>
@endif
