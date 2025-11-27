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
                            <td>: <a href="{{ route('superadmin.pengadaan.show', $penerimaan->idpengadaan) }}" class="text-decoration-none">#{{ $penerimaan->idpengadaan }}</a></td>
                        </tr>
                        <tr>
                            <th>Tgl Pengadaan</th>
                            <td>: {{ \Carbon\Carbon::parse($penerimaan->tanggal_pengadaan)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tgl Penerimaan</th>
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
                            <td>: <strong>{{ $penerimaan->nama_vendor }}</strong></td>
                        </tr>
                        <tr>
                            <th>Total Nilai</th>
                            <td>: <strong class="text-success">Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> <strong>Status Pengadaan:</strong><br>
                        @if($statusPengadaan == 'SELESAI')
                            <span class="badge bg-success mt-2">SELESAI - Semua Lengkap</span>
                        @elseif($statusPengadaan == 'PARTIAL')
                            <span class="badge bg-warning text-dark mt-2">PARTIAL - Masih Ada Sisa</span>
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
                            GROUP BY r.idretur, r.created_at, u.username
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
                                    <th class="text-center">Item</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returs as $rtr)
                                <tr>
                                    <td><span class="badge bg-danger">RTR-{{ str_pad($rtr->idretur, 4, '0', STR_PAD_LEFT) }}</span></td>
                                    <td class="small">{{ \Carbon\Carbon::parse($rtr->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="small">{{ $rtr->username }}</td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $rtr->jumlah_item }}</span></td>
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
                                    <th width="40">No</th>
                                    <th>Nama Barang</th>
                                    <th width="150">Kategori</th>
                                    <th class="text-center" width="100">Jumlah Diterima</th>
                                    <th class="text-end" width="120">Harga Barang Sebelum</th>
                                    <th class="text-end" width="120">Harga Invoice</th>
                                    <th class="text-end" width="120">Harga Barang Sekarang</th>
                                    <th class="text-end" width="130">Subtotal</th>
                                </tr>
                            </thead>
                           <tbody>
    @foreach($details as $index => $d)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td><strong>{{ $d->nama_barang }}</strong></td>
        <td><small class="text-muted">{{ $d->kategori_barang }}</small></td>
        <td class="text-center">
            <span class="badge bg-success">{{ $d->jumlah_terima }} {{ $d->nama_satuan }}</span>
        </td>
        
        <!-- HARGA BARANG SEBELUM PENERIMAAN INI -->
        <td class="text-end">
            @if($d->harga_barang_sebelum)
                <span class="text-muted">Rp {{ number_format($d->harga_barang_sebelum, 0, ',', '.') }}</span>
            @else
                <span class="badge bg-info text-white">Penerimaan Pertama</span>
            @endif
        </td>
        
        <!-- HARGA INVOICE (HARGA SATUAN TERIMA) -->
        <td class="text-end">
            @if($d->harga_barang_sebelum)
                @php
                    $selisih_invoice = $d->harga_satuan_terima - $d->harga_barang_sebelum;
                @endphp
                
                @if($selisih_invoice > 0)
                    {{-- Harga naik dari sebelumnya --}}
                    <strong class="text-danger" title="Naik Rp {{ number_format($selisih_invoice, 0, ',', '.') }}">
                        Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }} 
                        <i class="fas fa-arrow-up"></i>
                    </strong>
                @elseif($selisih_invoice < 0)
                    {{-- Harga turun dari sebelumnya --}}
                    <strong class="text-success" title="Turun Rp {{ number_format(abs($selisih_invoice), 0, ',', '.') }}">
                        Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }} 
                        <i class="fas fa-arrow-down"></i>
                    </strong>
                @else
                    {{-- Harga sama --}}
                    <strong>Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }}</strong>
                @endif
            @else
                <strong>Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }}</strong>
            @endif
        </td>
        
                                <!-- HARGA BARANG SEKARANG (SETELAH UPDATE TRIGGER) -->
                                <td class="text-end">
                                    @php
                                        // Bandingkan harga invoice dengan harga barang sekarang
                                        $selisih_sekarang = $d->harga_barang_sekarang - $d->harga_satuan_terima;
                                    @endphp
                                    
                                    @if($selisih_sekarang > 0)
                                        {{-- Harga naik setelah penerimaan ini (ada penerimaan baru) --}}
                                        <span class="badge bg-danger" title="Harga naik Rp {{ number_format($selisih_sekarang, 0, ',', '.') }}">
                                            Rp {{ number_format($d->harga_barang_sekarang, 0, ',', '.') }} 
                                            <i class="fas fa-arrow-up"></i>
                                        </span>
                                    @elseif($selisih_sekarang < 0)
                                        {{-- Harga turun setelah penerimaan ini (ada penerimaan baru dengan harga lebih murah) --}}
                                        <span class="badge bg-success" title="Harga turun Rp {{ number_format(abs($selisih_sekarang), 0, ',', '.') }}">
                                            Rp {{ number_format($d->harga_barang_sekarang, 0, ',', '.') }} 
                                            <i class="fas fa-arrow-down"></i>
                                        </span>
                                    @else
                                        {{-- Harga masih sama (belum ada penerimaan baru) --}}
                                        <span class="badge bg-secondary" title="Harga masih sama / penerimaan terakhir">
                                            Rp {{ number_format($d->harga_barang_sekarang, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="text-end"><strong>Rp {{ number_format($d->sub_total_terima, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                            <tr class="table-primary">
                                <td colspan="7" class="text-end"><strong>TOTAL NILAI PENERIMAAN</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle"></i> <strong>Penjelasan Kolom Harga:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>"Harga Barang Sebelum"</strong> = Harga barang dari penerimaan sebelumnya (jika ada)</li>
                            <li><strong>"Harga Invoice"</strong> = Harga dari invoice vendor saat penerimaan ini
                                <ul>
                                    <li><i class="fas fa-arrow-up text-danger"></i> = Harga naik dari sebelumnya</li>
                                    <li><i class="fas fa-arrow-down text-success"></i> = Harga turun dari sebelumnya</li>
                                </ul>
                            </li>
                            <li><strong>"Harga Barang Sekarang"</strong> = Harga terbaru di sistem (hasil update trigger)
                                <ul>
                                    <li>Badge <span class="badge bg-secondary">abu-abu</span> = Ini adalah penerimaan terakhir</li>
                                    <li>Badge <span class="badge bg-danger">merah <i class="fas fa-arrow-up"></i></span> = Ada penerimaan baru dengan harga lebih tinggi</li>
                                    <li>Badge <span class="badge bg-success">hijau <i class="fas fa-arrow-down"></i></span> = Ada penerimaan baru dengan harga lebih murah</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kartu Stok -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0"><i class="fas fa-clipboard-list"></i> Update Kartu Stok</h6>
                </div>
                <div class="card-body">
                    @if(count($kartuStok) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-center" width="120">Stok Masuk</th>
                                    <th class="text-center" width="120">Saldo Stok</th>
                                    <th width="150">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kartuStok as $ks)
                                <tr>
                                    <td><strong>{{ $ks->nama_barang }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-success">+{{ $ks->masuk }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-primary">{{ $ks->saldo_stok }}</strong>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($ks->created_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Tidak ada data kartu stok untuk penerimaan ini.
                    </div>
                    @endif
                    
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-check-circle"></i> <strong>Stok barang telah diperbarui otomatis </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection