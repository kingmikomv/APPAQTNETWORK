

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
                    <div class="col-12"> <!-- Full width column -->
                        <div class="card wide-card">
                            <div class="card-body text-center">
                                <h4 class="font-weight-bold">Proses Pembelian Coin</h4>
                                <p class="lead">Silahkan pilih metode pembayaran yang diinginkan.</p>
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <a href="{{ route('bank.transfer', $data) }}" class="btn btn-primary btn-block">
                                            <i class="fas fa-university"></i> Bank Transfer
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="" class="btn btn-primary btn-block">
                                            <i class="fas fa-credit-card"></i> DANA
                                        </a>
                                    </div>
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
