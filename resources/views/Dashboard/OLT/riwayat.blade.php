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
                                <h4><i class="fas fa-history"></i> Riwayat Pembelian</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if ($transactions->isEmpty())
                                        <p>Belum ada transaksi pembelian coin.</p>
                                    @else
                                        <table class="table table-bordered">
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
                                                @foreach ($transactions as $index => $transaction)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $transaction->coin_amount }} Coin</td>
                                                        <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                                                        <td>
                                                            @if ($transaction->status == 'pending')
                                                                <span class="badge bg-warning">Pending / Checking</span>
                                                            @elseif ($transaction->status === 'complete')
                                                                <span class="badge bg-success">Selesai</span>
                                                            @elseif ($transaction->status === 'failed')
                                                                <span class="badge bg-danger">Gagal</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                                       
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle"
                                                                    type="button" data-toggle="dropdown"
                                                                    aria-expanded="false">
                                                                    Action
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <!-- Tombol Bayar untuk membuka modal -->

                                                                    @if ($transaction->status == 'pending')
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('payment.process', $transaction->id) }}">
                                                                            Bayar
                                                                        </a>
                                                                    @endif


                                                                    <!-- Form untuk cancel transaksi -->
                                                                    
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
                </div>
            </section>
        </div>
        <x-dcore.footer />
    </div>
</div>


<x-dcore.script />
