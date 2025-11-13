<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardSuperadminController extends Controller
{
    public function index()
    {
        // 1. CARD STATISTICS
        
        // Total Barang Aktif
        $totalBarang = DB::table('v_data_barang_aktif')->count();
        
        // Total Nilai Inventory
        $nilaiInventory = DB::table('v_laporan_stok_barang as s')
            ->join('barang as b', 's.idbarang', '=', 'b.idbarang')
            ->selectRaw('SUM(s.saldo_akhir * b.harga) as total')
            ->value('total') ?? 0;
        
        // Penjualan Hari Ini
        $penjualanHariIni = DB::table('penjualan')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_nilai');
        
        // Total Stok Barang
        $totalStok = DB::table('v_laporan_stok_barang')
            ->sum('saldo_akhir');
        
        // 2. GRAFIK PENJUALAN BULANAN (6 bulan terakhir)
        $penjualanBulanan = DB::table('v_laporan_penjualan_bulanan')
            ->orderBy('periode', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();
        
        $chartLabels = $penjualanBulanan->pluck('periode')->map(function($periode) {
            return Carbon::createFromFormat('Y-m', $periode)->format('M Y');
        });
        $chartData = $penjualanBulanan->pluck('total_akhir');
        
        // 3. TOP 5 PRODUK TERLARIS (30 hari terakhir)
        $topProduk = DB::table('detail_penjualan as dp')
            ->join('barang as b', 'dp.idbarang', '=', 'b.idbarang')
            ->join('penjualan as p', 'dp.idpenjualan', '=', 'p.idpenjualan')
            ->where('p.created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                'b.nama',
                DB::raw('SUM(dp.jumlah) as total_terjual'),
                DB::raw('SUM(dp.subtotal) as total_nilai')
            )
            ->groupBy('b.idbarang', 'b.nama')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();
        
        // 4. STOK MENIPIS (< 10 unit)
        $stokMenipis = DB::table('v_laporan_stok_barang as s')
            ->join('v_data_barang_aktif as b', 's.idbarang', '=', 'b.idbarang')
            ->where('s.saldo_akhir', '<', 10)
            ->where('s.saldo_akhir', '>', 0)
            ->select('b.*', 's.saldo_akhir as stok')
            ->orderBy('s.saldo_akhir', 'asc')
            ->limit(5)
            ->get();
        
        // 5. TRANSAKSI TERAKHIR
        $transaksiTerakhir = DB::table('penjualan as p')
            ->join('user as u', 'p.iduser', '=', 'u.iduser')
            ->select('p.*', 'u.username')
            ->orderBy('p.created_at', 'desc')
            ->limit(5)
            ->get();
        
        // 6. PENGADAAN PENDING (belum diterima)
        $pengadaanPending = DB::table('pengadaan as pg')
            ->leftJoin('penerimaan as p', 'pg.idpengadaan', '=', 'p.idpengadaan')
            ->join('vendor as v', 'pg.idvendor', '=', 'v.idvendor')
            ->whereNull('p.idpenerimaan')
            ->orWhere('p.status', '0')
            ->select('pg.*', 'v.nama_vendor')
            ->orderBy('pg.timestamp', 'desc')
            ->limit(5)
            ->get();
        
        // 7. DISTRIBUSI KATEGORI BARANG (Pie Chart)
        $kategoriBarang = DB::table('barang')
            ->select(
                'jenis',
                DB::raw("CASE 
                    WHEN jenis = 'S' THEN 'Sembako'
                    WHEN jenis = 'M' THEN 'Minuman'
                    WHEN jenis = 'K' THEN 'Makanan'
                    WHEN jenis = 'P' THEN 'Personal Care'
                    WHEN jenis = 'H' THEN 'Household'
                    WHEN jenis = 'D' THEN 'Dapur'
                    ELSE 'Lainnya'
                END as nama_kategori"),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('status', 1)
            ->groupBy('jenis')
            ->get();
        
        return view('superadmin.dashboard', compact(
            'totalBarang',
            'nilaiInventory',
            'penjualanHariIni',
            'totalStok',
            'chartLabels',
            'chartData',
            'topProduk',
            'stokMenipis',
            'transaksiTerakhir',
            'pengadaanPending',
            'kategoriBarang'

            
        ));

$penjualanBulanan = DB::table('v_laporan_penjualan_bulanan')
    ->orderBy('periode', 'desc')
    ->limit(6)
    ->get()
    ->reverse()
    ->values();

// TAMBAHKAN CHECK INI
if ($penjualanBulanan->isEmpty()) {
    $chartLabels = collect(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']);
    $chartData = collect([0, 0, 0, 0, 0, 0]);
} else {
    $chartLabels = $penjualanBulanan->pluck('periode')->map(function($periode) {
        return Carbon::createFromFormat('Y-m', $periode)->format('M Y');
    });
    $chartData = $penjualanBulanan->pluck('total_akhir');
}
    }
}