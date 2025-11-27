@extends('layouts.master')
@section('title', 'Detail Kartu Stok')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list"></i> Riwayat Kartu Stok - {{ $barang->nama_barang }}</h2>
        <a href="{{ route('superadmin.kartu-stok.monitoring') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Info Barang -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">Informasi Barang</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="40%">Nama Barang</th>
                            <td>: <strong>{{ $barang->nama_barang }}</strong></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>: {{ $barang->kategori_barang }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>: {{ $barang->nama_satuan }}</td>
                        </tr>
                        <tr>
                            <th>Harga</th>
                            <td>: Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Stok Saat Ini</th>
                            <td>: 
                                <h4 class="mb-0">
                                    @if($barang->stok_tersedia == 0)
                                        <span class="text-danger">{{ $barang->stok_tersedia }}</span>
                                    @elseif($barang->stok_tersedia <= 10)
                                        <span class="text-warning">{{ $barang->stok_tersedia }}</span>
                                    @else
                                        <span class="text-success">{{ $barang->stok_tersedia }}</span>
                                    @endif
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <th>Status Stok</th>
                            <td>: 
                                @if($barang->status_stok == 'Habis')
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($barang->status_stok == 'Menipis')
                                    <span class="badge bg-warning">Menipis</span>
                                @else
                                    <span class="badge bg-success">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Pergerakan -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMasuk }}</div>
                            <small class="text-muted">Dari penerimaan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow border-left-danger">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Keluar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKeluar }}</div>
                            <small class="text-muted">Dari penjualan & retur</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Stok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $barang->stok_tersedia }}</div>
                            <small class="text-muted">Stok saat ini</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0">Ringkasan Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="text-success">{{ $jumlahPenerimaan }}</h5>
                            <small>Transaksi Penerimaan</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-primary">{{ $jumlahPenjualan }}</h5>
                            <small>Transaksi Penjualan</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-warning">{{ $jumlahRetur }}</h5>
                            <small>Transaksi Retur</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Kartu Stok -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0"><i class="fas fa-history"></i> Riwayat Pergerakan Stok</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Transaksi</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Keluar</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">ID Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if($r->jenis_transaksi == 'P')
                                    <span class="badge bg-success">Penerimaan</span>
                                @elseif($r->jenis_transaksi == 'J')
                                    <span class="badge bg-primary">Penjualan</span>
                                @elseif($r->jenis_transaksi == 'R')
                                    <span class="badge bg-warning">Retur</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($r->masuk > 0)
                                    <span class="badge bg-success">+{{ $r->masuk }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($r->keluar > 0)
                                    <span class="badge bg-danger">-{{ $r->keluar }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center"><strong>{{ $r->stock }}</strong></td>
                            <td class="text-center">
                                @if($r->jenis_transaksi == 'P')
                                    <a href="{{ route('superadmin.penerimaan.show', $r->idtransaksi) }}" class="btn btn-sm btn-info">
                                        #{{ $r->idtransaksi }}
                                    </a>
                                @elseif($r->jenis_transaksi == 'J')
                                    <a href="{{ route('superadmin.penjualan.show', $r->idtransaksi) }}" class="btn btn-sm btn-info">
                                        #{{ $r->idtransaksi }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">#{{ $r->idtransaksi }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada riwayat transaksi</td>
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