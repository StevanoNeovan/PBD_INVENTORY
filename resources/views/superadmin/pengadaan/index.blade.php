@extends('layouts.master')
@section('title', 'Data Pengadaan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart"></i> Manajemen Pengadaan Barang</h2>
        <a href="{{ route('superadmin.pengadaan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Pengadaan
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0"><i class="fas fa-list"></i> Daftar Pengadaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pengadaan</th>
                            <th>Tanggal</th>
                            <th>Vendor</th>
                            <th>Petugas</th>
                            <th>Subtotal</th>
                            <th>PPN (10%)</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengadaans as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>#{{ $p->idpengadaan }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pengadaan)->format('d/m/Y H:i') }}</td>
                            <td>{{ $p->nama_vendor }}</td>
                            <td>{{ $p->nama_petugas }}</td>
                            <td class="text-end">Rp {{ number_format($p->subtotal_nilai, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($p->status_pengadaan == 'SELESAI')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($p->status_pengadaan == 'PARTIAL')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Belum</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('superadmin.pengadaan.show', $p->idpengadaan) }}" 
                                       class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($p->status_pengadaan != 'SELESAI')
                                    <a href="{{ route('superadmin.penerimaan.create', ['pengadaan' => $p->idpengadaan]) }}" 
                                       class="btn btn-sm btn-success" title="Terima Barang">
                                        <i class="fas fa-box-open"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data pengadaan</td>
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
        }
    });
});
</script>
@endpush