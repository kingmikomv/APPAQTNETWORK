

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
                            <div class="card-body">
                                <form action="{{route('buatundian')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">PILIH SERVER YANG AKAN MELAKUKAN UNDIAN</label>
                                        <select class="form-control" name="site">
                                            <option disabled selected value>Pilih Server</option>
                                                @foreach($mikrotik as $mk)
                                            <option>{{$mk->site}}</option>
                                                @endforeach
                                        </select>
                                      </div>
                                      <div class="form-group">
                                        <label for="exampleFormControlSelect1">HADIAH UNDIAN</label>
                                        <input type="text" class="form-control" name="hadiah" placeholder="Hadiah Undian">
                                      </div>
                                    
                                      <div class="form-group">
                                        <label for="exampleFormControlFile1">FOTO HADIAH</label>
                                        <input type="file" class="form-control-file" name="file">
                                      </div>
                                      <div class="form-group">
                                        <label for="exampleFormControlFile1">TANGGAL DIMULAI</label>
                                        <input type="date" class="form-control" name="tanggal">
                                      </div>
                                      
                                      <div class="form-group">
                                        <input type="submit" class="btn btn-primary btn-block" value="Buat Undian">
                                      </div>
                                      
                                    </form>
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
