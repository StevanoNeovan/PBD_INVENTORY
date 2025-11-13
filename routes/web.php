<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Superadmin\DashboardSuperadminController;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\SatuanController;


Route::get('/', function () {
    return redirect()->route('login');
});
require __DIR__.'/superadmin.php';

Auth::routes();

Route::middleware(['auth', 'isSuperAdmin'])->group(function () {
        // Dashboard
        Route::get('superadmin.dashboard', [DashboardSuperadminController::class, 'index'])
            ->name('superadmin.dashboard');

        // Role
        Route::resource('role', RoleController::class)->only(['index']);

        // Satuan
        Route::resource('satuan', SatuanController::class);
        Route::post('satuan/{id}/toggle-status', [SatuanController::class, 'toggleStatus'])
            ->name('satuan.toggle-status');
    });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
