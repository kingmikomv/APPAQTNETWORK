<x-dcore.head />
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>

        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" style="font-size: 12px;" id="myTable2" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Start Date</th>
                                                <th>Start Time</th>
                                                <th>Run Count</th>
                                                <th>Duration (Minutes/Hours)</th>
                                                <th>Duration (Hours/Days)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            @foreach ($formattedData as $index => $schedule)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $schedule['name'] }}</td>
                                                    <td>{{ $schedule['start_date'] }}</td>
                                                    <td>{{ $schedule['start_time'] }}</td>
                                                    <td>{{ $schedule['run_count'] }}</td>
                                                    <td>
                                                        @php
                                                            $minutes = $schedule['run_count'] * 20 / 60;
                                                            echo $minutes < 60 ? round($minutes) . ' minutes' : round($minutes / 60) . ' hours';
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            $hours = $schedule['run_count'] * 20 / 60 / 60;
                                                            echo $hours < 24 ? round($hours) . ' hours' : round($hours / 24) . ' days';
                                                        @endphp
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

<!-- Modal for Schedule Details -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Schedule Details</h5>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <p><strong>Name:</strong> <span id="modalName"></span></p>
                <p><strong>Start Date:</strong> <span id="modalStartDate"></span></p>
                <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                <p><strong>Run Count:</strong> <span id="modalRunCount"></span></p>
                <p><strong>Duration (Minutes/Hours):</strong> <span id="modalDurationMinutesHours"></span></p>
                <p><strong>Duration (Hours/Days):</strong> <span id="modalDurationHoursDays"></span></p>
            </div>
            <div class="modal-footer">
                <button id="copyButton" class="btn btn-primary">Copy</button>
            </div>
        </div>
    </div>
</div>

<x-dcore.script />
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable with state saving enabled
        var table = $('#myTable2').DataTable({
            "pageLength": 50, // Set the default number of entries to 50
            "lengthMenu": [50, 100, 200, 300, 400, 500], // Options for number of entries to show
            "order": [[0, 'asc']],
            "destroy": true, // Allow DataTable to be re-initialized after the table refresh
            "stateSave": true // Enable state saving
        });

        // Function to reload the table data via AJAX
        function reloadTable() {
            $.ajax({
                url: "{{ route('aksesschedule') }}?ipmikrotik={{ request()->query('ipmikrotik') }}", // Pass the IP query parameter
                type: 'GET',
                dataType: 'json', // Expect JSON response
                success: function(data) {
                    // Clear existing table content
                    table.clear(); // Clear DataTable content

                    // Iterate through new data and populate table
                    $.each(data.formattedData, function(index, schedule) {
                        var minutes = schedule.run_count * 20 / 60;
                        var minutesDisplay = minutes < 60 ? Math.round(minutes) + ' menit' : Math.round(minutes / 60) + ' jam';

                        var hours = schedule.run_count * 20 / 60 / 60;
                        var hoursDisplay = hours < 24 ? Math.round(hours) + ' jam' : Math.round(hours / 24) + ' hari';

                        // Add row to DataTable
                        table.row.add([
                            index + 1,
                            schedule.name,
                            schedule.start_date,
                            schedule.start_time,
                            schedule.run_count,
                            minutesDisplay,
                            hoursDisplay
                        ]);
                    });

                    // Redraw the table to show the new data
                    table.draw();
                },
                error: function(xhr, status, error) {
                    console.error("Error refreshing data: ", status, error);
                }
            });
        }

        // Automatically reload the table every 20 seconds
        setInterval(reloadTable, 20000); // 20000 milliseconds = 20 seconds

        // Initial call to load data
        reloadTable();

        // Event listener for row click
        $('#myTable2 tbody').on('click', 'tr', function() {
            var data = table.row(this).data(); // Get data for the clicked row

            // Populate the modal with data
            $('#modalName').text(data[1]); // Name
            $('#modalStartDate').text(data[2]); // Start Date
            $('#modalStartTime').text(data[3]); // Start Time
            $('#modalRunCount').text(data[4]); // Run Count
            $('#modalDurationMinutesHours').text(data[5]); // Duration (Minutes/Hours)
            $('#modalDurationHoursDays').text(data[6]); // Duration (Hours/Days)

            // Show the modal
            $('#scheduleModal').modal('show');
        });
    });
</script>
<script>
    document.getElementById('copyButton').addEventListener('click', function() {
        // Mengambil elemen modal body
        var modalBody = document.getElementById('modalBodyContent');
        
        // Menyalin teks dari modal body
        var range = document.createRange();
        range.selectNode(modalBody);
        window.getSelection().removeAllRanges(); // Menghapus seleksi sebelumnya
        window.getSelection().addRange(range); // Menyeleksi teks modal body
        
        try {
            // Melakukan penyalinan
            var successful = document.execCommand('copy');
            if (successful) {
                alert('Data copied successfully!');
            } else {
                alert('Failed to copy data.');
            }
        } catch (err) {
            console.error('Error while copying data: ', err);
        }

        // Menghapus seleksi setelah penyalinan
        window.getSelection().removeAllRanges();
    });
</script>