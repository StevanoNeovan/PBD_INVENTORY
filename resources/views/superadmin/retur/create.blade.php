@extends('layouts.master')
@section('title', 'Retur Barang')
@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Buat Retur Barang</h1>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Penerimaan -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-box me-1"></i>
            Informasi Penerimaan
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Penerimaan</strong></td>
                            <td>: PNR-{{ str_pad($penerimaan->idpenerimaan, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($penerimaan->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Vendor</strong></td>
                            <td>: {{ $penerimaan->nama_vendor }}</td>
                        </tr>
                        <tr>
                            <td><strong>ID Pengadaan</strong></td>
                            <td>: PGD-{{ str_pad($penerimaan->idpengadaan, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('superadmin.retur.store', $penerimaan->idpenerimaan) }}" method="POST" id="formRetur">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-undo me-1"></i>
                Pilih Barang untuk Diretur
            </div>
            <div class="card-body">
                @if(empty($barangs))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Tidak ada barang yang bisa diretur (stok habis atau sudah diretur semua)
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Jumlah retur tidak boleh melebihi <strong>stok tersedia</strong></li>
                            <li>Centang barang yang ingin diretur, lalu isi jumlah dan alasan</li>
                            <li>Penerimaan ini sudah diretur <strong>{{ 2 - $barangs[0]->sudah_diretur > 0 ? 2 - $barangs[0]->sudah_diretur : 0 }}x lagi</strong></li>
                        </ul>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="tableBarang">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th class="text-end">Diterima</th>
                                    <th class="text-end">Sudah Retur</th>
                                    <th class="text-end">Stok Tersedia</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th width="15%">Jumlah Retur</th>
                                    <th width="20%">Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangs as $index => $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input checkBarang" 
                                               data-index="{{ $index }}"
                                               onchange="toggleRow(this, {{ $index }})">
                                    </td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->nama_satuan }}</td>
                                    <td class="text-end">{{ number_format($item->jumlah_terima) }}</td>
                                    <td class="text-end">{{ number_format($item->sudah_diretur) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ number_format($item->stok_tersedia) }}</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->harga_satuan_terima) }}</td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm jumlah-input" 
                                               id="jumlah_{{ $index }}"
                                               name="barangs[{{ $index }}][jumlah]" 
                                               min="1" 
                                               max="{{ $item->stok_tersedia }}"
                                               disabled
                                               placeholder="Max: {{ $item->stok_tersedia }}">
                                        <input type="hidden" name="barangs[{{ $index }}][iddetail_penerimaan]" value="{{ $item->iddetail_penerimaan }}">
                                    </td>
                                    <td>
                                        <input type="text" 
                                               class="form-control form-control-sm alasan-input" 
                                               id="alasan_{{ $index }}"
                                               name="barangs[{{ $index }}][alasan]" 
                                               maxlength="200"
                                               disabled
                                               placeholder="Alasan retur">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if(!empty($barangs))
        <div class="mb-4">
            <button type="submit" class="btn btn-danger" id="btnSubmit" disabled>
                <i class="fas fa-save"></i> Proses Retur
            </button>
            <a href="{{ route('superadmin.penerimaan.show', $penerimaan->idpenerimaan) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
        @else
        <div class="mb-4">
            <a href="{{ route('superadmin.penerimaan.show', $penerimaan->idpenerimaan) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleRow(checkbox, index) {
    const jumlahInput = document.getElementById(`jumlah_${index}`);
    const alasanInput = document.getElementById(`alasan_${index}`);
    
    if (checkbox.checked) {
        jumlahInput.disabled = false;
        alasanInput.disabled = false;
        jumlahInput.focus();
    } else {
        jumlahInput.disabled = true;
        alasanInput.disabled = true;
        jumlahInput.value = '';
        alasanInput.value = '';
    }
    
    checkSubmitButton();
}

// Check All
document.getElementById('checkAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.checkBarang');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        const index = cb.getAttribute('data-index');
        toggleRow(cb, index);
    });
});

// Enable/disable submit button
function checkSubmitButton() {
    const checkedBoxes = document.querySelectorAll('.checkBarang:checked');
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (checkedBoxes.length > 0) {
        btnSubmit.disabled = false;
    } else {
        btnSubmit.disabled = true;
    }
}

// Validasi sebelum submit
document.getElementById('formRetur')?.addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.checkBarang:checked');
    
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 barang untuk diretur!');
        return false;
    }
    
    let valid = true;
    checkboxes.forEach(cb => {
        const index = cb.getAttribute('data-index');
        const jumlah = document.getElementById(`jumlah_${index}`).value;
        const alasan = document.getElementById(`alasan_${index}`).value;
        
        if (!jumlah || jumlah <= 0) {
            alert('Jumlah retur harus diisi dan lebih dari 0!');
            valid = false;
            return;
        }
        
        if (!alasan || alasan.trim() === '') {
            alert('Alasan retur harus diisi!');
            valid = false;
            return;
        }
    });
    
    if (!valid) {
        e.preventDefault();
        return false;
    }
    
    return confirm('Apakah Anda yakin ingin memproses retur ini?');
});
</script>
@endpush