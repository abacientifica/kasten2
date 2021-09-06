<?php

namespace App\Task;

use App\Model\FiltrosCotizaciones;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Closure;

class ConvertirFiltrosCotizaciones {

    public function handle($data,Closure $next){
        $Filtros = isset($data[0]) ? \Funciones::ArraryToObject($data[0]) : null;
        $FiltrosActuales = $data[1];
        $FSaved = FiltrosCotizaciones::find(\Auth::user()->Usuario);
        if($Filtros && ($Filtros->fEstado || $Filtros->fTipo || $Filtros->fSubTipo  || $Filtros->fSeguimiento || $Filtros->fRangoFecha || $Filtros->NroCotizacion || $Filtros->FiltroGeneral)){
            if($Filtros->FiltroGeneral){
                $FSaved->FiltrosGeneralK2 = $Filtros->FiltroGeneral;
                $FiltroGen = explode(' ',$Filtros->FiltroGeneral." ");
                foreach($FiltroGen as $Filt){
                    $FiltrosActuales = $FiltrosActuales->where(DB::raw("concat_ws(' ',cotizaciones.IdCotizacion,cotizaciones.NroCotizacion,terceros.NombreCorto,CONVERT(NmCotizacionTipo USING utf8),CONVERT(NmTipoSeguimiento USING utf8),CONVERT(NmSubTipoCotizacion USING utf8),CONVERT(cotizaciones.Estado USING utf8),UsuarioSolicita,CONVERT(cotizaciones.Usuario USING utf8))")
                    ,'like','%'.$Filt.'%');
                }
            }
            else{
                $FSaved->FiltrosGeneralK2 = null;
            }
            if($Filtros->NroCotizacion){
                $FSaved->IdCotizacion = $Filtros->NroCotizacion;
                $FiltrosActuales = $FiltrosActuales->where(DB::raw('concat_ws(cotizaciones.IdCotizacion,cotizaciones.NroCotizacion)') ,'like','%'.$FSaved->IdCotizacion.'%')->orWhere('cotizaciones.IdCotizacion',$FSaved->IdCotizacion)->orWhere('cotizaciones.NroCotizacion',$FSaved->IdCotizacion);
            }
            else{
                $FSaved->IdCotizacion = null;
            }
            if($Filtros->fEstado){
                $FSaved->Estado = $Filtros->fEstado;
                $FiltrosActuales = $FiltrosActuales->where("cotizaciones.Estado",$FSaved->Estado);
            }
            else{
                $FSaved->Estado = null;
            }
            if($Filtros->fTipo){
                $FSaved->Tipo = $Filtros->fTipo;
                $FiltrosActuales = $FiltrosActuales->where("cotizaciones.IdCotizacionTipo",$FSaved->Tipo);
            }
            else{
                $FSaved->Tipo = null;
            }
            if($Filtros->fSubTipo){
                $FSaved->SubTipo = $Filtros->fSubTipo;
                $FiltrosActuales = $FiltrosActuales->where("cotizaciones.IdCotizacionSubTipo",$FSaved->SubTipo);
            }
            else{
                $FSaved->SubTipo = null;
            }
            if($Filtros->fSeguimiento){
                $FSaved->IdSeguimiento = $Filtros->fSeguimiento;
                $FiltrosActuales = $FiltrosActuales->where("tipos_seguimientos_cotizacion.IdTipoSeguimiento",$FSaved->IdSeguimiento);
            }
            else{
                $FSaved->IdSeguimiento = null;
            }
            if($Filtros->fRangoFecha){
                $Fecha1 = current($Filtros->fRangoFecha);
                $Fecha2 = next($Filtros->fRangoFecha);
                $FSaved->RangoFecha1 = $Fecha1;
                $FSaved->RangoFecha2 = $Fecha2;
                //$Fecha1 = Carbon::createFromFormat("Y-m-d",$Fecha1)->toDateString();
                //$Fecha2 = Carbon::createFromFormat('Y-m-d',$Fecha2)->toDateString();
                $FiltrosActuales = $FiltrosActuales->whereBetween("cotizaciones.FechaCotizacion",[$Fecha1,$Fecha2]);
            }
            else{
                $FSaved->RangoFecha1 = null;
                $FSaved->RangoFecha2 = null;
            }
            if($Filtros->LimiteDatos){
                $FSaved->Limite = $Filtros->LimiteDatos;
            }
            else{
                $FSaved->Limite = 200;
            }
            $FSaved->save();
            $data = $FiltrosActuales;
            return $next($FiltrosActuales);
        }
        else{
            $FSaved->FiltrosGeneralK2 = null;
            $FSaved->IdCotizacion = null;
            $FSaved->Estado = null;
            $FSaved->Tipo = null;
            $FSaved->SubTipo = null;
            $FSaved->IdSeguimiento = null;
            $FSaved->RangoFecha1 = null;
            $FSaved->RangoFecha2 = null;
            $FSaved->save();
        }
        return $next($FiltrosActuales);
    }
}