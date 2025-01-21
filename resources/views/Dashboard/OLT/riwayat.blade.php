<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <div class="row no-gutters">
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Transaksi Pending</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if ($transactionsPending->isEmpty())
                                        <p>Belum ada transaksi pembelian coin.</p>
                                    @else
                                        <table class="table table-bordered" id="pending">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Jumlah Coin</th>
                                                    <th>Harga</th>
                                                    <th>Status</th>
                                                   
                                                    <th>Tanggal</th>
                                                    <th>Option</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactionsPending as $index => $transaction)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $transaction->coin_amount }} Coin</td>
                                                        <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                        <td>
                                                            <span class="badge bg-warning">
                                                                Pending / Checking
                                                            </span>
                                                        </td>
                                                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                                    Action
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="{{ route('payment.process', $transaction->id) }}">Bayar</a>
                                                                    <form action="" method="POST">
                                                                        @csrf
                                                                        <button class="dropdown-item" type="submit">Batalkan</button>
                                                                    </form>
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
                                <h4><i class="fas fa-history"></i> Riwayat Transaksi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="riwayat">
                                        <thead>
                                            <tr>
                                                <th>#</th>
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
                                                    <td>{{ $transaction->coin_amount }} Coin</td>
                                                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Selesai</span>
                                                    </td>
                                                    <td>
                                                        
                                                        @if($transaction->payment_method == 'BANK_TRANSFER')
                                                            Bank Transfer
                                                        @elseif($transaction->payment_method == 'QR_CODE')
                                                            QRIS
                                                        @elseif($transaction->payment_method == 'RETAIL_OUTLET')
                                                            Retail Outlet
                                                        @elseif($transaction->payment_method == 'EWALLET')
                                                          E-Wallet
                                                        @endif
                                                    </td>
                                                    <td>{{$transaction->payment_channel}}</td>
                                                    <td>
                                                        @if (\Carbon\Carbon::hasFormat($transaction->paid_at, 'Y-m-d\TH:i:s.u\Z') || strtotime($transaction->paid_at))
                                                            {{ \Carbon\Carbon::parse($transaction->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                        @else
                                                            Data tidak valid
                                                        @endif
                                                    </td>
                                                    
                                                    
                                                    <td>
                                                        <a href="{{ route('invoice.pdf', $transaction->external_id) }}" class="btn btn-primary">Invoice PDF</a>
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
            </section>
        </div>
        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />

<script>
    $(document).ready(function() {
        $('#pending').DataTable();
        $('#riwayat').DataTable();

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
