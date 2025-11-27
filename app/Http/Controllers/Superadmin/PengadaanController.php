<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengadaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Mengambil data pengadaan dengan status dari view
            $pengadaans = DB::select(" SELECT * FROM v_data_pengadaan");
            
            return view('superadmin.pengadaan.index', compact('pengadaans'));
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
            // Ambil vendor aktif
            $vendors = DB::select("SELECT * FROM v_data_vendor_aktif ORDER BY nama_vendor ASC");
            
            // Ambil barang aktif
            $barangs = DB::select("SELECT * FROM v_data_barang_aktif ORDER BY nama_barang ASC");
            
            return view('superadmin.pengadaan.create', compact('vendors', 'barangs'));
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
            'idvendor' => 'required|exists:vendor,idvendor',
            'details' => 'required|array|min:1',
            'details.*.idbarang' => 'required|exists:barang,idbarang',
            'details.*.jumlah' => 'required|integer|min:1'
        ], [
            'idvendor.required' => 'Vendor harus dipilih',
            'details.required' => 'Minimal 1 barang harus ditambahkan',
            'details.min' => 'Minimal 1 barang harus ditambahkan'
        ]);

        try {
            DB::beginTransaction();
            
            $iduser = Auth::id();
            $idvendor = $request->idvendor;
            
            // Insert pengadaan (subtotal, ppn, total akan diupdate otomatis oleh trigger)
            DB::statement("
                INSERT INTO pengadaan (iduser, idvendor, subtotal_nilai, ppn, total_nilai, timestamp)
                VALUES (?, ?, 0, 0, 0, NOW())
            ", [$iduser, $idvendor]);
            
            // Ambil ID pengadaan yang baru dibuat
            $idpengadaan = DB::getPdo()->lastInsertId();
            
            // Insert detail pengadaan menggunakan Stored Procedure
            foreach ($request->details as $detail) {
                DB::statement("CALL sp_insert_detail_pengadaan(?, ?, ?)", [
                    $idpengadaan,
                    $detail['idbarang'],
                    $detail['jumlah']
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('superadmin.pengadaan.show', $idpengadaan)
                ->with('success', 'Pengadaan berhasil dibuat');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat pengadaan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
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
            ", [$id]);
            
            if (empty($pengadaan)) {
                return redirect()->route('superadmin.pengadaan.index')
                    ->with('error', 'Data pengadaan tidak ditemukan');
            }
            
            $pengadaan = $pengadaan[0];
            
            // Ambil detail barang dengan status penerimaan
            $details = DB::select("
                SELECT 
                    dp.iddetail_pengadaan,
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
                    dp.jumlah AS jumlah_pesan,
                    COALESCE(SUM(dpe.jumlah_terima), 0) AS jumlah_diterima,
                    dp.jumlah - COALESCE(SUM(dpe.jumlah_terima), 0) AS jumlah_sisa,
                    dp.harga_satuan,
                    dp.sub_total
                FROM detail_pengadaan dp
                JOIN barang b ON dp.idbarang = b.idbarang
                LEFT JOIN penerimaan pe ON pe.idpengadaan = ?
                LEFT JOIN detail_penerimaan dpe ON dpe.idpenerimaan = pe.idpenerimaan 
                    AND dpe.idbarang = dp.idbarang
                WHERE dp.idpengadaan = ?
                GROUP BY dp.iddetail_pengadaan
            ", [$id, $id]);
            
            // Ambil riwayat penerimaan
            $riwayatPenerimaan = DB::select("
                SELECT p.*, u.username
                FROM penerimaan p
                JOIN user u ON p.iduser = u.iduser
                WHERE p.idpengadaan = ?
                ORDER BY p.created_at DESC
            ", [$id]);
            
            return view('superadmin.pengadaan.show', compact('pengadaan', 'details', 'riwayatPenerimaan'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
}