@extends('layouts.master')
@section('title', 'Data Barang')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes"></i> Manajemen Barang</h2>
        <a href="{{ route('superadmin.barang.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Barang</a>
    </div>  

    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-filter"></i> Filter & Pencarian</h6></div>
        <div class="card-body">
            <form action="{{ route('superadmin.barang.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama barang..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="jenis" class="form-select">
                        <option value="">-- Filter Jenis --</option>
                        <option value="S" {{ request('jenis') == 'S' ? 'selected' : '' }}>Sembako & Bahan Pokok</option>
                        <option value="M" {{ request('jenis') == 'M' ? 'selected' : '' }}>Minuman</option>
                        <option value="K" {{ request('jenis') == 'K' ? 'selected' : '' }}>Makanan Olahan & Snack</option>
                        <option value="P" {{ request('jenis') == 'P' ? 'selected' : '' }}>Personal Care</option>
                        <option value="H" {{ request('jenis') == 'H' ? 'selected' : '' }}>Household</option>
                        <option value="D" {{ request('jenis') == 'D' ? 'selected' : '' }}>Dapur & Plastik</option>
                    </select>
                </div>
                <div class="col-md-3">
                <select name="satuan" class="form-select">
                <option value="">-- Filter Satuan --</option>
                    @foreach ($satuans as $s)
                        <option value="{{ $s->nama_satuan }}"
                            {{ request('satuan') == $s->nama_satuan ? 'selected' : '' }}>
                            {{ $s->nama_satuan }}
                        </option>
                    @endforeach
                </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('superadmin.barang.index') }}" class="btn btn-secondary w-100"><i class="fas fa-redo"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-list"></i> Daftar Barang</h6></div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead class="table-light">
                    <tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th>Harga</th><th>Status</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($barangs as $index => $barang)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $barang->nama_barang }}</strong></td>
                        <td><span class="badge bg-info">{{ $barang->kategori_barang }}</span></td>
                        <td>{{ $barang->nama_satuan }}</td>
                        <td class="text-end">Rp {{ number_format($barang->harga) }}</td>
                        <td><span class="badge bg-{{ $barang->status == 'Aktif' ? 'success' : 'danger' }}">{{ $barang->status }}</span></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('superadmin.barang.show', $barang->idbarang) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('superadmin.barang.edit', $barang->idbarang) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <button onclick="toggleStatus({{ $barang->idbarang }})" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt"></i></button>
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
    if(confirm('Yakin ingin mengubah status?')) {
        const form = document.getElementById('toggleForm');
        form.action = "{{ url('superadmin/barang') }}/" + id + "/toggle-status";
        form.submit();
    }
}
</script>
@endpush