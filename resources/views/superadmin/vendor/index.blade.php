@extends('layouts.master')
@section('title', 'Data Vendor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-building"></i> Manajemen Vendor</h2>
            <p class="text-muted mb-0">Kelola data vendor/supplier</p>
        </div>
        <a href="{{ route('superadmin.vendor.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Vendor
        </a>
    </div>


    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Data</h6>
        </div>
        <div class="card-body">
            <div class="btn-group mb-3" role="group">
                <a href="{{ route('superadmin.vendor.index', ['filter' => 'all']) }}" 
                   class="btn btn-sm {{ (!request('filter') || request('filter') == 'all') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-list"></i> Semua
                </a>
                <a href="{{ route('superadmin.vendor.index', ['filter' => 'aktif']) }}" 
                   class="btn btn-sm {{ request('filter') == 'aktif' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle"></i> Aktif
                </a>
                <a href="{{ route('superadmin.vendor.index', ['filter' => 'nonaktif']) }}" 
                   class="btn btn-sm {{ request('filter') == 'nonaktif' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times-circle"></i> Nonaktif
                </a>
            </div>

            <form action="{{ route('superadmin.vendor.filter.legalitas') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="badan_hukum" class="form-select">
                        <option value="">Filter Legalitas</option>
                        <option value="Y">Berbadan Hukum</option>
                        <option value="N">Tidak Berbadan Hukum</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list"></i> Daftar Vendor</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Vendor</th>
                            <th width="20%">Legalitas</th>
                            <th width="15%">Status</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $index => $vendor)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $vendor->nama_vendor }}</strong></td>
                            <td>
                                @if($vendor->legalitas == 'Berbadan Hukum')
                                    <span class="badge bg-success"><i class="fas fa-certificate"></i> {{ $vendor->legalitas }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $vendor->legalitas }}</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($vendor->status_vendor) && $vendor->status_vendor == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('superadmin.vendor.show', $vendor->idvendor) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('superadmin.vendor.edit', $vendor->idvendor) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="toggleStatus({{ $vendor->idvendor }})" class="btn btn-sm btn-secondary" title="Toggle Status">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Tidak ada data vendor</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="toggleForm" method="POST" style="display:none;">@csrf</form>
@endsection

@push('scripts')
<script>
function toggleStatus(id) {
    if(confirm('Yakin ingin mengubah status vendor ini?')) {
        const form = document.getElementById('toggleForm');
        form.action = "{{ url('superadmin/vendor') }}/" + id + "/toggle-status";
        form.submit();
    }
}
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });
});
</script>
@endpush