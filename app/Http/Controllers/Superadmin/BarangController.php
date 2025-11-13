<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    // Menampilkan semua barang menggunakan VIEW
    public function index(Request $request)
    {
        $jenis = $request->get('jenis');
        $keyword = $request->get('search');

        // Gunakan view v_data_barang
        $query = DB::table('v_data_barang');

        // Filter by jenis menggunakan stored procedure
        if ($jenis) {
            $barang = DB::select('CALL sp_get_barang_by_jenis(?)', [$jenis]);
            return view('superadmin.barang.index', compact('barang'));
        }

        // Search by nama menggunakan stored procedure
        if ($keyword) {
            $barang = DB::select('CALL sp_search_barang_by_nama(?)', [$keyword]);
            return view('superadmin.barang.index', compact('barang'));
        }

        // Default: tampilkan semua
        $barang = $query->get();
        
        return view('superadmin.barang.index', compact('barang'));
    }

    // Form tambah barang
    public function create()
    {
        // Ambil satuan aktif dari view
        $satuan = DB::table('v_data_satuan_aktif')->get();
        
        $jenisBarang = [
            'S' => 'Sembako & Bahan Pokok',
            'M' => 'Minuman',
            'K' => 'Makanan Olahan & Snack',
            'P' => 'Personal Care',
            'H' => 'Household / Home Care',
            'D' => 'Dapur & Plastik'
        ];

        return view('superadmin.barang.create', compact('satuan', 'jenisBarang'));
    }

    // Simpan barang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:S,M,K,P,H,D',
            'nama' => 'required|string|max:45',
            'idsatuan' => 'required|exists:satuan,idsatuan',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ]);

        try {
            Barang::create($validated);
            return redirect()->route('superadmin.barang.index')
                ->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage());
        }
    }

    // Form edit barang
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $satuan = DB::table('v_data_satuan_aktif')->get();
        
        $jenisBarang = [
            'S' => 'Sembako & Bahan Pokok',
            'M' => 'Minuman',
            'K' => 'Makanan Olahan & Snack',
            'P' => 'Personal Care',
            'H' => 'Household / Home Care',
            'D' => 'Dapur & Plastik'
        ];

        return view('superadmin.barang.edit', compact('barang', 'satuan', 'jenisBarang'));
    }

    // Update barang
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:S,M,K,P,H,D',
            'nama' => 'required|string|max:45',
            'idsatuan' => 'required|exists:satuan,idsatuan',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ]);

        try {
            $barang = Barang::findOrFail($id);
            $barang->update($validated);
            
            return redirect()->route('superadmin.barang.index')
                ->with('success', 'Barang berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update barang: ' . $e->getMessage());
        }
    }

    // Toggle status (soft delete)
    public function toggleStatus($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            $barang->status = $barang->status == 1 ? 0 : 1;
            $barang->save();

            return back()->with('success', 'Status barang berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    // API: Get barang by satuan (untuk dropdown/autocomplete)
    public function getBarangBySatuan($satuan)
    {
        $barang = DB::select('CALL sp_get_barang_by_satuan(?)', [$satuan]);
        return response()->json($barang);
    }

    // API: Get barang aktif (untuk form pengadaan/penjualan)
    public function getBarangAktif()
    {
        $barang = DB::table('v_data_barang_aktif')->get();
        return response()->json($barang);
    }
}