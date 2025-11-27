@extends('layouts.master')
@section('title', 'Detail Margin Penjualan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-percentage"></i> Detail Margin Penjualan</h2>
        <div>
            <a href="{{ route('superadmin.margin-penjualan.edit', $margin->idmargin_penjualan) }}" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('superadmin.margin-penjualan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Informasi Margin</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="35%">ID Margin</th><td>: <strong>#{{ $margin->idmargin_penjualan }}</strong></td></tr>
                        <tr><th>Persentase</th><td>: <strong class="text-success">{{ $margin->persen_margin }}%</strong></td></tr>
                        <tr><th>Status</th><td>: <span class="badge bg-{{ $margin->status_vendor == 'Aktif' ? 'success' : 'danger' }}">{{ $margin->status_vendor }}</span></td></tr>
                        <tr><th>Dibuat Oleh</th><td>: {{ $margin->dibuat_oleh }}</td></tr>
                        <tr><th>Tanggal Dibuat</th><td>: {{ \Carbon\Carbon::parse($margin->created_at)->format('d/m/Y H:i') }}</td></tr>
                        <tr><th>Terakhir Diperbarui</th><td>: {{ \Carbon\Carbon::parse($margin->updated_at)->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Riwayat Penjualan dengan Margin Ini</h5></div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if(!empty($riwayatPenjualan) && count($riwayatPenjualan) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>ID</th><th>Tanggal</th><th>Kasir</th><th>Total</th></tr></thead>
                                <tbody>
                                    @foreach($riwayatPenjualan as $p)
                                    <tr>
                                        <td>#{{ $p->idpenjualan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $p->username }}</td>
                                        <td class="text-success fw-bold">Rp {{ number_format($p->total_nilai) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada penjualan dengan margin ini</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection