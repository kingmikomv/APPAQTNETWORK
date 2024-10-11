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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generateHotspotModal">
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
                                                <th>MAC Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($userHotspots as $index => $hotspot)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $hotspot['name'] }}</td>
                                                    <td class="uptime">{{ $hotspot['uptime'] }}</td>
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
<!-- Modal for Generating Hotspot -->
<div class="modal fade" id="generateHotspotModal" tabindex="-1" aria-labelledby="generateHotspotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="generateHotspotForm" method="POST" action="{{ route('generateHotspot') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateHotspotModalLabel">Generate Hotspot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP VPN</label>
                        <input type="text" id="ipmikrotik" name="ipmikrotik" class="form-control" readonly value="{{ old('ipmikrotik', $ipmikrotik) }}">
                    </div>
                    <div class="form-group">
                        <label>Server</label>
                        <select class="form-control" id="serverSelect" name="server" required>
                            <option disabled selected value>Pilih Server</option>
                            @foreach ($serverhs as $servers)
                                <option value="{{ $servers['name'] }}">{{ $servers['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <input type="text" id="suffix" name="suffix" class="form-control" placeholder="Suffix (e.g., [Tanda]Generate)" required>
                    </div>
                    <div class="form-group">
                        <label>Profile</label>
                        <select class="form-control" id="profileSelect" name="profile" required>
                            <option disabled selected value>Pilih Profile</option>
                            @foreach ($profile as $profiles)
                                <option value="{{ $profiles['name'] }}">{{ $profiles['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Banyak</label>
                        <select class="form-control" id="quantitySelect" name="quantity" required>
                            <option disabled selected value>Pilih Banyaknya</option>
                            @for ($i = 10; $i <= 50; $i += 5)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" id="typeSelect" name="type" required>
                            <option disabled selected value>Pilih Type</option>
                            <option value="Username">Username</option>
                            <option value="Username & Password">Username & Password</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Panjang Karakter</label>
                        <select class="form-control" id="lengthSelect" name="length" required>
                            <option disabled selected value>Pilih Panjang Karakter</option>
                            <option value="4">4</option>
                            <option value="6">6</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Randomizer</label>
                        <select class="form-control" id="randomizerSelect" name="randomizer" required>
                            <option disabled selected value>Pilih Randomizer</option>
                            <option value="123ABC">123ABC</option>
                            <option value="123AbCdE">123AbCdE</option>
                            <option value="123abc">123abc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Generate Hotspot</button>
                </div>
            </form>
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
                        hotspot.uptime || 'N/A', // Use original uptime for formatting
                        hotspot.profile || 'N/A',
                        hotspot['mac-address'] || 'N/A'
                    ]).draw(false);
                });
                formatUptimeInTable(); // Format uptime after data refresh
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

    // Function to format uptime in JavaScript
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

        return formatted.trim().replace(/,\s*$/, ''); // Remove last comma
    }

    // Initial data fetch
    fetchUserHotspotData();

    // Set interval to refresh data every 5 seconds
    setInterval(fetchUserHotspotData, 5000);
});
</script>
