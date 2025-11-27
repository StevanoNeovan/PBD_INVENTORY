@extends('layouts.master')
@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-building"></i> Edit Vendor</h2>
        <a href="{{ route('superadmin.vendor.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Form Edit Vendor</h5></div>
                <div class="card-body">
                    <form action= "{{ route('superadmin.vendor.update', $vendor->idvendor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama Vendor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_vendor') is-invalid @enderror" 
                                   name="nama_vendor" value="{{ old('nama_vendor', $vendor->nama_vendor) }}" required>
                            @error('nama_vendor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Badan Hukum <span class="text-danger">*</span></label>
                            <select class="form-select @error('badan_hukum') is-invalid @enderror" name="badan_hukum" required>
                                <option value="">-- Pilih --</option>
                                <option value="Y" {{ old('badan_hukum', $vendor->badan_hukum) == 'Y' ? 'selected' : '' }}>Berbadan Hukum</option>
                                <option value="N" {{ old('badan_hukum', $vendor->badan_hukum) == 'N' ? 'selected' : '' }}>Tidak Berbadan Hukum</option>
                            </select>
                            @error('badan_hukum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ old('status', $vendor->status) == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status', $vendor->status) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.vendor.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection