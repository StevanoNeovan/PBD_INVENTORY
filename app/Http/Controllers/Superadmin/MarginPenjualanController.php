<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MarginPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Menggunakan view yang sudah ada di database
            $margins = DB::select("SELECT * FROM v_data_margin_penjualan ORDER BY created_at DESC");
            
            return view('superadmin.margin-penjualan.index', compact('margins'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.margin-penjualan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persen' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:0,1'
        ], [
            'persen.required' => 'Persentase margin harus diisi',
            'persen.numeric' => 'Persentase harus berupa angka',
            'persen.min' => 'Persentase minimal 0',
            'persen.max' => 'Persentase maksimal 100',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid'
        ]);

        try {
            DB::beginTransaction();
            
            $iduser = Auth::id();
            
            // Logika: Jika status aktif dipilih, nonaktifkan margin lain yang aktif
            if ($request->status == 1) {
                DB::statement("UPDATE margin_penjualan SET status = 0 WHERE status = 1");
            }
            
            // Insert margin baru secara manual (tanpa SP)
            DB::statement("
                INSERT INTO margin_penjualan (persen, status, iduser, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
            ", [
                $request->persen,
                $request->status,
                $iduser
            ]);

            DB::commit();
            
            return redirect()->route('superadmin.margin-penjualan.index')
                ->with('success', 'Margin penjualan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menambahkan margin: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Mengambil detail margin dari view
            $margins = DB::select("SELECT * FROM v_data_margin_penjualan WHERE idmargin_penjualan = ?", [$id]);
            
            if (empty($margins)) {
                return redirect()->route('superadmin.margin-penjualan.index')
                    ->with('error', 'Data margin tidak ditemukan');
            }
            
            $margin = $margins[0];

            // Mengambil riwayat penjualan yang menggunakan margin ini
            $riwayatPenjualan = DB::select("
                SELECT p.idpenjualan, p.created_at, p.total_nilai, u.username
                FROM penjualan p
                INNER JOIN user u ON p.iduser = u.iduser
                WHERE p.idmargin_penjualan = ?
                ORDER BY p.created_at DESC
                LIMIT 50
            ", [$id]);

            return view('superadmin.margin-penjualan.show', compact('margin', 'riwayatPenjualan'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // Mengambil data dari tabel langsung (bukan view) untuk editing
            $margins = DB::select("SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?", [$id]);
            
            if (empty($margins)) {
                return redirect()->route('superadmin.margin-penjualan.index')
                    ->with('error', 'Data margin tidak ditemukan');
            }
            
            $margin = $margins[0];

            return view('superadmin.margin-penjualan.edit', compact('margin'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'persen' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:0,1'
        ], [
            'persen.required' => 'Persentase margin harus diisi',
            'persen.numeric' => 'Persentase harus berupa angka',
            'persen.min' => 'Persentase minimal 0',
            'persen.max' => 'Persentase maksimal 100',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid'
        ]);

        try {
            DB::beginTransaction();
            
            // Logika: Jika status aktif dipilih, nonaktifkan margin lain yang aktif
            if ($request->status == 1) {
                DB::statement("
                    UPDATE margin_penjualan 
                    SET status = 0 
                    WHERE status = 1 AND idmargin_penjualan != ?
                ", [$id]);
            }
            
            // Update margin secara manual (tanpa SP)
            DB::statement("
                UPDATE margin_penjualan 
                SET persen = ?, status = ?, updated_at = NOW()
                WHERE idmargin_penjualan = ?
            ", [
                $request->persen,
                $request->status,
                $id
            ]);

            DB::commit();

            return redirect()->route('superadmin.margin-penjualan.index')
                ->with('success', 'Margin penjualan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui margin: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status margin (aktif/non-aktif)
     */
    public function toggleStatus(string $id)
    {
        try {
            DB::beginTransaction();
            
            // Ambil status saat ini
            $currentMargin = DB::select("SELECT status FROM margin_penjualan WHERE idmargin_penjualan = ?", [$id]);
            
            if (empty($currentMargin)) {
                return back()->with('error', 'Data margin tidak ditemukan');
            }
            
            $currentStatus = $currentMargin[0]->status;
            $newStatus = $currentStatus == 1 ? 0 : 1;
            
            // Jika akan diaktifkan, nonaktifkan margin lain
            if ($newStatus == 1) {
                DB::statement("UPDATE margin_penjualan SET status = 0 WHERE status = 1");
            }
            
            // Toggle status
            DB::statement("
                UPDATE margin_penjualan 
                SET status = ?, updated_at = NOW()
                WHERE idmargin_penjualan = ?
            ", [$newStatus, $id]);

            DB::commit();

            return redirect()->route('superadmin.margin-penjualan.index')
                ->with('success', 'Status margin berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}