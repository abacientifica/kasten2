<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\CheckRegister;
use App\Model\Plantillas;

class NotificarCheckPlantillas implements ShouldQueue
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
    public function handle(CheckRegister $event)
    {
        $nuevoCheck = $event->newCheck;
        $ListaCheck = $event->ListaCheck;
        $ListaCheck = array_filter($ListaCheck,function($e){
            return !$e->Usuario;
        });
        $CheckMarcado = array_filter($event->ListaCheck,function($e) use ($nuevoCheck){
            return $e->Id_Check == $nuevoCheck->Id_Check;
        });
        $CheckMarcado = reset($CheckMarcado);
        if($ListaCheck){
            $Plantilla = Plantillas::with('tercero','direccion','plantillasdet')->find($nuevoCheck->IdPlantilla);
            $ListaCheck = reset($ListaCheck);
            $Usuarios = \Funciones::ObtenerUsuariosPermiso($ListaCheck->Id_Check);
            $Mensaje = "El usuario ".strtoupper($nuevoCheck->Usuario)." ha marcado el check ".strtoupper($CheckMarcado->NmCheck)." de la plantilla cliente  ID : ".$Plantilla->IdPlantilla." para :".strtoupper($Plantilla->tercero->NombreCorto).",<br> 
            el siguiente check de revisión es ".strtoupper($ListaCheck->NmCheck).", el cual debes realizar el seguimiento para terminar el proceso de cotización.<br>
            <strong>Comentario usuario</strong> : ".$nuevoCheck->Comentarios;
            $Correos = null;
            foreach($Usuarios as $usuario){
                $Correos[] = $usuario->Email;
            }
            if($Correos){
                \Funciones::EnviarEmail("Seguimiento Plantilla ".$Plantilla->IdPlantilla." ".strtoupper($ListaCheck->NmCheck),$Correos,$Mensaje);
            }
        }
    }
}
