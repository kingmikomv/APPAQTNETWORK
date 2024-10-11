<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        
        <div class="main-content">
            <section class="section">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Hotspot Aktif</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="activeHotspotTable" class="table table-bordered table-striped" style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>User</th>
                                                <th>Address</th>
                                                <th>MAC Address</th>
                                                <th>Uptime</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activeHotspots as $hotspot)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $hotspot['user'] }}</td>
                                                    <td>{{ $hotspot['address'] }}</td>
                                                    <td>{{ $hotspot['mac-address'] }}</td>
                                                    <td>{{ $hotspot['uptime'] }}</td>
                                                    <td>
                                                        <a href="#" 
                                                           class="disconnect-btn btn btn-danger" 
                                                           data-mac="{{ $hotspot['mac-address'] }}" 
                                                           data-ip="{{ $ipmikrotik }}">
                                                            <i class="fas fa-trash"></i> Putuskan
                                                        </a>
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
        
        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#activeHotspotTable').DataTable({
        responsive: true,
        pageLength: 10, 
        autoWidth: false, 
        columnDefs: [
            { targets: "_all", className: "text-center" } 
        ]
    });

    // Fetch and reload hotspot data
    function fetchHotspotData() {
        $.ajax({
            url: '{{ route("aksesactivehotspot", ["ipmikrotik" => $ipmikrotik]) }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                table.clear().draw();
                data.activeHotspots.forEach(function(hotspot, index) {
                    table.row.add([
                        index + 1,
                        hotspot.user,
                        hotspot.address,
                        hotspot['mac-address'],
                        hotspot.uptime,
                        `<a href="#" class="disconnect-btn btn btn-danger" data-mac="${hotspot['mac-address']}" data-ip="{{ $ipmikrotik }}">
                            <i class="fas fa-trash"></i> Putuskan
                        </a>`
                    ]).draw(false);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching hotspot data:', textStatus, errorThrown);
            }
        });
    }

    // Initial data fetch
    fetchHotspotData();

    // Set interval to refresh data every 5 seconds
    setInterval(fetchHotspotData, 5000);

    // Handle disconnect button click
    $(document).on('click', '.disconnect-btn', function(e) {
        e.preventDefault();

        var macAddress = $(this).data('mac');
        var ipAddress = $(this).data('ip');

        // SweetAlert confirmation
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin memutuskan pengguna ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, putuskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("disconnect.hotspot") }}',
                    method: 'POST',
                    data: {
                        mac_address: macAddress,
                        ipaddress: ipAddress, 
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            fetchHotspotData();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error disconnecting user:', textStatus, errorThrown);
                        Swal.fire('Error!', 'Terjadi kesalahan saat memutuskan pengguna.', 'error');
                    }
                });
            }
        });
    });
});
</script>
