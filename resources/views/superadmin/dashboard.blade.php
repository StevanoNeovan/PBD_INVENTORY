@extends('layouts.master')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Dashboard</h2>
            <p class="text-muted">Selamat datang, {{ auth()->user()->username }}!</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar"></i> {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Barang -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Barang Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalBarang }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Inventory -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Nilai Inventory
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($nilaiInventory, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Penjualan Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stok -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Stok
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalStok, 0, ',', '.') }} Unit
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="row">
        <!-- Chart Penjualan Bulanan -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> Grafik Penjualan Bulanan
                    </h6>
                    <small class="text-muted">6 Bulan Terakhir</small>
                </div>
                <div class="card-body">
                    <canvas id="chartPenjualan" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 5 Produk Terlaris -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-trophy"></i> Top 5 Produk Terlaris
                    </h6>
                    <small class="text-muted">30 Hari Terakhir</small>
                </div>
                <div class="card-body">
                    @if($topProduk->isEmpty())
                        <p class="text-center text-muted">Belum ada data penjualan</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($topProduk as $index => $produk)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary rounded-circle">{{ $index + 1 }}</span>
                                        <strong class="ms-2">{{ $produk->nama }}</strong>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-success fw-bold">{{ $produk->total_terjual }} unit</div>
                                        <small class="text-muted">Rp {{ number_format($produk->total_nilai) }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & Recent Transactions -->
    <div class="row">
        <!-- Stok Menipis -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow border-left-danger">
                <div class="card-header py-3 bg-danger text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle"></i> Stok Menipis (< 10 unit)
                    </h6>
                    <a href="{{ route('superadmin.kartu-stok.low-stock') }}" class="btn btn-sm btn-light">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($stokMenipis->isEmpty())
                        <p class="text-center text-muted">Semua stok aman 👍</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Stok</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stokMenipis as $barang)
                                    <tr>
                                        <td>{{ $barang->nama_barang }}</td>
                                        <td><span class="badge bg-secondary">{{ $barang->kategori_barang }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">{{ $barang->stok }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('superadmin.pengadaan.create') }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus"></i> Pesan
                                            </a>
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

        <!-- Transaksi Terakhir -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow border-left-info">
                <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-receipt"></i> Transaksi Penjualan Terakhir
                    </h6>
                    <a href="{{ route('superadmin.penjualan.index') }}" class="btn btn-sm btn-light">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($transaksiTerakhir->isEmpty())
                        <p class="text-center text-muted">Belum ada transaksi</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaksiTerakhir as $transaksi)
                                    <tr>
                                        <td><strong>#{{ $transaksi->idpenjualan }}</strong></td>
                                        <td>{{ Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transaksi->username }}</td>
                                        <td class="text-end text-success fw-bold">
                                            Rp {{ number_format($transaksi->total_nilai) }}
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

    <!-- Pengadaan Pending -->
    @if($pengadaanPending->isNotEmpty())
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow border-left-warning">
                <div class="card-header py-3 bg-warning d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clock"></i> Pengadaan Menunggu Penerimaan
                    </h6>
                    <a href="{{ route('superadmin.penerimaan.create') }}" class="btn btn-sm btn-dark">
                        Proses Penerimaan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Vendor</th>
                                    <th class="text-end">Total Nilai</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengadaanPending as $pengadaan)
                                <tr>
                                    <td><strong>#{{ $pengadaan->idpengadaan }}</strong></td>
                                    <td>{{ Carbon\Carbon::parse($pengadaan->timestamp)->format('d/m/Y') }}</td>
                                    <td>{{ $pengadaan->nama_vendor }}</td>
                                    <td class="text-end">Rp {{ number_format($pengadaan->total_nilai) }}</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    <td>
                                        <a href="{{ route('superadmin.pengadaan.show', $pengadaan->idpengadaan) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.text-xs {
    font-size: .7rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari backend
    var chartLabels = @json($chartLabels);
    var chartDataValues = @json($chartData);
    
    // Chart Penjualan Bulanan
    var ctx = document.getElementById('chartPenjualan');
    if (ctx) {
        ctx = ctx.getContext('2d');
        
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: chartDataValues,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush