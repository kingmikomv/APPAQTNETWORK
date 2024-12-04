<x-dcore.head />
<style>
    .card {
    padding: 20px; /* Memberikan padding pada keseluruhan card */
  }

  .card p {
    margin-bottom: 15px; /* Memberikan jarak antara paragraf dan elemen lainnya */
    padding: 10px; /* Memberikan ruang di dalam elemen p */
  }

  .card hr {
    margin-top: 20px; /* Memberikan jarak antara garis horizontal dan teks */
    margin-bottom: 20px;
  }
  
</style>
<div id="app"> 
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <x-dcore.modal />

        <div class="main-content">
            <section class="section">
              
                <!-- MAIN CONTENT -->
                <div class="row no-gutters">
                    <!-- Pemberitahuan Section -->
                   
                    <!-- Data VPN Section -->
                    <div class="col-12">
                        <div class="card wide-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Data VPN</h4>
                                <div>
                                    <!-- Button to Trigger Add VPN Modal -->
                                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#addVpnModal">
                                        <i class="fas fa-plus"></i> Tambah VPN
                                    </button>
                            
                                    <!-- Button to Trigger Info Modal -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#info">
                                        <i class="fas fa-info"></i> Informasi dan Cara Penggunaan
                                    </button>
                                </div>
                                
                                


                            </div>
                            <div class="card-body">
                                @if($data->isEmpty())
                                    <p>No data found for your unique ID.</p>
                                @else
                                    <div class="table-responsive">
                                        <table id="vpnTable" class="table table-striped table-bordered display nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Username</th>
                                                    <th>Password</th>
                                                    <th>IP Address</th>
                                                    <th>PORT Winbox</th>
                                                    <th>VPN MikroTik</th>
                                                    <th>Skrip Mikrotik</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach($data as $item)
                                                    <tr>
                                                        <td>{{$no++}}</td>
                                                        <td>{{ $item->namaakun }}</td>
                                                        <td>{{ $item->username }}</td>
                                                        <td>{{ $item->password }}</td>
                                                        <td>{{ $item->ipaddress }}</td>
                                                        <td>{{ $item->portwbx }}</td>
                                                        <!-- <td>{{ "id-1.aqtnetwork.my.id:". $item->portmikrotik }}</td> -->
                                                        <td>
                                <!-- Address MikroTik -->
                                <span id="mikrotikAddress{{ $item->id }}">id-1.aqtnetwork.my.id:{{ $item->portmikrotik }}</span>
                                <!-- Tombol Copy -->
                            </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#vpnInfoModal">
                                                                <i class="fas fa-info"></i> Info
                                                            </button>
                                                        </td>
                                                        <td>
                                                          <button type="button" class="btn btn-danger btn-delete" data-id="{{ $item->id }}" data-username="{{ $item->username }}">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END MAIN CONTENT -->
            </section>
        </div>

        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />

<!-- Modal for Add VPN -->
<div class="modal fade" id="addVpnModal" tabindex="-1" role="dialog" aria-labelledby="addVpnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVpnModalLabel">Tambah VPN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="vpnForm" action="{{ route('uploadvpn') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="namaAkun">Nama Akun</label>
                        <input type="text" class="form-control" placeholder="Nama Akun" name="namaakun" id="namaAkunInput">
                    </div>

                    <div class="form-group">
                        <label for="username">User</label>
                        <input type="text" class="form-control" placeholder="Username" name="username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" placeholder="Password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="password">Port Winbox ( OPTIONAL )</label>
                        <input type="number" class="form-control" placeholder="Default : 8291" name="portmk">
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-success" value="Buat VPN">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for VPN Info -->
<div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="vpnInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vpnInfoModalLabel">VPN Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
         
                              <p style="font-size: 20px;">VPN digunakan untuk menghubungkan Router MikroTik anda dengan Router kami melalui jaringan internet/public. 
                                Radius server kami tidak dapat meneruskan paket request dari router anda jika router anda tidak mempunyai IP Public atau tidak dalam satu jaringan. Setelah router MikroTik anda terhubung 
                                dengan router kami, otomatis radius server akan merespond paket request anda melalui IP Private dari VPN.
                            </p>
                            <hr>
                            <p class="mb-0" style="font-size: 20px;">Jika Router MikroTik anda tidak mempunyai IP Public, silahkan buat account vpn pada form yang sudah di siapkan. Gratis tanpa ada biaya tambahan dan boleh lebih dari satu.</p>
                      

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="vpnInfoModal" tabindex="-1" role="dialog" aria-labelledby="vpnInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vpnInfoModalLabel">VPN Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- VPN Usage Tips -->
                <ol class="list-unstyled mb-4">
                    <li>
                        <h5><i class="fa fa-info-circle"></i> Tips - Cara penggunaan</h5>
                        <ol>
                            <li>Pilih salah satu mode yang akan digunakan.</li>
                            <li>Salin / Copy seluruh isi script pada kolom mode yang dipilih.</li>
                            <li>Login mikrotik melalui Winbox, buka menu <strong>New Terminal</strong> kemudian Tempel / Paste script yang sudah di salin / copy sebelumnya, lanjut tekan tombol Enter di keyboard.</li>
                            <li>Buka menu <strong>PPP > Interface</strong> jika langkah di atas sudah berhasil, maka akan tampil interface VPN baru sesuai mode yang dipilih.</li>
                            <li>Lihat status interface VPN, jika belum terhubung / Connected silahkan coba menggunakan mode yang lain. Jika sudah terhubung / connected (cirinya ada icon huruf <b>R</b> di samping interface VPN).</li>
                            <li>Gagal terhubung / Connected biasanya karna mode yang anda pilih di blok oleh ISP anda.</li>
                        </ol>
                    </li>
                </ol>

                <!-- VPN Script Section -->
                <div class="form-group">
                    <label for="scriptL2tp">Mode L2TP</label>
                    <div class="copy-script p-1" data-id="scriptL2tp">
                        <button type="button" class="btn btn-sm btn-secondary">Copy</button>
                    </div>
                    <textarea class="form-control pt-3" rows="5" readonly id="scriptL2tp"></textarea>
                </div>

                <div class="form-group">
                    <label for="scriptPptp">Mode PPTP</label>
                    <div class="copy-script p-1" data-id="scriptPptp">
                        <button type="button" class="btn btn-sm btn-secondary">Copy</button>
                    </div>
                    <textarea class="form-control pt-3" rows="5" readonly id="scriptPptp"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize DataTable with options
        $('#vpnTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            responsive: true,
            scrollX: true // Enables horizontal scrolling
        });

        // Handle the Info button click
        $('#vpnTable').on('click', '.btn-primary', function() {
            // Get the data from the row
            var row = $(this).closest('tr');
            var namaAkun = row.find('td:eq(1)').text();
            var username = row.find('td:eq(2)').text();
            var password = row.find('td:eq(3)').text();
            var ipAddress = row.find('td:eq(4)').text();
            var wbx = row.find('td:eq(5)').text();

            // Generate the MikroTik L2TP script dynamically
            var skripL2tp = `/ip service set api port=9000          
/ip service set winbox port=${wbx}
/interface l2tp-client add name="AQTNetwork_VPN" connect-to="id-1.aqtnetwork.my.id" user="${username}" password="${password}" comment="AQT_VPN_L2TP" disabled=no`;

            // Generate the MikroTik PPTP script 
            var skripPptp = `/ip service set api port=9000
/ip service set winbox port=${wbx}
/interface pptp-client add name="AQTNetwork_VPN" connect-to="id-1.aqtnetwork.my.id" user="${username}" password="${password}" comment="AQT_VPN_PPTP" disabled=no`;

            // Set the data in the textareas
            $('#scriptL2tp').val(skripL2tp);
            $('#scriptPptp').val(skripPptp);

            // Show the modal
            $('#vpnInfoModal').modal('show');
        });

        // Handle the Copy button click for L2TP
        $('.copy-script[data-id="scriptL2tp"] button').click(function() {
            var skripMikrotik = $('#scriptL2tp').val();
            navigator.clipboard.writeText(skripMikrotik).then(function() {
                alert('Script L2TP copied to clipboard!');
            }, function(err) {
                console.error('Failed to copy script: ', err);
            });
        });

        // Handle the Copy button click for PPTP
        $('.copy-script[data-id="scriptPptp"] button').click(function() {
            var skripMikrotik = $('#scriptPptp').val();
            navigator.clipboard.writeText(skripMikrotik).then(function() {
                alert('Script PPTP copied to clipboard!');
            }, function(err) {
                console.error('Failed to copy script: ', err);
            });
        });

        // Handle delete button click
        $('#vpnTable').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var username = $(this).data('username');

            // Replace the confirm dialog with SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete the data
                    $.ajax({
                        url: 'datavpn/' + id,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "username": username // Include the username in the data
                        },
                        success: function(response) {
                            // Replace the alert with SweetAlert success message
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to update the table
                            });
                        },
                        error: function(xhr) {
                            // Replace the alert with SweetAlert error message
                            Swal.fire(
                                'Gagal!',
                                'Gagal menghapus data: ' + xhr.responseText,
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Prevent spaces from being entered in the Nama Akun field
        document.getElementById('namaAkunInput').addEventListener('keydown', function(event) {
            if (event.key === ' ') {
                event.preventDefault(); // Prevent space from being entered
            }
        });
    });

    @if (session("success"))
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            showConfirmButton: true
        });
    @elseif (session("error"))
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            showConfirmButton: true
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Input Error',
            text: '{{ implode(", ", $errors->all()) }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
<!-- Tambahkan script untuk copy ke clipboard -->
<script>
    function copyToClipboard(elementId) {
        // Ambil teks dari elemen span berdasarkan ID
        var copyText = document.getElementById(elementId).innerText;

        // Buat elemen textarea sementara untuk menyalin teks
        var tempInput = document.createElement("textarea");
        tempInput.value = copyText;
        document.body.appendChild(tempInput);

        // Salin teks dari textarea sementara
        tempInput.select();
        document.execCommand("copy");

        // Hapus elemen sementara setelah penyalinan
        document.body.removeChild(tempInput);

        // Tampilkan notifikasi
        alert("Copied: " + copyText);
    }
</script>