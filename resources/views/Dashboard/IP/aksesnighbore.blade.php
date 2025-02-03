@php
    // Helper function to format uptime
    function formatUptime($uptime) {
        if (!$uptime) return "N/A"; // Handle if uptime is null

        // Regular expression to match uptime format (w: weeks, d: days, h: hours, m: minutes, s: seconds)
        preg_match('/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/', $uptime, $matches);

        // Extract values or set default to 0
        $weeks = isset($matches[1]) ? (int) $matches[1] : 0;
        $days = isset($matches[2]) ? (int) $matches[2] : 0;
        $hours = isset($matches[3]) ? (int) $matches[3] : 0;
        $minutes = isset($matches[4]) ? (int) $matches[4] : 0;

        // Calculate total days (weeks * 7 + days)
        $totalDays = ($weeks * 7) + $days;

        // Construct readable uptime string
        $formattedUptime = '';
        if ($totalDays > 0) {
            $formattedUptime .= $totalDays . ' hari ';
        }
        if ($hours > 0) {
            $formattedUptime .= $hours . ' jam ';
        }
        if ($totalDays == 0 && $hours == 0 && $minutes > 0) {
            $formattedUptime .= $minutes . ' menit';
        }

        return trim($formattedUptime);
    }
@endphp


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
                        <th>IFACE</th>
                        <th>MAC</th>
                        <th>Platform</th>
                        <th>Uptime</th>
                        <th>Identity</th>
                        <th>Address</th>
                        <th>Board</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php 
                            use Carbon\Carbon;    
                            $no = 1; 
                        @endphp
                        @foreach ($response as $res => $d)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$d['interface']}}</td>
                            <td>{{$d['mac-address']}}</td>
                            <td>{{$d['platform']}}</td>
                            <td>{{ formatUptime($d['uptime'] ?? "N/A") }}</td>
                            <td>{{$d['identity' ?? 'N/A']}}</td>
                            <td>{{$d['address'] ?? 'N/A'}}</td>
                            <td>{{$d['board'] ?? "N/A"}}</td>
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
    $(document).ready(function() {
        // Initialize DataTable with custom settings for smaller font and responsive display
        var table = $('#myTable2').DataTable({
            paging: true,
            responsive: true,
            pageLength: 10, // Number of rows per page
            autoWidth: false, // Disable automatic column width adjustment
            columnDefs: [
                { targets: "_all", className: "text-center" } // Center align all columns
            ]
        });

        // Function to reload table data while maintaining the current page
        function reloadTable() {
            var currentPage = table.page.info().page; // Get the current page number
            table.ajax.reload(null, false); // Reload table without resetting pagination
            table.page(currentPage).draw(false); // Redraw table and stay on the same page
        }

        // Set interval to reload table data every 10 seconds
        setInterval(reloadTable, 10000); // 10000 milliseconds = 10 seconds

        // Optionally, call reloadTable once to load initial data
        reloadTable();
    });
</script>

<x-dcore.alert />