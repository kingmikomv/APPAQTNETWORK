<x-dcore.head />
<style>
  /* Add some basic styling */
  #trafficChart {
      width: 100%;
      max-width: 800px;
      margin: auto;
  }
  #trafficInfo {
      text-align: center;
      margin-top: 20px;
  }
</style>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <x-dcore.nav />
      <x-dcore.sidebar />
      
      <div class="main-content">
        <section class="section">
        {{-- <x-dcore.card /> --}}
        <div class="row">
           
          <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-server"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total VPN</h4>
                </div>
                <div class="card-body">
                  {{ $totalvpn }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-server"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total MikroTik</h4>
                </div>
                <div class="card-body">
                  {{ $totalmikrotik }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-users"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total Client</h4>
                </div>
                <div class="card-body">
                  {{ $totaluser }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-bolt"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>User Active</h4>
                </div>
                <div class="card-body">
                  {{ $totalactive }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-bolt"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Status</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12" id="cpuLoad" style="font-size: 12px;"></div>
                    <div class="col-md-12 mb-3" style="font-size: 12px;">
                    <b> Model : {{$model}} <br>Version : {{$version}}</b>
                    </div>
                  </div>
               </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Kinerja</h4>
                </div>
                <div class="card-body">

                  <div class="row">
                    <div class="col-md-12" style="font-size: 12px;" id="uptime">Loading...</div>
                    <div class="col-md-12 mb-3" style="font-size: 12px;">
                    <b>Tanggal : {{ $date }}</b>
                    </div>
                  </div>
                 
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-statistic-2">
              <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-bolt"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Hotspot</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12" style="font-size: 12px;">Jml. VCR : {{$ttuser}}</div>
                    <div class="col-md-12 mb-3" style="font-size: 12px;">
                    <b>Active : {{ $activeUserCount }}</b>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
        <!-- MAIN OF CENTER CONTENT -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                
                <div class="card-body">
                  <div class="row d-flex justify-content-center">
                    <div class="col-md-12 d-flex justify-content-center">
                      <h3>Selamat Datang Di MikroTik {{$site ?? '-'}}</h3>
                    </div>
                    <div class="col-md-12">

                      <form action="{{ route('keluarmikrotik') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block">Logout MikroTik</button>
                    </form>
    
                    </div>
                  </div>
                 

                </div>
              </div>
              
            </div>
            <!-- traffic -->
            <div class="col-lg-12">
              <div class="card">
                
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-12">
                       
                            <form id="interfaceForm">
    <div class="form-group">
        <label for="interface">Select Interface</label>
        <select class="form-control" id="interface" name="interface">
            @foreach ($interfaces as $interface)
                <option value="{{ $interface }}">{{ $interface }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">

    <!-- Input hidden untuk ipmikrotik -->
    <input type="hidden" id="ipmikrotik" name="ipmikrotik" value="{{ $ipmikrotik }}">
    <button type="submit" class="btn btn-primary">Get Traffic</button>
    </div>

</form>

                    </div>
                    <div class="col-lg-12">
                      
                        <canvas id="trafficChart"></canvas>
                        <div id="trafficInfo">
                          <p>Trafik Download: <span id="currentRx">0</span></p>
                          <p>Trafik Upload : <span id="currentTx">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-12">
                    <small class="mt-2">*Data Dalam Bentuk Mpbs <br>Jika Berganti Ethernet Tunggu 20 Detik Maka Data Grafik Akan Berganti Ke Ethernet Yang Di Pilih</small>
                    </div>
                </div>
                </div>
              </div>
              
            </div>
             <!-- Voucher -->
            
          </div>
        <!-- END OF CENTER CONTENT -->
       


        </section>
    
      </div>
      <x-dcore.footer />
    </div>
  </div>
<x-dcore.script />
<!-- <script>
  function fetchCpuLoad() {
      $.ajax({
          url: '/mikrotik/cpu-load/{{ $ipmikrotik }}',
          method: 'GET',
          success: function(response) {
              $('#cpuLoad').text(response.cpuLoad);
          },
          error: function() {
              $('#cpuLoad').text('Error');
          }
      });
  }

  function fetchCurrentTime() {
      $.ajax({
          url: '/mikrotik/current-time/{{ $ipmikrotik }}',
          method: 'GET',
          success: function(response) {
              $('#currentTime').text(response.time);
          },
          error: function() {
              $('#currentTime').text('Error');
          }
      });
  }

  // Fetch CPU load and current time immediately and then every 5 seconds
  fetchCpuLoad();
  fetchCurrentTime();
  setInterval(fetchCpuLoad, 1000); // Refresh CPU load every 5 seconds
  setInterval(fetchCurrentTime, 1000); // Refresh current time every 5 seconds
</script> -->
<script>
  function fetchUptime() {
    $.ajax({
        url: '/mikrotik/uptime/{{ $ipmikrotik }}',
        method: 'GET',
        success: function(response) {
            if (response.error) {
                $('#uptime').text('Uptime: Error');
            } else {
                $('#uptime').text('Uptime: ' + response.uptime);
            }
        },
        error: function() {
            $('#uptime').text('Uptime: Error');
        }
    });
  }

  // Fetch uptime immediately and then every 5 minutes
  fetchUptime();
  setInterval(fetchUptime, 300000); // Refresh uptime every 5 minutes (300000 milliseconds)
</script>
<script>
 $(document).ready(function() {
    let chart = null;
    let pollingInterval = null; // Variable for interval
    let dataPoints = 20; // Number of points to show on the chart

    $('#interfaceForm').on('submit', function(event) {
        event.preventDefault();
        const selectedInterface = $('#interface').val();
        const ipmikrotik = $('#ipmikrotik').val();

        // Clear any previous polling
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }

        // Destroy existing chart if it exists
        if (chart) {
            chart.destroy();
            chart = null;
        }

        // Reset data for the new chart
        const initialLabels = Array(dataPoints).fill('').map((_, i) => i + 1); // Labels from 1 to 20
        const initialData = Array(dataPoints).fill(0); // Start with 0 values

        // Create a new chart instance
        const ctx = document.getElementById('trafficChart').getContext('2d');
        chart = new Chart(ctx, {
            type: 'line', // Use 'line' chart type
            data: {
                labels: initialLabels, // Start with static labels 1-20
                datasets: [{
                    label: 'Trafik Download (Mbps)', // Label for RX in Mbps
                    data: initialData.slice(), // Copy dummy data for RX
                    backgroundColor: 'rgba(54, 162, 235, 0.3)', // Light blue with some opacity
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 2, // Thinner line
                    borderCapStyle: 'round', // Rounded cap style
                    borderJoinStyle: 'round', // Rounded join style
                    fill: true, // Fill under the line
                    tension: 0.4 // Increased tension for a more fluid line
                },
                {
                    label: 'Trafik Upload (Mbps)', // Label for TX in Mbps
                    data: initialData.slice(), // Copy dummy data for TX
                    backgroundColor: 'rgba(255, 99, 132, 0.3)', // Light red with some opacity
                    borderColor: 'rgba(255, 99, 132, 1)', // Red for TX line
                    borderWidth: 2, // Thinner line
                    borderCapStyle: 'round', // Rounded cap style
                    borderJoinStyle: 'round', // Rounded join style
                    fill: true, // Fill under the line
                    tension: 0.4 // Increased tension for a more fluid line
                }]
            },
            options: {
                responsive: true, // Make the chart responsive
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Traffic (Mbps)' // Changed to Mbps
                        },
                        ticks: {
                            stepSize: 0.5, // Set the step size to 0.5 for 1 Mbps, 1.5 Mbps, etc.
                            callback: function(value) {
                                return value + ' Mbps'; // Add 'Mbps' to tick labels
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                // Add 'Mbps' suffix to tooltip data, rounded to nearest whole number
                                return tooltipItem.dataset.label + ': ' + Math.round(tooltipItem.raw) + ' Mbps';
                            }
                        }
                    }
                }
            }
        });

        // Function to fetch traffic data and update chart
        function fetchTrafficData() {
            $.ajax({
                url: '/mikrotik/traffic',
                method: 'GET',
                data: { interface: selectedInterface, ipmikrotik: ipmikrotik },
                success: function(response) {
                    console.log('Response Data:', response); // Debugging

                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    // Convert RX and TX data from bytes to Mbps and round to 2 decimal places
                    const rxMbps = (response.rx / 1000000).toFixed(2); // Convert RX to Mbps
                    const txMbps = (response.tx  / 1000000).toFixed(2); // Convert TX to Mbps

                    // Update the chart data
                    if (chart) {
                        const currentTime = new Date().toLocaleTimeString(); // Add time label
                        chart.data.labels.push(currentTime); // Add new label (time)

                        chart.data.datasets[0].data.push(rxMbps); // Update RX data in Mbps
                        chart.data.datasets[1].data.push(txMbps); // Update TX data in Mbps

                        // Maintain only the last dataPoints data points
                        if (chart.data.labels.length > dataPoints) {
                            chart.data.labels.shift(); // Remove old label (time)
                            chart.data.datasets[0].data.shift(); // Remove old RX data
                            chart.data.datasets[1].data.shift(); // Remove old TX data
                        }

                        chart.update(); // Redraw chart

                        // Update the traffic info
                        $('#currentRx').text(rxMbps + ' Mbps');
                        $('#currentTx').text(txMbps + ' Mbps');
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr); // Debugging
                    alert('Error retrieving traffic data.');
                }
            });
        }

        // Start polling the traffic data every 1 second
        pollingInterval = setInterval(fetchTrafficData, 1000);
        
        // Fetch initial data to populate the chart immediately
        fetchTrafficData();
    });
});

</script>
