<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\VendorController;
use App\Http\Controllers\Superadmin\SatuanController;
use App\Http\Controllers\Superadmin\BarangController;
use App\Http\Controllers\Superadmin\MarginPenjualanController;



Auth::routes();

/*
|--------------------------------------------------------------------------
| Data Master Routes - Superadmin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'isSuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    Route::get('/dashboard', function () {
    return view('superadmin.dashboard');
    })->name('dashboard');


    // USER MANAGEMENT
    Route::resource('user', UserController::class);
    Route::get('user/filter/role', [UserController::class, 'filterByRole'])->name('user.filter.role');

    // VENDOR MANAGEMENT
    Route::resource('vendor', VendorController::class);
    Route::post('vendor/{id}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle-status');
    Route::get('vendor/filter/legalitas', [VendorController::class, 'filterByLegalitas'])->name('vendor.filter.legalitas');

    // SATUAN MANAGEMENT
    Route::resource('satuan', SatuanController::class);
    Route::post('satuan/{id}/toggle-status', [SatuanController::class, 'toggleStatus'])->name('satuan.toggle-status');

    // BARANG MANAGEMENT
    Route::resource('barang', BarangController::class);
    Route::post('barang/{id}/toggle-status', [BarangController::class, 'toggleStatus'])->name('barang.toggle-status');
    Route::get('barang/filter/satuan', [BarangController::class, 'filterBySatuan'])->name('barang.filter.satuan');

    // MARGIN PENJUALAN MANAGEMENT
    Route::resource('margin-penjualan', MarginPenjualanController::class);
    Route::post('margin-penjualan/{id}/toggle-status', [MarginPenjualanController::class, 'toggleStatus'])->name('margin-penjualan.toggle-status');
    Route::get('margin-penjualan/api/active', [MarginPenjualanController::class, 'getActiveMargin'])->name('margin-penjualan.active');
});