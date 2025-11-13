<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller
{
    // Laporan stok semua barang
    public function index()
    {
        // Gunakan VIEW v_laporan_stok_barang
        $stok = DB::table('v_laporan_stok_barang')
            ->orderBy('nama_barang')
            ->get();

        // Hitung total nilai inventory
        $totalInventory = DB::table('v_laporan_stok_barang as s')
            ->join('barang as b', 's.idbarang', '=', 'b.idbarang')
            ->selectRaw('SUM(s.saldo_akhir * b.harga) as total_nilai')
            ->value('total_nilai');

        return view('superadmin.kartu_stok.index', compact('stok', 'totalInventory'));
    }

    // Detail kartu stok per barang
    public function show($idbarang)
    {
        // Info barang
        $barang = DB::table('v_data_barang')
            ->where('idbarang', $idbarang)
            ->first();

        if (!$barang) {
            abort(404, 'Barang tidak ditemukan');
        }

        // History transaksi dari kartu_stok
        $history = DB::table('kartu_stok as k')
            ->where('k.idbarang', $idbarang)
            ->orderBy('k.created_at', 'desc')
            ->select(
                'k.*',
                DB::raw("CASE 
                    WHEN k.jenis_transaksi = 'M' THEN 'Penerimaan'
                    WHEN k.jenis_transaksi = 'J' THEN 'Penjualan'
                    WHEN k.jenis_transaksi = 'R' THEN 'Retur'
                    ELSE 'Lainnya'
                END as nama_transaksi")
            )
            ->get();

        // Summary
        $summary = DB::table('v_laporan_stok_barang')
            ->where('idbarang', $idbarang)
            ->first();

        return view('superadmin.kartu_stok.detail', compact('barang', 'history', 'summary'));
    }

    // Filter kartu stok by periode
    public function filter(Request $request, $idbarang)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $barang = DB::table('v_data_barang')
            ->where('idbarang', $idbarang)
            ->first();

        $query = DB::table('kartu_stok as k')
            ->where('k.idbarang', $idbarang)
            ->select(
                'k.*',
                DB::raw("CASE 
                    WHEN k.jenis_transaksi = 'M' THEN 'Penerimaan'
                    WHEN k.jenis_transaksi = 'J' THEN 'Penjualan'
                    WHEN k.jenis_transaksi = 'R' THEN 'Retur'
                    ELSE 'Lainnya'
                END as nama_transaksi")
            );

        if ($startDate && $endDate) {
            $query->whereBetween('k.created_at', [$startDate, $endDate]);
        }

        $history = $query->orderBy('k.created_at', 'desc')->get();

        // Hitung summary filtered
        $totalMasuk = $history->sum('masuk');
        $totalKeluar = $history->sum('keluar');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        return view('superadmin.kartu_stok.detail', compact(
            'barang', 
            'history', 
            'totalMasuk', 
            'totalKeluar', 
            'saldoAkhir'
        ));
    }

    // Barang dengan stok menipis (alert)
    public function lowStock()
    {
        $threshold = 10; // Threshold stok minimum

        $lowStock = DB::table('v_laporan_stok_barang as s')
            ->join('v_data_barang as b', 's.idbarang', '=', 'b.idbarang')
            ->where('s.saldo_akhir', '<', $threshold)
            ->where('s.saldo_akhir', '>', 0)
            ->select('b.*', 's.saldo_akhir as stok')
            ->get();

        $outOfStock = DB::table('v_laporan_stok_barang as s')
            ->join('v_data_barang as b', 's.idbarang', '=', 'b.idbarang')
            ->where('s.saldo_akhir', '<=', 0)
            ->select('b.*', 's.saldo_akhir as stok')
            ->get();

        return view('superadmin.kartu_stok.low_stock', compact('lowStock', 'outOfStock'));
    }

    // Export Excel (opsional)
    public function export()
    {
        $stok = DB::table('v_laporan_stok_barang')
            ->join('barang', 'v_laporan_stok_barang.idbarang', '=', 'barang.idbarang')
            ->select('v_laporan_stok_barang.*', 'barang.harga')
            ->get();

        // Format data untuk export
        $data = $stok->map(function($item) {
            return [
                'ID Barang' => $item->idbarang,
                'Nama Barang' => $item->nama_barang,
                'Satuan' => $item->nama_satuan,
                'Total Masuk' => $item->total_masuk,
                'Total Keluar' => $item->total_keluar,
                'Saldo Akhir' => $item->saldo_akhir,
                'Harga Satuan' => $item->harga,
                'Nilai Inventory' => $item->saldo_akhir * $item->harga
            ];
        });

        return response()->json($data);
        // Atau gunakan package seperti Maatwebsite/Excel
    }
}