<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\DashboardSuperadminController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\VendorController;
use App\Http\Controllers\Superadmin\SatuanController;
use App\Http\Controllers\Superadmin\BarangController;
use App\Http\Controllers\Superadmin\PengadaanController;
use App\Http\Controllers\Superadmin\PenerimaanController;
use App\Http\Controllers\Superadmin\ReturController;
use App\Http\Controllers\Superadmin\PenjualanController;
use App\Http\Controllers\Superadmin\MarginPenjualanController;
use App\Http\Controllers\Superadmin\KartuStokController;


Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'isSuperAdmin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardSuperadminController::class, 'index'])->name('dashboard');
    
    // Master Data
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    
    // Vendor
    Route::resource('vendor', VendorController::class);
    Route::post('vendor/{id}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle-status');
    
    // Satuan
    Route::resource('satuan', SatuanController::class);
    Route::post('satuan/{id}/toggle-status', [SatuanController::class, 'toggleStatus'])->name('satuan.toggle-status');
    Route::get('/satuan/create', [SatuanController::class, 'create'])->name('satuan.create');
    
    // Barang
    Route::resource('barang', BarangController::class);
    Route::post('barang/{id}/toggle-status', [BarangController::class, 'toggleStatus'])->name('barang.toggle-status');
    
    // API Barang (untuk AJAX/dropdown)
    Route::get('api/barang/aktif', [BarangController::class, 'getBarangAktif'])->name('api.barang.aktif');
    Route::get('api/barang/satuan/{satuan}', [BarangController::class, 'getBarangBySatuan'])->name('api.barang.satuan');
    
    // Pengadaan
    Route::resource('pengadaan', PengadaanController::class);
    Route::post('api/pengadaan/hitung-subtotal', [PengadaanController::class, 'hitungSubtotal'])->name('api.pengadaan.subtotal');
    Route::get('api/pengadaan/hitung-ppn/{subtotal}', [PengadaanController::class, 'hitungPPN'])->name('api.pengadaan.ppn');
    Route::get('api/pengadaan/total/{id}', [PengadaanController::class, 'getTotalPengadaan'])->name('api.pengadaan.total');
    
    // Penerimaan
    Route::resource('penerimaan', PenerimaanController::class);
    Route::get('api/penerimaan/detail-pengadaan/{id}', [PenerimaanController::class, 'getDetailPengadaan'])->name('api.penerimaan.detail');
    Route::post('penerimaan/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.update-status');
    
    // Retur
    Route::resource('retur', ReturController::class);
    Route::get('api/retur/detail-penerimaan/{id}', [ReturController::class, 'getDetailPenerimaan'])->name('api.retur.detail');
    
    // Penjualan (POS)
    Route::get('penjualan/pos', [PenjualanController::class, 'pos'])->name('penjualan.pos');
    Route::get('penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('penjualan/laporan-bulanan', [PenjualanController::class, 'laporanBulanan'])->name('penjualan.laporan-bulanan');
    Route::get('penjualan/invoice/{id}', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
    
    // Cart Management (AJAX)
    Route::post('penjualan/cart/add', [PenjualanController::class, 'addToCart'])->name('penjualan.cart.add');
    Route::put('penjualan/cart/{id}', [PenjualanController::class, 'updateCartItem'])->name('penjualan.cart.update');
    Route::delete('penjualan/cart/{id}', [PenjualanController::class, 'removeFromCart'])->name('penjualan.cart.remove');
    Route::delete('penjualan/cart', [PenjualanController::class, 'clearCart'])->name('penjualan.cart.clear');
    
    // Checkout
    Route::post('penjualan/checkout', [PenjualanController::class, 'checkout'])->name('penjualan.checkout');
    
    // Margin Penjualan
    Route::resource('margin', MarginPenjualanController::class);
    Route::post('margin/{id}/toggle-status', [MarginPenjualanController::class, 'toggleStatus'])->name('margin.toggle-status');
    
    // Kartu Stok & Laporan
    Route::get('kartu-stok', [KartuStokController::class, 'index'])->name('kartu-stok.index');
    Route::get('kartu-stok/{idbarang}', [KartuStokController::class, 'show'])->name('kartu-stok.show');
    Route::get('kartu-stok/{idbarang}/filter', [KartuStokController::class, 'filter'])->name('kartu-stok.filter');
    Route::get('kartu-stok/low-stock', [KartuStokController::class, 'lowStock'])->name('kartu-stok.low-stock');
    Route::get('kartu-stok/export', [KartuStokController::class, 'export'])->name('kartu-stok.export');
});