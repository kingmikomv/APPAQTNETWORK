<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN CONTENT -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSecretModal">
                                    <i class="fas fa-plus"></i> Tambah Secret / Akun
                                </button>
                                <div class="table-responsive">
                                    <table id="myTable2" class="table table-striped table-bordered table-sm" style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Service</th>
                                                <th>Profile</th>
                                                <th>Remote Address</th>
                                                <th>Comment</th>
                                                <th>Disabled</th>
                                                <th>Last Logged Out</th> <!-- New Column -->
                                                <th>Actions</th> <!-- New Column for Actions -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $no = 1; 
                                            @endphp
                                            @foreach ($secrets as $d)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $d['name'] ?? 'N/A' }}</td>
                                                    <td>{{ $d['service'] ?? 'N/A' }}</td>
                                                    <td>{{ $d['profile'] ?? 'N/A' }}</td>
                                                    <td>{{ $d['remote-address'] ?? 'N/A' }}</td>
                                                    <td>{{ $d['comment'] ?? 'N/A' }}</td>
                                                    <td>{{ $d['disabled'] ? 'Yes' : 'No' }}</td>
                                                    <td>
                                                        @if(isset($d['last_logged_out']) && !empty($d['last_logged_out']))
                {{ \Carbon\Carbon::parse($d['last_logged_out'])->format('Y-m-d H:i:s') }}
            @else
                N/A
            @endif
                                                    </td>
                                                   
                                                    <td>
                                                        <!-- Delete Button -->
                                                        <form method="POST" action="{{ route('secrets.destroy', ['id' => $d['.id']]) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="ipmikrotik" value="{{ $ipmikrotik }}">
                                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $d['.id'] }}" data-ip="{{ $ipmikrotik }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        
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
                <!-- END OF MAIN CONTENT -->
            </section>
            
            <!-- Add Secret Modal -->
            <div class="modal fade" id="addSecretModal" tabindex="-1" aria-labelledby="addSecretModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSecretModalLabel">Add New Secret</h5>
                          
                        </div>
                        <form method="post" action="{{ route('store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="service" class="form-label">Service</label>
                                    <select id="service" name="service" class="form-control" required>
                                        <option value="pppoe">PPPoE</option>
                                        <option value="any">Any</option>
                                        <option value="l2tp">L2TP</option>
                                        <option value="pptp">PPTP</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="profile" class="form-label">Profile</label>
                                    <select id="profile" name="profile" class="form-control" required>
                                        <!-- Options will be dynamically filled -->
                                        @foreach ($profiles as $profile)
                                            <option value="{{ $profile['.id'] }}">{{ $profile['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">IP Mikrotik</label>
                                    <input type="text" id="name" name="ipmikrotik" class="form-control" readonly value="{{ $ipmikrotik }}">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Password</label>
                                    <input type="password" id="name" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea id="comment" name="comment" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />

<script>
    $(document).ready(function() {
        $('#myTable2').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 75, 100],
            "order": [[0, 'asc']],
        });

        // SweetAlert for session flash messages
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @elseif(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const ipmikrotik = $(this).data('ip');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                $(this).closest('form').submit();
            }
        });
    });
    });
</script>
