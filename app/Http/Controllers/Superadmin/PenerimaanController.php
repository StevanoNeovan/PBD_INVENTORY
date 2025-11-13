<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Penerimaan;
use App\Models\DetailPenerimaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanController extends Controller
{
    // Tampilkan daftar penerimaan dari VIEW
    public function index(Request $request)
    {
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = DB::table('v_data_penerimaan');

        if ($status) {
            $query->where('status_penerimaan', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_penerimaan', [$startDate, $endDate]);
        }

        $penerimaan = $query->orderBy('idpenerimaan', 'desc')->paginate(15);

        return view('superadmin.penerimaan.index', compact('penerimaan'));
    }

    // Form penerimaan barang
    public function create()
    {
        // Ambil pengadaan yang belum ada penerimaannya atau masih pending
        $pengadaan = DB::table('pengadaan as p')
            ->leftJoin('penerimaan as pe', 'p.idpengadaan', '=', 'pe.idpengadaan')
            ->join('vendor as v', 'p.idvendor', '=', 'v.idvendor')
            ->whereNull('pe.idpenerimaan')
            ->orWhere('pe.status', '0')
            ->select('p.idpengadaan', 'v.nama_vendor', 'p.timestamp', 'p.total_nilai')
            ->get();

        return view('superadmin.penerimaan.create', compact('pengadaan'));
    }

    // Get detail pengadaan untuk form penerimaan (AJAX)
    public function getDetailPengadaan($idpengadaan)
    {
        $detail = DB::table('v_data_pengadaan')
            ->where('idpengadaan', $idpengadaan)
            ->get();

        return response()->json($detail);
    }

    // Simpan penerimaan (menggunakan stored procedure)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpengadaan' => 'required|exists:pengadaan,idpengadaan',
            'detail' => 'required|array|min:1',
            'detail.*.idbarang' => 'required|exists:barang,idbarang',
            'detail.*.jumlah_terima' => 'required|integer|min:1',
            'detail.*.harga_satuan_terima' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $iduser = Auth::id();
            $idpengadaan = $request->idpengadaan;

            // GUNAKAN STORED PROCEDURE sp_insert_penerimaan
            // Procedure ini otomatis update kartu_stok
            DB::statement('CALL sp_insert_penerimaan(?, ?)', [$idpengadaan, $iduser]);

            // Ambil ID penerimaan yang baru dibuat
            $idpenerimaan = DB::table('penerimaan')
                ->where('idpengadaan', $idpengadaan)
                ->where('iduser', $iduser)
                ->latest('idpenerimaan')
                ->value('idpenerimaan');

            // Insert detail penerimaan
            foreach ($request->detail as $item) {
                $subtotal = $item['jumlah_terima'] * $item['harga_satuan_terima'];
                
                DetailPenerimaan::create([
                    'idpenerimaan' => $idpenerimaan,
                    'idbarang' => $item['idbarang'],
                    'jumlah_terima' => $item['jumlah_terima'],
                    'harga_satuan_terima' => $item['harga_satuan_terima'],
                    'sub_total_terima' => $subtotal
                ]);
            }

            DB::commit();
            return redirect()->route('superadmin.penerimaan.index')
                ->with('success', 'Penerimaan berhasil. Stok otomatis bertambah!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal proses penerimaan: ' . $e->getMessage());
        }
    }

    // Detail penerimaan
    public function show($id)
    {
        $penerimaan = DB::table('v_data_penerimaan')
            ->where('idpenerimaan', $id)
            ->get();

        if ($penerimaan->isEmpty()) {
            abort(404, 'Penerimaan tidak ditemukan');
        }

        $header = $penerimaan->first();

        return view('superadmin.penerimaan.detail', compact('penerimaan', 'header'));
    }

    // Update status penerimaan
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1'
        ]);

        try {
            $penerimaan = Penerimaan::findOrFail($id);
            $penerimaan->status = $validated['status'];
            $penerimaan->save();

            $message = $validated['status'] == '1' 
                ? 'Penerimaan disetujui' 
                : 'Penerimaan ditolak';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }
}