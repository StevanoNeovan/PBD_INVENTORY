@extends('layouts.master')
@section('title', 'Detail Pengadaan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart"></i> Detail Pengadaan #{{ $pengadaan->idpengadaan }}</h2>
        <a href="{{ route('superadmin.pengadaan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

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
                            <th width="40%">ID Pengadaan</th>
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
                            <th>Petugas</th>
                            <td>: {{ $pengadaan->username }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                @if($pengadaan->status_pengadaan == 'SELESAI')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($pengadaan->status_pengadaan == 'PARTIAL')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Belum Diterima</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <table class="table table-borderless table-sm">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">Rp {{ number_format($pengadaan->subtotal_nilai, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>PPN (10%)</th>
                            <td class="text-end">Rp {{ number_format($pengadaan->ppn, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Total</th>
                            <td class="text-end"><strong>Rp {{ number_format($pengadaan->total_nilai, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>

                    @if($pengadaan->status_pengadaan != 'SELESAI')
                    <div class="d-grid mt-3">
                        <a href="{{ route('superadmin.penerimaan.create', ['pengadaan' => $pengadaan->idpengadaan]) }}" 
                           class="btn btn-success">
                            <i class="fas fa-box-open"></i> Proses Penerimaan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Detail Barang</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Pesan</th>
                                    <th class="text-center">Diterima</th>
                                    <th class="text-center">Sisa</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td>{{ $d->kategori_barang }}</td>
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
                                    <td class="text-end">Rp {{ number_format($d->sub_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Penerimaan -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0">Riwayat Penerimaan</h6>
                </div>
                <div class="card-body">
                    @if(count($riwayatPenerimaan) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Petugas</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatPenerimaan as $r)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $r->username }}</td>
                                    <td>
                                        @if($r->status == '1')
                                            <span class="badge bg-success">Diterima</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.penerimaan.show', $r->idpenerimaan) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted">Belum ada penerimaan barang untuk pengadaan ini</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection