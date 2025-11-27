@extends('layouts.master')
@section('title', 'Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-box-open"></i> Penerimaan Barang dari Pengadaan</h2>
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('superadmin.penerimaan.store') }}" method="POST" id="formPenerimaan">
        @csrf
        <input type="hidden" name="idpengadaan" value="{{ $pengadaan->idpengadaan }}">
        
        <div class="row">
            <!-- Informasi Pengadaan -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0">Informasi Pengadaan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="45%">ID Pengadaan</th>
                                <td>: <strong>#{{ $pengadaan->idpengadaan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ \Carbon\Carbon::parse($pengadaan->timestamp)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Vendor</th>
                                <td>: {{ $pengadaan->nama_vendor }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: 
                                    @if($pengadaan->status_pengadaan == 'SELESAI')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($pengadaan->status_pengadaan == 'PARTIAL')
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <div class="mb-3">
                            <label>Status Penerimaan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>✅ Diterima</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>❌ Ditolak</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih status penerimaan barang</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> <strong>Catatan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Isi jumlah yang <strong>diterima</strong></li>
                                <li>Bisa kurang dari jumlah pesan</li>
                                <li>Stok akan bertambah otomatis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Barang yang Diterima -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0">Detail Barang yang Diterima</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th class="text-center">Jumlah Pesan</th>
                                        <th class="text-center">Sudah Diterima</th>
                                        <th class="text-center">Sisa</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-center">Terima Sekarang <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($details as $d)
                                    <tr>
                                        <td>
                                            <strong>{{ $d->nama_barang }}</strong>
                                            <input type="hidden" name="details[{{ $d->idbarang }}][idbarang]" value="{{ $d->idbarang }}">
                                            <input type="hidden" name="details[{{ $d->idbarang }}][harga_satuan]" value="{{ $d->harga_satuan }}">
                                        </td>
                                        <td>{{ $d->nama_satuan }}</td>
                                        <td class="text-center"><strong>{{ $d->jumlah_pesan }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $d->jumlah_diterima }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($d->jumlah_sisa > 0)
                                                <span class="badge bg-warning">{{ $d->jumlah_sisa }}</span>
                                            @else
                                                <span class="badge bg-secondary">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($d->jumlah_sisa > 0)
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center" 
                                                       name="details[{{ $d->idbarang }}][jumlah_terima]" 
                                                       min="0" 
                                                       max="{{ $d->jumlah_sisa }}"
                                                       value="{{ old('details.'.$d->idbarang.'.jumlah_terima', $d->jumlah_sisa) }}"
                                                       placeholder="0">
                                                <small class="text-muted">Max: {{ $d->jumlah_sisa }}</small>
                                            @else
                                                <!-- Barang sudah lengkap, kirim value 0 -->
                                                <input type="hidden" name="details[{{ $d->idbarang }}][jumlah_terima]" value="0">
                                                <span class="badge bg-success">✓ Lengkap</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($pengadaan->status_pengadaan == 'SELESAI')
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle"></i> Pengadaan ini sudah <strong>SELESAI</strong>. Semua barang telah diterima lengkap.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.pengadaan.show', $pengadaan->idpengadaan) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    @if($pengadaan->status_pengadaan != 'SELESAI')
                    <button type="submit" class="btn btn-success" id="btnSubmit">
                        <i class="fas fa-check"></i> Proses Penerimaan
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#formPenerimaan').on('submit', function(e) {
        // Cek apakah ada barang yang diterima
        let hasItem = false;
        $('input[name*="[jumlah_terima]"]').each(function() {
            if (parseInt($(this).val()) > 0) {
                hasItem = true;
            }
        });

        if (!hasItem) {
            e.preventDefault();
            alert('Minimal 1 barang harus diterima dengan jumlah > 0!');
            return false;
        }

        // Konfirmasi
        if (!confirm('Yakin ingin memproses penerimaan barang ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush