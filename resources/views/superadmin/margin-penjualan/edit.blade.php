@extends('layouts.master')
@section('title', 'Edit Margin Penjualan')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-percentage"></i> Edit Margin Penjualan</h2>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning"><h5 class="mb-0">Form Edit Margin</h5></div>
                <div class="card-body">
                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Jika status aktif dipilih, margin lain yang aktif akan otomatis nonaktif.</div>
                    <form action="{{ route('superadmin.margin-penjualan.update', $margin->idmargin_penjualan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Persentase Margin (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('persen') is-invalid @enderror" name="persen" value="{{ old('persen', $margin->persen) }}" required>
                            @error('persen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ $margin->status == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $margin->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.margin-penjualan.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection