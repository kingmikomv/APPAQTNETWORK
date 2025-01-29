<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <!-- Statistik Transaksi -->
                    <div class="col-md-12">
                        <div class="card table-responsive">
                            <div class="card-body text-center ">
                                
                                    <h4>Laporan Transaksi dari {{ request()->input('start_date') }} hingga {{ request()->input('end_date') }}</h4>

                                    <!-- Transaction Table -->
                                    <table class="table table-bordered" id="myTable">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $index => $transaction)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $transaction->external_id }}</td>
                                                    <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                    <td>{{ $transaction->coin_amount }} Coin</td>
                                                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status == 'complete' ? 'success' : ($transaction->status == 'pending' ? 'warning' : ($transaction->status == 'cancel' ? 'danger' : 'secondary')) }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                        
                                                    </td>
                                                    <td>{{ $transaction->payment_method }}</td>
                                                    <td>{{ $transaction->payment_channel ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('d M Y, H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                               
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
    $('#myTable').DataTable( {
    buttons: [
        {
            extend: 'copy',
            text: 'Copy to clipboard'
        },
        'excel',
        'pdf'
    ]
} );
</script>