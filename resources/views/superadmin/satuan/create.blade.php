@extends('layouts.master')

@section('title', 'Tambah Satuan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Satuan Baru</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.satuan.index') }}">Satuan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-wpforms"></i> Form Satuan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.satuan.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nama Satuan -->
                        <div class="mb-3">
                            <label for="nama_satuan" class="form-label">
                                Nama Satuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_satuan') is-invalid @enderror" 
                                   id="nama_satuan" 
                                   name="nama_satuan" 
                                   value="{{ old('nama_satuan') }}"
                                   placeholder="Contoh: Pcs, Box, Kg, Liter"
                                   required>
                            @error('nama_satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unit pengukuran untuk barang</small>
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
                            <a href="{{ route('superadmin.satuan.index') }}" class="btn btn-secondary">
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
                    <p class="small mb-2">Satuan digunakan sebagai unit pengukuran barang dalam sistem.</p>
                    <hr>
                    <p class="small mb-1"><strong>Contoh Satuan:</strong></p>
                    <ul class="small mb-0">
                        <li>Pcs (Pieces) - untuk barang satuan</li>
                        <li>Box - untuk kemasan dus</li>
                        <li>Pack - untuk kemasan paket</li>
                        <li>Kg (Kilogram) - untuk barang berat</li>
                        <li>Liter - untuk barang cair</li>
                        <li>Lusin - untuk 12 unit</li>
                        <li>Karton - untuk kemasan besar</li>
                    </ul>
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