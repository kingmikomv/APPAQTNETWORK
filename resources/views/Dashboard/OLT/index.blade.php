<!-- your-view.blade.php -->
<x-dcore.head />
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <x-dcore.modal />

        <div class="main-content">
            <section class="section">
                <!-- MAIN CONTENT -->
                <div class="row">
                    <!-- Pemberitahuan Section -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="font-size: 20px;"><i class="fas fa-info-circle"></i> Pemberitahuan</h4>
                            </div>
                            <div class="card-body">
                                <p style="font-size: 20px;">Pada halaman ini berfungsi sebagai halaman penambahan data OLT EPON HA7304</p>
                                <hr>
                                <p class="mb-0" style="font-size: 20px;">Jika OLT anda tidak mempunyai IP Public, silahkan Hubungi Administrator Untuk memasang VPN pada OLT EPON HA7304 anda</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form to Add VPN -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tambah OLT</h4>
                            </div>
                            <div class="card-body">
                                <form id="yourFormId" action="{{ route('tambaholt') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="ipolt">IP OLT</label>
                                        <input type="text" class="form-control" placeholder="172.160.x.x" name="ipolt" id="ipolt">
                                    </div>
                                    <div class="form-group">
                                        <label for="site">Site / Nama OLT</label>
                                        <input type="text" class="form-control" placeholder="Site Indramayu" name="site" id="site">
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-success" value="Tambah OLT">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Data OLT Section -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data OLT</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="oltTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>IP OLT</th>
                                                <th>Site</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($olts as $olt)
                                                <tr>
                                                    <td>{{ $olt->id }}</td>
                                                    <td>{{ $olt->ipolt }}</td>
                                                    <td>{{ $olt->site }}</td>
                                                    <td>
                                                        <!-- Dropdown Button for Actions -->
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $olt->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $olt->id }}">
                                                                <a class="dropdown-item" href="{{ route('aksesolt', ['ipolt' => $olt->ipolt]) }}">
                                                                    <i class="fas fa-bolt"></i> Akses
                                                                </a>
                                                                <a class="dropdown-item" href="{{ route('hapusolt', ['id' => $olt->id]) }}">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END MAIN CONTENT -->
            </section>
        </div>

        <x-dcore.footer />
    </div>
</div>


<x-dcore.script />
<!-- DataTables Script -->
<script>
     $(document).ready(function() {
    $('#oltTable').DataTable();
     });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            showConfirmButton: true
        });
    </script>
@elseif (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            showConfirmButton: true
        });
    </script>
@endif
