@extends('layouts.master')
@section('title', 'Detail Penerimaan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box-open"></i> Detail Penerimaan #{{ $penerimaan->idpenerimaan }}</h2>
        <div>
            @php
                $jumlah_retur = DB::selectOne("
                    SELECT COUNT(*) AS total
                    FROM retur
                    WHERE idpenerimaan = ?
                ", [$penerimaan->idpenerimaan])->total ?? 0;
            @endphp
            
            @if($jumlah_retur < 2 && $penerimaan->status == '1')
                <a href="{{ route('superadmin.retur.create', $penerimaan->idpenerimaan) }}" class="btn btn-warning me-2">
                    <i class="fas fa-undo"></i> Buat Retur
                </a>
            @elseif($jumlah_retur >= 2)
                <button class="btn btn-warning me-2" disabled title="Sudah retur maksimal 2x">
                    <i class="fas fa-ban"></i> Retur Maksimal
                </button>
            @endif
            
            <a href="{{ route('superadmin.pengadaan.show', $penerimaan->idpengadaan) }}" class="btn btn-info me-2">
                <i class="fas fa-shopping-cart"></i> Lihat Pengadaan
            </a>
            <a href="{{ route('superadmin.penerimaan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Informasi Penerimaan + History Retur -->
        <div class="col-md-4">
            <!-- Informasi Penerimaan -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0">Informasi Penerimaan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="45%">ID Penerimaan</th>
                            <td>: <strong>#{{ $penerimaan->idpenerimaan }}</strong></td>
                        </tr>
                        <tr>
                            <th>ID Pengadaan</th>
                            <td>: <a href="{{ route('superadmin.pengadaan.show', $penerimaan->idpengadaan) }}">#{{ $penerimaan->idpengadaan }}</a></td>
                        </tr>
                        <tr>
                            <th>Tanggal Terima</th>
                            <td>: {{ \Carbon\Carbon::parse($penerimaan->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                @if($penerimaan->status == '1')
                                    <span class="badge bg-success">✅ Diterima</span>
                                @else
                                    <span class="badge bg-danger">❌ Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Petugas</th>
                            <td>: {{ $penerimaan->username }}</td>
                        </tr>
                        <tr>
                            <th>Vendor</th>
                            <td>: {{ $penerimaan->nama_vendor }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> <strong>Status Pengadaan:</strong><br>
                        @if($statusPengadaan == 'SELESAI')
                            <span class="badge bg-success mt-2">SELESAI - Semua Lengkap</span>
                        @elseif($statusPengadaan == 'PARTIAL')
                            <span class="badge bg-warning mt-2">PARTIAL - Masih Ada Sisa</span>
                        @else
                            <span class="badge bg-danger mt-2">BELUM - Belum Ada Penerimaan</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- History Retur -->
            @if($jumlah_retur > 0)
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0">
                        <i class="fas fa-history"></i> History Retur ({{ $jumlah_retur }}x)
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $returs = DB::select("
                            SELECT 
                                r.idretur,
                                r.created_at,
                                u.username,
                                COUNT(dr.iddetail_retur) AS jumlah_item
                            FROM retur r
                            JOIN user u ON r.iduser = u.iduser
                            LEFT JOIN detail_retur dr ON r.idretur = dr.idretur
                            WHERE r.idpenerimaan = ?
                            GROUP BY r.idretur
                            ORDER BY r.created_at DESC
                        ", [$penerimaan->idpenerimaan]);
                    @endphp
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Retur</th>
                                    <th>Tanggal</th>
                                    <th>Petugas</th>
                                    <th>Item</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returs as $rtr)
                                <tr>
                                    <td><span class="badge bg-danger">RTR-{{ str_pad($rtr->idretur, 4, '0', STR_PAD_LEFT) }}</span></td>
                                    <td class="small">{{ \Carbon\Carbon::parse($rtr->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="small">{{ $rtr->username }}</td>
                                    <td><span class="badge bg-secondary">{{ $rtr->jumlah_item }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.retur.show', $rtr->idretur) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Detail Barang + Kartu Stok -->
        <div class="col-md-8">
            <!-- Detail Barang yang Diterima -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Detail Barang yang Diterima</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Diterima</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach($details as $index => $d)
                                @php $grandTotal += $d->sub_total_terima; @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $d->nama_barang }}</strong></td>
                                    <td>{{ $d->kategori_barang }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $d->jumlah_terima }} {{ $d->nama_satuan }}</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }}</td>
                                    <td class="text-end"><strong>Rp {{ number_format($d->sub_total_terima, 0, ',', '.') }}</strong></td>
                                </tr>
                                @endforeach
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end"><strong>TOTAL</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kartu Stok -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0"><i class="fas fa-clipboard-list"></i> Update Kartu Stok</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th class="text-center">Stok Masuk</th>
                                    <th class="text-center">Stok Sekarang</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kartuStok as $ks)
                                <tr>
                                    <td>{{ $ks->nama_barang }}</td>
                                    <td class="text-center"><span class="badge bg-success">+{{ $ks->masuk }}</span></td>
                                    <td class="text-center"><strong>{{ $ks->stock }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($ks->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-check-circle"></i> Stok barang telah diperbarui otomatis ke kartu stok.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection