<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    @can('isAdmin')
                        <!-- Statistik Transaksi -->
                        <div class="col-md-12">
                            <div class="card wide-card">
                                <div class="card-body text-center">
                                    <h5 class="mt-2">Statistik Transaksi</h5>
                                    <div class="chart-container">
                                        <canvas id="coinChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Data Mikrotik -->
                        <div class="col-md-6 mt-3">
                            <div class="card wide-card">
                                <div class="card-body text-center">
                                    <h5 class="mt-2">Data Mikrotik</h5>
                                    <p><strong>Total Perangkat: {{ $totalMikrotik }}</strong></p>

                                    <a href="" class="btn btn-primary btn-gradient btn-block mt-2">
                                        Lihat Data Mikrotik
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Data VPN -->
                        <div class="col-md-6 mt-3">
                            <div class="card wide-card">
                                <div class="card-body text-center">
                                    <h5 class="mt-2">Data VPN</h5>
                                    <p><strong>Total Akun: {{ $totalVPN }}</strong> </p>
                                    <a href="" class="btn btn-primary btn-gradient btn-block mt-2">
                                        Lihat Data VPN
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="card wide-card">
                                <div class="card-body text-center">
                                    <h3>Selamat Datang di Aplikasi Management Mikrotik (AMMIK) AQT Network V.0.1!</h3>
                                    <div class="row mt-3">
                                        <div class="col-md-4 col-12 mt-2">
                                            <a href="{{ route('datavpn') }}"
                                                class="btn btn-primary btn-gradient btn-block">Data VPN</a>
                                        </div>
                                        <div class="col-md-4 col-12 mt-2">
                                            <a href="{{ route('datamikrotik') }}"
                                                class="btn btn-primary btn-gradient btn-block">Data Mikrotik</a>
                                        </div>
                                        <div class="col-md-4 col-12 mt-2">
                                            <a href="{{ route('dataolt') }}"
                                                class="btn btn-primary btn-gradient btn-block">Data OLT</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </section>
        </div>

        <x-dcore.footer />
    </div>
</div>
<x-dcore.script />

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('coinChart').getContext('2d');
        const chartData = @json($chartTotalTransaksi);

        if (chartData) {
            const coinChart = new Chart(ctx, {
                type: 'polarArea', // Polar Area Chart
                data: {
                    labels: Object.keys(chartData),
                    datasets: [{
                        label: 'Total Transaksi',
                        data: Object.values(chartData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                        ],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allow flexibility in chart size
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                },
            });
        }
    });
</script>

<!-- CSS -->
<style>
    .chart-container {
        position: relative;
        width: 100%;
        max-width: 400px;
        /* Max width for larger screens */
        margin: 0 auto;
        /* Center align the chart */
    }

    canvas {
        width: 100%;
        height: auto;
        /* Automatically adjust height to maintain aspect ratio */
    }

    @media (max-width: 768px) {
        .chart-container {
            max-width: 250px;
            /* Reduce chart size for smaller screens */
        }
    }
</style>
