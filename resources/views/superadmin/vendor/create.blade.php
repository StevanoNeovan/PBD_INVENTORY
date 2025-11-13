@extends('layouts.master')

@section('title', 'Tambah Vendor')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Vendor Baru</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.vendor.index') }}">Vendor</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-wpforms"></i> Form Vendor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.vendor.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nama Vendor -->
                        <div class="mb-3">
                            <label for="nama_vendor" class="form-label">
                                Nama Vendor <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_vendor') is-invalid @enderror" 
                                   id="nama_vendor" 
                                   name="nama_vendor" 
                                   value="{{ old('nama_vendor') }}"
                                   placeholder="Contoh: Pt. Abadi nan Jaya"
                                   required>
                            @error('nama_vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nama perusahaan penyedia barang</small>
                        </div>
                    
                         <!-- Badan Hukum -->
                        <div class="mb-3">
                            <label class="form-label">
                                Badan Hukum <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('badan_hukum') is-invalid @enderror" 
                                           type="radio" 
                                           name="badan_hukum" 
                                           id="BerbadanHukum" 
                                           value="Y" 
                                           {{ old('badan_hukum', '1') == 'Y' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="BerbadanHukum">
                                        <i class="fas fa-check-circle text-success"></i> Ada
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('badan_hukum') is-invalid @enderror" 
                                           type="radio" 
                                           name="badan_hukum" 
                                           id="NonBerbadanHukum
                                           value="N"
                                           {{ old('badan_hukum', '0') == 'N' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="NonBerbadanHukum">
                                        <i class="fas fa-times-circle text-danger"></i> Tidak Ada
                                    </label>
                                </div>
                            </div>
                            @error('badan_hukum')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('status') is-invalid @enderror" 
                                           type="radio" 
                                           name="status" 
                                           id="statusAktif" 
                                           value="1" 
                                           {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusAktif">
                                        <i class="fas fa-check-circle text-success"></i> Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('status') is-invalid @enderror" 
                                           type="radio" 
                                           name="status" 
                                           id="statusNonaktif" 
                                           value="0"
                                           {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusNonaktif">
                                        <i class="fas fa-times-circle text-danger"></i> Tidak Aktif
                                    </label>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.vendor.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-md-4">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <h6 class="text-info"><i class="fas fa-info-circle"></i> Panduan</h6>
                    <p class="small mb-2">Vendor digunakan sebagai pengadaan barang dalam sistem.Vendor digunakan untuk mencatat pihak penyedia barang atau jasa dalam sistem.
Setiap transaksi pengadaan harus terhubung ke vendor yang valid agar data pembelian dapat ditelusuri dengan mudah.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
</style>
@endpush