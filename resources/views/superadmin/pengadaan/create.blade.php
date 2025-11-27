@extends('layouts.master')
@section('title', 'Tambah Pengadaan')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Tambah Pengadaan Barang</h2>
    
    <form action="{{ route('superadmin.pengadaan.store') }}" method="POST" id="formPengadaan">
        @csrf
        
        <div class="row">
            <!-- Informasi Pengadaan -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0">Informasi Pengadaan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Vendor <span class="text-danger">*</span></label>
                            <select class="form-select @error('idvendor') is-invalid @enderror" 
                                    name="idvendor" id="idvendor" required>
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($vendors as $v)
                                <option value="{{ $v->idvendor }}" {{ old('idvendor') == $v->idvendor ? 'selected' : '' }}>
                                    {{ $v->nama_vendor }} ({{ $v->legalitas }})
                                </option>
                                @endforeach
                            </select>
                            @error('idvendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Tanggal Pengadaan</label>
                            <input type="text" class="form-control" value="{{ date('d/m/Y H:i') }}" readonly>
                            <small class="text-muted">Otomatis terisi saat ini</small>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <strong>Subtotal:</strong>
                            <div class="text-end h5" id="displaySubtotal">Rp 0</div>
                        </div>
                        <div class="mb-2">
                            <strong>PPN (10%):</strong>
                            <div class="text-end h5" id="displayPPN">Rp 0</div>
                        </div>
                        <div class="mb-2">
                            <strong>Total:</strong>
                            <div class="text-end h3 text-primary" id="displayTotal">Rp 0</div>
                        </div>

                        <input type="hidden" name="subtotal_nilai" id="subtotal_nilai" value="0">
                        <input type="hidden" name="ppn" id="ppn" value="0">
                        <input type="hidden" name="total_nilai" id="total_nilai" value="0">
                    </div>
                </div>
            </div>

            <!-- Detail Barang -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0">Detail Barang Pengadaan</h6>
                    </div>
                    <div class="card-body">
                        <!-- Form Tambah Barang -->
                        <div class="border p-3 mb-3 bg-light">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Barang</label>
                                    <select class="form-select" id="selectBarang">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($barangs as $b)
                                        <option value="{{ $b->idbarang }}" 
                                                data-nama="{{ $b->nama_barang }}"
                                                data-satuan="{{ $b->nama_satuan }}"
                                                data-harga="{{ $b->harga }}">
                                            {{ $b->nama_barang }} ({{ $b->kategori_barang }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Harga Satuan</label>
                                    <input type="number" class="form-control" id="inputHarga" placeholder="0" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label>Jumlah</label>
                                    <input type="number" class="form-control" id="inputJumlah" placeholder="0" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="btnTambahBarang">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Barang yang Ditambahkan -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Barang</th>
                                        <th>Satuan</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDetailBarang">
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted">Belum ada barang ditambahkan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.pengadaan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Simpan Pengadaan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
let detailBarang = [];
let counter = 0;

$(document).ready(function() {
    // Event: Pilih Barang
    $('#selectBarang').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const harga = selectedOption.data('harga') || 0;
        $('#inputHarga').val(harga);
        $('#inputJumlah').val(1).focus();
    });

    // Event: Tambah Barang
    $('#btnTambahBarang').on('click', function() {
        const idbarang = $('#selectBarang').val();
        const namaBarang = $('#selectBarang').find(':selected').data('nama');
        const satuan = $('#selectBarang').find(':selected').data('satuan');
        const harga = parseInt($('#inputHarga').val()) || 0;
        const jumlah = parseInt($('#inputJumlah').val()) || 0;

        if (!idbarang) {
            alert('Pilih barang terlebih dahulu!');
            return;
        }
        if (jumlah <= 0) {
            alert('Jumlah harus lebih dari 0!');
            return;
        }

        // Cek apakah barang sudah ada
        const exists = detailBarang.find(item => item.idbarang == idbarang);
        if (exists) {
            alert('Barang sudah ditambahkan!');
            return;
        }

        const subtotal = harga * jumlah;
        counter++;

        detailBarang.push({
            id: counter,
            idbarang: idbarang,
            nama: namaBarang,
            satuan: satuan,
            harga: harga,
            jumlah: jumlah,
            subtotal: subtotal
        });

        renderTable();
        hitungTotal();
        resetForm();
    });

    // Event: Hapus Barang
    $(document).on('click', '.btnHapus', function() {
        const id = $(this).data('id');
        detailBarang = detailBarang.filter(item => item.id != id);
        renderTable();
        hitungTotal();
    });

    // Event: Submit Form
    $('#formPengadaan').on('submit', function(e) {
        if (detailBarang.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 barang!');
            return false;
        }

        // Tambahkan detail barang ke form sebagai JSON
        detailBarang.forEach((item, index) => {
            $('<input>').attr({
                type: 'hidden',
                name: `details[${index}][idbarang]`,
                value: item.idbarang
            }).appendTo('#formPengadaan');
            
            $('<input>').attr({
                type: 'hidden',
                name: `details[${index}][jumlah]`,
                value: item.jumlah
            }).appendTo('#formPengadaan');
        });
    });
});

function renderTable() {
    const tbody = $('#tableDetailBarang');
    tbody.empty();

    if (detailBarang.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">Belum ada barang ditambahkan</td></tr>');
        return;
    }

    detailBarang.forEach(item => {
        tbody.append(`
            <tr>
                <td>${item.nama}</td>
                <td>${item.satuan}</td>
                <td class="text-end">Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td class="text-center">${item.jumlah}</td>
                <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btnHapus" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

function hitungTotal() {
    const subtotal = detailBarang.reduce((sum, item) => sum + item.subtotal, 0);
    const ppn = Math.round(subtotal * 0.1);
    const total = subtotal + ppn;

    $('#displaySubtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
    $('#displayPPN').text('Rp ' + ppn.toLocaleString('id-ID'));
    $('#displayTotal').text('Rp ' + total.toLocaleString('id-ID'));

    $('#subtotal_nilai').val(subtotal);
    $('#ppn').val(ppn);
    $('#total_nilai').val(total);
}

function resetForm() {
    $('#selectBarang').val('');
    $('#inputHarga').val('');
    $('#inputJumlah').val('');
}
</script>
@endpush