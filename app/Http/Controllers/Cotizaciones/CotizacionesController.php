<?php

namespace App\Http\Controllers\Cotizaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Cotizaciones;
use App\Model\CotizacionesDet;
use App\Model\FiltrosCotizaciones;
use App\Events\NuevaCotizacion;
use App\Events\EditarCotizacion;

class CotizacionesController extends Controller 
{
    public function CrearCotizacion(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $DatosForm = \Funciones::ArraryToObject($request->params['formNewCot']);
        try{
            DB::beginTransaction();
            $Cotizacion = New Cotizaciones();
            $Cotizacion->FechaCreacion = date('Y-m-d H:i:s');
            $Cotizacion->FechaCotizacion = date('Y-m-d H:i:s');
            $Cotizacion->FechaDesde = $DatosForm->VigDesde;
            $Cotizacion->FechaHasta = $DatosForm->VigHasta;
            $Cotizacion->IdTerceroCotizacion = $DatosForm->IdTercero;
            $Cotizacion->IdDireccionCotizacion = $DatosForm->IdDireccion;
            $Cotizacion->IdCotizacionTipo = $DatosForm->IdTipo;
            $Cotizacion->IdCotizacionSubTipo = $DatosForm->IdSubTipo;
            $Cotizacion->Soporte = $DatosForm->soporteCot;
            $Cotizacion->NmCotizacion = $DatosForm->nombreCot;
            $Cotizacion->ContactoCotizacion = $DatosForm->contactoCot;
            $Cotizacion->EmailCotizacion = $DatosForm->emailCot;
            $Cotizacion->Plazo = 0;
            $Cotizacion->Estado = 'DIGITADA';
            $Cotizacion->Usuario = \Auth::user()->Usuario;
            $Cotizacion->Comentarios = $DatosForm->comentarioCot;
            $Cotizacion->CondDevolucion = $DatosForm->comentarioDev;
            $Cotizacion->TiempoEntrega = $DatosForm->tiempoEntrega;
            $Cotizacion->TipoTiempoEntrega = $DatosForm->tpEntrega;
            $Cotizacion->IdDocumento = 2;
            $Cotizacion->Digitalizado = 0;
            $Cotizacion->PerteneceContrato = 0;
            $Cotizacion->AsesorCotizacion = $DatosForm->IdAsesor;
            $Cotizacion->save();
            $Log = [
                'IdAccion'=>8,
                'IdCotizacion'=>$Cotizacion->IdCotizacion
            ];
            event(new NuevaCotizacion($Log,$Cotizacion));
            DB::commit();
            return[
                'status'=>201,
                'msg'=>'La cotización se creo correctamente con el ID '.$Cotizacion->IdCotizacion,
                'IdCotizacion'=>$Cotizacion->IdCotizacion
                
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'status'=>500,
                'msg'=>'Ocurrio un error al crear la cotización. '.$e->getMessage()
            ];
        }
    }

    public function Actualizar(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $DatosForm = \Funciones::ArraryToObject($request->params['formEditCot']);
        try{
            DB::beginTransaction();
            $Cotizacion =  Cotizaciones::find($DatosForm->IdCotizacion);
            $DatosOld = Cotizaciones::find($DatosForm->IdCotizacion);
            $Cotizacion->FechaDesde = $DatosForm->VigDesde;
            $Cotizacion->FechaHasta = $DatosForm->VigHasta;
            $Cotizacion->IdTerceroCotizacion = $DatosForm->IdTercero;
            $Cotizacion->IdDireccionCotizacion = $DatosForm->IdDireccion;
            $Cotizacion->IdCotizacionTipo = $DatosForm->IdTipo;
            $Cotizacion->IdCotizacionSubTipo = $DatosForm->IdSubTipo;
            $Cotizacion->Soporte = $DatosForm->soporteCot;
            $Cotizacion->NmCotizacion = $DatosForm->nombreCot;
            $Cotizacion->ContactoCotizacion = $DatosForm->contactoCot;
            $Cotizacion->EmailCotizacion = $DatosForm->emailCot;
            $Cotizacion->Comentarios = $DatosForm->comentarioCot;
            $Cotizacion->CondDevolucion = $DatosForm->comentarioDev;
            $Cotizacion->TiempoEntrega = $DatosForm->tiempoEntrega;
            $Cotizacion->TipoTiempoEntrega = $DatosForm->tpEntrega;
            $Cotizacion->AsesorCotizacion = $DatosForm->IdAsesor;
            $Cotizacion->Plazo = $DatosForm->Plazo;
            $Cotizacion->DctoFin = $DatosForm->DctoFro;
            $Cotizacion->DiasDctoFin = $DatosForm->DiasDcto;
            $Cotizacion->PerteneceContrato = $DatosForm->PerteneceCCto;
            $Cotizacion->TpPrecio = $DatosForm->TpPrecio;
            $Cotizacion->RequiereGestion = $DatosForm->RequiereGestion;
            $Cotizacion->PlazoGestion = $DatosForm->PlazoGestion;
            $Cotizacion->save();
            event(new EditarCotizacion($Cotizacion,$DatosOld));
            $Log = [
                'IdAccion'=>8,
                'Cotizacion'=>$Cotizacion->IdCotizacion
            ];
            //event(new EditarCotizacion($Log,$Cotizacion,$cotizacionOld));
            DB::commit();
            return[
                'status'=>201,
                'msg'=>'La cotización se edito correctamente.',
                'Cotizacion'=>\FuncionesCotizaciones::ObtenerCotizacion($Cotizacion->IdCotizacion)
                
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'status'=>500,
                'msg'=>'Ocurrio un error al editar la cotización. '.$e->getMessage()
            ];
        }
    }
    public function ListaCotizaciones(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $filtros = isset($request->params['filtros']) ? $request->params['filtros'] : null;
            $limiteDatos = isset($request->params['limite']) ? $request->params['limite'] : null;
            $filtrar = $request->params['set_filtro'];
            if(!$filtrar){
                $FSaved = \FuncionesCotizaciones::ObtenerFiltrosCotizaciones();
                $FSaved = $FSaved['filtros'];
                $filtros = [
                    'FiltroGeneral'=> $FSaved['FiltrosGeneralK2'],
                    'NroCotizacion'=> $FSaved['IdCotizacion'],
                    'fEstado'=> $FSaved['Estado'],
                    'fTipo'=> $FSaved['Tipo'],
                    'fSubTipo'=> $FSaved['SubTipo'],
                    'fSeguimiento'=> $FSaved['IdSeguimiento'],
                    'fRangoFecha'=> $FSaved['RangoFecha1'] ? [$FSaved['RangoFecha1'],$FSaved['RangoFecha2']] : null,
                    'LimiteDatos'=>$FSaved['Limite'] ? $FSaved['Limite'] : 100,
                ];
            }
            $Cotizaciones = \FuncionesCotizaciones::ObtenerListaCotizaciones($filtros,$limiteDatos);
            $Tipos = \FuncionesCotizaciones::TiposCotizaciones();
            $SubTiposCot = \FuncionesCotizaciones::SubTiposcotizaciones();
            $TiposSeguimientos = \FuncionesCotizaciones::TiposSeguimientos();
            return [
                'status'=>201,
                'cotizaciones'=>$Cotizaciones,
                'datos_filtros'=>[
                    'tipos'=>$Tipos,
                    'subtipos'=>$SubTiposCot,
                    'seguimientos'=>$TiposSeguimientos,
                ]
            ];
        }
        catch(Exception $e){
            return [
                'status'=>500,
                'msg'=>'Ocurrio un error al cargar las cotizaciones',
            ];
        }
    }

    public function ObtenerCotizacion(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Cotizacion = \FuncionesCotizaciones::ObtenerCotizacion($request->IdCotizacion);
            $DctosFinancieros = \Funciones::DevDctosFinancierosTercero($Cotizacion->IdTerceroCotizacion);
            return[
                'cotizacion'=>$Cotizacion,
                'dctos_fin'=>$DctosFinancieros,
                'cotizaciones_det'=> []
            ];
        }
        catch(Exception $e){
            return[
                'status'=>500,
                'msg'=> 'Ocurrio un error al cargar '.$e->getMessage()
            ];
        }
    }

    public function FiltrosUsuarioLista(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $FiltrosK = \FuncionesCotizaciones::ObtenerFiltrosCotizaciones();
            return[
                'filtros'=>$FiltrosK['filtros']
            ];
        }
        catch(Exception $e){
            return [
                'filtros'=>[],
                'status'=>500,
                'msg'=> 'Ocurrio un error al cargar los filtros '.$e->getMessage()
            ];
        }
    }

    public function GuardarFiltroIndex(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try {
            $FiltrosK = $request->params['filtros'];
            $ColumnasK = $request->params['columnas'];
            $FiltrosAct = DB::select("select * from datos_trabajo where IdUsuario = '".\Auth::user()->Usuario."'");
            if($FiltrosAct){
                DB::select("update  datos_trabajo set FiltrosIndexK2 = '".$FiltrosK."', ColumnasIndexK2 = '".$ColumnasK."'  where IdUsuario = '".\Auth::user()->Usuario."'");
            }
            else{
                DB::select("insert  datos_trabajo  (IdUsuario,FiltrosIndexK2,ColumnasIndexK2) values  ('".\Auth::user()->Usuario."','".$FiltrosK."','".$ColumnasK."'");
            }

            return [
                'filtros'=> $FiltrosK,
                'columnas'=> $ColumnasK
            ];
        }
        catch(Exception $e){

        }
    }
}
