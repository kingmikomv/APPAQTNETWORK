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
                                <div class="table-responsive">
                                    <table id="myTable2" class="table table-striped table-bordered table-sm" style="font-size: 12px;">
                                        

                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Interface</th>
                                                <th>MAC Address</th>
                                                <th>MTU</th>
                                                <th>Type</th>
                                                <th>Status Disabled</th>
                                                <th>Status Running</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $no = 1; 
                                            @endphp
                                            @foreach ($interface as $d)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $d['name'] ?? 'N/A' }}</td>
                                                <td>{{ $d['mac-address'] ?? 'N/A' }}</td>
                                                <td>{{ $d['mtu'] ?? 'N/A' }}</td>
                                                <td>{{ $d['type'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($d['disabled'] == 'true')
                                                    <span class="text-danger">{{ 'YA' }}</span>
                                                    @else
                                                    <span class="text-primary">{{ 'TIDAK' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d['running'] == 'true')
                                                    <span class="text-primary">{{ 'YA' }}</span>
                                                    @else
                                                    <span class="text-danger">{{ 'TIDAK' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d['disabled'] == 'true')
                                                    <form action="{{ route('interface.enable', $d['.id']) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="ipmikrotik" value="{{ request()->query('ipmikrotik') }}">

                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-bell"></i>  Nyalakan</button>
                                                    </form>
                                                    @else
                                                    <form action="{{ route('interface.disable', $d['.id']) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="ipmikrotik" value="{{ request()->query('ipmikrotik') }}">

                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-bell-slash"></i> Matikan</button>
                                                    </form>
                                                    @endif
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
        </div>
        <x-dcore.footer />
    </div>
</div>
<x-dcore.script />
<script>
    // Initialize DataTable with custom settings for smaller font and responsive display
    var table = $('#myTable2').DataTable({
        responsive: true,
        pageLength: 10, // Number of rows per page
        autoWidth: false, // Disable automatic column width adjustment
        columnDefs: [
            { targets: "_all", className: "text-center" } // Center align all columns
        ]
    });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: true
        });
    </script>
@elseif (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: true
        });
    </script>
@endif
