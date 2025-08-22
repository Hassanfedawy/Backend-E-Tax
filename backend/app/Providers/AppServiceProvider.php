<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB; // ✅ Add this line


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
        try {
            DB::statement('CREATE DATABASE IF NOT EXISTS etax CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
            DB::statement('USE etax;');
        } catch (\Exception $e) {
            // prevent crash if db server is unreachable
        }

                Schema::defaultStringLength(191);
    }
}
