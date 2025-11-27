@extends('layouts.master')
@section('title', 'Data Penjualan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cash-register"></i> Manajemen Penjualan</h2>
        <a href="{{ route('superadmin.penjualan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Transaksi Baru
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

    <!-- Info Margin Aktif -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        <strong>Margin Penjualan Aktif:</strong> 
        @if($marginAktif)
            <span class="badge bg-success">{{ $marginAktif->persen_margin }}%</span>
            <small class="text-muted">(Digunakan untuk menghitung harga jual)</small>
        @else
            <span class="badge bg-danger">Tidak ada margin aktif!</span>
            <a href="{{ route('superadmin.margin-penjualan.index') }}" class="btn btn-sm btn-warning ms-2">
                <i class="fas fa-cog"></i> Atur Margin
            </a>
        @endif
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0"><i class="fas fa-list"></i> Daftar Transaksi Penjualan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Penjualan</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Margin</th>
                            <th>Subtotal</th>
                            <th>PPN (10%)</th>
                            <th>Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualans as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>#{{ $p->idpenjualan }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $p->username }}</td>
                            <td><span class="badge bg-info">{{ $p->persen_margin }}%</span></td>
                            <td class="text-end">Rp {{ number_format($p->subtotal_nilai, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                            <td class="text-end"><strong class="text-success">Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</strong></td>
                            <td class="text-center">
                                <a href="{{ route('superadmin.penjualan.show', $p->idpenjualan) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data penjualan</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL PENJUALAN:</th>
                            <th class="text-end">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</th>
                            <th class="text-end">Rp {{ number_format($totalPPN, 0, ',', '.') }}</th>
                            <th class="text-end"><strong class="text-success">Rp {{ number_format($totalGrand, 0, ',', '.') }}</strong></th>
                            <th></th>
                        </tr>
                    </tfoot>
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
        pageLength: 25
    });
});
</script>
@endpush