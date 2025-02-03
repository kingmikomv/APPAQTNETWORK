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
                                                <select class="form-control" id="exampleFormControlSelect1"
                                                    name="option">
                                                    <option disabled selected value>Pilih MikroTik</option>
                                                    @foreach ($mikrotik as $dm)
                                                    <option value="{{ $dm->ipmikrotik }}">{{ $dm->site }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block"><i
                                                    class="fas fa-search"></i> Cari</button>
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
                                    <i class="fas fa-sync-alt"></i> Sinkronisasi Data
                                </a>
                                <sub class="text-danger">*lakukan setiap ada data baru</sub>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="alert alert-info" role="alert">
                            <strong>Info!</strong> Posisi Kamu Ada Di MikroTik <strong
                                class="text-dark">{{ $data->site }}</strong>
                        </div>
                    </div>
                    @endif

                    <!-- Tabel Pengguna Offline dan Isolir -->
                    @if (isset($offlineUsers) && isset($isolir))
                    <div class="col-md-6 mt-3">
                        <div class="card">
                            <div
                                class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Pengguna Offline</h5>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalId">
                                    <i class="fas fa-info"></i> Info
                                </button>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered" id="user2">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center">Pengguna Offline</th>
                                            <th class="text-center">Terakhir Online</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($offlineUsers as $user)
                                        <tr>
                                            <td class="text-center">{{ $user['username'] }}</td>
                                            <td class="text-center">{{ $user['last_seen'] }}</td>
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
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="user">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Pengguna Isolir</th>
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
                    </div>
                    
                    @endif
                </div>
            </section>
        </div>
        @if (isset($offlineUsers) && isset($isolir))
        <!-- Modal -->
        <div class="modal fade" id="modalId" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Informasi Pengguna Offline</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Pengguna offline merujuk pada pengguna yang tidak terhubung ke jaringan internet dan mengalami masalah dengan indikasi sebagai berikut:
                        <ul>
                            <li>Kabel Terputus</li>
                            <li>PPPoE Terputus</li>
                            <li>Perangkat Mengalami Error (terus-menerus restart)</li>
                            <li>Adaptor Rusak</li>
                        </ul>
                        Untuk informasi lebih lanjut dan pengecekan lebih detail, silakan periksa OLT melalui tautan berikut: <a href="{{ route('dataolt') }}">CEK OLT</a>, atau hubungi pihak Client untuk informasi lebih lanjut.
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>

        @endif
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
            paging: $('#user tbody tr').length >=
                60, // Aktifkan pagination jika jumlah baris 60 atau lebih
            pageLength: 50 // Tampilkan 50 baris per halaman jika pagination aktif
        });

        // Inisialisasi DataTable untuk tabel #user2
        $('#user2').DataTable({
            responsive: true,
            autoWidth: false,
            paging: $('#user2 tbody tr').length >=
                60, // Aktifkan pagination jika jumlah baris 60 atau lebih
            pageLength: 50 // Tampilkan 50 baris per halaman jika pagination aktif
        });
    });

</script>
@if (session('success'))
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: "success",
        title: '{{ session('
        success ') }}',
    });

</script>
@elseif (session('error'))
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: 'error',
        title: '{{ session('
        error ') }}',
    });

</script>
@endif
