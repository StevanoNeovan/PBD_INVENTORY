@extends('layouts.master')

@section('title', 'Point of Sale - Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Side: Product List -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Pilih Barang</h5>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <input type="text" id="searchBarang" class="form-control" 
                               placeholder="🔍 Cari barang...">
                    </div>

                    <!-- Filter Kategori -->
                    <div class="btn-group mb-3" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="">Semua</button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="S">Sembako</button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="M">Minuman</button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="K">Makanan</button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="P">Personal Care</button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-kategori" data-jenis="H">Household</button>
                    </div>

                    <!-- Product Grid -->
                    <div class="row" id="productGrid">
                        @foreach($barang as $item)
                        <div class="col-md-4 mb-3 product-item" data-jenis="{{ $item->jenis }}" 
                             data-nama="{{ strtolower($item->nama_barang) }}">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $item->nama_barang }}</h6>
                                    <p class="text-muted small mb-1">{{ $item->kategori_barang }}</p>
                                    <p class="text-success fw-bold">Rp {{ number_format($item->harga) }}</p>
                                    <p class="text-info small">Stok: {{ $item->stok ?? 0 }}</p>
                                    <button class="btn btn-sm btn-primary btn-add-cart" 
                                            data-id="{{ $item->idbarang }}"
                                            data-nama="{{ $item->nama_barang }}"
                                            data-harga="{{ $item->harga }}"
                                            data-stok="{{ $item->stok ?? 0 }}">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Cart & Checkout -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Keranjang Belanja</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($cart->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                            <p>Keranjang masih kosong</p>
                        </div>
                    @else
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th width="80">Qty</th>
                                    <th width="100">Harga</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                @foreach($cart as $item)
                                <tr data-id="{{ $item->idtemp }}">
                                    <td>{{ $item->barang->nama }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm qty-input" 
                                               value="{{ $item->jumlah }}" min="1" 
                                               data-id="{{ $item->idtemp }}">
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger btn-remove" 
                                                data-id="{{ $item->idtemp }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="card-footer">
                    <!-- Summary -->
                    <table class="table table-borderless mb-3">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-end fw-bold" id="subtotal">Rp {{ number_format($subtotal) }}</td>
                        </tr>
                        <tr>
                            <td>PPN 11%:</td>
                            <td class="text-end" id="ppn">Rp {{ number_format($ppn) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold fs-5">TOTAL:</td>
                            <td class="text-end fw-bold fs-5 text-success" id="grandTotal">
                                Rp {{ number_format($grandTotal) }}
                            </td>
                        </tr>
                    </table>

                    <!-- Margin Selection -->
                    <div class="mb-3">
                        <label class="form-label">Margin Penjualan:</label>
                        <select class="form-select" id="marginPenjualan" name="idmargin_penjualan" required>
                            <option value="">Pilih Margin</option>
                            @foreach($margin as $m)
                            <option value="{{ $m->idmargin_penjualan }}">{{ $m->persen_margin }}%</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg" id="btnCheckout" 
                                {{ $cart->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle"></i> BAYAR (F9)
                        </button>
                        <button class="btn btn-warning" id="btnClearCart" 
                                {{ $cart->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-eraser"></i> Bersihkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add to Cart -->
<div class="modal fade" id="modalAddCart" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah ke Keranjang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="addIdBarang">
                <input type="hidden" id="addMaxStok">
                <h5 id="addNamaBarang"></h5>
                <p class="text-muted">Harga: <span id="addHargaBarang"></span></p>
                
                <div class="mb-3">
                    <label class="form-label">Jumlah:</label>
                    <input type="number" class="form-control" id="addJumlah" value="1" min="1">
                    <small class="text-muted">Stok tersedia: <span id="addStok"></span></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnConfirmAdd">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const marginSelect = $('#marginPenjualan');
    
    // Filter kategori
    $('.filter-kategori').click(function() {
        $('.filter-kategori').removeClass('active');
        $(this).addClass('active');
        
        const jenis = $(this).data('jenis');
        if (jenis === '') {
            $('.product-item').show();
        } else {
            $('.product-item').hide();
            $(`.product-item[data-jenis="${jenis}"]`).show();
        }
    });

    // Search barang
    $('#searchBarang').on('keyup', function() {
        const keyword = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const nama = $(this).data('nama');
            $(this).toggle(nama.includes(keyword));
        });
    });

    // Add to cart - show modal
    $(document).on('click', '.btn-add-cart', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const harga = $(this).data('harga');
        const stok = $(this).data('stok');
        
        $('#addIdBarang').val(id);
        $('#addNamaBarang').text(nama);
        $('#addHargaBarang').text('Rp ' + harga.toLocaleString());
        $('#addStok').text(stok);
        $('#addMaxStok').val(stok);
        $('#addJumlah').val(1).attr('max', stok);
        
        $('#modalAddCart').modal('show');
    });

    // Confirm add to cart
    $('#btnConfirmAdd').click(function() {
        const idbarang = $('#addIdBarang').val();
        const jumlah = parseInt($('#addJumlah').val());
        const maxStok = parseInt($('#addMaxStok').val());
        const idmargin = marginSelect.val();
        
        if (!idmargin) {
            alert('Pilih margin penjualan terlebih dahulu!');
            return;
        }
        
        if (jumlah > maxStok) {
            alert(`Jumlah melebihi stok! Maksimal: ${maxStok}`);
            return;
        }
        
        $.ajax({
            url: '{{ route("superadmin.penjualan.cart.add") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                idbarang: idbarang,
                jumlah: jumlah,
                idmargin_penjualan: idmargin
            },
            success: function(response) {
                $('#modalAddCart').modal('hide');
                location.reload(); // Refresh page to update cart
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message);
            }
        });
    });

    // Update quantity
    $(document).on('change', '.qty-input', function() {
        const id = $(this).data('id');
        const jumlah = $(this).val();
        
        $.ajax({
            url: `/superadmin/penjualan/cart/${id}`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                jumlah: jumlah
            },
            success: function() {
                location.reload();
            }
        });
    });

    // Remove from cart
    $(document).on('click', '.btn-remove', function() {
        if (!confirm('Hapus item ini?')) return;
        
        const id = $(this).data('id');
        $.ajax({
            url: `/superadmin/penjualan/cart/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                location.reload();
            }
        });
    });

    // Clear cart
    $('#btnClearCart').click(function() {
        if (!confirm('Kosongkan keranjang?')) return;
        
        $.ajax({
            url: '{{ route("superadmin.penjualan.cart.clear") }}',
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                location.reload();
            }
        });
    });

    // Checkout
    $('#btnCheckout').click(function() {
        const idmargin = marginSelect.val();
        
        if (!idmargin) {
            alert('Pilih margin penjualan terlebih dahulu!');
            return;
        }
        
        if (confirm('Proses pembayaran?')) {
            $('<form>', {
                method: 'POST',
                action: '{{ route("superadmin.penjualan.checkout") }}'
            }).append(
                $('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }),
                $('<input>', { type: 'hidden', name: 'idmargin_penjualan', value: idmargin })
            ).appendTo('body').submit();
        }
    });

    // Keyboard shortcut F9 for checkout
    $(document).keydown(function(e) {
        if (e.keyCode === 120) { // F9
            e.preventDefault();
            $('#btnCheckout').click();
        }
    });
});
</script>
@endpush