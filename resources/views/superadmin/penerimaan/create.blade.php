@extends('layouts.master')
@section('title', 'Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-box-open"></i> Penerimaan Barang dari Pengadaan</h2>

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
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
                                        <span class="badge bg-warning text-dark">Partial</span>
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
                                <option value="">Pilih Status</option>
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
                                <li><strong>Input harga sesuai invoice vendor</strong></li>
                                <li>Harga barang akan otomatis terupdate</li>
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
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th class="text-center" width="100">Jumlah Pesan</th>
                                        <th class="text-center" width="100">Sudah Diterima</th>
                                        <th class="text-center" width="80">Sisa</th>
                                        <th class="text-end" width="130">Harga Pengadaan</th>
                                        <th class="text-end" width="130">Harga Invoice <span class="text-danger">*</span></th>
                                        <th class="text-center" width="120">Terima Sekarang <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($details as $index => $d)
                                    <tr>
                                        <td>
                                            <strong>{{ $d->nama_barang }}</strong>
                                            <input type="hidden" name="details[{{ $index }}][idbarang]" value="{{ $d->idbarang }}">
                                        </td>
                                        <td>{{ $d->nama_satuan }}</td>
                                        <td class="text-center"><strong>{{ $d->jumlah_pesan }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $d->jumlah_diterima }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($d->jumlah_sisa > 0)
                                                <span class="badge bg-warning text-dark">{{ $d->jumlah_sisa }}</span>
                                            @else
                                                <span class="badge bg-secondary">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <small class="text-muted">Rp {{ number_format($d->harga_pengadaan, 0, ',', '.') }}</small>
                                        </td>
                                        <td>
                                            @if($d->jumlah_sisa > 0)
                                                <input type="number" 
                                                       class="form-control form-control-sm text-end harga-input" 
                                                       name="details[{{ $index }}][harga_satuan_terima]" 
                                                       min="1"
                                                       value="{{ old('details.'.$index.'.harga_satuan_terima', $d->harga_pengadaan) }}"
                                                       placeholder="0"
                                                       required>
                                                <small class="text-muted">Harga terakhir: Rp {{ number_format($d->harga_barang_terakhir, 0, ',', '.') }}</small>
                                            @else
                                                <input type="hidden" name="details[{{ $index }}][harga_satuan_terima]" value="{{ $d->harga_pengadaan }}">
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($d->jumlah_sisa > 0)
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center jumlah-input" 
                                                       name="details[{{ $index }}][jumlah_terima]" 
                                                       min="0" 
                                                       max="{{ $d->jumlah_sisa }}"
                                                       value="{{ old('details.'.$index.'.jumlah_terima', $d->jumlah_sisa) }}"
                                                       placeholder="0"
                                                       data-max="{{ $d->jumlah_sisa }}"
                                                       data-nama="{{ $d->nama_barang }}"
                                                       data-satuan="{{ $d->nama_satuan }}">
                                                <small class="text-muted">Max: {{ $d->jumlah_sisa }} {{ $d->nama_satuan }}</small>
                                            @else
                                                <!-- Barang sudah lengkap, kirim value 0 -->
                                                <input type="hidden" name="details[{{ $index }}][jumlah_terima]" value="0">
                                                <span class="badge bg-success">✓ Lengkap</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($details) == 0)
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
                    @if(count($details) > 0)
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
    // Validasi jumlah terima tidak boleh melebihi max
    $('.jumlah-input').on('input', function() {
        let max = parseInt($(this).data('max'));
        let val = parseInt($(this).val());
        let nama = $(this).data('nama');
        let satuan = $(this).data('satuan');
        
        if (val > max) {
            alert(`Jumlah terima untuk "${nama}" tidak boleh melebihi ${max} ${satuan}!`);
            $(this).val(max);
        }
        
        if (val < 0) {
            $(this).val(0);
        }
    });

    // Validasi harga tidak boleh 0 atau negatif
    $('.harga-input').on('input', function() {
        let val = parseInt($(this).val());
        
        if (val < 1) {
            $(this).val(1);
        }
    });

    // Format rupiah saat blur
    $('.harga-input').on('blur', function() {
        let val = parseInt($(this).val());
        if (isNaN(val) || val < 1) {
            $(this).val(1);
        }
    });

    $('#formPenerimaan').on('submit', function(e) {
        // Cek apakah ada barang yang diterima
        let hasItem = false;
        let invalidQty = false;
        let invalidPrice = false;
        
        $('.jumlah-input').each(function() {
            let qty = parseInt($(this).val());
            let max = parseInt($(this).data('max'));
            let nama = $(this).data('nama');
            let satuan = $(this).data('satuan');
            
            if (qty > 0) {
                hasItem = true;
                
                // Validasi tidak melebihi max
                if (qty > max) {
                    alert(`Jumlah terima untuk "${nama}" melebihi sisa (${max} ${satuan})!`);
                    invalidQty = true;
                    return false;
                }
            }
        });

        // Validasi harga
        $('.harga-input').each(function() {
            let price = parseInt($(this).val());
            if (isNaN(price) || price < 1) {
                alert('Harga satuan harus diisi dan minimal Rp 1!');
                invalidPrice = true;
                $(this).focus();
                return false;
            }
        });

        if (invalidQty || invalidPrice) {
            e.preventDefault();
            return false;
        }

        if (!hasItem) {
            e.preventDefault();
            alert('Minimal 1 barang harus diterima dengan jumlah > 0!');
            return false;
        }

        // Konfirmasi
        if (!confirm('Yakin ingin memproses penerimaan barang ini?\n\nHarga barang akan otomatis terupdate sesuai harga invoice!')) {
            e.preventDefault();
            return false;
        }

        // Disable button untuk mencegah double submit
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
    });
});
</script>
@endpush