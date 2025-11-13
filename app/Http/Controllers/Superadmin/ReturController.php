<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturController extends Controller
{
    // Daftar retur
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Join dengan penerimaan dan vendor
        $query = DB::table('retur as r')
            ->join('penerimaan as pen', 'r.idpenerimaan', '=', 'pen.idpenerimaan')
            ->join('pengadaan as pg', 'pen.idpengadaan', '=', 'pg.idpengadaan')
            ->join('vendor as v', 'pg.idvendor', '=', 'v.idvendor')
            ->join('user as u', 'r.iduser', '=', 'u.iduser')
            ->select(
                'r.idretur',
                'r.created_at as tanggal_retur',
                'v.nama_vendor',
                'u.username as petugas',
                'pen.idpenerimaan'
            );

        if ($startDate && $endDate) {
            $query->whereBetween('r.created_at', [$startDate, $endDate]);
        }

        $retur = $query->orderBy('r.idretur', 'desc')->paginate(15);

        return view('superadmin.retur.index', compact('retur'));
    }

    // Form buat retur
    public function create()
    {
        // Ambil penerimaan yang statusnya diterima (status = 1)
        $penerimaan = DB::table('penerimaan as p')
            ->join('pengadaan as pg', 'p.idpengadaan', '=', 'pg.idpengadaan')
            ->join('vendor as v', 'pg.idvendor', '=', 'v.idvendor')
            ->where('p.status', '1')
            ->select(
                'p.idpenerimaan',
                'p.created_at',
                'v.nama_vendor',
                'pg.idpengadaan'
            )
            ->get();

        return view('superadmin.retur.create', compact('penerimaan'));
    }

    // Get detail penerimaan untuk retur (AJAX)
    public function getDetailPenerimaan($idpenerimaan)
    {
        $detail = DB::table('detail_penerimaan as dp')
            ->join('barang as b', 'dp.idbarang', '=', 'b.idbarang')
            ->join('satuan as s', 'b.idsatuan', '=', 's.idsatuan')
            ->where('dp.idpenerimaan', $idpenerimaan)
            ->select(
                'dp.iddetail_penerimaan',
                'dp.idbarang',
                'b.nama as nama_barang',
                's.nama_satuan',
                'dp.jumlah_terima',
                'dp.harga_satuan_terima'
            )
            ->get();

        return response()->json($detail);
    }

    // Proses retur (menggunakan stored procedure)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpenerimaan' => 'required|exists:penerimaan,idpenerimaan',
            'idbarang' => 'required|exists:barang,idbarang',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'required|string|max:200'
        ]);

        DB::beginTransaction();
        try {
            $iduser = Auth::id();

            // Validasi: jumlah retur tidak boleh melebihi jumlah terima
            $detailPenerimaan = DB::table('detail_penerimaan')
                ->where('idpenerimaan', $validated['idpenerimaan'])
                ->where('idbarang', $validated['idbarang'])
                ->first();

            if (!$detailPenerimaan) {
                return back()->with('error', 'Barang tidak ditemukan dalam penerimaan ini');
            }

            if ($validated['jumlah'] > $detailPenerimaan->jumlah_terima) {
                return back()->with('error', 
                    "Jumlah retur melebihi jumlah terima. Maksimal: {$detailPenerimaan->jumlah_terima}");
            }

            // GUNAKAN STORED PROCEDURE sp_retur_barang
            // Procedure ini akan:
            // 1. Insert ke tabel retur
            // 2. Insert ke detail_retur
            // 3. Update kartu_stok (stok keluar)
            
            DB::statement('CALL sp_retur_barang(?, ?, ?, ?, ?)', [
                $validated['idpenerimaan'],
                $iduser,
                $validated['idbarang'],
                $validated['jumlah'],
                $validated['alasan']
            ]);

            DB::commit();

            return redirect()->route('superadmin.retur.index')
                ->with('success', 'Retur berhasil diproses. Stok otomatis dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal proses retur: ' . $e->getMessage());
        }
    }

    // Detail retur
    public function show($id)
    {
        // Header retur
        $retur = DB::table('retur as r')
            ->join('penerimaan as pen', 'r.idpenerimaan', '=', 'pen.idpenerimaan')
            ->join('pengadaan as pg', 'pen.idpengadaan', '=', 'pg.idpengadaan')
            ->join('vendor as v', 'pg.idvendor', '=', 'v.idvendor')
            ->join('user as u', 'r.iduser', '=', 'u.iduser')
            ->where('r.idretur', $id)
            ->select(
                'r.*',
                'v.nama_vendor',
                'u.username as petugas',
                'pen.idpenerimaan',
                'pg.idpengadaan'
            )
            ->first();

        if (!$retur) {
            abort(404, 'Data retur tidak ditemukan');
        }

        // Detail retur
        $detail = DB::table('detail_retur as dr')
            ->join('detail_penerimaan as dp', 'dr.iddetail_penerimaan', '=', 'dp.iddetail_penerimaan')
            ->join('barang as b', 'dp.idbarang', '=', 'b.idbarang')
            ->join('satuan as s', 'b.idsatuan', '=', 's.idsatuan')
            ->where('dr.idretur', $id)
            ->select(
                'dr.*',
                'b.nama as nama_barang',
                's.nama_satuan',
                'dp.harga_satuan_terima'
            )
            ->get();

        return view('superadmin.retur.detail', compact('retur', 'detail'));
    }
}