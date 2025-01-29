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
                                            <div class="col-md-12 mt-2">
                                                <button type="button" id="confirmPurchaseBtn" class="btn btn-primary btn-block">
                                                    <i class="fas fa-shopping-cart"></i> Masukan Keranjang
                                                </button>
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
                    
                     <!-- Transaksi Pending -->
                     <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Transaksi Pending</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if ($transactionsPending->isEmpty())
                                        <p class="text-center">Belum ada transaksi pembelian coin.</p>
                                    @else
                                        <table class="table table-bordered" id="pending">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Jumlah Coin</th>
                                                    <th>Harga</th>
                                                    <th>Status</th>
                                                    <th>Tanggal</th>
                                                    <th>Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactionsPending as $index => $transaction)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $transaction->coin_amount }} Coin</td>
                                                        <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                        <td><span class="badge bg-warning">Pending / Checking</span></td>
                                                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Action
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="{{ route('payment.process', $transaction->id) }}"><i class="fas fa-check"></i> Bayar</a>
                                                                    <a class="dropdown-item" href="{{ route('payment.cancel', $transaction->id) }}"><i class="fas fa-times-circle"></i> Batalkan</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Transaksi Port</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if ($transactionsPending->isEmpty())
                                        <p class="text-center">Belum ada transaksi Port.</p>
                                    @else
                                        <table class="table table-bordered" id="port">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Paket</th>
                                                    <th>Coin</th>
                                                    <th>Port</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactionPort as $indexs => $transactionP)
                                                    <tr>
                                                        <td>{{ $indexs + 1 }}</td>
                                                        <td>{{ $transactionP->paket }} </td>
                                                        <td>{{ $transactionP->coin}} Coin</td>
                                                        <td>{{ $transactionP->port}}</td>
                                                       
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Transaksi -->
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Riwayat Transaksi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="riwayat">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID Invoice</th>
                                                <th>Jumlah Coin</th>
                                                <th>Harga</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                                <th>TP</th>
                                                <th>Tanggal</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $index => $transaction)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if (strtolower($transaction->status) === 'canceled')
                                                            <span class="badge bg-danger">Dibatalkan</span>
                                                        @else
                                                            {{ $transaction->external_id }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->coin_amount }} Coin</td>
                                                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                    <td>
                                                        @php
                                                            $statusBadge = [
                                                                'pending' => 'bg-warning',
                                                                'success' => 'bg-success',
                                                                'paid' => 'bg-success',
                                                                'settle' => 'bg-success',
                                                                'canceled' => 'bg-danger'
                                                            ];
                                                        @endphp
                                                        <span class="badge {{ $statusBadge[strtolower($transaction->status)] ?? 'bg-secondary' }}">
                                                            {{ ucfirst(strtolower($transaction->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $paymentMethods = [
                                                                'BANK_TRANSFER' => 'Bank Transfer',
                                                                'QR_CODE' => 'QRIS',
                                                                'RETAIL_OUTLET' => 'Retail Outlet',
                                                                'EWALLET' => 'E-Wallet'
                                                            ];
                                                        @endphp
                                                        {{ $paymentMethods[$transaction->payment_method] ?? 'Data tidak valid' }}
                                                    </td>
                                                    <td>
                                                        {{ strtolower($transaction->status) === 'canceled' ? 'Dibatalkan' : $transaction->payment_channel }}
                                                    </td>
                                                    <td>
                                                        @if ($transaction->paid_at)
                                                            {{ \Carbon\Carbon::parse($transaction->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                        @else
                                                            {{ strtolower($transaction->status) === 'canceled' ? 'Dibatalkan' : 'Data tidak valid' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (strtolower($transaction->status) === 'canceled')
                                                            -
                                                        @else
                                                            <a href="{{ route('invoice.pdf', $transaction->external_id) }}" class="btn btn-primary">Invoice PDF</a>
                                                        @endif
                                                    </td>
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


    </div>
</div>
<x-dcore.script />

<script>
    $(document).ready(function() {
        $('#pending').DataTable({
            responsive: true,
            autoWidth: false
        });
        $('#riwayat').DataTable({
            responsive: true,
            autoWidth: false
        });
        $('#port').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
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
