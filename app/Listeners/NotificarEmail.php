<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Events\NuevaCotizacion;

class NotificarEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    /*public function __construct()
    {
        //
    }*/

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NuevaCotizacion $event)
    {
        $Cotizacion = $event->Cotizacion;
        if($Cotizacion['PerteneceContrato'] == 1){
            $Mensaje = "El usuario ".strtoupper(\Auth::user()->Usuario)." ha marcado la cotizacion con ID : ".$Cotizacion['IdCotizacion']." pertenece a contrato, ingresa a kasten y realiza las acciones pertinentes.
                <strong>Comentario usuario</strong> : ".$nuevoCheck->Comentarios;
            $Correos = 'gestioncontratos@aba.com.co';
            foreach($Usuarios as $usuario){
                $Correos[] = $usuario->Email;
            }
            if($Correos){
                \Funciones::EnviarEmail("Cotizacion Contrato ".$Cotizacion['IdCotizacion'],$Correos,$Mensaje);
            }
        }
    }
}
