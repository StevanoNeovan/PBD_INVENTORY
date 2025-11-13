<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\TempDetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    // Halaman POS (Point of Sale)
    public function pos()
    {
        // Barang aktif dengan stok
        $barang = DB::table('v_data_barang_aktif as b')
            ->leftJoin('v_laporan_stok_barang as s', 'b.idbarang', '=', 's.idbarang')
            ->select('b.*', 's.saldo_akhir as stok')
            ->where('s.saldo_akhir', '>', 0)
            ->get();

        // Margin penjualan aktif
        $margin = DB::table('v_data_margin_penjualan_aktif')->get();

        // Cart items (temp table)
        $cart = TempDetailPenjualan::with('barang', 'marginPenjualan')->get();

        // Hitung total cart
        $subtotal = $cart->sum('subtotal');
        $ppn = $subtotal * 0.11;
        $grandTotal = $subtotal + $ppn;

        return view('superadmin.penjualan.pos', compact(
            'barang', 
            'margin', 
            'cart', 
            'subtotal', 
            'ppn', 
            'grandTotal'
        ));
    }

    // Tambah item ke cart (AJAX)
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'idbarang' => 'required|exists:barang,idbarang',
            'jumlah' => 'required|integer|min:1',
            'idmargin_penjualan' => 'required|exists:margin_penjualan,idmargin_penjualan'
        ]);

        try {
            // Get harga beli barang
            $barang = DB::table('barang')->find($validated['idbarang']);
            $hargaBeli = $barang->harga;

            // Cek stok available
            $stok = DB::table('v_laporan_stok_barang')
                ->where('idbarang', $validated['idbarang'])
                ->value('saldo_akhir');

            if ($stok < $validated['jumlah']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup! Stok tersedia: ' . $stok
                ], 400);
            }

            // Insert ke temp table
            // TRIGGER akan otomatis hitung harga_jual dan subtotal
            TempDetailPenjualan::create([
                'idbarang' => $validated['idbarang'],
                'jumlah' => $validated['jumlah'],
                'harga_beli' => $hargaBeli,
                'idmargin_penjualan' => $validated['idmargin_penjualan']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah item: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update quantity item di cart (AJAX)
    public function updateCartItem(Request $request, $id)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        try {
            $item = TempDetailPenjualan::findOrFail($id);
            
            // Cek stok
            $stok = DB::table('v_laporan_stok_barang')
                ->where('idbarang', $item->idbarang)
                ->value('saldo_akhir');

            if ($stok < $validated['jumlah']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup!'
                ], 400);
            }

            $item->jumlah = $validated['jumlah'];
            $item->save();
            // Trigger akan re-calculate subtotal

            return response()->json([
                'success' => true,
                'message' => 'Quantity diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus item dari cart (AJAX)
    public function removeFromCart($id)
    {
        try {
            TempDetailPenjualan::findOrFail($id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Clear cart
    public function clearCart()
    {
        try {
            TempDetailPenjualan::truncate();

            return response()->json([
                'success' => true,
                'message' => 'Keranjang dikosongkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Proses checkout (menggunakan stored procedure)
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'idmargin_penjualan' => 'required|exists:margin_penjualan,idmargin_penjualan'
        ]);

        DB::beginTransaction();
        try {
            $iduser = Auth::id();
            $idMargin = $validated['idmargin_penjualan'];

            // Validasi cart tidak kosong
            $cartCount = TempDetailPenjualan::count();
            if ($cartCount == 0) {
                return back()->with('error', 'Keranjang belanja kosong!');
            }

            // GUNAKAN STORED PROCEDURE sp_insert_penjualan
            // Procedure ini akan:
            // 1. Hitung subtotal dari temp table
            // 2. Hitung PPN 11%
            // 3. Insert ke penjualan
            // 4. Pindahkan data dari temp ke detail_penjualan
            // 5. Trigger akan update kartu_stok (stok keluar)
            // 6. Clear temp table
            
            DB::statement('CALL sp_insert_penjualan(?, ?)', [$iduser, $idMargin]);

            // Get ID penjualan terakhir
            $idpenjualan = DB::table('penjualan')
                ->where('iduser', $iduser)
                ->latest('idpenjualan')
                ->value('idpenjualan');

            DB::commit();

            return redirect()->route('superadmin.penjualan.invoice', $idpenjualan)
                ->with('success', 'Transaksi berhasil! Stok otomatis berkurang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }

    // Invoice / Struk
    public function invoice($id)
    {
        $penjualan = DB::table('v_data_penjualan')
            ->where('idpenjualan', $id)
            ->get();

        if ($penjualan->isEmpty()) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $header = $penjualan->first();

        return view('superadmin.penjualan.invoice', compact('penjualan', 'header'));
    }

    // Laporan penjualan
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = DB::table('v_data_penjualan');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_penjualan', [$startDate, $endDate]);
        }

        $penjualan = $query->orderBy('idpenjualan', 'desc')->paginate(15);

        return view('superadmin.penjualan.index', compact('penjualan'));
    }

    // Laporan bulanan
    public function laporanBulanan()
    {
        $laporan = DB::table('v_laporan_penjualan_bulanan')->get();

        return view('superadmin.penjualan.laporan_bulanan', compact('laporan'));
    }
}