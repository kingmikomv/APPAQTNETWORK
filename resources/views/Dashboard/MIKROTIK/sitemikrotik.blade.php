<x-dcore.head />
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <x-dcore.nav />
      <x-dcore.sidebar />
      <div class="main-content">
        <section class="section">
        {{-- <x-dcore.card /> --}}

        <!-- MAIN OF CENTER CONTENT -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                
                <div class="card-body text-center">
                  
                  <h3>Selamat Datang Di Mikrotik Site {{$site ?? '-'}}</h3>
                </div>
              </div>
              
            </div>
        
          </div>
        <!-- END OF CENTER CONTENT -->
        <form action="{{ route('keluarmikrotik') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>


        </section>
    
      </div>
      <x-dcore.footer />
    </div>
  </div>
<x-dcore.script />
