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
                                <h4>User Hotspot</h4>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                    Generate Hotspot
                                </button>

                                <div class="table-responsive">
                                    <table id="userHotspotTable" class="table table-bordered table-striped" style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Username</th>
                                                <th>Uptime</th>
                                                <th>Profile</th>
                                                <th>MAC Address</th> <!-- Tetap tampilkan kolom MAC Address -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($userHotspots as $index => $hotspot)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $hotspot['name'] }}</td>
                                                    <td class="uptime">{{ $hotspot['uptime'] }}</td> <!-- Uptime tetap disimpan untuk pemformatan di JavaScript -->
                                                    <td>{{ $hotspot['profile'] ?? 'N/A' }}</td>
                                                    <td>{{ $hotspot['mac-address'] ?? 'N/A' }}</td>
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

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Generate Hotspot</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>IP VPN</label>
                            <input type="text" id="name" name="ipmikrotik" class="form-control" readonly value="{{ $ipmikrotik }}">
                        </div>
                        <div class="form-group">
                            <label>Server</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option disabled selected value>Pilih Server</option>
                                @foreach ($serverhs as $servers)
                                    <option>{{ $servers['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Suffix</label>
                            <input type="text" id="name" name="suffix" class="form-control" placeholder="Suffix ( [Tanda]Generate )">
                        </div>
                        <div class="form-group">
                            <label>Profile</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option disabled selected value>Pilih Profile</option>
                                @foreach ($profile as $profiles)
                                    <option>{{ $profiles['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Banyak</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option disabled selected value>Pilih Banyaknya</option>
                                @for ($i = 10; $i <= 50; $i += 5)
                                    <option>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option disabled selected value>Pilih Type nya</option>
                                <option>Username = Username</option>
                                <option>Username & Password</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
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
    // Initialize DataTable
    var table = $('#userHotspotTable').DataTable({
        responsive: true,
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
            { targets: "_all", className: "text-center" } // Center align all columns
        ]
    });

    // Fetch and reload hotspot user data dynamically via AJAX
    function fetchUserHotspotData() {
        $.ajax({
            url: '{{ route("aksesuserhotspot", ["ipmikrotik" => $ipmikrotik]) }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                table.clear().draw(); // Clear existing data in table
                $.each(data.userHotspots, function(index, hotspot) {
                    table.row.add([
                        index + 1,
                        hotspot.name || 'N/A',
                        hotspot.uptime || 'N/A', // Simpan uptime asli untuk pemformatan
                        hotspot['profile'] || 'N/A',
                        hotspot['mac-address'] || 'N/A'
                    ]).draw(false);
                });
                formatUptimeInTable(); // Format uptime setelah data di-refresh
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching hotspot users:', textStatus, errorThrown);
            }
        });
    }

    // Function to format uptime in the table
    function formatUptimeInTable() {
        $('#userHotspotTable tbody tr').each(function() {
            var uptimeCell = $(this).find('td.uptime');
            var uptime = uptimeCell.text();
            var formattedUptime = formatUptime(uptime);
            uptimeCell.text(formattedUptime);
        });
    }

    // Fungsi untuk memformat uptime di JavaScript
    function formatUptime(uptime) {
        var days = uptime.match(/(\d+)d/);
        var hours = uptime.match(/(\d+)h/);
        var minutes = uptime.match(/(\d+)m/);

        var formatted = '';
        if (days) {
            formatted += days[1] + ' hari, ';
        }
        if (hours) {
            formatted += hours[1] + ' jam, ';
        }
        if (minutes) {
            formatted += minutes[1] + ' menit';
        }

        return formatted.trim().replace(/,\s*$/, ''); // Buang koma terakhir
    }

    // Initial data fetch
    fetchUserHotspotData();

    // Set interval to refresh data every 5 seconds
    setInterval(fetchUserHotspotData, 5000);
});
</script>
