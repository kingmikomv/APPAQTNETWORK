<!-- olt-akses.blade.php -->
<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        
        <div class="main-content">
            <section class="section">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Akses OLT</h4>
                            </div>
                            <div class="card-body">
                                <iframe src="http://{{ $ipolt }}" frameborder="0" style="width:100%; height:600px;"></iframe>
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
