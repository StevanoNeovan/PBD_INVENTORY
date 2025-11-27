<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Superadmin\{
    DashboardSuperadminController,
    UserController,
    RoleController,
    VendorController,
    SatuanController,
    BarangController,
    MarginPenjualanController,
    PengadaanController,
    PenerimaanController,
    PenjualanController,
    KartuStokController,
    ReturController
};

/*
|--------------------------------------------------------------------------
| SUPERADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'isSuperAdmin'])
    ->group(function () {

    // ==========================
    // DASHBOARD
    // ==========================
    Route::get('/dashboard', [DashboardSuperadminController::class, 'index'])
        ->name('dashboard');

    // ==========================
    // USER MANAGEMENT
    // ==========================
    Route::resource('user', UserController::class);
    Route::get('user/filter/role', [UserController::class, 'filterByRole'])
        ->name('user.filter.role');

    // ==========================
    // ROLE MANAGEMENT
    // ==========================
    Route::resource('role', RoleController::class)->only(['index']);

    // ==========================
    // VENDOR MANAGEMENT
    // ==========================
    Route::resource('vendor', VendorController::class);
    Route::post('vendor/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])
        ->name('vendor.toggle-status');
    Route::get('vendor/filter/legalitas', [VendorController::class, 'filterByLegalitas'])
        ->name('vendor.filter.legalitas');

    // ==========================
    // SATUAN MANAGEMENT
    // ==========================
    Route::resource('satuan', SatuanController::class);
    Route::post('satuan/{id}/toggle-status', [SatuanController::class, 'toggleStatus'])
        ->name('satuan.toggle-status');

    // ==========================
    // BARANG MANAGEMENT
    // ==========================
    Route::resource('barang', BarangController::class);
    Route::post('barang/{id}/toggle-status', [BarangController::class, 'toggleStatus'])
        ->name('barang.toggle-status');

    // API BARANG
    Route::prefix('api/barang')->name('api.barang.')->group(function () {
        Route::get('aktif', [BarangController::class, 'getBarangAktif'])->name('aktif');
        Route::get('satuan/{satuan}', [BarangController::class, 'getBarangBySatuan'])->name('satuan');
    });

    // ==========================
    // MARGIN PENJUALAN MANAGEMENT
    // ==========================
    Route::resource('margin-penjualan', MarginPenjualanController::class);
    Route::post('margin-penjualan/{id}/toggle-status', [MarginPenjualanController::class, 'toggleStatus'])
        ->name('margin-penjualan.toggle-status');

    // ==========================
    // PENGADAAN MANAGEMENT
    // ==========================
    Route::resource('pengadaan', PengadaanController::class);
    
    // API Pengadaan
    Route::prefix('api/pengadaan')->name('api.pengadaan.')->group(function () {
        Route::post('hitung-subtotal', [PengadaanController::class, 'hitungSubtotal'])->name('subtotal');
        Route::get('hitung-ppn/{subtotal}', [PengadaanController::class, 'hitungPPN'])->name('ppn');
        Route::get('total/{id}', [PengadaanController::class, 'getTotalPengadaan'])->name('total');
    });

    // ==========================
    // PENERIMAAN MANAGEMENT
    // ==========================
    Route::resource('penerimaan', PenerimaanController::class);

    // ==========================
    // RETUR MANAGEMENT
    // ==========================
    Route::prefix('penerimaan/{idpenerimaan}/retur')
        ->name('retur.')
        ->group(function () {
            Route::get('create', [ReturController::class, 'create'])->name('create');
            Route::post('/', [ReturController::class, 'store'])->name('store');
        });

    Route::resource('retur', ReturController::class)->only(['index', 'show']);

    // ==========================
    // PENJUALAN MANAGEMENT (POS)
    // ==========================
    Route::resource('penjualan', PenjualanController::class);

    // ==========================
    // KARTU STOK & LAPORAN
    // ==========================
    Route::prefix('kartu-stok')->name('kartu-stok.')->group(function () {
        Route::get('/', [KartuStokController::class, 'index'])->name('index');
        Route::get('/monitoring', [KartuStokController::class, 'monitoring'])->name('monitoring');
        Route::get('/detail/{idbarang}', [KartuStokController::class, 'detail'])->name('detail');
    });
});
