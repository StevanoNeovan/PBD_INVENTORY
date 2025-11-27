@extends('layouts.master')
@section('title', 'Monitoring Stok')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line"></i> Monitoring Stok Barang</h2>
        <a href="{{ route('superadmin.kartu-stok.index') }}" class="btn btn-secondary">
            <i class="fas fa-clipboard-list"></i> Kartu Stok
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBarang }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Stok Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stokTersedia }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stok Menipis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stokMenipis }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stokHabis }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Stok Menipis -->
    @if(count($barangMenipis) > 0)
    <div class="alert alert-warning alert-dismissible fade show">
        <h5><i class="fas fa-exclamation-triangle"></i> Peringatan Stok Menipis!</h5>
        <p>Ada <strong>{{ count($barangMenipis) }} barang</strong> dengan stok menipis (≤ 10). Segera lakukan pengadaan!</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(count($barangHabis) > 0)
    <div class="alert alert-danger alert-dismissible fade show">
        <h5><i class="fas fa-times-circle"></i> Stok Habis!</h5>
        <p>Ada <strong>{{ count($barangHabis) }} barang</strong> yang stoknya habis. Tidak bisa dijual!</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="stokTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                <i class="fas fa-boxes"></i> Semua Barang ({{ count($semuaBarang) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-success" id="tersedia-tab" data-bs-toggle="tab" data-bs-target="#tersedia" type="button">
                <i class="fas fa-check-circle"></i> Tersedia ({{ count($barangTersedia) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-warning" id="menipis-tab" data-bs-toggle="tab" data-bs-target="#menipis" type="button">
                <i class="fas fa-exclamation-triangle"></i> Menipis ({{ count($barangMenipis) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-danger" id="habis-tab" data-bs-toggle="tab" data-bs-target="#habis" type="button">
                <i class="fas fa-times-circle"></i> Habis ({{ count($barangHabis) }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="stokTabContent">
        <!-- Tab Semua -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <div class="card shadow">
                <div class="card-body">
                    @include('superadmin.kartu-stok.partials.table-stok', ['barangs' => $semuaBarang])
                </div>
            </div>
        </div>

        <!-- Tab Tersedia -->
        <div class="tab-pane fade" id="tersedia" role="tabpanel">
            <div class="card shadow">
                <div class="card-body">
                    @include('superadmin.kartu-stok.partials.table-stok', ['barangs' => $barangTersedia])
                </div>
            </div>
        </div>

        <!-- Tab Menipis -->
        <div class="tab-pane fade" id="menipis" role="tabpanel">
            <div class="card shadow">
                <div class="card-body">
                    @include('superadmin.kartu-stok.partials.table-stok', ['barangs' => $barangMenipis])
                </div>
            </div>
        </div>

        <!-- Tab Habis -->
        <div class="tab-pane fade" id="habis" role="tabpanel">
            <div class="card shadow">
                <div class="card-body">
                    @include('superadmin.kartu-stok.partials.table-stok', ['barangs' => $barangHabis])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.table-stok').each(function() {
        $(this).DataTable({
            order: [[3, 'asc']], // Sort by stok ascending
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 25
        });
    });
});
</script>
@endpush