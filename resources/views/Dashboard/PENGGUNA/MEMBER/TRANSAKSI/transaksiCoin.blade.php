<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">

        <div class="navbar-bg"></div>

        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <!-- Card untuk menghitung jumlah transaksi -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Transaksi Selesai</h4>
                                </div>
                                <div class="card-body">
                                    {{ $transactions->whereIn('status', ['SUCCESS', 'success', 'PAID', 'paid', 'SETTLE', 'settle', 'complete'])->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Transaksi Pending</h4>
                                </div>
                                <div class="card-body">
                                    {{ $transactions->whereIn('status', ['PENDING', 'pending'])->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Pendapatan Bulan Ini</h4>
                                </div>
                                <div class="card-body">
                                    Rp{{ number_format($transactions->whereIn('status', ['SUCCESS', 'success', 'PAID', 'paid', 'SETTLE', 'settle', 'complete'])->filter(function($transaction) {
                                        return \Carbon\Carbon::parse($transaction->paid_at)->isCurrentMonth();
                                    })->sum('price'), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-calendar-alt"></i> Cari Laporan Berdasarkan Tanggal</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('generate.report') }}" method="GET">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="start_date">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="end_date">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cari Laporan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
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
                                                        @elseif ($transaction->status == 'CANCELED' || $transaction->status == 'canceled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
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

<x-dcore.alert />