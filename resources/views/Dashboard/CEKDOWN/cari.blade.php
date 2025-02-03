<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <!-- Card untuk menampilkan dropdown dan tombol -->
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('cari') }}" method="GET">
                                    <div class="row">
                                        <!-- Dropdown -->
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select class="form-control" id="exampleFormControlSelect1" name="option">
                                                    <option disabled selected value>Pilih MikroTik</option>
                                                    @foreach ($mikrotik as $dm)
                                                        <option value="{{ $dm->ipmikrotik }}">{{ $dm->site }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Cari</button>
                                        </div>
                                    </div>

                                   
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sync Button -->
                    @if (isset($data))
                        <div class="col-md-12 mt-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <a href="{{ route('sync.active.connection', ['option' => $data->ipmikrotik]) }}"
                                        class="btn btn-primary btn-block">
                                        <i class="fas fa-sync-alt"></i> Sync Data
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="alert alert-info" role="alert">
                                <strong>Info!</strong> Posisi Kamu Ada Di MikroTik {{ $data->site }}
                            </div>
                        </div>
                    @endif

                    <!-- Tabel Pengguna Offline dan Isolir -->
                    @if (isset($offlineUsers) && isset($isolir))
                        <div class="col-md-6 mt-3">
                            <div class="card">
                                <div class="card-header text-center bg-warning text-white">
                                    <h5>Pengguna Offline</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered" id="user2">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Pengguna Offline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($offlineUsers as $user)
                                                <tr>
                                                    <td class="text-center">{{ $user }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="card">
                                <div class="card-header text-center bg-danger text-white">
                                    <h5>Pengguna Isolir</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered" id="user">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Pengguna Isolir (IP dimulai dengan 172)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($isolir as $user)
                                                <tr>
                                                    <td class="text-center">{{ $user }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />

<script>
    $(document).ready(function () {
        // Inisialisasi DataTable untuk tabel #user
        $('#user').DataTable({
            responsive: true,
            autoWidth: false,
            paging: $('#user tbody tr').length >= 60, // Aktifkan pagination jika jumlah baris 60 atau lebih
            pageLength: 50 // Tampilkan 50 baris per halaman jika pagination aktif
        });

        // Inisialisasi DataTable untuk tabel #user2
        $('#user2').DataTable({
            responsive: true,
            autoWidth: false,
            paging: $('#user2 tbody tr').length >= 60, // Aktifkan pagination jika jumlah baris 60 atau lebih
            pageLength: 50 // Tampilkan 50 baris per halaman jika pagination aktif
        });
    });
</script>
