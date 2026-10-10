<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compatibilité avec les anciennes versions de MySQL / MariaDB (XAMPP, Laragon)
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));
        Paginator::defaultView('pagination::tailwind');
    }
}
