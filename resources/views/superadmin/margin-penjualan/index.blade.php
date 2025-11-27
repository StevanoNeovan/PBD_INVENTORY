@extends('layouts.master')
@section('title', 'Data Margin Penjualan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-percentage"></i> Manajemen Margin Penjualan</h2>
        <a href="{{ route('superadmin.margin-penjualan.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Margin</a>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Catatan:</strong> Hanya 1 margin yang boleh aktif. Margin aktif akan digunakan untuk menghitung harga jual.</div>

    <div class="card shadow">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-list"></i> Daftar Margin</h6></div>
        <div class="card-body">
            <table class="table table-bordered" id="dataTable">
                <thead class="table-light">
                    <tr><th>No</th><th>Persen Margin</th><th>Status</th><th>Dibuat Oleh</th><th>Dibuat</th><th>Diperbarui</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($margins as $index => $margin)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong class="text-success">{{ $margin->persen_margin }}%</strong></td>
                        <td><span class="badge bg-{{ $margin->status_vendor == 'Aktif' ? 'success' : 'danger' }}">{{ $margin->status_vendor }}</span></td>
                        <td>{{ $margin->dibuat_oleh }}</td>
                        <td>{{ \Carbon\Carbon::parse($margin->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($margin->updated_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('superadmin.margin-penjualan.show', $margin->idmargin_penjualan) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('superadmin.margin-penjualan.edit', $margin->idmargin_penjualan) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <button onclick="toggleStatus({{ $margin->idmargin_penjualan }})" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
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
    if(confirm('Yakin? Jika diaktifkan, margin lain akan otomatis nonaktif.')) {
        const form = document.getElementById('toggleForm');
        form.action = "{{ url('superadmin/margin-penjualan') }}/" + id + "/toggle-status";
        form.submit();
    }
}
</script>
@endpush