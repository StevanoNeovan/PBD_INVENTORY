@extends('layouts.master')
@section('title', 'Data Penerimaan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box-open"></i> Manajemen Penerimaan Barang</h2>
        <a href="{{ route('superadmin.pengadaan.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i> Lihat Pengadaan
        </a>
    </div>


    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h6 class="m-0"><i class="fas fa-list"></i> Daftar Penerimaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Penerimaan</th>
                            <th>ID Pengadaan</th>
                            <th>Tanggal</th>
                            <th>Vendor</th>
                            <th>Petugas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimaans as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>#{{ $p->idpenerimaan }}</strong></td>
                            <td><a href="{{ route('superadmin.pengadaan.show', $p->idpengadaan) }}">#{{ $p->idpengadaan }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $p->nama_vendor }}</td>
                            <td>{{ $p->username }}</td>
                            <td>
                                @if($p->status == '1')
                                    <span class="badge bg-success">Diterima</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('superadmin.penerimaan.show', $p->idpenerimaan) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data penerimaan</td>
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