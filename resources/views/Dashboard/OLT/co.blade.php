

<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <form action="{{ route('prosespembayaran', ['unique_id' => $unique, 'pembelian_id' => $pembelian_id, 'banyaknya' => $banyaknya]) }}" method="post">
                    @csrf
                    <input type="hidden" name="unique_id" value="{{ $unique }}">
                    <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">
                    <input type="hidden" name="banyaknya" value="{{ $banyaknya }}">
                
                    <div class="invoice">
                        <div class="invoice-title">
                            <h2>Invoice</h2>
                            <div class="invoice-number">Order #{{ $pembelian_id }}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <address>
                                    <strong>Billed To:</strong><br>
                                    {{ $billed }} ({{ $unique }})<br>
                                    {{ $email }}
                                </address>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <address>
                                    <strong>Order Date:</strong><br>
                                    {{ now()->format('d M Y') }}
                                </address>
                            </div>
                        </div>
                        <div class="section-title mt-4">Order Summary</div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-right">Totals</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Port OLT</td>
                                    <td class="text-center">Rp. 10.000,-</td>
                                    <td class="text-center">{{ $banyaknya }}</td>
                                    <td class="text-right">Rp. {{ number_format(10000 * $banyaknya, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-right">
                            <strong>Total: Rp. {{ number_format(10000 * $banyaknya, 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-credit-card"></i> Proses Pembayaran</button>
                        <a class="btn btn-danger" href="{{route('dataolt')}}"><i class="fas fa-times"></i> Cancel</a>
                    </div>
                </form>
                

            </section>
          </div>
        <x-dcore.footer />
    </div>
</div>
<x-dcore.script />
