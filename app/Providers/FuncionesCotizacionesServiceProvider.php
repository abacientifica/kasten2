<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FuncionesCotizacionesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path() . '/Helpers/FuncionesCotizaciones.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
