@extends('layouts.master')
@section('title', 'Data Retur')
@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Data Retur Barang</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Retur</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-undo me-1"></i>
            Daftar Retur
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="datatablesSimple">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>ID Retur</th>
                            <th>Tanggal Retur</th>
                            <th>ID Penerimaan</th>
                            <th>Vendor</th>
                            <th>Petugas</th>
                            <th>Jumlah Item</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returs as $index => $retur)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-danger">RTR-{{ str_pad($retur->idretur, 4, '0', STR_PAD_LEFT) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($retur->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('superadmin.penerimaan.show', $retur->idpenerimaan) }}" class="text-decoration-none">
                                    PNR-{{ str_pad($retur->idpenerimaan, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>{{ $retur->nama_vendor }}</td>
                            <td>{{ $retur->petugas }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $retur->jumlah_item }} item</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('superadmin.retur.show', $retur->idretur) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data retur</td>
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
        $('#datatablesSimple').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            order: [[2, 'desc']]
        });
    });
</script>
@endpush