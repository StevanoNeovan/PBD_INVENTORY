@extends('layouts.master')
@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cash-register"></i> Detail Penjualan #{{ $penjualan->idpenjualan }}</h2>
        <div>
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Cetak Struk
            </button>
            <a href="{{ route('superadmin.penjualan.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Penjualan -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0">Informasi Transaksi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="45%">ID Penjualan</th>
                            <td>: <strong>#{{ $penjualan->idpenjualan }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>: {{ \Carbon\Carbon::parse($penjualan->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Kasir</th>
                            <td>: {{ $penjualan->username }}</td>
                        </tr>
                        <tr>
                            <th>Margin</th>
                            <td>: <span class="badge bg-info">{{ $penjualan->persen_margin }}%</span></td>
                        </tr>
                    </table>

                    <hr>

                    <table class="table table-borderless table-sm">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">Rp {{ number_format($penjualan->subtotal_nilai, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>PPN (10%)</th>
                            <td class="text-end">Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-success">
                            <th><h5 class="mb-0">Total Bayar</h5></th>
                            <td class="text-end"><h5 class="mb-0 text-success"><strong>Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</strong></h5></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Kartu Stok -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0"><i class="fas fa-boxes"></i> Update Stok</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th class="text-center">Keluar</th>
                                    <th class="text-center">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kartuStok as $ks)
                                <tr>
                                    <td>{{ $ks->nama_barang }}</td>
                                    <td class="text-center"><span class="badge bg-danger">-{{ $ks->keluar }}</span></td>
                                    <td class="text-center"><strong>{{ $ks->stock }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-check-circle"></i> Stok telah dikurangi otomatis.
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang yang Dijual -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Detail Barang yang Dijual</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $d->nama_barang }}</strong></td>
                                    <td>{{ $d->kategori_barang }}</td>
                                    <td class="text-center">{{ $d->jumlah }} {{ $d->nama_satuan }}</td>
                                    <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end"><strong>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>SUBTOTAL</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($penjualan->subtotal_nilai, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>PPN (10%)</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="5" class="text-end"><h5 class="mb-0">TOTAL BAYAR</h5></td>
                                    <td class="text-end"><h5 class="mb-0 text-success"><strong>Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</strong></h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Struk Print Area -->
            <div class="d-none d-print-block" style="max-width: 300px; margin: 0 auto;">
                <div class="text-center mb-3">
                    <h4>TOKO INVENTORY PBD</h4>
                    <p class="mb-0">Jl. Contoh No. 123</p>
                    <p class="mb-0">Telp: 0812-3456-7890</p>
                </div>
                <hr>
                <table style="width: 100%; font-size: 12px;">
                    <tr>
                        <td>No. Transaksi</td>
                        <td>: #{{ $penjualan->idpenjualan }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($penjualan->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Kasir</td>
                        <td>: {{ $penjualan->username }}</td>
                    </tr>
                </table>
                <hr>
                <table style="width: 100%; font-size: 12px;">
                    @foreach($details as $d)
                    <tr>
                        <td colspan="3"><strong>{{ $d->nama_barang }}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 50px;">{{ $d->jumlah }}x</td>
                        <td style="text-align: right;">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td style="text-align: right; width: 80px;">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </table>
                <hr>
                <table style="width: 100%; font-size: 12px;">
                    <tr>
                        <td>Subtotal</td>
                        <td style="text-align: right;">Rp {{ number_format($penjualan->subtotal_nilai, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>PPN (10%)</td>
                        <td style="text-align: right;">Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td style="text-align: right;"><strong>Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
                <hr>
                <div class="text-center">
                    <p class="mb-0">Terima Kasih</p>
                    <p class="mb-0">Selamat Berbelanja Kembali</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .d-print-block, .d-print-block * {
        visibility: visible;
    }
    .d-print-block {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endsection