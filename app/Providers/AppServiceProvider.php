<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
            public function boot(): void
        {
            // Register Low Stock View Composer untuk navbar
            View::composer('layouts.navbar', \App\Http\ViewComposers\LowStockViewComposer::class);
        }
        
        // Atau jika ingin share ke semua view (uncomment salah satu):
        // View::composer('*', \App\Http\ViewComposers\LowStockViewComposer::class);
    }
