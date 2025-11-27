<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filter = $request->get('filter', 'all'); // all, aktif, nonaktif
            
            if ($filter === 'aktif') {
                $vendors = DB::select("SELECT * FROM v_data_vendor_aktif");
            } elseif ($filter === 'nonaktif') {
                $vendors = DB::select("SELECT * FROM v_data_vendor_nonaktif");
            } else {
                $vendors = DB::select("SELECT * FROM v_data_vendor");
            }
            
            return view('superadmin.vendor.index', compact('vendors', 'filter'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data vendor: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.vendor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:Y,N',
            'status' => 'required|in:0,1'
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi',
            'badan_hukum.required' => 'Status badan hukum wajib dipilih',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            DB::statement("
                INSERT INTO vendor (nama_vendor, badan_hukum, status) 
                VALUES (?, ?, ?)
            ", [
                $request->nama_vendor,
                $request->badan_hukum,
                $request->status
            ]);

            return redirect()->route('superadmin.vendor.index')
                           ->with('success', 'Vendor berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan vendor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vendor = DB::select("
                SELECT * FROM v_data_vendor WHERE idvendor = ?
            ", [$id]);

            if (empty($vendor)) {
                return redirect()->route('superadmin.vendor.index')
                               ->with('error', 'Vendor tidak ditemukan');
            }

            // Ambil riwayat pengadaan dari vendor ini
            $riwayatPengadaan = DB::select("
                SELECT 
                    p.idpengadaan,
                    p.timestamp,
                    p.total_nilai,
                    u.username
                FROM pengadaan p
                JOIN user u ON p.iduser = u.iduser
                WHERE p.idvendor = ?
                ORDER BY p.timestamp DESC
                LIMIT 10
            ", [$id]);

            return view('superadmin.vendor.show', [
                'vendor' => $vendor[0],
                'riwayatPengadaan' => $riwayatPengadaan
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
            $vendor = DB::select("SELECT * FROM vendor WHERE idvendor = ?", [$id]);
            
            if (empty($vendor)) {
                return redirect()->route('superadmin.vendor.index')
                               ->with('error', 'Vendor tidak ditemukan');
            }
            
            return view('superadmin.vendor.edit', ['vendor' => $vendor[0]]);
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
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:Y,N',
            'status' => 'required|in:0,1'
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi',
            'badan_hukum.required' => 'Status badan hukum wajib dipilih',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            DB::statement("
                UPDATE vendor 
                SET nama_vendor = ?, badan_hukum = ?, status = ?
                WHERE idvendor = ?
            ", [
                $request->nama_vendor,
                $request->badan_hukum,
                $request->status,
                $id
            ]);

            return redirect()->route('superadmin.vendor.index')
                           ->with('success', 'Vendor berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Cek apakah vendor digunakan
            $isUsed = DB::select("
                SELECT COUNT(*) as total FROM pengadaan WHERE idvendor = ?
            ", [$id]);

            if ($isUsed[0]->total > 0) {
                return redirect()->back()
                               ->with('warning', 'Vendor tidak dapat dihapus karena sudah digunakan di pengadaan!');
            }

            // Soft delete dengan update status menjadi nonaktif
            DB::statement("UPDATE vendor SET status = 0 WHERE idvendor = ?", [$id]);

            return redirect()->route('superadmin.vendor.index')
                           ->with('success', 'Vendor berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menonaktifkan vendor: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status vendor
     */
    public function toggleStatus($id)
    {
        try {
            $vendor = DB::select("SELECT status FROM vendor WHERE idvendor = ?", [$id]);
            
            if (empty($vendor)) {
                return redirect()->back()->with('error', 'Vendor tidak ditemukan');
            }

            $newStatus = $vendor[0]->status == 1 ? 0 : 1;
            
            DB::statement("UPDATE vendor SET status = ? WHERE idvendor = ?", [$newStatus, $id]);

            $message = $newStatus == 1 ? 'Vendor berhasil diaktifkan!' : 'Vendor berhasil dinonaktifkan!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Filter vendor by legalitas using stored procedure
     */
    public function filterByLegalitas(Request $request)
    {
        try {
            $badan_hukum = $request->get('badan_hukum');
            
            if ($badan_hukum) {
                // Menggunakan stored procedure sp_get_vendor_by_legalitas
                $vendors = DB::select("CALL sp_get_vendor_by_legalitas(?)", [$badan_hukum]);
            } else {
                $vendors = DB::select("SELECT * FROM v_data_vendor");
            }

            $filter = 'all';
            return view('superadmin.vendor.index', compact('vendors', 'filter'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal filter data: ' . $e->getMessage());
        }
    }
}