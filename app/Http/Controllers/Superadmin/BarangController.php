<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    try {
        $filter  = $request->get('filter', 'all'); // all, aktif
        $jenis   = $request->get('jenis');        // S, M, K, P, H, D
        $search  = $request->get('search');
        $satuanFilter = $request->get('satuan');  // nama_satuan

        // Ambil data satuan untuk dropdown
        $dropdownSatuan = DB::select("SELECT * FROM v_data_satuan_aktif");

        // ===== PILIH SP / VIEW SESUAI FILTER =====

        if ($search) {
            $barangs = DB::select("CALL sp_search_barang_by_nama(?)", [$search]);

        } elseif ($jenis) {
            $barangs = DB::select("CALL sp_get_barang_by_jenis(?)", [$jenis]);

        } elseif ($satuanFilter) {
            $barangs = DB::select("CALL sp_get_barang_by_satuan(?)", [$satuanFilter]);

        } elseif ($filter === 'aktif') {
            $barangs = DB::select("SELECT * FROM v_data_barang_aktif");

        } else {
            $barangs = DB::select("SELECT * FROM v_data_barang");
        }

        $barangs = collect($barangs);

        return view('superadmin.barang.index', [
            'barangs' => $barangs,
            'satuans' => $dropdownSatuan,  
            'filter'  => $filter,
            'jenis'   => $jenis,
            'search'  => $search,
            'satuan'  => $satuanFilter      
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal memuat data barang: ' . $e->getMessage());
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Ambil satuan aktif
            $satuans = DB::select("SELECT * FROM v_data_satuan_aktif");
            
            return view('superadmin.barang.create', compact('satuans'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:45',
            'jenis' => 'required|in:S,M,K,P,H,D',
            'idsatuan' => 'required|integer|exists:satuan,idsatuan',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ], [
            'nama.required' => 'Nama barang wajib diisi',
            'jenis.required' => 'Jenis barang wajib dipilih',
            'idsatuan.required' => 'Satuan wajib dipilih',
            'harga.required' => 'Harga wajib diisi',
            'harga.min' => 'Harga tidak boleh negatif',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            DB::statement("
                INSERT INTO barang (nama, jenis, idsatuan, harga, status) 
                VALUES (?, ?, ?, ?, ?)
            ", [
                $request->nama,
                $request->jenis,
                $request->idsatuan,
                $request->harga,
                $request->status
            ]);

            return redirect()->route('superadmin.barang.index')
                           ->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan barang: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $barang = DB::select("
                SELECT * FROM v_data_barang WHERE idbarang = ?
            ", [$id]);

            if (empty($barang)) {
                return redirect()->route('superadmin.barang.index')
                               ->with('error', 'Barang tidak ditemukan');
            }

            // Ambil stok dari kartu_stok
            $stok = DB::select("
                SELECT 
                    SUM(masuk) as total_masuk,
                    SUM(keluar) as total_keluar,
                    (SUM(masuk) - SUM(keluar)) as saldo_akhir
                FROM kartu_stok
                WHERE idbarang = ?
            ", [$id]);

            // Ambil riwayat transaksi terakhir
            $riwayatStok = DB::select("
                SELECT 
                    jenis_transaksi,
                    masuk,
                    keluar,
                    stock,
                    created_at,
                    idtransaksi
                FROM kartu_stok
                WHERE idbarang = ?
                ORDER BY created_at DESC
                LIMIT 10
            ", [$id]);

            return view('superadmin.barang.show', [
                'barang' => $barang[0],
                'stok' => $stok[0] ?? (object)['total_masuk' => 0, 'total_keluar' => 0, 'saldo_akhir' => 0],
                'riwayatStok' => $riwayatStok
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $barang = DB::select("SELECT * FROM barang WHERE idbarang = ?", [$id]);
            
            if (empty($barang)) {
                return redirect()->route('superadmin.barang.index')
                               ->with('error', 'Barang tidak ditemukan');
            }

            $satuans = DB::select("SELECT * FROM v_data_satuan_aktif");
            
            return view('superadmin.barang.edit', [
                'barang' => $barang[0],
                'satuans' => $satuans
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:45',
            'jenis' => 'required|in:S,M,K,P,H,D',
            'idsatuan' => 'required|integer|exists:satuan,idsatuan',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ], [
            'nama.required' => 'Nama barang wajib diisi',
            'jenis.required' => 'Jenis barang wajib dipilih',
            'idsatuan.required' => 'Satuan wajib dipilih',
            'harga.required' => 'Harga wajib diisi',
            'harga.min' => 'Harga tidak boleh negatif',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            DB::statement("
                UPDATE barang 
                SET nama = ?, jenis = ?, idsatuan = ?, harga = ?, status = ?
                WHERE idbarang = ?
            ", [
                $request->nama,
                $request->jenis,
                $request->idsatuan,
                $request->harga,
                $request->status,
                $id
            ]);

            return redirect()->route('superadmin.barang.index')
                           ->with('success', 'Barang berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui barang: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Soft delete dengan update status menjadi nonaktif
            DB::statement("UPDATE barang SET status = 0 WHERE idbarang = ?", [$id]);

            return redirect()->route('superadmin.barang.index')
                           ->with('success', 'Barang berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menonaktifkan barang: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status barang
     */
    public function toggleStatus(string $id)
    {
        try {
            $barang = DB::select("SELECT status FROM barang WHERE idbarang = ?", [$id]);
            
            if (empty($barang)) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan');
            }

            $newStatus = $barang[0]->status == 1 ? 0 : 1;
            
            DB::statement("UPDATE barang SET status = ? WHERE idbarang = ?", [$newStatus, $id]);

            $message = $newStatus == 1 ? 'Barang berhasil diaktifkan!' : 'Barang berhasil dinonaktifkan!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Filter barang by satuan using stored procedure
     */
    public function filterBySatuan(Request $request)
    {
        try {
            $nama_satuan = $request->get('nama_satuan');
            
            if ($nama_satuan) {
                // Menggunakan stored procedure sp_get_barang_by_satuan
                $barangs = DB::select("CALL sp_get_barang_by_satuan(?)", [$nama_satuan]);
            } else {
                $barangs = DB::select("SELECT * FROM v_data_barang");
            }

            $filter = 'all';
            $jenis = null;
            $search = null;
            
            return view('superadmin.barang.index', compact('barangs', 'filter', 'jenis', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal filter data: ' . $e->getMessage());
        }
    }

    /**
     * API: Get barang aktif (untuk dropdown)
     */
    public function getBarangAktif()
    {
        try {
            $barangs = DB::select("SELECT * FROM v_data_barang_aktif");
            return response()->json(['success' => true, 'data' => $barangs]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get barang by satuan
     */
    public function getBarangBySatuan(string $satuan)
    {
        try {
            $barangs = DB::select("CALL sp_get_barang_by_satuan(?)", [$satuan]);
            return response()->json(['success' => true, 'data' => $barangs]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}