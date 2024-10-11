<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Monitoring</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Traffic Monitoring for Interface: {{ $interfaceName }}</h1>

    @if(isset($error))
        <p style="color: red;">{{ $error }}</p>
    @else
        <p>RX Traffic: <span id="rx-traffic">{{ number_format($traffic['rx'] / 1024, 2) }}</span> Kbps</p>
        <p>TX Traffic: <span id="tx-traffic">{{ number_format($traffic['tx'] / 1024, 2) }}</span> Kbps</p>
        
        <canvas id="trafficChart" width="400" height="200"></canvas>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('trafficChart').getContext('2d');
                var trafficChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [], // Initially empty labels
                        datasets: [
                            {
                                label: 'Received Traffic (Kbps)',
                                data: [],
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            },
                            {
                                label: 'Transmitted Traffic (Kbps)',
                                data: [],
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Traffic (Kbps)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time'
                                }
                            }
                        }
                    }
                });

                function fetchTrafficData() {
                    $.ajax({
                        url: '{{ route('mikrotik.traffic') }}',
                        method: 'GET',
                        data: { interface: '{{ $interfaceName }}', ipmikrotik: '{{ $ipmikrotikreq }}' },
                        success: function(response) {
                            if (response.error) {
                                alert(response.error);
                                return;
                            }

                            var rxKbps = (response.traffic.rx / 1024).toFixed(2); // Convert to Kbps
                            var txKbps = (response.traffic.tx / 1024).toFixed(2); // Convert to Kbps

                            var now = new Date().toLocaleTimeString(); // Current time as label

                            if (trafficChart.data.labels.length > 20) {
                                trafficChart.data.labels.shift(); // Remove oldest label
                                trafficChart.data.datasets[0].data.shift(); // Remove oldest RX data
                                trafficChart.data.datasets[1].data.shift(); // Remove oldest TX data
                            }

                            trafficChart.data.labels.push(now); // Add new label
                            trafficChart.data.datasets[0].data.push(parseFloat(rxKbps)); // Add new RX data
                            trafficChart.data.datasets[1].data.push(parseFloat(txKbps)); // Add new TX data

                            trafficChart.update(); // Update chart

                            // Update traffic display
                            $('#rx-traffic').text(rxKbps);
                            $('#tx-traffic').text(txKbps);
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr);
                        }
                    });
                }

                setInterval(fetchTrafficData, 2000); // Poll every 2 seconds
                fetchTrafficData(); // Initial fetch
            });
        </script>
    @endif
</body>
</html>
