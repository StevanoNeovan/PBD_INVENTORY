<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengadaanController extends Controller
{
    // Tampilkan daftar pengadaan dari VIEW
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $vendor = $request->get('vendor');

        $query = DB::table('v_data_pengadaan');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pengadaan', [$startDate, $endDate]);
        }

        if ($vendor) {
            $query->where('nama_vendor', 'like', "%{$vendor}%");
        }

        $pengadaan = $query->orderBy('idpengadaan', 'desc')->paginate(15);

        // Untuk dropdown filter vendor
        $vendors = DB::table('v_data_vendor_aktif')->get();

        return view('superadmin.pengadaan.index', compact('pengadaan', 'vendors'));
    }

    // Form tambah pengadaan
    public function create()
    {
        $vendors = DB::table('v_data_vendor_aktif')->get();
        $barang = DB::table('v_data_barang_aktif')->get();

        return view('superadmin.pengadaan.create', compact('vendors', 'barang'));
    }

    // Simpan pengadaan (menggunakan stored procedure)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idvendor' => 'required|exists:vendor,idvendor',
            'detail' => 'required|array|min:1',
            'detail.*.idbarang' => 'required|exists:barang,idbarang',
            'detail.*.jumlah' => 'required|integer|min:1',
            'detail.*.harga_satuan' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $iduser = Auth::id();
            $idvendor = $request->idvendor;

            // Step 1: Insert pengadaan header (pakai procedure)
            $result = DB::select('CALL sp_insert_pengadaan(?, ?)', [$iduser, $idvendor]);
            
            // Ambil ID pengadaan yang baru dibuat
            $idpengadaan = DB::table('pengadaan')
                ->where('iduser', $iduser)
                ->where('idvendor', $idvendor)
                ->latest('idpengadaan')
                ->value('idpengadaan');

            // Step 2: Insert detail pengadaan
            foreach ($request->detail as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                
                DetailPengadaan::create([
                    'idpengadaan' => $idpengadaan,
                    'idbarang' => $item['idbarang'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'sub_total' => $subtotal
                ]);
                // Trigger akan otomatis update total di tabel pengadaan
            }

            DB::commit();
            return redirect()->route('superadmin.pengadaan.index')
                ->with('success', 'Pengadaan berhasil dibuat. Total akan dihitung otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pengadaan: ' . $e->getMessage());
        }
    }

    // Detail pengadaan (untuk cetak/view)
    public function show($id)
    {
        // Gunakan view untuk get detail lengkap
        $pengadaan = DB::table('v_data_pengadaan')
            ->where('idpengadaan', $id)
            ->get();

        if ($pengadaan->isEmpty()) {
            abort(404, 'Pengadaan tidak ditemukan');
        }

        // Group by untuk header info
        $header = $pengadaan->first();

        return view('superadmin.pengadaan.detail', compact('pengadaan', 'header'));
    }

    // API: Hitung subtotal (gunakan function MySQL)
    public function hitungSubtotal(Request $request)
    {
        $jumlah = $request->jumlah;
        $harga = $request->harga_satuan;

        $result = DB::select('SELECT fn_hitung_subtotal(?, ?) as subtotal', [$jumlah, $harga]);
        
        return response()->json([
            'subtotal' => $result[0]->subtotal
        ]);
    }

    // API: Hitung PPN (gunakan function MySQL)
    public function hitungPPN($subtotal)
    {
        $result = DB::select('SELECT fn_hitung_ppn(?) as ppn', [$subtotal]);
        
        return response()->json([
            'ppn' => $result[0]->ppn
        ]);
    }

    // API: Get total pengadaan (untuk preview sebelum submit)
    public function getTotalPengadaan($idpengadaan)
    {
        $result = DB::select('SELECT fn_total_pengadaan(?) as total', [$idpengadaan]);
        
        return response()->json([
            'total' => $result[0]->total
        ]);
    }
}