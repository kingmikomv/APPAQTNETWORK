<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row"> <!-- Remove gutter space between columns -->
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-coins"></i> Beli Coin</h4>
                            </div>
                            <div class="card-body">
                                <!-- Display User's Current Coins -->
                                <div class="alert alert-primary d-flex align-items-center" role="alert"
                                    style="font-size: 1rem;">
                                    <i class="fas fa-coins"
                                        style="color: #ffc107;"></i>
                                    <div>
                                        <strong> Jumlah Coin Anda Saat Ini : </strong>
                                        <span class="margin-left: 10px">
                                             {{ auth()->user()->total_coin }} Coin
                                        </span>
                                    </div>
                                </div>

                                <form id="purchaseCoinsForm" method="POST" action="{{ route('purchase.coin') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="Coins" style="font-weight: bold;">Pilih Jumlah Coin</label>
                                        <select name="coin_amount" id="Coins" class="form-control">
                                            <option value="5" data-price="Rp10,500">5 Coin - Rp10,500</option>
                                            <option value="10" data-price="Rp21,000">10 Coin - Rp21,000</option>
                                            <option value="20" data-price="Rp39,500">20 Coin - Rp39,500</option>
                                            <option value="50" data-price="Rp97,000">50 Coin - Rp97,000</option>
                                            <option value="100" data-price="Rp152,500">100 Coin - Rp152,500</option>
                                            <option value="200" data-price="Rp295,000">200 Coin - Rp295,000</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 mt-2">
                                                <button type="button" id="confirmPurchaseBtn" class="btn btn-primary btn-block">
                                                    <i class="fas fa-shopping-cart"></i> Masukan Keranjang
                                                </button>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <a class="btn btn-primary btn-block" href="{{ route('coin.history') }}">
                                                    <i class="fas fa-history"></i> Riwayat Keranjang
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Purchase Coins Form -->




                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-network-wired"></i> Beli Port</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Paket</th>
                                            <th>Coin</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>30 Hari</td>
                                            <td>20 Coin</td>
                                            <td>
                                                <button class="btn btn-primary beli-btn" data-paket="30 Hari" data-coin="20" 
                                                    data-url="{{ route('beli.paket', ['paket' => 'bulan']) }}">
                                                    Beli Sekarang
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>1 Tahun</td>
                                            <td>60 Coin</td>
                                            <td>
                                                <button class="btn btn-primary beli-btn" data-paket="1 Tahun" data-coin="60" 
                                                    data-url="{{ route('beli.paket', ['paket' => 'tahun']) }}">
                                                    Beli Sekarang
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Unlimited</td>
                                            <td>200 Coin</td>
                                            <td>
                                                <button class="btn btn-primary beli-btn" data-paket="Unlimited" data-coin="200" 
                                                    data-url="{{ route('beli.paket', ['paket' => 'permanen']) }}">
                                                    Beli Sekarang
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tambahkan SweetAlert2 -->
                  
                    
                    <!-- Form to Add VPN -->
                    <div class="col-md-12">
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
                                            <th scope="col">Expire</th>
                                            <th scope="col">Jml. Coin</th>
                                            <th scope="col">Tipe PORT</th>
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
                                                    @if ($olt->expire !== null)
                                                        @if ($olt->expire < \Carbon\Carbon::now())
                                                            <!-- Tampilkan jika sudah expired, tanpa menampilkan tanggal -->
                                                            <span class="badge badge-danger">Expired</span>
                                                        @elseif ($olt->expire <= \Carbon\Carbon::now()->addDays(7))
                                                            <!-- Tampilkan jika akan expired dalam 7 hari -->
                                                            {{ \Carbon\Carbon::parse($olt->expire)->format('Y-m-d') }}
                                                            <span class="badge badge-warning">Akan Expired</span>
                                                        @else
                                                            <!-- Tampilkan tanggal expire jika masih berlaku -->
                                                            {{ \Carbon\Carbon::parse($olt->expire)->format('Y-m-d') }}
                                                        @endif
                                                    @else
                                                        <!-- Tampilkan badge Permanen hanya jika $olt->paket adalah 'permanen' -->
                                                        @if (strtolower($olt->paket) === 'permanen')
                                                            <span class="badge badge-success">Permanen</span>
                                                        @else
                                                            <!-- Jika expire null dan bukan permanen -->
                                                            -
                                                        @endif
                                                    @endif


                                                </td>
                                                <td>{{ $olt->coin }} Coin</td>

                                                <td>{{ $olt->paket }}</td>
                                                
                                                <td>
                                                    <a href="http://id-1.aqtnetwork.my.id:{{ $olt->portvpn }}"
                                                        target="_blank" class="btn btn-primary">
                                                        Akses Cepat
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#"
                                                                data-toggle="modal" data-target="#scriptModal"
                                                                onclick="generateScript('{{ $olt->ipolt }}', '{{ $olt->portvpn }}', '{{ $olt->portolt }}')">
                                                                Lihat Script
                                                            </a>
                                                            <!-- Tampilkan opsi Perpanjang Expire hanya jika tidak permanen -->
                                                            @if ($olt->expire !== null && $olt->expire < \Carbon\Carbon::now() && strtolower($olt->paket) !== 'permanen')
                                                                <a href="{{route('perpanjang.paket', ['paket' => $olt->paket, 'port' => $olt->portvpn, 'unique_id' => auth()->user()->unique_id])}}" class="dropdown-item">Perpanjang
                                                                    Expire</a>
                                                            @endif

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
                                        @if (is_object($portvvppnn) && isset($portvvppnn->port))
                                            <option value="{{ $portvvppnn->port }}">{{ (int) $portvvppnn->port }}
                                            </option>
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
<script>
    $(document).ready(function() {
        $('#oltTable').DataTable();
        $('#oltTable2').DataTable();
        $('#oltTable3').DataTable();

    });
</script>

<script>
    function generateScript(ipolt, portvpn, portolt) {
        const script = `/ip firewall nat\n` +
            `add chain=dstnat comment="generate By AQT Network"\\\n` +
            `dst-port=${portvpn} protocol=tcp\\\n` +
            `action=dst-nat to-addresses=${ipolt} to-ports=${portolt}`;
        document.getElementById('mikrotikScript').value = script;
    }

    function hapusOlt(id) {
        if (confirm("Apakah Anda yakin ingin menghapus OLT ini?")) {
            // Kirim request ke server untuk menghapus OLT
            console.log("Hapus OLT dengan ID:", id);
        }
    }

    function editOlt(id) {
        // Tambahkan logika untuk mengedit OLT
        console.log("Edit OLT dengan ID:", id);
    }

    function copyToClipboard() {
        const textarea = document.getElementById('mikrotikScript');
        textarea.select(); // Select the text
        textarea.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(textarea.value) // Copy the text
            .then(() => {
                alert('Script copied to clipboard!');
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert2 untuk tombol beli paket
        const purchaseButtons = document.querySelectorAll('.beli-btn');
        purchaseButtons.forEach(button => {
            button.addEventListener('click', function () {
                const paket = this.dataset.paket;
                const coin = this.dataset.coin;
                const url = this.dataset.url;

                Swal.fire({
                    title: 'Konfirmasi Pembelian',
                    html: `Apakah Anda yakin ingin membeli paket <b>${paket}</b> dengan harga <b>${coin} Coin</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Beli Sekarang!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect ke URL pembelian
                        window.location.href = url;
                    }
                });
            });
        });

        // SweetAlert2 untuk formulir pembelian koin
        const form = document.getElementById('purchaseCoinsForm');
        const confirmBtn = document.getElementById('confirmPurchaseBtn');
        const coinSelect = document.getElementById('Coins');

        if (form && confirmBtn && coinSelect) {
            confirmBtn.addEventListener('click', function () {
                const selectedOption = coinSelect.options[coinSelect.selectedIndex];
                const coinAmount = selectedOption.value;
                const price = selectedOption.dataset.price;

                Swal.fire({
                    title: 'Konfirmasi Pembelian',
                    html: `Apakah Anda yakin ingin membeli <b>${coinAmount} Coin</b> dengan harga <b>${price}</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Masukan Keranjang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Kirim formulir jika dikonfirmasi
                    }
                });
            });
        }
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
