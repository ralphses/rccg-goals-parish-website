<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Shared hosting MySQL variants can still enforce the older 1000-byte
        // index limit for utf8mb4. Keep indexed strings migration-safe.
        Schema::defaultStringLength(191);
    }
}
