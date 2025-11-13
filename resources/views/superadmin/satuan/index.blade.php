@extends('layouts.master')

@section('title', 'Master Satuan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-ruler"></i> Master Satuan</h2>
            <p class="text-muted mb-0">Kelola satuan/unit pengukuran barang</p>
        </div>
        <a href="{{ route('superadmin.satuan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Satuan
        </a>
    </div>

    <!-- Card Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Satuan</h5>
        </div>
        <div class="card-body">
            <!-- Filter Status -->
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <a href="{{ route('superadmin.satuan.index') }}" 
                       class="btn btn-sm btn-outline-primary {{ !request('status') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> Semua
                    </a>
                     <a href="{{ route('superadmin.satuan.index', ['status' => 'aktif']) }}" 
                       class="btn btn-sm btn-outline-success {{ request('status') == 'aktif' ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> Aktif
                    </a>
                    <a href="{{ route('superadmin.satuan.index', ['status' => 'nonaktif']) }}" 
                       class="btn btn-sm btn-outline-danger {{ request('status') == 'nonaktif' ? 'active' : '' }}">
                        <i class="fas fa-times-circle"></i> Non-Aktif
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableSatuan">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="text-center">ID</th>
                            <th>Nama Satuan</th>
                            <th width="150" class="text-center">Status</th>
                            <th width="200" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($satuan as $item)
                        <tr>
                            <td class="text-center">{{ $item->idsatuan }}</td>
                            <td><strong>{{ $item->nama_satuan }}</strong></td>
                            <td class="text-center">
                                @if($item->status_satuan == 'Aktif')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('superadmin.satuan.edit', $item->idsatuan) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('superadmin.satuan.toggle-status', $item->idsatuan) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $item->status_satuan == 'Aktif' ? 'btn-secondary' : 'btn-success' }}"
                                                onclick="return confirm('Ubah status satuan ini?')">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('superadmin.satuan.destroy', $item->idsatuan) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus satuan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data satuan
                            </td>
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
    // DataTables (optional)
    $('#tableSatuan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 10,
        "order": [[0, "desc"]]
    });
});
</script>
@endpush