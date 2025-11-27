@extends('layouts.master')
@section('title', 'Kartu Stok')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list"></i> Kartu Stok Barang</h2>
        <a href="{{ route('superadmin.kartu-stok.monitoring') }}" class="btn btn-info">
            <i class="fas fa-chart-line"></i> Monitoring Stok
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0"><i class="fas fa-filter"></i> Filter Kartu Stok</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.kartu-stok.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Barang</label>
                    <select name="idbarang" class="form-select">
                        <option value="">-- Semua Barang --</option>
                        @foreach($barangs as $b)
                        <option value="{{ $b->idbarang }}" {{ request('idbarang') == $b->idbarang ? 'selected' : '' }}>
                            {{ $b->nama_barang }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Jenis Transaksi</label>
                    <select name="jenis_transaksi" class="form-select">
                        <option value="">-- Semua Transaksi --</option>
                        <option value="P" {{ request('jenis_transaksi') == 'P' ? 'selected' : '' }}>Penerimaan</option>
                        <option value="J" {{ request('jenis_transaksi') == 'J' ? 'selected' : '' }}>Penjualan</option>
                        <option value="R" {{ request('jenis_transaksi') == 'R' ? 'selected' : '' }}>Retur</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-2">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kartu Stok -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h6 class="m-0"><i class="fas fa-list"></i> Riwayat Kartu Stok</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Jenis Transaksi</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Keluar</th>
                            <th class="text-center">Saldo Stok</th>
                            <th class="text-center">ID Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kartuStoks as $index => $ks)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($ks->created_at)->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $ks->nama_barang }}</strong></td>
                            <td>
                                @if($ks->jenis_transaksi == 'P')
                                    <span class="badge bg-success"><i class="fas fa-box-open"></i> Penerimaan</span>
                                @elseif($ks->jenis_transaksi == 'J')
                                    <span class="badge bg-primary"><i class="fas fa-cash-register"></i> Penjualan</span>
                                @elseif($ks->jenis_transaksi == 'R')
                                    <span class="badge bg-warning"><i class="fas fa-undo"></i> Retur</span>
                                @else
                                    <span class="badge bg-secondary">Lainnya</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($ks->masuk > 0)
                                    <span class="badge bg-success">+{{ $ks->masuk }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($ks->keluar > 0)
                                    <span class="badge bg-danger">-{{ $ks->keluar }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong class="text-primary">{{ $ks->stock }}</strong>
                            </td>
                            <td class="text-center">
                                @if($ks->jenis_transaksi == 'P')
                                    <a href="{{ route('superadmin.penerimaan.show', $ks->idtransaksi) }}" class="btn btn-sm btn-info">
                                        #{{ $ks->idtransaksi }}
                                    </a>
                                @elseif($ks->jenis_transaksi == 'J')
                                    <a href="{{ route('superadmin.penjualan.show', $ks->idtransaksi) }}" class="btn btn-sm btn-info">
                                        #{{ $ks->idtransaksi }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">#{{ $ks->idtransaksi }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kartu stok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 50
    });
});
</script>
@endpush