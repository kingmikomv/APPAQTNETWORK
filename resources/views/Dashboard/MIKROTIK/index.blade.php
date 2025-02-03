<x-dcore.head />
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <x-dcore.modal />

        <div class="main-content">
            <section class="section">
                <div class="row">
                   
                    <!-- Data VPN Section -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>Data Mikrotik</h4>
                                <div>
                                      <!-- Button to Trigger Info Modal -->
                                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMikrotikModal">
                                        <i class="fas fa-plus"></i> Tambah Mikrotik 
                                    </button>
                                    <!-- Buttons aligned to the right -->
                                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#notificationModal">
                                        <i class="fas fa-info"></i> Informasi Syarat dan Ketentuan
                                    </button>
                        
                                  
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="mikrotikTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>IP Mikrotik</th>
                                                <th>Site</th>
                                                <th>Username</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($mikrotik as $item)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $item->ipmikrotik }}</td>
                                                <td>{{ $item->site }}</td>
                                                <td>{{ $item->username }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('aksesmikrotik', [
                                                                'ipmikrotik' => $item->ipmikrotik,
                                                                'username' => $item->username,
                                                                'password' => $item->password
                                                            ]) }}"><i class="fas fa-bolt"></i> Cek Akses</a>
                                                            <a class="dropdown-item" href="{{ route('masukmikrotik', [
                                                                'ipmikrotik' => $item->ipmikrotik,
                                                                'portweb' => $item->portweb
                                                            ]) }}"><i class="fas fa-sign-in-alt"></i> Masuk</a>
                                                            <a class="dropdown-item editMikrotik" href="javascript:void(0)" data-id="{{ $item->id }}"><i class="fas fa-edit"></i> Edit</a>
                                                            <a class="dropdown-item deleteMikrotik" href="javascript:void(0)" data-id="{{ $item->id }}"><i class="fas fa-trash"></i> Hapus</a>
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

            </section>
        </div>

                <!-- Edit MikroTik Modal -->
                <div class="modal fade" id="editMikrotikModal" tabindex="-1" role="dialog" aria-labelledby="editMikrotikModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editMikrotikModalLabel">Edit MikroTik</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editMikrotikForm" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="edit_ipmikrotik">IP VPN / IP Public</label>
                                        <input type="text" class="form-control" id="edit_ipmikrotik" name="ipmikrotik" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_site">Site / Nama Mikrotik</label>
                                        <input type="text" class="form-control" id="edit_site" name="site" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_username">Username</label>
                                        <input type="text" class="form-control" id="edit_username" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_password">Password</label>
                                        <input type="password" class="form-control" id="edit_password" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Mikrotik</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        <!-- Modal Tambah Mikrotik -->
        <div class="modal fade" id="addMikrotikModal" tabindex="-1" role="dialog" aria-labelledby="addMikrotikModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMikrotikModalLabel">Tambah Mikrotik</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addMikrotikForm" action="{{ route('tambahmikrotik') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="ipmikrotik">IP VPN / IP Public</label>
                                <input type="text" class="form-control" placeholder="172.160.x.x" name="ipmikrotik" id="ipmikrotik" required>
                            </div>
                            <div class="form-group">
                                <label for="site">Site / Nama Mikrotik</label>
                                <input type="text" class="form-control" placeholder="Site Indramayu" name="site" id="site" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" placeholder="Username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-success">Tambah Mikrotik</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pemberitahuan -->
        <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationModalLabel">Pemberitahuan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: 20px;">Pada halaman ini berfungsi sebagai halaman penambahan mikrotik, entah itu dari Mikrotik yang sudah terhubung dengan VPN yang telah dibuat di halaman <a href="{{ route('datavpn') }}">Data VPN</a> atau data mikrotik Anda yang sudah memiliki IP Public sendiri.</p>
                        <hr>
                        <p class="mb-0" style="font-size: 20px;">Jika Router MikroTik Anda tidak mempunyai IP Public, silakan buat akun <a href="{{ route('datavpn') }}">VPN</a> pada form yang sudah disiapkan. Gratis tanpa biaya tambahan dan boleh lebih dari satu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-dcore.footer />
<x-dcore.script />
<script>
  $(document).ready(function() {
    $('#mikrotikTable').DataTable();

    // Handle Edit
    $('.editMikrotik').click(function() {
        var id = $(this).data('id');
        $.get('{{ route('mikrotik.edit', '') }}/' + id, function(data) {
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
                return;
            }
            $('#editMikrotikModal').modal('show');
            $('#editMikrotikForm').attr('action', '{{ url("/home/datamikrotik/") }}/' + id + '/update');
            $('#edit_ipmikrotik').val(data.ipmikrotik);
            $('#edit_site').val(data.site);
            $('#edit_username').val(data.username);
            $('#edit_password').val(data.password);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', 'Gagal memuat data: ' + textStatus, 'error');
        });
    });

    // Handle Delete
    $('.deleteMikrotik').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('mikrotik.delete', '') }}/' + id,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if(response.status === 'success') {
                            Swal.fire('Dihapus!', 'Data Mikrotik telah dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Data Mikrotik gagal dihapus.', 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data: ' + textStatus, 'error');
                    }
                });
            }
        });
    });
  });
</script>
<x-dcore.alert />