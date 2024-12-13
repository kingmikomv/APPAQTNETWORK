<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row no-gutters justify-content-center"> <!-- Center the content horizontally -->
                    <!-- Welcome Card -->
                    <div class="col-12 col-md-8 col-lg-6"> <!-- Control column width on different screen sizes -->
                        <div class="card wide-card">
                            <div class="card-body">
                                <div class="text-center">
                                    <p class="font-weight-bold">ID Pembelian: <span class="text-info">{{ $pembelian_id }}</span></p>
                                </div>

                                <div class="invoice">
                                    <!-- Invoice Header -->
                                    <div class="invoice-header">
                                        <h3 class="text-center font-weight-bold">Invoice</h3>
                                        <p class="text-center">Nomor: #{{ $pembelian_id }}</p>
                                        <p class="text-center">Tanggal: {{ now()->format('d-m-Y') }}</p>
                                    </div>

                                    <!-- Invoice Body -->
                                    <div class="invoice-body">
                                        <div class="invoice-item">
                                            <div class="item-description">
                                                <p class="font-weight-bold">Port OLT</p>
                                            </div>
                                            <div class="item-details">
                                                <p>Harga: Rp. 10.000,-</p>
                                                <p>Jumlah: {{ $dataPort->count() }}</p>
                                                <p>Total: Rp. {{ number_format(10000 * $dataPort->count(), 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Invoice Footer -->
                                    <div class="invoice-footer">
                                        <hr>
                                        <div class="total">
                                            <h5 class="font-weight-bold">Total Pembayaran: Rp. {{ number_format(10000 * $dataPort->count(), 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Proof Form -->
                                <div class="d-flex justify-content-center mt-4">
                                    <form action="{{ route('payment.submit') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">
                                        
                                        <div class="form-group text-center">
                                            <label for="paymentProof" class="font-weight-bold">Kirim Bukti Pembayaran</label>
                                            <input type="file" class="form-control-file" id="paymentProof" name="paymentProof" required>
                                        </div>
                                    
                                        <!-- Submit Button -->
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary mt-3">Kirim Bukti Pembayaran</button>
                                        </div>
                                    </form>
                                    
                                    
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

<!-- Styling -->
<style>
    .invoice {
        font-family: Arial, sans-serif;
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        border: 1px solid #ddd;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .invoice-header {
        margin-bottom: 20px;
    }

    .invoice-body {
        margin-bottom: 20px;
    }

    .invoice-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .item-description p,
    .item-details p {
        margin: 0;
    }

    .invoice-footer {
        text-align: center;
    }

    .total {
        margin-top: 10px;
    }

    hr {
        margin: 20px 0;
    }

    .form-group label {
        margin-right: 10px;
    }

    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 20px;
    }

    .text-info {
        color: #17a2b8;
    }

    .font-weight-bold {
        font-weight: 600;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
