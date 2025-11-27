<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm table-stok">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Stok Tersedia</th>
                <th>Satuan</th>
                <th class="text-end">Harga</th>
                <th class="text-center">Status Stok</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $index => $b)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $b->nama_barang }}</strong></td>
                <td>{{ $b->kategori_barang }}</td>
                <td class="text-center">
                    <h5 class="mb-0">
                        @if($b->stok_tersedia == 0)
                            <span class="text-danger">{{ $b->stok_tersedia }}</span>
                        @elseif($b->stok_tersedia <= 10)
                            <span class="text-warning">{{ $b->stok_tersedia }}</span>
                        @else
                            <span class="text-success">{{ $b->stok_tersedia }}</span>
                        @endif
                    </h5>
                </td>
                <td>{{ $b->nama_satuan }}</td>
                <td class="text-end">Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($b->status_stok == 'Habis')
                        <span class="badge bg-danger">Habis</span>
                    @elseif($b->status_stok == 'Menipis')
                        <span class="badge bg-warning">Menipis</span>
                    @else
                        <span class="badge bg-success">Tersedia</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('superadmin.kartu-stok.detail', $b->idbarang) }}" class="btn btn-sm btn-info" title="Lihat Riwayat">
                        <i class="fas fa-history"></i> Riwayat
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>