@extends('layouts.master')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-0"><i class="fas fa-edit"></i> Edit Vendor</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.vendor.index') }}">Vendor</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-wpforms"></i> Form Edit Vendor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.vendor.update', $vendor->idvendor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- ID (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label">ID Vendor</label>
                            <input type="text" class="form-control" value="{{ $vendor->idvendor }}" disabled>
                        </div>

                        <!-- Nama Vendor -->
                        <div class="mb-3">
                            <label for="nama_vendor" class="form-label">
                                Nama Vendor <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_vendor') is-invalid @enderror" 
                                   id="nama_vendor" 
                                   name="nama_vendor" 
                                   value="{{ old('nama_vendor', $vendor->nama_vendor) }}"
                                   required>
                            @error('nama_vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="status" 
                                           id="statusAktif" 
                                           value="1" 
                                           {{ old('status', $vendor->status) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusAktif">
                                        <i class="fas fa-check-circle text-success"></i> Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="status" 
                                           id="statusNonaktif" 
                                           value="0"
                                           {{ old('status', $vendor->status) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusNonaktif">
                                        <i class="fas fa-times-circle text-danger"></i> Tidak Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.vendor.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Usage -->
        <div class="col-md-4">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
                    <p class="small mb-2">Vendor ini digunakan oleh:</p>
                    @php
                        $usageCount = DB::table('pengadaan')
                            ->where('idvendor', $vendor->idvendor)
                            ->count();
                    @endphp
                    <div class="alert alert-info">
                        <strong>{{ $usageCount }} Pengadaan Aktif</strong>
                    </div>
                    @if($usageCount > 0)
                        <p class="small text-danger mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Jika dinonaktifkan, vendor tidak dapat digunakan untuk pengadaan baru.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
</style>
@endpush