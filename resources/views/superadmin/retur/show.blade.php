@extends('layouts.master')
@section('title', 'Detail Retur')
@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Retur Barang</h1>

    <div class="row">
        <!-- Info Retur -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-undo me-1"></i>
                    Informasi Retur
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Retur</strong></td>
                            <td>: RTR-{{ str_pad($retur->idretur, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Retur</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($retur->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Petugas</strong></td>
                            <td>: {{ $retur->petugas }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Penerimaan -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-box me-1"></i>
                    Informasi Penerimaan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Penerimaan</strong></td>
                            <td>: 
                                <a href="{{ route('superadmin.penerimaan.show', $retur->idpenerimaan) }}" class="text-decoration-none">
                                    PNR-{{ str_pad($retur->idpenerimaan, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ID Pengadaan</strong></td>
                            <td>: 
                                <a href="{{ route('superadmin.pengadaan.show', $retur->idpengadaan) }}" class="text-decoration-none">
                                    PGD-{{ str_pad($retur->idpengadaan, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Vendor</strong></td>
                            <td>: {{ $retur->nama_vendor }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Barang Diretur -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-list me-1"></i>
            Detail Barang Diretur
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th class="text-end">Jumlah Retur</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($details as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_satuan }}</td>
                            <td class="text-end">{{ number_format($item->jumlah) }}</td>
                            <td class="text-end">Rp {{ number_format($item->harga_satuan_terima) }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal) }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $item->alasan }}</span>
                            </td>
                        </tr>
                        @php $total += $item->subtotal; @endphp
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL NILAI RETUR</th>
                            <th class="text-end">Rp {{ number_format($total) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="{{ route('superadmin.retur.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection