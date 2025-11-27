@extends('layouts.master')
@section('title', 'Edit Barang')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-boxes"></i> Edit Barang</h2>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning"><h5 class="mb-0">Form Edit Barang</h5></div>
                <div class="card-body">
                    <form action="{{ route('superadmin.barang.update', $barang->idbarang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $barang->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Jenis Barang <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis') is-invalid @enderror" name="jenis" required>
                                <option value="S" {{ $barang->jenis == 'S' ? 'selected' : '' }}>Sembako & Bahan Pokok</option>
                                <option value="M" {{ $barang->jenis == 'M' ? 'selected' : '' }}>Minuman</option>
                                <option value="K" {{ $barang->jenis == 'K' ? 'selected' : '' }}>Makanan Olahan & Snack</option>
                                <option value="P" {{ $barang->jenis == 'P' ? 'selected' : '' }}>Personal Care</option>
                                <option value="H" {{ $barang->jenis == 'H' ? 'selected' : '' }}>Household</option>
                                <option value="D" {{ $barang->jenis == 'D' ? 'selected' : '' }}>Dapur & Plastik</option>
                            </select>
                            @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Satuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('idsatuan') is-invalid @enderror" name="idsatuan" required>
                                @foreach($satuans as $satuan)
                                    <option value="{{ $satuan->idsatuan }}" {{ $barang->idsatuan == $satuan->idsatuan ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                                @endforeach
                            </select>
                            @error('idsatuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" value="{{ old('harga', $barang->harga) }}" required>
                            @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ $barang->status == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $barang->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.barang.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection