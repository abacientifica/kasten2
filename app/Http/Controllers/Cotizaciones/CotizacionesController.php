<?php

namespace App\Http\Controllers\Cotizaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Cotizaciones;
use App\Model\CotizacionesDet;
use App\Model\FiltrosCotizaciones;

class CotizacionesController extends Controller
{
    public function ListaCotizaciones(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $filtros = isset($request->params['filtros']) ? $request->params['filtros'] : null;
            $limiteDatos = isset($request->params['limite']) ? $request->params['limite'] : null;
            $filtrar = $request->params['set_filtro'];
            if(!$filtrar){
                $FSaved = $this->FiltrosUsuarioLista();
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

    }

    public function FiltrosUsuarioLista(){
        $FiltrosK = FiltrosCotizaciones::find(\Auth::user()->Usuario);
        return[
            'filtros'=>$FiltrosK
        ];
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
