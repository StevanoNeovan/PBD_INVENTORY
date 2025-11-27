<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class LowStockViewComposer
{
    public function compose(View $view)
    {
        try {
            $lowStockItems = DB::select("
                SELECT idbarang, nama_barang, stok_tersedia, status_stok
                FROM v_stok_barang
                WHERE stok_tersedia <= 10
                ORDER BY stok_tersedia ASC
                LIMIT 10
            ");
            
            $lowStockCount = count($lowStockItems);
            
            $view->with([
                'lowStockItems' => $lowStockItems,
                'lowStockCount' => $lowStockCount
            ]);
        } catch (\Exception $e) {
            $view->with([
                'lowStockItems' => [],
                'lowStockCount' => 0
            ]);
        }
    }
}