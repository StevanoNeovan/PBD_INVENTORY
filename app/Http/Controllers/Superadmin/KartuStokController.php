<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller
{
    /**
     * Display a listing of the resource (Kartu Stok dengan filter)
     */
    public function index(Request $request)
    {
        try {
            // Ambil filter
            $idbarang = $request->get('idbarang');
            $jenis_transaksi = $request->get('jenis_transaksi');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Build query dengan filter
            $query = "
                SELECT 
                    ks.idkartu_stok,
                    ks.created_at,
                    ks.jenis_transaksi,
                    ks.masuk,
                    ks.keluar,
                    ks.stock,
                    ks.idtransaksi,
                    b.nama AS nama_barang
                FROM kartu_stok ks
                JOIN barang b ON ks.idbarang = b.idbarang
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($idbarang) {
                $query .= " AND ks.idbarang = ?";
                $params[] = $idbarang;
            }
            
            if ($jenis_transaksi) {
                $query .= " AND ks.jenis_transaksi = ?";
                $params[] = $jenis_transaksi;
            }
            
            if ($tanggal_dari) {
                $query .= " AND DATE(ks.created_at) >= ?";
                $params[] = $tanggal_dari;
            }
            
            if ($tanggal_sampai) {
                $query .= " AND DATE(ks.created_at) <= ?";
                $params[] = $tanggal_sampai;
            }
            
            $query .= " ORDER BY ks.created_at DESC, ks.idkartu_stok DESC";
            
            $kartuStoks = DB::select($query, $params);
            
            // Ambil semua barang untuk dropdown filter
            $barangs = DB::select("SELECT idbarang, nama AS nama_barang FROM barang WHERE status = 1 ORDER BY nama ASC");
            
            return view('superadmin.kartu-stok.index', compact('kartuStoks', 'barangs'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Monitoring Stok (Dashboard Stok)
     */
    public function monitoring()
    {
        try {
            // Menggunakan view v_stok_barang
            $semuaBarang = DB::select("SELECT * FROM v_stok_barang ORDER BY nama_barang ASC");
            
            // Filter berdasarkan status stok
            $barangTersedia = array_filter($semuaBarang, function($b) {
                return $b->status_stok == 'Tersedia';
            });
            
            $barangMenipis = array_filter($semuaBarang, function($b) {
                return $b->status_stok == 'Menipis';
            });
            
            $barangHabis = array_filter($semuaBarang, function($b) {
                return $b->status_stok == 'Habis';
            });
            
            // Summary
            $totalBarang = count($semuaBarang);
            $stokTersedia = count($barangTersedia);
            $stokMenipis = count($barangMenipis);
            $stokHabis = count($barangHabis);
            
            return view('superadmin.kartu-stok.monitoring', compact(
                'semuaBarang', 
                'barangTersedia', 
                'barangMenipis', 
                'barangHabis',
                'totalBarang',
                'stokTersedia',
                'stokMenipis',
                'stokHabis'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Detail kartu stok per barang
     */
    public function detail(string $idbarang)
    {
        try {
            // Ambil info barang dengan stok
            $barang = DB::select("SELECT * FROM v_stok_barang WHERE idbarang = ?", [$idbarang]);
            
            if (empty($barang)) {
                return redirect()->route('superadmin.kartu-stok.monitoring')
                    ->with('error', 'Data barang tidak ditemukan');
            }
            
            $barang = $barang[0];
            
            // Ambil riwayat kartu stok untuk barang ini
            $riwayat = DB::select("
                SELECT *
                FROM kartu_stok
                WHERE idbarang = ?
                ORDER BY created_at DESC, idkartu_stok DESC
            ", [$idbarang]);
            
            // Hitung summary
            $totalMasuk = DB::selectOne("
                SELECT COALESCE(SUM(masuk), 0) AS total
                FROM kartu_stok
                WHERE idbarang = ?
            ", [$idbarang])->total;
            
            $totalKeluar = DB::selectOne("
                SELECT COALESCE(SUM(keluar), 0) AS total
                FROM kartu_stok
                WHERE idbarang = ?
            ", [$idbarang])->total;
            
            // Hitung jumlah transaksi per jenis
            $jumlahPenerimaan = DB::selectOne("
                SELECT COUNT(*) AS total
                FROM kartu_stok
                WHERE idbarang = ? AND jenis_transaksi = 'P'
            ", [$idbarang])->total;
            
            $jumlahPenjualan = DB::selectOne("
                SELECT COUNT(*) AS total
                FROM kartu_stok
                WHERE idbarang = ? AND jenis_transaksi = 'J'
            ", [$idbarang])->total;
            
            $jumlahRetur = DB::selectOne("
                SELECT COUNT(*) AS total
                FROM kartu_stok
                WHERE idbarang = ? AND jenis_transaksi = 'R'
            ", [$idbarang])->total;
            
            return view('superadmin.kartu-stok.detail', compact(
                'barang', 
                'riwayat', 
                'totalMasuk', 
                'totalKeluar',
                'jumlahPenerimaan',
                'jumlahPenjualan',
                'jumlahRetur'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
}