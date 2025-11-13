<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
            <i class="fas fa-bars"></i>
        </button>

        <div class="ms-auto d-flex align-items-center">
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-outline-danger position-relative" type="button" 
                        id="notificationDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @php
                        $lowStockCount = DB::table('v_laporan_stok_barang')
                            ->where('saldo_akhir', '<', 10)
                            ->where('saldo_akhir', '>', 0)
                            ->count();
                    @endphp
                    @if($lowStockCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $lowStockCount }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                    <li class="dropdown-header">Notifikasi Stok Menipis</li>
                    @if($lowStockCount > 0)
                        <li><a class="dropdown-item" href="{{ route('superadmin.kartu-stok.low-stock') }}">
                            <i class="fas fa-exclamation-triangle text-warning"></i> 
                            {{ $lowStockCount }} barang stok menipis
                        </a></li>
                    @else
                        <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
                    @endif
                </ul>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                        id="userDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ auth()->user()->username }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text"><strong>{{ auth()->user()->role->nama_role }}</strong></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>