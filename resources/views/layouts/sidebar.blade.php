<nav id="sidebar" class="bg-dark text-white">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <h4><i class="fas fa-warehouse"></i> Inventory PBD</h4>
        <small class="text-muted">{{ auth()->user()->role->nama_role }}</small>
    </div>

    <ul class="list-unstyled components p-3">
        <!-- Dashboard -->
        <li class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('superadmin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <!-- Master Data -->
        <li>
            <a href="#masterDataSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                <i class="fas fa-database"></i> Master Data
            </a>
            <ul class="collapse list-unstyled" id="masterDataSubmenu">
                <li><a href="{{ route('superadmin.user.index') }}">👤 User</a></li>
                <li><a href="{{ route('superadmin.role.index') }}">🔐 Role</a></li>
                <li><a href="{{ route('superadmin.vendor.index') }}">🏢 Vendor</a></li>
                <li><a href="{{ route('superadmin.satuan.index') }}">📏 Satuan</a></li>
                <li><a href="{{ route('superadmin.barang.index') }}">📦 Barang</a></li>
                <li><a href="{{ route('superadmin.margin.index') }}">💰 Margin Penjualan</a></li>
            </ul>
        </li>

        <!-- Transaksi -->
        <li>
            <a href="#transaksiSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                <i class="fas fa-exchange-alt"></i> Transaksi
            </a>
            <ul class="collapse list-unstyled" id="transaksiSubmenu">
                <li><a href="{{ route('superadmin.pengadaan.index') }}">📥 Pengadaan</a></li>
                <li><a href="{{ route('superadmin.penerimaan.index') }}">✅ Penerimaan</a></li>
                <li><a href="{{ route('superadmin.retur.index') }}">↩️ Retur</a></li>
                <li><a href="{{ route('superadmin.penjualan.pos') }}">🛒 POS / Kasir</a></li>
                <li><a href="{{ route('superadmin.penjualan.index') }}">💵 Laporan Penjualan</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li>
            <a href="#laporanSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
            <ul class="collapse list-unstyled" id="laporanSubmenu">
                <li><a href="{{ route('superadmin.kartu-stok.index') }}">📊 Kartu Stok</a></li>
                <li><a href="{{ route('superadmin.kartu-stok.low-stock') }}">⚠️ Stok Menipis</a></li>
                <li><a href="{{ route('superadmin.penjualan.laporan-bulanan') }}">📈 Penjualan Bulanan</a></li>
            </ul>
        </li>
    </ul>
</nav>

<style>
#sidebar {
    min-width: 250px;
    max-width: 250px;
    min-height: 100vh;
    position: fixed;
    overflow-y: auto;
}

#sidebar ul li a {
    padding: 10px 15px;
    display: block;
    color: #adb5bd;
    text-decoration: none;
    transition: all 0.3s;
}

#sidebar ul li a:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

#sidebar ul li.active > a {
    background: #007bff;
    color: #fff;
}

#sidebar ul ul a {
    padding-left: 40px;
    font-size: 0.9em;
}

#content {
    margin-left: 250px;
    min-height: 100vh;
}
</style>