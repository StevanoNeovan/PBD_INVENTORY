<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
            <i class="fas fa-bars"></i>
        </button>

        <div class="ms-auto d-flex align-items-center">
            <!-- Notification Button -->
            <div class="dropdown">
                <button class="btn btn-link position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg text-warning"></i>
                    @if($lowStockCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $lowStockCount }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-exclamation-triangle text-warning"></i> Notifikasi Stok Menipis</span>
                        @if($lowStockCount > 0)
                            <span class="badge bg-danger">{{ $lowStockCount }}</span>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    @if($lowStockCount > 0)
                        @foreach($lowStockItems as $item)
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('superadmin.kartu-stok.detail', $item->idbarang) }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        @if($item->stok_tersedia == 0)
                                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning fa-lg"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-bold text-truncate" style="max-width: 200px;">
                                            {{ $item->nama_barang }}
                                        </div>
                                        <div class="small text-muted">
                                            Stok: 
                                            @if($item->stok_tersedia == 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @else
                                                <span class="badge bg-warning">{{ $item->stok_tersedia }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @if(!$loop->last)
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        @endforeach
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary" href="{{ route('superadmin.kartu-stok.monitoring') }}">
                                <i class="fas fa-eye"></i> Lihat Semua Stok
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="dropdown-item text-center text-muted">
                                <i class="fas fa-check-circle text-success"></i>
                                <div class="mt-2">Semua stok aman</div>
                            </span>
                        </li>
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