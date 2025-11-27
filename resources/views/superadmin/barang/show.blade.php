@extends('layouts.master')
@section('title', 'Detail Barang')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes"></i> Detail Barang</h2>
        <div>
            <a href="{{ route('superadmin.barang.edit', $barang->idbarang) }}" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('superadmin.barang.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Informasi Barang</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="35%">ID Barang</th><td>: <strong>#{{ $barang->idbarang }}</strong></td></tr>
                        <tr><th>Nama Barang</th><td>: <strong>{{ $barang->nama_barang }}</strong></td></tr>
                        <tr><th>Kategori</th><td>: <span class="badge bg-info">{{ $barang->kategori_barang }}</span></td></tr>
                        <tr><th>Satuan</th><td>: {{ $barang->nama_satuan }}</td></tr>
                        <tr><th>Harga</th><td>: <strong class="text-success">Rp {{ number_format($barang->harga) }}</strong></td></tr>
                        <tr><th>Status</th><td>: <span class="badge bg-{{ $barang->status == 'Aktif' ? 'success' : 'danger' }}">{{ $barang->status }}</span></td></tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Informasi Stok</h5></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="text-muted">Total Masuk</h6>
                            <h3 class="text-success">{{ $stok->total_masuk ?? 0 }}</h3>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted">Total Keluar</h6>
                            <h3 class="text-danger">{{ $stok->total_keluar ?? 0 }}</h3>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted">Saldo Akhir</h6>
                            <h3 class="text-primary">{{ $stok->saldo_akhir ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Riwayat Transaksi Stok (10 Terakhir)</h5></div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if(!empty($riwayatStok) && count($riwayatStok) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr><th>Tanggal</th><th>Jenis</th><th>Masuk</th><th>Keluar</th><th>Saldo</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatStok as $riwayat)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($riwayat->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($riwayat->jenis_transaksi == 'P')
                                                <span class="badge bg-success">Pengadaan</span>
                                            @elseif($riwayat->jenis_transaksi == 'J')
                                                <span class="badge bg-primary">Penjualan</span>
                                            @elseif($riwayat->jenis_transaksi == 'R')
                                                <span class="badge bg-danger">Retur</span>
                                            @endif
                                        </td>
                                        <td class="text-success">{{ $riwayat->masuk }}</td>
                                        <td class="text-danger">{{ $riwayat->keluar }}</td>
                                        <td class="fw-bold">{{ $riwayat->stock }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada riwayat transaksi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection