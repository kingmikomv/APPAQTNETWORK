<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('login') }}">{{ env('APP_NAME') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('login') }}">AN</a>
        </div>
        <ul class="sidebar-menu">
            <!-- Server Section -->
            <li class="menu-header">Server</li>
            <li class="dropdown {{ request()->routeIs('datavpn', 'dashboardmikrotik', 'datamikrotik', 'dataolt') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-server"></i> <span>Server</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('datavpn') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('datavpn') }}"><i class="fas fa-tty"></i> Data VPN</a>
                    </li>
                    @if(session('mikrotik_connected'))
                    <li class="{{ request()->routeIs('dashboardmikrotik') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboardmikrotik', ['ipmikrotik' => session('ipmikrotik')]) }}"><i class="fas fa-dice-d6"></i> Dashboard</a>
                    </li>
                    @else
                    <li class="{{ request()->routeIs('datamikrotik') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('datamikrotik') }}"><i class="fas fa-sitemap"></i> Data Mikrotik</a>
                    </li>
                    @endif
                    <li class="{{ request()->routeIs('dataolt') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dataolt') }}"><i class="fas fa-project-diagram"></i> Data OLT</a>
                    </li>
                </ul>
            </li>

            <!-- Main Menu Section (Shown if connected to MikroTik) -->
            @if(session('mikrotik_connected'))
            <li class="menu-header">Main Menu</li>
            <li class="dropdown {{ request()->routeIs('active-connection', 'aksesschedule', 'aksessecret', 'aksesinterface', 'aksesactivehotspot', 'aksesuserhotspot') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-route"></i> <span>Main Menu</span></a>
                <ul class="dropdown-menu">
                    <!-- Monitoring -->
                    <li class="dropdown {{ request()->routeIs('active-connection', 'aksesschedule') ? 'active' : '' }}">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-sitemap"></i> <span>Monitoring</span></a>
                        <ul class="dropdown-menu">
                            <li class="{{ request()->routeIs('active-connection') ? 'active' : '' }}">
                                <a href="{{ route('active-connection', ['ipmikrotik' => session('ipmikrotik')]) }}">Active Connection</a>
                            </li>
                            <li class="{{ request()->routeIs('aksesschedule') ? 'active' : '' }}">
                                <a href="{{ route('aksesschedule', ['ipmikrotik' => session('ipmikrotik')]) }}">Schedule</a>
                            </li>
                        </ul>
                    </li>

                    <!-- PPP -->
                    <li class="dropdown {{ request()->routeIs('aksessecret') ? 'active' : '' }}">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>PPP</span></a>
                        <ul class="dropdown-menu">
                            <li class="{{ request()->routeIs('aksessecret') ? 'active' : '' }}">
                                <a href="{{ route('aksessecret', ['ipmikrotik' => session('ipmikrotik')]) }}">Secret</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Interface -->
                    <li class="dropdown {{ request()->routeIs('aksesinterface') ? 'active' : '' }}">
                        <a href="#" class="nav-link has-dropdown"><i class="fa fa-tasks"></i> <span>Interface</span></a>
                        <ul class="dropdown-menu">
                            <li class="{{ request()->routeIs('aksesinterface') ? 'active' : '' }}">
                                <a href="{{ route('aksesinterface', ['ipmikrotik' => session('ipmikrotik')]) }}">Interface</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Hotspot -->
                    <li class="dropdown {{ request()->routeIs('aksesactivehotspot', 'aksesuserhotspot') ? 'active' : '' }}">
                        <a href="#" class="nav-link has-dropdown"><i class="fa fa-rss"></i> <span>Hotspot</span></a>
                        <ul class="dropdown-menu">
                            <li class="{{ request()->routeIs('aksesactivehotspot') ? 'active' : '' }}">
                                <a href="{{ route('aksesactivehotspot', ['ipmikrotik' => session('ipmikrotik')]) }}">Active</a>
                            </li>
                            <li class="{{ request()->routeIs('aksesuserhotspot') ? 'active' : '' }}">
                                <a href="{{ route('aksesuserhotspot', ['ipmikrotik' => session('ipmikrotik')]) }}">User</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif
            @if(['can:isAdmin', 'can:isUser'])
            <li class="dropdown {{ request()->routeIs('shop') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-shopping-cart"></i> <span>Beli</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('shop') ? 'active' : '' }}">
                        <a href="{{ route('shop') }}"><i class="fas fa-coins"></i> Coin</a>
                    </li>
                  
                </ul>
            </li>
            @endif
            
            <!-- Admin Section -->
            @can('isAdmin')
            <li class="menu-header">Admin</li>
            <li class="dropdown {{ request()->routeIs('member', 'undianadmin') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>User</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('member') ? 'active' : '' }}">
                        <a href="{{ route('member') }}"><i class="fas fa-users"></i> Data Pengguna</a>
                    </li>
                    <li class="{{ request()->routeIs('undianadmin') ? 'active' : '' }}">
                        <a href="{{ route('undianadmin') }}"><i class="fas fa-gift"></i>Undian</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown {{ request()->routeIs('transaksiCoin') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-money-bill-wave"></i> <span>Transaksi</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('transaksiCoin') ? 'active' : '' }}">
                        <a href="{{ route('transaksiCoin') }}"><i class="fas fa-coins"></i> Coin</a>
                    </li>
                  
                </ul>
            </li>

            @endcan
            <li class="dropdown ">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-user"></i> <span>Profil</span></a>
                <ul class="dropdown-menu">
                    <li class="">
                        <a href="{{route('myakun')}}"><i class="fas fa-key"></i> Akun Saya</a>
                    </li>
                  
                </ul>
            </li>
        </ul>

        <!-- Footer -->
        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="#" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Tutorial
            </a>
        </div>
    </aside>
</div>
