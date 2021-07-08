<?php

namespace App\Http\Controllers\Plantillas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Plantillas;
use App\Model\PlantillasDet;

class PlantillasClientesController extends Controller
{
    public function ListaPlantillas(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Id = isset($request->Id) ? $request->Id : null;
            $Estado = isset($request->Estado) ? $request->Estado : null;
            $Cliente = isset($request->IdTercero) ? $request->IdTercero : null;
            $oRangoFechas = isset($request->oRangoFechas) ? $request->oRangoFechas : null;
            
            $Plantillas = Plantillas::with('tercero','direccion')->where('Tipo',0);
            if($Id){
                //$Plantillas = $Plantillas->where("IdPlantilla",$Id);
            }
            if($Estado){
                $Plantillas = $Plantillas->where("Estado",$Estado);
            }
            if($Cliente){
                $Plantillas = $Plantillas->where("IdTerceroPlant",$Cliente);
            }
            if($oRangoFechas){
                //$Plantillas = $Plantillas->whereBetween('FhAutoriza',[$Filtros->oRangoFechas[0],$Filtros->oRangoFechas[1]]);
            }
            $Plantillas = $Plantillas->get();
            return[
                'plantillas'=>$Plantillas
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }
    }

    public function ObtenerPlantilla(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Id = isset($request->Id) ? $request->Id : null;
            $Plantillas = Plantillas::with('tercero','direccion','plantillasdet')->find($Id);
            $PlantillasDet = \Funciones::CargarDetallesPlantillaClientes($Id);
            return[
                'plantilla'=>$Plantillas,
                'plantillas_det'=>$PlantillasDet['plantillas_det'],
                'columnas'=>$PlantillasDet['columnas'],
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }
    }

    public function CrearPlantilla(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            DB::beginTransaction();
            $Datos = $request->params['fillNuevaPlantilla'];
            $Plantilla = new Plantillas();
            $Plantilla->IdTerceroPlant = $Datos['nIdTercero'];
            $Plantilla->IdDireccionPlant = $Datos['nIdDireccion'];
            $Plantilla->NmPlantilla = $Datos['cNmPlantilla'];
            $Plantilla->FhEntregaPropuesta = $Datos['FechaEntregaPropuesta'];
            $Plantilla->Periodo =  $Datos['dPeriodoAnio']."-".$Datos['dPeriodoMes'];
            $Plantilla->Estado = 'DIGITADA';
            $Plantilla->Usuario = \Auth::user()->Usuario;
            $Plantilla->Comentarios = $Datos['cComentarios'];
            $Plantilla->CantidadConsumoMes = $Datos['nMesesConsumo'];
            $Plantilla->FhPlantilla = date('Y-m-d H:i:s');
            $Plantilla->VigDesde = $Datos['oVigenciaOferta'][0];
            $Plantilla->VigHasta = $Datos['oVigenciaOferta'][1];
            $Plantilla->Tipo =0;
            $Plantilla->save();
            DB::commit();
            \Funciones::CrearLog(8, $Plantilla->IdPlantilla, \Auth::user()->Usuario);
            return [
                'plantilla'=>$Plantilla->IdPlantilla,
                'msg'=>"La plantilla ha sido creada con exito ".$Plantilla->IdPlantilla,
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        } 
    }

    public function CargarDatosHomologacion(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $Filtros = $request->Filtros;
        $Criterios = $request->Criterios;
        $IdTercero = $request->IdTercero;
        $Sql = \Funciones::DatosHomologarPlantilla($IdTercero,$Criterios,$Filtros);
        $Datos = DB::select($Sql);
        return[
            'listas_det'=>$Datos,
            'sql'=>$Sql
        ];
    }

    public function AsignarLista(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        $PlantDet = new PlantillasDet();
        $PlantDet = PlantillasDet::find($request->params['IdPlantillaDet']);
        $PlantDet->IdListaCostosDetPlantDet = $request->params['IdLista'];

        if ($PlantDet->save()) {

            //\Funciones::ActualizarDatosPlantillaClientes($PlantDet->IdPlantilla);
            return[
                'status'=>201,
                'msg'=>"El detalle ha sido homologado"
            ];
        }
        else{
            return[
                'status'=>500,
                'msg'=>"Ocurrio un error"
            ];
        }
    }

    public function GuardarDatosEdit(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $PlantillaDet = PlantillasDet::find($request->params['IdPlantillaDet']);
            $DatosEdit = $request->params['arrCambios'];
            foreach($DatosEdit as $key=>$dato){
                $Columna = $dato['Columna'];
                $DatoNuevo = $dato['DatoNuevo'];
                $PlantillaDet->$Columna = $DatoNuevo;
                $PlantillaDet->save();
                $Comentario = "La columna '".$Columna."'  dato anterior era  = ".$dato['DatoAnt']." y el dato nuevo es = ".$DatoNuevo;
                \Funciones::CrearLogPlantillas(16,$PlantillaDet->IdPlantilla,$PlantillaDet->IdPlantillaDet,$Comentario);
            }
            DB::commit();
            return [
                'msg'=>"El los datos del detalle ".$request->params['IdPlantillaDet']." han sido modificados!!",
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        }
    }
        
}
