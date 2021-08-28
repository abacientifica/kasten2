<?php

namespace App\Listeners;

use App\Events\RegistrarLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\LogPlantillas;
use App\Model\Log;

class CrearLog
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *Tipo = 1 Plantillas, Tipo = 2 Movimientos, Tipo =3 Cotizaciones
     * @param  RegistrarLog  $event
     * @return void
     */
    public function handle(RegistrarLog $event)
    {
        if($event->Log['Tipo'] == 1){
            $Log = new LogPlantillas();
            $Log->Fecha = date('Y-m-d H:i:s');
            $Log->IdAccion = $event->Log['IdAccion'];
            $Log->Usuario = \Auth::user()->Usuario;
            $Log->IdPlantilla = $event->Log['Id'];
            $Log->IdPlantillaDet = $event->Log['IdDet'];
            $Log->IdItem = $event->Log['IdItem'];
            $Log->Comentarios = $event->Log['Comentarios'];
            $Log->save();
        }
    }
}
