<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanController extends Controller
{
    /**
     * Display list of satuan
     */
    public function index(Request $request)
    {
    
         try {
            $filter = $request->get('filter', 'all'); // all, aktif, nonaktif
            
            if ($filter === 'aktif') {
                $satuans = DB::select("SELECT * FROM v_data_satuan_aktif");
            } elseif ($filter === 'nonaktif') {
                $satuans = DB::select("SELECT * FROM v_data_satuan_nonaktif");
            } else {
                $satuans = DB::select("SELECT * FROM v_data_satuan");
            }
            
            return view('superadmin.satuan.index', compact('satuans', 'filter'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data satuan: ' . $e->getMessage());
        }
    }


    public function create()
    {
        return view('superadmin.satuan.create');
    }
    /**
     * Store new satuan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45|unique:satuan,nama_satuan',
            'status' => 'required|in:0,1'
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi',
            'nama_satuan.unique' => 'Nama satuan sudah ada',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            Satuan::create($validated);
            
            return redirect()->route('superadmin.satuan.index')
                ->with('success', 'Satuan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan satuan: ' . $e->getMessage());
        }

        return view('superadmin.satuan.create', compact('satuan'));
    }

   public function show(string $id)
{
    try {
            $satuans = DB::select("
                SELECT * FROM v_data_satuan WHERE idsatuan = ?
            ", [$id]);

            if (empty($satuans)) {
                return redirect()->route('superadmin.satuan.index')
                               ->with('error', 'Satuan tidak ditemukan');
            }

        // Ambil barang yang menggunakan satuan ini
        $barangs = DB::select("
            SELECT 
                b.idbarang,
                b.nama,
                b.jenis,
                b.harga,
                CASE 
                WHEN STATUS = 1 THEN 'Aktif'
                ELSE 'Tidak Aktif'
                END AS status
            FROM barang b
            WHERE b.idsatuan = ?
            ORDER BY b.nama ASC
        ", [$id]);

        return view('superadmin.satuan.show', [
            'satuans' => $satuans[0],
            'barangs' => $barangs
        ]);

    } catch (\Exception $e) {
        return redirect()
            ->route('superadmin.satuan.index')
            ->with('error', 'Gagal memuat data: ' . $e->getMessage());
    }
}


    /**
     * Show edit form
     */
    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('superadmin.satuan.edit', compact('satuan'));
    }

    /**
     * Update satuan
     */
    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45|unique:satuan,nama_satuan,' . $id . ',idsatuan',
            'status' => 'required|in:0,1'
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi',
            'nama_satuan.unique' => 'Nama satuan sudah ada',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            $satuan->update($validated);
            
            return redirect()->route('superadmin.satuan.index')
                ->with('success', 'Satuan berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal update satuan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status (soft delete alternative)
     */
    public function toggleStatus($id)
    {
        try {
            $satuan = DB::select("SELECT status FROM satuan WHERE idsatuan = ?", [$id]);
            
            if (empty($satuan)) {
                return redirect()->back()->with('error', 'satuan tidak ditemukan');
            }

            $newStatus = $satuan[0]->status == 1 ? 0 : 1;
            
            DB::statement("UPDATE satuan SET status = ? WHERE idsatuan = ?", [$newStatus, $id]);

            $message = $newStatus == 1 ? 'Satuan berhasil diaktifkan!' : 'Satuan berhasil dinonaktifkan!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Delete satuan (hard delete)
     */
    public function destroy(string $id)
    {
        try {
            // Cek apakah satuan digunakan
            $isUsed = DB::select("
                SELECT COUNT(*) as total FROM barang WHERE idsatuan = ?
            ", [$id]);

            if ($isUsed[0]->total > 0) {
                return redirect()->back()
                               ->with('warning', 'Satuan tidak dapat dihapus karena sudah digunakan di pengadaan!');
            }

            // Soft delete dengan update status menjadi nonaktif
            DB::statement("UPDATE satuan SET status = 0 WHERE idsatuan = ?", [$id]);

            return redirect()->route('superadmin.satuan.index')
                           ->with('success', 'Satuan berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menonaktifkan satuan: ' . $e->getMessage());
        }
    }
}