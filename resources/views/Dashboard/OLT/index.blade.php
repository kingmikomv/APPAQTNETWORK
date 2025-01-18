<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row no-gutters"> <!-- Remove gutter space between columns -->
                    <!-- Welcome Card -->
                    {{-- <div class="col-lg-12">
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
                    </div> --}}
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-coins"></i> Beli Coin</h4>
                            </div>
                            <div class="card-body">
                                <!-- Display User's Current Coins -->
                                <div class="alert alert-primary d-flex align-items-center" role="alert"
                                    style="font-size: 1rem;">
                                    <i class="fas fa-coins"
                                        style="color: #ffc107; font-size: 1.5rem; margin-right: 10px;"></i>
                                    <div>
                                        <strong>Jumlah Coin Anda Saat Ini:</strong>
                                        <span style="font-weight: bold; font-size: 1.2rem;">
                                            {{ auth()->user()->total_coin }} Coin</span>
                                    </div>
                                </div>

                                <a class="btn btn-primary btn-block" data-toggle="collapse" href="#coins"
                                    role="button" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-coins"></i> Beli Coin
                                </a>
                                <div class="collapse" id="coins">
                                    <div class="card card-body">
                                        <form id="purchaseCoinsForm" method="POST"
                                            action="{{ route('purchase.coin') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="Coins" style="font-weight: bold;">Pilih Jumlah
                                                    Coin</label>
                                                <select name="coin_amount" id="Coins" class="form-control">
                                                    <option value="5">5 Coin - Rp10,500</option>
                                                    <option value="10">10 Coin - Rp21,000</option>
                                                    <option value="20">20 Coin - Rp39,500</option>
                                                    <option value="50">50 Coin - Rp97,000</option>
                                                    <option value="100">100 Coin - Rp152,500</option>
                                                    <option value="200">200 Coin - Rp295,000</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <button type="submit" class="btn btn-primary btn-block">
                                                            <i class="fas fa-shopping-cart"></i> Beli Coin
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <a class="btn btn-primary btn-block"
                                                            href="{{ route('coin.history') }}">
                                                            <i class="fas fa-history"></i> Riwayat Pembelian Coin
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>

                                    </div>
                                </div>

                                <!-- Purchase Coins Form -->




                                <a class="btn btn-warning btn-block mt-3" data-toggle="collapse" href="#collapseExample"
                                    role="button" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-network-wired"></i> Beli Port OLT
                                </a>
                                <div class="collapse" id="collapseExample">
                                    <div class="card card-body">
                                        <div class="row">
                                            <!-- Paket Per Bulan -->
                                            <div class="col-md-4">
                                                <div class="card border-primary shadow-sm">
                                                    <div class="card-header bg-primary text-white text-center">
                                                        <h5>Paket Per Bulan</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <p class="mb-1">Harga: <strong>20 Coin</strong></p>
                                                        <p class="mb-3">Durasi: 30 Hari</p>
                                                        <button class="btn btn-primary btn-block"
                                                            onclick="location.href='{{ route('beli.paket', ['paket' => 'bulan']) }}'">
                                                            Beli Sekarang
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paket Per Tahun -->
                                            <div class="col-md-4">
                                                <div class="card border-success shadow-sm">
                                                    <div class="card-header bg-success text-white text-center">
                                                        <h5>Paket Per Tahun</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <p class="mb-1">Harga: <strong>60 Coin</strong></p>
                                                        <p class="mb-3">Durasi: 12 Bulan</p>
                                                        <button class="btn btn-success btn-block"
                                                            onclick="location.href='{{ route('beli.paket', ['paket' => 'tahun']) }}'">
                                                            Beli Sekarang
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paket Permanen -->
                                            <div class="col-md-4">
                                                <div class="card border-danger shadow-sm">
                                                    <div class="card-header bg-danger text-white text-center">
                                                        <h5>Paket Permanen</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <p class="mb-1">Harga: <strong>250 Coin</strong></p>
                                                        <p class="mb-3">Durasi: Selamanya</p>
                                                        <button class="btn btn-danger btn-block"
                                                            onclick="location.href='{{ route('beli.paket', ['paket' => 'permanen']) }}'">
                                                            Beli Sekarang
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Form to Add VPN -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tambah OLT</h4>
                            </div>
                            <div class="card-body table-responsive">

                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addModalOlt">
                                    Tambah OLT
                                </button>
                                <table class="table mt-2" id="oltTable3">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Nama OLT</th>
                                            <th scope="col">IP OLT</th>
                                            <th scope="col">PORT OLT</th>
                                            <th scope="col">PORT VPN</th>
                                            <th scope="col">Akses Cepat</th>
                                            <th scope="col">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($olts as $olt)
                                            <tr>
                                                <th scope="row">{{ $no++ }}</th>
                                                <td>{{ $olt->site }}</td>
                                                <td>{{ $olt->ipolt }}</td>
                                                <td>{{ $olt->portolt }}</td>
                                                <td>{{ $olt->portvpn }}</td>
                                                <td>
                                                    <a href="http://id-1.aqtnetwork.my.id:{{ $olt->portvpn }}"
                                                        target="_blank" class="btn btn-primary">Akses Cepat</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                                data-target="#scriptModal"
                                                                onclick="generateScript('{{ $olt->ipolt }}', '{{ $olt->portvpn }}', '{{ $olt->portolt }}')">
                                                                Lihat Script
                                                            </a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('hapusolt', $olt->id) }}">Hapus</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
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

                    <!-- Data OLT Section -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data VPN</h4>
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
                                            @foreach ($datavpn as $vpnolt)
                                                <tr>
                                                    <th scope="row">{{ $no++ }}</th>
                                                    <td>{{ $vpnolt->namaakun }}</td>
                                                    <td>{{ $vpnolt->username }}</td>
                                                    <td>{{ $vpnolt->password }}</td>
                                                    <td>{{ $vpnolt->ipaddress }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- END OF CENTER CONTENT -->
            </section>
        </div>
        <x-dcore.footer />
        

        <div class="modal fade" id="addModalOlt" tabindex="-1" role="dialog" aria-labelledby="addVpnModalLabel"
            aria-hidden="true">
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
                                <input type="text" class="form-control" placeholder="Nama Akun" name="site"
                                    id="namaAkunInput">
                            </div>

                            <div class="form-group">
                                <label for="username">IP OLT</label>
                                <input type="text" class="form-control"
                                    placeholder="IP OLT (Cth. 192.168.xxx.xxx)" name="ipolt">
                            </div>

                            <div class="form-group">
                                <label for="password">Port OLT</label>
                                <input type="text" class="form-control" placeholder="Port OLT (Cth. 80, 8080)"
                                    name="portolt">
                            </div>
                            <div class="form-group">
                                <label for="password">IP Address VPN</label>
                                <input type="text" class="form-control" placeholder="IP Address VPN"
                                    name="ipvpn">
                            </div>
                            <div class="form-group">
                                <label for="password">Port VPN</label>
                                <select class="form-control" id="exampleFormControlSelect1" name="portvpn">
                                    <option disabled selected value>Pilih Port VPN</option>
                                    @foreach ($availablePorts as $portvvppnn)
        @if(is_object($portvvppnn) && isset($portvvppnn->port))
            <option value="{{ $portvvppnn->port }}">{{ (int) $portvvppnn->port }}</option>
        @elseif(is_string($portvvppnn))
            <option value="{{ $portvvppnn }}">{{ (int) $portvvppnn }}</option>
        @endif
    @endforeach
                                </select>
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





        <!-- Modal for MikroTik Script -->
        <div class="modal fade" id="scriptModal" tabindex="-1" aria-labelledby="scriptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVpnModalLabel">Script MikroTik</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea id="mikrotikScript" class="form-control" rows="10" style="width: 100%; height: 300px; resize: none;"
                            disabled></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="copyToClipboard()">Copy</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<x-dcore.script />
