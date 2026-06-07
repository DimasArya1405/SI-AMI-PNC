<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
        Carbon::setLocale(config('app.locale', 'id'));
        CarbonImmutable::setLocale(config('app.locale', 'id'));
        setlocale(LC_TIME, 'id_ID', 'id_ID.utf8', 'Indonesian_Indonesia.1252', 'Indonesian');
    }
}
