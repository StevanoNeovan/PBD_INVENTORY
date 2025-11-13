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
    
        if ($request->status == 'nonaktif') {
            $satuan = DB::table('v_data_satuan_nonaktif')
                ->orderBy('idsatuan', 'desc')
                ->get();
        }elseif ($request->status == 'aktif') {
            $satuan = DB::table('v_data_satuan_aktif')
                ->orderBy('idsatuan', 'desc')
                ->get();
        }else {
            $satuan = DB::table('v_data_satuan')
                ->orderBy('idsatuan', 'desc')
                ->get();
        }

        return view('superadmin.satuan.index', compact('satuan'));
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
            $satuan = Satuan::findOrFail($id);
            
            // Cek apakah satuan sedang digunakan
            $isUsed = DB::table('barang')
                ->where('idsatuan', $id)
                ->where('status', 1)
                ->exists();
            
            if ($isUsed && $satuan->status == 1) {
                return back()->with('warning', 
                    'Tidak dapat menonaktifkan satuan yang masih digunakan oleh barang aktif');
            }
            
            $satuan->status = $satuan->status == 1 ? 0 : 1;
            $satuan->save();
            
            $message = $satuan->status == 1 
                ? 'Satuan berhasil diaktifkan' 
                : 'Satuan berhasil dinonaktifkan';
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Delete satuan (hard delete)
     */
    public function destroy($id)
    {
        try {
            $satuan = Satuan::findOrFail($id);
            
            // Cek apakah satuan pernah digunakan
            $isUsed = DB::table('barang')
                ->where('idsatuan', $id)
                ->exists();
            
            if ($isUsed) {
                return back()->with('warning', 
                    'Tidak dapat menghapus satuan yang sudah digunakan. Gunakan fitur nonaktifkan.');
            }
            
            $satuan->delete();
            
            return redirect()->route('superadmin.satuan.index')
                ->with('success', 'Satuan berhasil dihapus');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus satuan: ' . $e->getMessage());
        }
    }
}