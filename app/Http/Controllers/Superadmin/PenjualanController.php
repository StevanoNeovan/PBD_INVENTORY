<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Ambil margin aktif
            $marginAktif = DB::select("SELECT * FROM v_data_margin_penjualan_aktif LIMIT 1");
            $marginAktif = !empty($marginAktif) ? $marginAktif[0] : null;
            
            // Ambil semua penjualan
            $penjualans = DB::select("
                SELECT DISTINCT
                    p.idpenjualan,
                    p.created_at,
                    p.subtotal_nilai,
                    p.ppn,
                    p.total_nilai,
                    u.username,
                    m.persen AS persen_margin
                FROM penjualan p
                JOIN user u ON p.iduser = u.iduser
                JOIN margin_penjualan m ON p.idmargin_penjualan = m.idmargin_penjualan
                ORDER BY p.created_at DESC
            ");
            
            // Hitung total
            $totalSubtotal = array_sum(array_column($penjualans, 'subtotal_nilai'));
            $totalPPN = array_sum(array_column($penjualans, 'ppn'));
            $totalGrand = array_sum(array_column($penjualans, 'total_nilai'));
            
            return view('superadmin.penjualan.index', compact('penjualans', 'marginAktif', 'totalSubtotal', 'totalPPN', 'totalGrand'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Ambil margin aktif
            $marginAktif = DB::select("SELECT * FROM v_data_margin_penjualan_aktif LIMIT 1");
            $marginAktif = !empty($marginAktif) ? $marginAktif[0] : null;
            
            // Ambil barang aktif dengan stok dan harga jual
            $barangs = DB::select("
                SELECT 
                    b.idbarang,
                    b.nama AS nama_barang,
                    s.nama_satuan,
                    b.harga AS harga_beli,
                    fn_cek_stok(b.idbarang) AS stok_tersedia,
                    CASE 
                        WHEN ? IS NOT NULL THEN 
                            ROUND(b.harga * (1 + (? / 100)), 0)
                        ELSE 
                            b.harga
                    END AS harga_jual
                FROM barang b
                JOIN satuan s ON b.idsatuan = s.idsatuan
                WHERE b.status = 1
                HAVING stok_tersedia > 0
                ORDER BY b.nama ASC
            ", [
                $marginAktif ? $marginAktif->persen_margin : null,
                $marginAktif ? $marginAktif->persen_margin : null
            ]);
            
            return view('superadmin.penjualan.create', compact('barangs', 'marginAktif'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idmargin_penjualan' => 'required|exists:margin_penjualan,idmargin_penjualan',
            'details' => 'required|array|min:1',
            'details.*.idbarang' => 'required|exists:barang,idbarang',
            'details.*.jumlah' => 'required|integer|min:1'
        ], [
            'idmargin_penjualan.required' => 'Margin penjualan harus ada',
            'details.required' => 'Minimal 1 barang harus ditambahkan',
            'details.min' => 'Minimal 1 barang harus ditambahkan'
        ]);

        try {
            DB::beginTransaction();
            
            $iduser = Auth::id();
            $idmargin = $request->idmargin_penjualan;
            
            // Ambil persen margin
            $margin = DB::selectOne("SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = ?", [$idmargin]);
            $persenMargin = $margin->persen;
            
            // Validasi stok untuk semua barang
            foreach ($request->details as $detail) {
                $stok = DB::selectOne("SELECT fn_cek_stok(?) AS stok", [$detail['idbarang']])->stok;
                if ($stok < $detail['jumlah']) {
                    $namaBarang = DB::selectOne("SELECT nama FROM barang WHERE idbarang = ?", [$detail['idbarang']])->nama;
                    return back()->withInput()
                        ->with('error', "Stok tidak mencukupi untuk {$namaBarang}! Stok tersedia: {$stok}");
                }
            }
            
            // Insert penjualan (subtotal, ppn, total akan diupdate otomatis oleh trigger)
            DB::statement("
                INSERT INTO penjualan (iduser, idmargin_penjualan, subtotal_nilai, ppn, total_nilai, created_at)
                VALUES (?, ?, 0, 0, 0, NOW())
            ", [$iduser, $idmargin]);
            
            // Ambil ID penjualan yang baru dibuat
            $idpenjualan = DB::getPdo()->lastInsertId();
            
            // Insert detail penjualan
            foreach ($request->details as $detail) {
                // Ambil harga beli
                $hargaBeli = DB::selectOne("SELECT harga FROM barang WHERE idbarang = ?", [$detail['idbarang']])->harga;
                
                // Hitung harga jual dengan margin
                $hargaJual = round($hargaBeli * (1 + ($persenMargin / 100)), 0);
                $subtotal = $hargaJual * $detail['jumlah'];
                
                // Insert detail penjualan
                DB::statement("
                    INSERT INTO detail_penjualan (idpenjualan, idbarang, jumlah, harga_satuan, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ", [$idpenjualan, $detail['idbarang'], $detail['jumlah'], $hargaJual, $subtotal]);
                
                // Trigger tr_after_insert_detail_penjualan akan otomatis:
                // 1. Update kartu_stok (stok keluar)
                // 2. Update total di tabel penjualan
            }
            
            DB::commit();
            
            return redirect()->route('superadmin.penjualan.show', $idpenjualan)
                ->with('success', 'Penjualan berhasil diproses');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memproses penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Ambil data penjualan
            $penjualan = DB::select("
                SELECT 
                    p.*,
                    u.username,
                    m.persen AS persen_margin
                FROM penjualan p
                JOIN user u ON p.iduser = u.iduser
                JOIN margin_penjualan m ON p.idmargin_penjualan = m.idmargin_penjualan
                WHERE p.idpenjualan = ?
            ", [$id]);
            
            if (empty($penjualan)) {
                return redirect()->route('superadmin.penjualan.index')
                    ->with('error', 'Data penjualan tidak ditemukan');
            }
            
            $penjualan = $penjualan[0];
            
            // Ambil detail barang yang dijual
            $details = DB::select("
                SELECT 
                    dp.iddetail_penjualan,
                    dp.idbarang,
                    b.nama AS nama_barang,
                    CASE 
                        WHEN b.jenis = 'S' THEN 'Sembako & Bahan Pokok'
                        WHEN b.jenis = 'M' THEN 'Minuman'
                        WHEN b.jenis = 'K' THEN 'Makanan Olahan & Snack'
                        WHEN b.jenis = 'P' THEN 'Personal Care'
                        WHEN b.jenis = 'H' THEN 'Household / Home Care'
                        WHEN b.jenis = 'D' THEN 'Dapur & Plastik'
                        ELSE 'Lain-lain'
                    END AS kategori_barang,
                    s.nama_satuan,
                    dp.jumlah,
                    dp.harga_satuan,
                    dp.subtotal
                FROM detail_penjualan dp
                JOIN barang b ON dp.idbarang = b.idbarang
                JOIN satuan s ON b.idsatuan = s.idsatuan
                WHERE dp.idpenjualan = ?
            ", [$id]);
            
            // Ambil kartu stok untuk penjualan ini
            $kartuStok = DB::select("
                SELECT 
                    ks.*,
                    b.nama AS nama_barang
                FROM kartu_stok ks
                JOIN barang b ON ks.idbarang = b.idbarang
                WHERE ks.jenis_transaksi = 'J' AND ks.idtransaksi = ?
                ORDER BY ks.created_at DESC
            ", [$id]);
            
            return view('superadmin.penjualan.show', compact('penjualan', 'details', 'kartuStok'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
}