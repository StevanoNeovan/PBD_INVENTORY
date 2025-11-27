@extends('layouts.master')
@section('title', 'Detail Satuan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-ruler"></i> Detail Satuan</h2>
        <div>
            <a href="{{ route('superadmin.satuan.edit', $satuans->idsatuan) }}" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('superadmin.satuan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Informasi Satuan</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="40%">ID Satuan</th><td>: <strong>#{{ $satuans->idsatuan }}</strong></td></tr>
                        <tr><th>Nama Satuan</th><td>: <strong>{{ $satuans->nama_satuan }}</strong></td></tr>
                        <tr><th>Status</th><td>: <span class="badge bg-{{ $satuans->status_satuan == 'Aktif' ? 'success' : 'danger' }}">{{ $satuans->status_satuan }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Barang yang Menggunakan Satuan Ini</h5></div>
                <div class="card-body">
                    @if(!empty($barangs) && count($barangs) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>Nama Barang</th><th>Harga</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach($barangs as $brg)
                                    <tr>
                                        <td>{{ $brg->nama }}</td>
                                        <td>Rp {{ number_format($brg->harga) }}</td>
                                        <td><span class="badge bg-{{ $brg->status == 'Aktif' ? 'success' : 'danger' }}">{{ $brg->status}}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada barang yang menggunakan satuan ini</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection