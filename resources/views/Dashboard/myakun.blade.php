<x-dcore.head />

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-dcore.nav />
        <x-dcore.sidebar />

        <div class="main-content">
            <section class="section">
                <div class="row">
                    <div class="col-12">
                        <div class="card wide-card">
                            <!-- Card Header -->
                            <div class="card-header">
                                <h4 class="w-100"><i class="fas fa-key"></i> My Account</h4>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <form action="{{ route('account.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <!-- Name -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control @error('name') is-invalid @enderror" 
                                                    id="name" 
                                                    name="name" 
                                                    value="{{ old('name', Auth::user()->name) }}" 
                                                    required
                                                />
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Email (Readonly) -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    id="email" 
                                                    value="{{ Auth::user()->email }}" 
                                                    readonly
                                                />
                                            </div>
                                        </div>
                                        <!-- WhatsApp Number -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefon">No Whatsapp</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control @error('telefon') is-invalid @enderror" 
                                                    id="telefon" 
                                                    name="telefon" 
                                                    value="{{ old('telefon', Auth::user()->telefon) }}" 
                                                />
                                                @error('telefon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Password -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input 
                                                    type="password" 
                                                    class="form-control @error('password') is-invalid @enderror" 
                                                    id="password" 
                                                    name="password"
                                                />
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Confirm Password -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input 
                                                    type="password" 
                                                    class="form-control" 
                                                    id="password_confirmation" 
                                                    name="password_confirmation"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                                
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <x-dcore.footer />
    </div>
</div>

<x-dcore.script />
@if (session('success'))
              <script>
                  Swal.fire({
                      icon: 'success',
                      title: '{{ session('success') }}',
                      showConfirmButton: true
                  });
              </script>
          @elseif (session('error'))
              <script>
                  Swal.fire({
                      icon: 'error',
                      title: '{{ session('error') }}',
                      showConfirmButton: true
                  });
              </script>
          @endif