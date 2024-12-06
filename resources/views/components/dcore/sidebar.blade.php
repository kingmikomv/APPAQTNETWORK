<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{route('login')}}">{{ env('APP_NAME') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{route('login')}}">AN</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Server</li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-server"></i> <span>Server</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('datavpn') }}">Data VPN</a></li>
                    @if(session('mikrotik_connected'))
                    <li><a class="nav-link" href="{{ route('dashboardmikrotik', ['ipmikrotik' => session('ipmikrotik')]) }}">Dashboard Mikrotik</a></li>
                    @else
                    <li><a class="nav-link" href="{{ route('datamikrotik') }}">Data Mikrotik</a></li>

                    @endif
                    <li><a class="nav-link" href="{{route('dataolt')}}">Data OLT</a></li>
                </ul>
            </li>

            {{-- Conditionally show IP and Setting menus if connected to MikroTik --}}
            @if(session('mikrotik_connected'))
            <li class="dropdown active">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-route"></i> <span>Main Menu</span></a>
                <ul class="dropdown-menu">
                    <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-sitemap"></i><span>Monitoring</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('active-connection', ['ipmikrotik' => session('ipmikrotik')])}}">Active Connection</a></li>
                            <li><a href="{{route('aksesschedule', ['ipmikrotik' => session('ipmikrotik')])}}">Scehdule</a></li>

                         
                        </ul>
                    </li>

                   

                    <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>PPP</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('aksessecret', ['ipmikrotik' => session('ipmikrotik')])}}">Secret</a></li>
                         
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fa fa-tasks"></i><span>Interface</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('aksesnightbore', ['ipmikrotik' => session('ipmikrotik')])}}">Neighbore</a></li>
                            <li><a href="{{route('aksesinterface', ['ipmikrotik' => session('ipmikrotik')])}}">Interface</a></li>
                         
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fa fa-rss"></i><span>Hotspot</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('aksesactivehotspot', ['ipmikrotik' => session('ipmikrotik')])}}">Active</a></li>
                            <li><a href="{{route('aksesuserhotspot', ['ipmikrotik' => session('ipmikrotik')])}}">User</a></li>
                         
                        </ul>
                    </li>
                    {{-- <li><a class="nav-link" href="{{route('aksesschedule', ['ipmikrotik' => session('ipmikrotik')])}}">Schedule</a></li> --}}
            
                    <!-- Dropdown di dalam dropdown -->
                    
                </ul>
            </li>
            

            @endif

            @can('isAdmin')
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>User</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{route('member')}}">Data Pengguna</a></li>
                        <li><a class="nav-link" href="">Daftar Mikrotik</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>Hiburan</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{route('undianadmin')}}">Undian</a></li>
                        <li><a class="nav-link" href="">Youtube</a></li>
                    </ul>
                </li>
            @endcan
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="#" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Billing
            </a>
        </div>
    </aside>
</div>
