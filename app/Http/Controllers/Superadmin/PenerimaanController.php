<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Ambil semua penerimaan
            $penerimaans = DB::select("
                SELECT 
                    p.idpenerimaan,
                    p.created_at,
                    p.status,
                    p.idpengadaan,
                    u.username,
                    v.nama_vendor
                FROM penerimaan p
                JOIN user u ON p.iduser = u.iduser
                JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
                JOIN vendor v ON pg.idvendor = v.idvendor
                ORDER BY p.created_at DESC
            ");
            
            return view('superadmin.penerimaan.index', compact('penerimaans'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $idpengadaan = $request->get('pengadaan');
            
            if (!$idpengadaan) {
                return redirect()->route('superadmin.pengadaan.index')
                    ->with('error', 'ID Pengadaan tidak ditemukan');
            }
            
            // Ambil data pengadaan
            $pengadaan = DB::select("
                SELECT 
                    p.*,
                    v.nama_vendor,
                    u.username,
                    fn_status_pengadaan(p.idpengadaan) AS status_pengadaan
                FROM pengadaan p
                JOIN vendor v ON p.idvendor = v.idvendor
                JOIN user u ON p.iduser = u.iduser
                WHERE p.idpengadaan = ?
            ", [$idpengadaan]);
            
            if (empty($pengadaan)) {
                return redirect()->route('superadmin.pengadaan.index')
                    ->with('error', 'Data pengadaan tidak ditemukan');
            }
            
            $pengadaan = $pengadaan[0];
            
            // Ambil detail barang dengan status penerimaan
            $details = DB::select("
                SELECT 
                    dp.iddetail_pengadaan,
                    dp.idbarang,
                    b.nama AS nama_barang,
                    s.nama_satuan,
                    dp.jumlah AS jumlah_pesan,
                    (
                        SELECT COALESCE(SUM(dpe.jumlah_terima), 0)
                        FROM detail_penerimaan dpe
                        JOIN penerimaan pe ON dpe.idpenerimaan = pe.idpenerimaan
                        WHERE pe.idpengadaan = ? AND dpe.idbarang = dp.idbarang
                    ) AS jumlah_diterima,
                    dp.jumlah - (
                        SELECT COALESCE(SUM(dpe.jumlah_terima), 0)
                        FROM detail_penerimaan dpe
                        JOIN penerimaan pe ON dpe.idpenerimaan = pe.idpenerimaan
                        WHERE pe.idpengadaan = ? AND dpe.idbarang = dp.idbarang
                    ) AS jumlah_sisa,
                    dp.harga_satuan
                FROM detail_pengadaan dp
                JOIN barang b ON dp.idbarang = b.idbarang
                JOIN satuan s ON b.idsatuan = s.idsatuan
                WHERE dp.idpengadaan = ?
            ", [$idpengadaan, $idpengadaan, $idpengadaan]);
            
            return view('superadmin.penerimaan.create', compact('pengadaan', 'details'));
            
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
            'idpengadaan' => 'required|exists:pengadaan,idpengadaan',
            'status' => 'required|in:0,1',
            'details' => 'required|array|min:1',
            'details.*.idbarang' => 'required|exists:barang,idbarang',
            'details.*.jumlah_terima' => 'required|integer|min:0',
            'details.*.harga_satuan' => 'required|integer|min:0'
        ], [
            'idpengadaan.required' => 'ID Pengadaan harus diisi',
            'status.required' => 'Status penerimaan harus dipilih',
            'details.required' => 'Detail barang harus diisi',
            'details.min' => 'Minimal 1 barang harus ada'
        ]);

        try {
            DB::beginTransaction();
            
            $iduser = Auth::id();
            $idpengadaan = $request->idpengadaan;
            $status = $request->status;
            
            // Cek apakah ada barang yang diterima dengan jumlah > 0
            $adaBarangDiterima = false;
            foreach ($request->details as $detail) {
                if ((int) $detail['jumlah_terima'] > 0) {
                    $adaBarangDiterima = true;
                    break;
                }
            }
            
            // Jika tidak ada barang yang diterima, kembalikan dengan error
            if (!$adaBarangDiterima) {
                return back()->withInput()
                    ->with('error', 'Minimal 1 barang harus diterima dengan jumlah > 0');
            }
            
            // Insert penerimaan
            DB::statement("
                INSERT INTO penerimaan (idpengadaan, iduser, status, created_at)
                VALUES (?, ?, ?, NOW())
            ", [$idpengadaan, $iduser, $status]);
            
            // Ambil ID penerimaan yang baru dibuat
            $idpenerimaan = DB::getPdo()->lastInsertId();
            
            // Insert detail penerimaan menggunakan Stored Procedure
            foreach ($request->details as $detail) {
                $jumlahTerima = (int) $detail['jumlah_terima'];
                
                // Hanya insert jika jumlah terima > 0
                if ($jumlahTerima > 0) {
                    DB::statement("CALL sp_insert_detail_penerimaan(?, ?, ?, ?)", [
                        $idpenerimaan,
                        $detail['idbarang'],
                        $jumlahTerima,
                        $detail['harga_satuan']
                    ]);
                    
                    // Update kartu stok (stok masuk)
                    // Hitung stok sebelum dan sesudah
                    $stokSebelum = DB::selectOne("SELECT fn_cek_stok(?) AS stok", [$detail['idbarang']])->stok;
                    $stokSekarang = $stokSebelum + $jumlahTerima;
                    
                    DB::statement("
                        INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang, created_at)
                        VALUES ('P', ?, 0, ?, ?, ?, NOW())
                    ", [$jumlahTerima, $stokSekarang, $idpenerimaan, $detail['idbarang']]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('superadmin.penerimaan.show', $idpenerimaan)
                ->with('success', 'Penerimaan barang berhasil diproses');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Ambil data penerimaan
            $penerimaan = DB::select("
                SELECT 
                    p.*,
                    u.username,
                    pg.idpengadaan,
                    v.nama_vendor
                FROM penerimaan p
                JOIN user u ON p.iduser = u.iduser
                JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
                JOIN vendor v ON pg.idvendor = v.idvendor
                WHERE p.idpenerimaan = ?
            ", [$id]);
            
            if (empty($penerimaan)) {
                return redirect()->route('superadmin.penerimaan.index')
                    ->with('error', 'Data penerimaan tidak ditemukan');
            }
            
            $penerimaan = $penerimaan[0];
            
            // Ambil detail barang yang diterima
            $details = DB::select("
                SELECT 
                    dp.iddetail_penerimaan,
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
                    dp.jumlah_terima,
                    dp.harga_satuan_terima,
                    dp.sub_total_terima
                FROM detail_penerimaan dp
                JOIN barang b ON dp.idbarang = b.idbarang
                JOIN satuan s ON b.idsatuan = s.idsatuan
                WHERE dp.idpenerimaan = ?
            ", [$id]);
            
            // Ambil kartu stok untuk penerimaan ini
            $kartuStok = DB::select("
                SELECT 
                    ks.*,
                    b.nama AS nama_barang
                FROM kartu_stok ks
                JOIN barang b ON ks.idbarang = b.idbarang
                WHERE ks.jenis_transaksi = 'P' AND ks.idtransaksi = ?
                ORDER BY ks.created_at DESC
            ", [$id]);
            
            // Ambil status pengadaan
            $statusPengadaan = DB::selectOne("
                SELECT fn_status_pengadaan(?) AS status
            ", [$penerimaan->idpengadaan])->status;
            
            // Ambil retur (jika ada)
            $retur = DB::select("
                SELECT 
                    r.*,
                    dr.jumlah,
                    dr.alasan,
                    b.nama AS nama_barang
                FROM retur r
                JOIN detail_retur dr ON r.idretur = dr.idretur
                JOIN detail_penerimaan dp ON dr.iddetail_penerimaan = dp.iddetail_penerimaan
                JOIN barang b ON dp.idbarang = b.idbarang
                WHERE r.idpenerimaan = ?
            ", [$id]);
            
            return view('superadmin.penerimaan.show', compact('penerimaan', 'details', 'kartuStok', 'statusPengadaan', 'retur'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
}