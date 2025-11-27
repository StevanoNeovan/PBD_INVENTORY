@extends('layouts.master')
@section('title', 'Transaksi Penjualan')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-cash-register"></i> Transaksi Penjualan Baru</h2>
    
    @if(!$marginAktif)
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Tidak ada margin penjualan aktif!</strong> 
        Silakan aktifkan margin terlebih dahulu.
        <a href="{{ route('superadmin.margin-penjualan.index') }}" class="btn btn-sm btn-warning ms-2">
            <i class="fas fa-cog"></i> Atur Margin
        </a>
    </div>
    @else
    
    <form action="{{ route('superadmin.penjualan.store') }}" method="POST" id="formPenjualan">
        @csrf
        <input type="hidden" name="idmargin_penjualan" value="{{ $marginAktif->idmargin_penjualan }}">
        
        <div class="row">
            <!-- Informasi Penjualan -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0">Informasi Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Tanggal Transaksi</label>
                            <input type="text" class="form-control" value="{{ date('d/m/Y H:i') }}" readonly>
                            <small class="text-muted">Otomatis terisi saat ini</small>
                        </div>

                        <div class="mb-3">
                            <label>Kasir</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->username }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Margin Penjualan</label>
                            <input type="text" class="form-control bg-info text-white" 
                                   value="{{ $marginAktif->persen_margin }}%" readonly>
                            <small class="text-muted">Harga sudah termasuk margin ini</small>
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
                            <strong>Total Bayar:</strong>
                            <div class="text-end h3 text-success" id="displayTotal">Rp 0</div>
                        </div>

                        <input type="hidden" name="subtotal_nilai" id="subtotal_nilai" value="0">
                        <input type="hidden" name="ppn" id="ppn" value="0">
                        <input type="hidden" name="total_nilai" id="total_nilai" value="0">
                    </div>
                </div>

                <!-- Stok Info -->
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0"><i class="fas fa-boxes"></i> Info Stok</h6>
                    </div>
                    <div class="card-body">
                        <div id="infoStokBarang">
                            <p class="text-muted text-center">Pilih barang untuk melihat stok</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Barang -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0">Detail Barang yang Dijual</h6>
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
                                                data-harga="{{ $b->harga_jual }}"
                                                data-stok="{{ $b->stok_tersedia }}">
                                            {{ $b->nama_barang }} - Stok: {{ $b->stok_tersedia }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Harga Jual</label>
                                    <input type="number" class="form-control bg-light" id="inputHarga" placeholder="0" readonly>
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
                                        <th class="text-end">Harga Jual</th>
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
                    <a href="{{ route('superadmin.penjualan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                        <i class="fas fa-check"></i> Proses Penjualan
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

@endsection

@push('scripts')
<script>
let detailBarang = [];
let counter = 0;
const marginPersen = {{ $marginAktif ? $marginAktif->persen_margin : 0 }};

$(document).ready(function() {
    // Event: Pilih Barang
    $('#selectBarang').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const harga = parseFloat(selectedOption.data('harga')) || 0;
        const stok = parseInt(selectedOption.data('stok')) || 0;
        const nama = selectedOption.data('nama');
        
        $('#inputHarga').val(harga);
        $('#inputJumlah').val(1).attr('max', stok).focus();
        
        // Update info stok
        if (selectedOption.val()) {
            let statusBadge = '';
            if (stok === 0) {
                statusBadge = '<span class="badge bg-danger">Habis</span>';
            } else if (stok <= 10) {
                statusBadge = '<span class="badge bg-warning">Menipis</span>';
            } else {
                statusBadge = '<span class="badge bg-success">Tersedia</span>';
            }
            
            $('#infoStokBarang').html(`
                <h6><strong>${nama}</strong></h6>
                <div class="mb-2">Stok: <strong class="text-primary">${stok}</strong></div>
                <div>Status: ${statusBadge}</div>
            `);
        }
    });

    // Event: Tambah Barang
    $('#btnTambahBarang').on('click', function() {
        const idbarang = $('#selectBarang').val();
        const namaBarang = $('#selectBarang').find(':selected').data('nama');
        const satuan = $('#selectBarang').find(':selected').data('satuan');
        const harga = parseFloat($('#inputHarga').val()) || 0;
        const jumlah = parseInt($('#inputJumlah').val()) || 0;
        const stokTersedia = parseInt($('#selectBarang').find(':selected').data('stok')) || 0;

        if (!idbarang) {
            alert('Pilih barang terlebih dahulu!');
            return;
        }
        if (jumlah <= 0) {
            alert('Jumlah harus lebih dari 0!');
            return;
        }
        if (jumlah > stokTersedia) {
            alert(`Stok tidak mencukupi! Stok tersedia: ${stokTersedia}`);
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
            subtotal: subtotal,
            stok: stokTersedia
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
    $('#formPenjualan').on('submit', function(e) {
        if (detailBarang.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 barang!');
            return false;
        }

        // Tambahkan detail barang ke form
        detailBarang.forEach((item, index) => {
            $('<input>').attr({
                type: 'hidden',
                name: `details[${index}][idbarang]`,
                value: item.idbarang
            }).appendTo('#formPenjualan');
            
            $('<input>').attr({
                type: 'hidden',
                name: `details[${index}][jumlah]`,
                value: item.jumlah
            }).appendTo('#formPenjualan');
        });

        if (!confirm('Yakin ingin memproses penjualan ini?')) {
            e.preventDefault();
            return false;
        }
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
                <td class="text-end"><strong>Rp ${item.subtotal.toLocaleString('id-ID')}</strong></td>
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
    $('#infoStokBarang').html('<p class="text-muted text-center">Pilih barang untuk melihat stok</p>');
}
</script>
@endpush