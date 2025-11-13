@extends('layouts.master')

@section('title', 'Edit Satuan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-0"><i class="fas fa-edit"></i> Edit Satuan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.satuan.index') }}">Satuan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-wpforms"></i> Form Edit Satuan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.satuan.update', $satuan->idsatuan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- ID (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label">ID Satuan</label>
                            <input type="text" class="form-control" value="{{ $satuan->idsatuan }}" disabled>
                        </div>

                        <!-- Nama Satuan -->
                        <div class="mb-3">
                            <label for="nama_satuan" class="form-label">
                                Nama Satuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_satuan') is-invalid @enderror" 
                                   id="nama_satuan" 
                                   name="nama_satuan" 
                                   value="{{ old('nama_satuan', $satuan->nama_satuan) }}"
                                   required>
                            @error('nama_satuan')
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
                                           {{ old('status', $satuan->status) == 1 ? 'checked' : '' }}>
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
                                           {{ old('status', $satuan->status) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusNonaktif">
                                        <i class="fas fa-times-circle text-danger"></i> Tidak Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.satuan.index') }}" class="btn btn-secondary">
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
                    <p class="small mb-2">Satuan ini digunakan oleh:</p>
                    @php
                        $usageCount = DB::table('barang')
                            ->where('idsatuan', $satuan->idsatuan)
                            ->where('status', 1)
                            ->count();
                    @endphp
                    <div class="alert alert-info">
                        <strong>{{ $usageCount }} Barang Aktif</strong>
                    </div>
                    @if($usageCount > 0)
                        <p class="small text-danger mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Jika dinonaktifkan, satuan tidak dapat digunakan untuk barang baru.
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