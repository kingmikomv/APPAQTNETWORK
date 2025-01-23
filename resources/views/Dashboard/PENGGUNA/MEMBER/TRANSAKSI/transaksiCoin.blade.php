<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">

        <div class="navbar-bg"></div>

        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                
                
                <div class="row no-gutters">
                 
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Semua Riwayat Transaksi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="riwayat">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID Invoice</th>
                                                <th>Nama</th>
                                                <th>Jumlah Coin</th>
                                                <th>Harga</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                                <th>TP</th>
                                                <th>Tanggal Dibayar</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $index => $transaction)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        {{ Str::limit($transaction->external_id, 9, '...') ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if ($transaction->user)
                                                            {{ $transaction->user->name }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->coin_amount }} Coin</td>
                                                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if ($transaction->status == 'PENDING' || $transaction->status == 'pending')
                                                        <span class="badge bg-warning">Menunggu</span>
                                                    @elseif ($transaction->status == 'SUCCESS' || $transaction->status == 'success' || $transaction->status == 'PAID' || $transaction->status == 'paid' || $transaction->status == 'SETTLE' || $transaction->status == 'settle' || $transaction->status == 'complete')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @endif
                                                    
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
                                                        @else
                                                        N/A
                                                        @endif
                                                    </td>
                                                    <td>{{$transaction->payment_channel ?? 'N/A'}}</td>
                                                    <td>
                                                        @if (\Carbon\Carbon::hasFormat($transaction->paid_at, 'Y-m-d\TH:i:s.u\Z') || strtotime($transaction->paid_at))
                                                            {{ \Carbon\Carbon::parse($transaction->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                        @else
                                                            Data tidak valid
                                                        @endif
                                                    </td>
                                                     <td>
                                                        @if ($transaction->status == 'complete' || $transaction->status == 'completed')
                                                        <a href="{{ route('invoice.pdf', $transaction->external_id) }}" class="btn btn-primary">Invoice PDF</a>
                                                        @else
                                                        -
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
