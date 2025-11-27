@extends('layouts.master')
@section('title', 'Data Satuan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-ruler"></i> Manajemen Satuan</h2>
        <a href="{{ route('superadmin.satuan.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Satuan</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-filter"></i> Filter</h6></div>
        <div class="card-body">
            <div class="btn-group">
                <a href="{{ route('superadmin.satuan.index', ['filter' => 'all']) }}" class="btn btn-sm {{ (!request('filter') || request('filter') == 'all') ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                <a href="{{ route('superadmin.satuan.index', ['filter' => 'aktif']) }}" class="btn btn-sm {{ request('filter') == 'aktif' ? 'btn-success' : 'btn-outline-success' }}">Aktif</a>
                <a href="{{ route('superadmin.satuan.index', ['filter' => 'nonaktif']) }}" class="btn btn-sm {{ request('filter') == 'nonaktif' ? 'btn-danger' : 'btn-outline-danger' }}">Nonaktif</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-list"></i> Daftar Satuan</h6></div>
        <div class="card-body">
            <table class="table table-bordered" id="dataTable">
                <thead class="table-light">
                    <tr><th width="5%">No</th><th>Nama Satuan</th><th width="15%">Status</th><th width="15%" class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($satuans as $index => $satuan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $satuan->nama_satuan }}</strong></td>
                        <td><span class="badge bg-{{ $satuan->status_satuan == 'Aktif' ? 'success' : 'danger' }}">{{ $satuan->status_satuan }}</span></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('superadmin.satuan.show', $satuan->idsatuan) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('superadmin.satuan.edit', $satuan->idsatuan) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <button onclick="toggleStatus({{ $satuan->idsatuan }})" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<form id="toggleForm" method="POST" style="display:none;">@csrf</form>
@endsection

@push('scripts')
<script>
function toggleStatus(id) {
    if(confirm('Yakin ingin mengubah status?')) {
        const form = document.getElementById('toggleForm');
        form.action = "{{ url('superadmin/satuan') }}/" + id + "/toggle-status";
        form.submit();
    }
}
$(document).ready(function() { $('#dataTable').DataTable(); });
</script>
@endpush