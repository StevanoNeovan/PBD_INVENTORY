<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturController extends Controller
{
    // Menampilkan semua data retur
    public function index()
    {
        $returs = DB::select("
            SELECT 
                r.idretur,
                r.created_at,
                p.idpenerimaan,
                u.username AS petugas,
                v.nama_vendor,
                COUNT(dr.iddetail_retur) AS jumlah_item
            FROM retur r
            JOIN penerimaan p ON r.idpenerimaan = p.idpenerimaan
            JOIN user u ON r.iduser = u.iduser
            JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
            JOIN vendor v ON pg.idvendor = v.idvendor
            LEFT JOIN detail_retur dr ON r.idretur = dr.idretur
            GROUP BY r.idretur
            ORDER BY r.created_at DESC
        ");

        return view('superadmin.retur.index', compact('returs'));
    }

    // Menampilkan detail retur
    public function show($id)
    {
        // Data header retur
        $retur = DB::selectOne("
            SELECT 
                r.idretur,
                r.created_at,
                p.idpenerimaan,
                u.username AS petugas,
                v.nama_vendor,
                pg.idpengadaan
            FROM retur r
            JOIN penerimaan p ON r.idpenerimaan = p.idpenerimaan
            JOIN user u ON r.iduser = u.iduser
            JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
            JOIN vendor v ON pg.idvendor = v.idvendor
            WHERE r.idretur = ?
        ", [$id]);

        if (!$retur) {
            return redirect()->route('superadmin.retur.index')
                ->with('error', 'Data retur tidak ditemukan');
        }

        // Detail barang yang diretur
        $details = DB::select("
            SELECT 
                dr.iddetail_retur,
                b.nama AS nama_barang,
                s.nama_satuan,
                dr.jumlah,
                dp.harga_satuan_terima,
                (dr.jumlah * dp.harga_satuan_terima) AS subtotal,
                dr.alasan
            FROM detail_retur dr
            JOIN detail_penerimaan dp ON dr.iddetail_penerimaan = dp.iddetail_penerimaan
            JOIN barang b ON dp.idbarang = b.idbarang
            JOIN satuan s ON b.idsatuan = s.idsatuan
            WHERE dr.idretur = ?
        ", [$id]);

        return view('superadmin.retur.show', compact('retur', 'details'));
    }

    // Form create retur (dari penerimaan)
    public function create($idpenerimaan)
    {
        // Cek penerimaan exist
        $penerimaan = DB::selectOne("
            SELECT 
                p.idpenerimaan,
                p.created_at,
                v.nama_vendor,
                pg.idpengadaan
            FROM penerimaan p
            JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
            JOIN vendor v ON pg.idvendor = v.idvendor
            WHERE p.idpenerimaan = ?
        ", [$idpenerimaan]);

        if (!$penerimaan) {
            return redirect()->route('superadmin.penerimaan.index')
                ->with('error', 'Data penerimaan tidak ditemukan');
        }

        // Cek sudah retur berapa kali
        $jumlah_retur = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM retur
            WHERE idpenerimaan = ?
        ", [$idpenerimaan])->total;

        if ($jumlah_retur >= 2) {
            return redirect()->route('superadmin.penerimaan.show', $idpenerimaan)
                ->with('error', 'Penerimaan ini sudah diretur 2x. Tidak bisa retur lagi.');
        }

        // Ambil barang yang sudah diterima (dengan stok tersedia)
        $barangs = DB::select("
            SELECT 
                dp.iddetail_penerimaan,
                b.idbarang,
                b.nama AS nama_barang,
                s.nama_satuan,
                dp.jumlah_terima,
                dp.harga_satuan_terima,
                fn_cek_stok(b.idbarang) AS stok_tersedia,
                COALESCE(SUM(dr.jumlah), 0) AS sudah_diretur
            FROM detail_penerimaan dp
            JOIN barang b ON dp.idbarang = b.idbarang
            JOIN satuan s ON b.idsatuan = s.idsatuan
            LEFT JOIN detail_retur dr ON dp.iddetail_penerimaan = dr.iddetail_penerimaan
            WHERE dp.idpenerimaan = ?
            GROUP BY dp.iddetail_penerimaan
            HAVING stok_tersedia > 0
        ", [$idpenerimaan]);

        return view('superadmin.retur.create', compact('penerimaan', 'barangs'));
    }

    // Proses retur
    public function store(Request $request, $idpenerimaan)
    {
        $request->validate([
            'barangs' => 'required|array|min:1',
            'barangs.*.iddetail_penerimaan' => 'required|integer',
            'barangs.*.jumlah' => 'required|integer|min:1',
            'barangs.*.alasan' => 'required|string|max:200',
        ], [
            'barangs.required' => 'Pilih minimal 1 barang untuk diretur',
            'barangs.*.jumlah.required' => 'Jumlah retur harus diisi',
            'barangs.*.jumlah.min' => 'Jumlah retur minimal 1',
            'barangs.*.alasan.required' => 'Alasan retur harus diisi',
            'barangs.*.alasan.max' => 'Alasan maksimal 200 karakter',
        ]);

        DB::beginTransaction();
        try {
            $iduser = Auth::user()->iduser;
            
            // Cek max 2x retur
            $jumlah_retur = DB::selectOne("
                SELECT COUNT(*) AS total
                FROM retur
                WHERE idpenerimaan = ?
            ", [$idpenerimaan])->total;

            if ($jumlah_retur >= 2) {
                throw new \Exception('Penerimaan ini sudah diretur 2x');
            }

            // Validasi setiap barang
            foreach ($request->barangs as $item) {
                $detail = DB::selectOne("
                    SELECT 
                        dp.idbarang,
                        b.nama AS nama_barang,
                        dp.jumlah_terima,
                        fn_cek_stok(dp.idbarang) AS stok_tersedia,
                        COALESCE(SUM(dr.jumlah), 0) AS sudah_diretur
                    FROM detail_penerimaan dp
                    JOIN barang b ON dp.idbarang = b.idbarang
                    LEFT JOIN detail_retur dr ON dp.iddetail_penerimaan = dr.iddetail_penerimaan
                    WHERE dp.iddetail_penerimaan = ?
                    GROUP BY dp.iddetail_penerimaan
                ", [$item['iddetail_penerimaan']]);

                if (!$detail) {
                    throw new \Exception('Detail penerimaan tidak valid');
                }

                // Validasi stok
                if ($item['jumlah'] > $detail->stok_tersedia) {
                    throw new \Exception(
                        "Barang {$detail->nama_barang}: Stok tersedia hanya {$detail->stok_tersedia}, tidak bisa retur {$item['jumlah']}"
                    );
                }

                // Validasi tidak melebihi yang diterima
                $max_retur = $detail->jumlah_terima - $detail->sudah_diretur;
                if ($item['jumlah'] > $max_retur) {
                    throw new \Exception(
                        "Barang {$detail->nama_barang}: Max retur {$max_retur} (diterima {$detail->jumlah_terima}, sudah retur {$detail->sudah_diretur})"
                    );
                }
            }

            // Proses retur
            foreach ($request->barangs as $item) {

               DB::statement("CALL sp_retur_barang(?, ?, ?, ?, ?)", [
                $idpenerimaan,
                $iduser,
                $item['iddetail_penerimaan'],
                $item['jumlah'],
                $item['alasan']
            ]);

            }

            DB::commit();
            return redirect()->route('superadmin.penerimaan.show', $idpenerimaan)
                ->with('success', 'Retur berhasil diproses');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal proses retur: ' . $e->getMessage());
        }
    }
}