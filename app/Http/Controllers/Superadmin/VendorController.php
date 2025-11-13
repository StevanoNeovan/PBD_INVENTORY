<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display list of vendor
     */
    public function index(Request $request)
    {
        
        $vendor = Vendor::when($request->status == 'nonaktif', function ($query) {
                return $query->from('v_data_vendor_nonaktif');
            })
            ->when($request->status == 'aktif', function ($query) {
                return $query->from('v_data_vendor_aktif');
            })
            ->when(!in_array($request->status, ['aktif', 'nonaktif']), function ($query) {
                return $query->from('v_data_vendor');
            })
            ->orderBy('idvendor', 'desc')
            ->get();

        return view('superadmin.vendor.index', compact('vendor'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('superadmin.vendor.create');
    }

    /**
     * Store new vendor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:45|unique:vendor,nama_vendor',
            'badan_hukum' => 'required|string|in:Y,N',
            'status' => 'required|in:0,1'
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi',
            'nama_vendor.unique' => 'Nama vendor sudah ada',
            'badan_hukum.required' => 'Badan hukum wajib dipilih',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            Vendor::create($validated);
            
            return redirect()->route('superadmin.vendor.index')
                ->with('success', 'Vendor berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan vendor: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('superadmin.vendor.edit', compact('vendor'));
    }

    /**
     * Update vendor
     */
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:45|unique:vendor,nama_vendor,' . $id . ',idvendor',
            'status' => 'required|in:0,1'
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi',
            'nama_vendor.unique' => 'Nama vendor sudah ada',
            'badan_hukum.required' => 'Badan hukum wajib diisi',
            'status.required' => 'Status wajib dipilih'
        ]);

        try {
            $vendor->update($validated);
            
            return redirect()->route('superadmin.vendor.index')
                ->with('success', 'Vendor berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal update vendor: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status (soft delete alternative)
     */
    public function toggleStatus($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

              // Cek apakah vendor pernah digunakan
            $isUsed = DB::table('pengadaan')
                ->where('idvendor', $id)
                ->exists();
            
            if ($isUsed) {
                return back()->with('warning', 
                    'Tidak dapat menghapus vendor yang sudah digunakan. Gunakan fitur nonaktifkan.');
            }
            
            $vendor->status = $vendor->status == 1 ? 0 : 1;
            $vendor->save();
            
            $message = $vendor->status == 1 
                ? 'Vendor berhasil diaktifkan' 
                : 'Vendor berhasil dinonaktifkan';
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Delete vendor (hard delete)
     */
    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            
            // Cek apakah vendor pernah digunakan
            $isUsed = DB::table('pengadaan')
                ->where('idvendor', $id)
                ->exists();
            
            if ($isUsed) {
                return back()->with('warning', 
                    'Tidak dapat menghapus vendor yang sudah digunakan. Gunakan fitur nonaktifkan.');
            }
            
            $vendor->delete();
            
            return redirect()->route('superadmin.vendor.index')
                ->with('success', 'Vendor berhasil dihapus');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus vendor: ' . $e->getMessage());
        }
    }
}