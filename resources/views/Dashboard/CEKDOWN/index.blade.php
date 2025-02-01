<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <!-- Card untuk menampilkan pengguna isolir dan offline -->
                    <div class="col-md-6">
                        <div class="card wide-card">
                            <div class="card-body">
                                <!-- Tabel Pengguna Isolir -->


                                <table class="table table-bordered mt-2" id="user2">
                                    <thead>
                                        <tr>
                                            <th>Pengguna Offline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($offlineUsers as $user)
                                            <tr>
                                                <td>{{ $user }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card wide-card">
                            <div class="card-body">
                                <!-- Tabel Pengguna Isolir -->
                                
                                <table class="table table-bordered" id="user">
                                    <thead>
                                        <tr>
                                            <th>Pengguna Isolir (IP dimulai dengan 172)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($isolir as $user)
                                            <tr>
                                                <td>{{ $user }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Tabel Pengguna Offline -->
                                
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Sync Data -->
                    <div class="col-md-12 mt-2">
                        <div class="card wide-card">
                            <div class="card-body text-center">
                                <a href="{{ route('sync.active.connection', ['ipmikrotik' => session('ipmikrotik')]) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-sync-alt"></i> Sync Data 
                                </a>
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
        $('#user').DataTable({
            responsive: true,
            autoWidth: false
        });
        $('#user2').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
</script>
