@extends('layouts.master')
@section('title', 'Detail Vendor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-building"></i> Detail Vendor</h2>
        <div>
            <a href="{{ route('superadmin.vendor.edit', $vendor->idvendor) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('superadmin.vendor.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Informasi Vendor</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="35%">ID Vendor</th><td>: <strong>#{{ $vendor->idvendor }}</strong></td></tr>
                        <tr><th>Nama Vendor</th><td>: <strong>{{ $vendor->nama_vendor }}</strong></td></tr>
                        <tr><th>Legalitas</th><td>: <span class="badge bg-{{ $vendor->legalitas == 'Berbadan Hukum' ? 'success' : 'secondary' }}">{{ $vendor->legalitas }}</span></td></tr>
                        <tr><th>Status</th><td>: <span class="badge bg-{{ $vendor->status_vendor == 'Aktif' ? 'success' : 'danger' }}">{{ $vendor->status_vendor }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Riwayat Pengadaan</h5></div>
                <div class="card-body">
                    @if(!empty($riwayatPengadaan) && count($riwayatPengadaan) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>ID</th><th>Tanggal</th><th>Total</th></tr></thead>
                                <tbody>
                                    @foreach($riwayatPengadaan as $p)
                                    <tr>
                                        <td>#{{ $p->idpengadaan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->timestamp)->format('d/m/Y') }}</td>
                                        <td class="text-success fw-bold">Rp {{ number_format($p->total_nilai) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada riwayat pengadaan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection