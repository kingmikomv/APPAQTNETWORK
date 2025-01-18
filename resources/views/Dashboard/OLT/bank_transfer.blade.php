<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- MAIN OF CENTER CONTENT -->
                <div class="row no-gutters justify-content-center"> <!-- Center content horizontally -->
                    <!-- Payment Info Card -->
                    <div class="col-md-8"> <!-- Medium width for better presentation on larger screens -->
                        <div class="card shadow-lg rounded">
                            <div class="card-header text-center">
                                <h4 class="font-weight-bold">Total Pembayaran</h4>
                            </div>
                            <div class="card-body text-center">
                                <p class="lead mb-4">
                                    Total pembayaran yang harus dibayarkan adalah 
                                    <strong>{{ $paymentAmount }}</strong>
                                </p>
                                <p class="mb-4">Silahkan transfer ke rekening berikut:</p>
                                <div class="alert alert-info">
                                    <strong>Rekening Bank:</strong> 3031096659 (Bank Central Asia - BCA)
                                    <br>
                                    <strong>Nama Pemilik Rekening:</strong> Doni Irawan
                                </div>

                                <p class="mb-4">Pastikan Anda mentransfer dengan jumlah yang tepat.</p>

                                <!-- Optional: Add a button for uploading a payment proof -->
                                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#uploadModal">
                                    Upload Bukti Pembayaran
                                </button>
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

<!-- Modal for Upload Payment Proof -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('upload.payment.proof') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Pilih File Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="payment_proof" name="payment_proof" required>
                        
                    </div>
                    <button type="submit" class="btn btn-success">Kirim Bukti Pembayaran</button>
                </form>
            </div>
        </div>
    </div>
</div>

<x-dcore.script />
