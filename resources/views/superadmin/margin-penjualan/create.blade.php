@extends('layouts.master')
@section('title', 'Tambah Margin Penjualan')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-percentage"></i> Tambah Margin Penjualan</h2>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Form Margin</h5></div>
                <div class="card-body">
                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Jika status aktif dipilih, margin lain yang aktif akan otomatis nonaktif.</div>
                    <form action="{{ route('superadmin.margin-penjualan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Persentase Margin (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('persen') is-invalid @enderror" name="persen" value="{{ old('persen') }}" required>
                            @error('persen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Contoh: 10 untuk 10%</small>
                        </div>
                        <div class="mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.margin-penjualan.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection