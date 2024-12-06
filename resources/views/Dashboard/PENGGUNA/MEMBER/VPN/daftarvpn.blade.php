

<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row no-gutters"> <!-- Remove gutter space between columns -->
                    <!-- Welcome Card -->
                    <div class="col-12"> <!-- Full width column -->
                        <div class="card wide-card">
                            <div class="card-body text-center table-responsive">
                                <table class="table" id="tableMember">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama VPN</td>
                                            <td>IP Address</td>
                                            <td>Username</td>
                                            <td>Password</td>
                                            <td>PORT API</td>
                                            <td>PORT WEB</td>
                                            <td>PORT MIKROTIK</td>
                                            <td>OPT</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($dataVPN as $mb)
                                          
                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$mb->namaakun}}</td>
                                            <td>{{$mb->ipaddress}}</td>
                                            <td>{{$mb->username}}</td>
                                            <td>{{$mb->password}}</td>
                                            <td>{{$mb->portapi}}</td>
                                            <td>{{$mb->portweb}}</td>
                                            <td>{{$mb->portmikrotik}}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                      Option
                                                    </button>
                                                    <div class="dropdown-menu">
                                                      
                                                    </div>
                                                  </div>
                                            </td>
                                        </tr>

                                          
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- END OF CENTER CONTENT -->
            </section>
        </div>
        <x-dcore.footer />
    </div>
</div>
<x-dcore.script />
<script>
    $(document).ready(function () {
        // Initialize DataTable with options
        $('#tableMember').DataTable({
            columnDefs: [
                { className: "text-center", targets: "_all" } // Terapkan ke semua kolom
            ]
        });
    });

</script>