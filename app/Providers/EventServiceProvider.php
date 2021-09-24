<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserLogin;
use App\Events\CheckRegister;
use App\Events\RegistrarLog;
use App\Events\NuevaCotizacion;
use App\Events\EditarCotizacion;
use App\Events\CambioRol;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserLogin::class=>[
            'App\Listeners\LoginUser'
        ],
        CheckRegister::class=>[
            \App\Listeners\NotificarCheckPlantillas::class,
        ],
        RegistrarLog::class=>[
            \App\Listeners\CrearLog::class,
        ],
        NuevaCotizacion::class=>[
            \App\Listeners\CreateCotizacionesLog::class,
            \App\Listeners\NotificarEmail::class,
        ],
        EditarCotizacion::class=>[
            \App\Listeners\CreateCotizacionesLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
