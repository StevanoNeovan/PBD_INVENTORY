@extends('layouts.master')
@section('title', 'Edit Satuan')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-ruler"></i> Edit Satuan</h2>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning"><h5 class="mb-0">Form Edit Satuan</h5></div>
                <div class="card-body">
                    <form action="{{ route('superadmin.satuan.update', $satuan->idsatuan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Nama Satuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_satuan') is-invalid @enderror" name="nama_satuan" value="{{ old('nama_satuan', $satuan->nama_satuan) }}" required>
                            @error('nama_satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ $satuan->status == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $satuan->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.satuan.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
