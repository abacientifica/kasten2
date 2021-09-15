<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Model\LogCotizaciones;
use \App\Events\NuevaCotizacion;
use \App\Events\EditarCotizacion;

class CreateCotizacionesLog
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
    public function handle($event)
    {
        if($event instanceof NuevaCotizacion){
            $varLog = $event->Log;
            $Log = new LogCotizaciones();
            $Log->IdAccion = $varLog['IdAccion'];
            $Log->IdCotizacion = isset($varLog['IdCotizacion']) ? $varLog['IdCotizacion'] : null ;
            $Log->Usuario = \Auth::user()->Usuario;
            $Log->Comentarios =  isset($varLog['Comentarios']) ? $varLog['Comentarios'] : null ;
            $Log->IdCotizacionDet =  isset($varLog['IdCotizacionDet']) ? $varLog['IdCotizacionDet'] : null ;
            $Log->Id_Item =  isset($varLog['Id_Item']) ?$varLog['Id_Item'] : null ;
            $Log->Fecha = date('y-m-d H:i:s');
            $Log->save();
        }
        else if($event instanceof EditarCotizacion){
            $Columnas = \Funciones::NombresColumnasTabla('cotizaciones');
            $CotNew = $event->CotizacionNew;
            $CotOld = $event->CotizacionOld;
            $StrCambios ='';
            foreach($Columnas as $Col){
                if($CotNew->$Col != $CotOld->$Col){
                    $StrCambios .=" Columna ".$Col." ( Nuevo '".$CotNew->$Col."' - Ant '".$CotOld->$Col."') ";
                }
            }
            $Log = new LogCotizaciones();
            $Log->IdAccion = 16;
            $Log->IdCotizacion = $CotNew->IdCotizacion ;
            $Log->Usuario = \Auth::user()->Usuario;
            $Log->Comentarios =  $StrCambios ;
            $Log->IdCotizacionDet =  null ;
            $Log->Id_Item = null;
            $Log->Fecha = date('y-m-d H:i:s');
            $Log->save();
        }   
    }
}
