

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
                                <h3>Selamat Datang Di Aplikasi Management Mikrotik ( AMMIK ) AQT Network V.0.1 !</h3>
                                <div class="row mt-3"> <!-- Remove gutter space between buttons -->
                                    <div class="col-md-4 col-12 mt-2">
                                        <a href="{{ route('datavpn') }}" class="btn btn-primary btn-gradient btn-block">Data VPN</a>
                                    </div>
                                    <div class="col-md-4 col-12 mt-2">
                                        <a href="{{ route('datamikrotik') }}" class="btn btn-primary btn-gradient btn-block">Data Mikrotik</a>
                                    </div>
                                    <div class="col-md-4 col-12 mt-2">
                                        <a href="{{ route('dataolt') }}" class="btn btn-primary btn-gradient btn-block">Data OLT</a>
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
