<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Conceptos;
use App\Model\Direcciones;
use App\Model\Lineas;
use App\Model\SubGrupos;
use App\Model\Asesores;
class ControladorGeneral extends Controller 
{
    public function __construct()
    {
        if(!\Auth::check()){
           return  redirect('/login');
        }
    }

    
    public function ObtenerAsesores(Request $request)
    {
        if(!$request->ajax())  return  redirect('/'); 
        $usuario = $request->usuario;
        $builder = Asesores::query();
        $builder->where('Inactivo',0)
        ->when($usuario,function($builder,$usuario){
            return $builder->where('UsuarioAsesor',$usuario);
        })->orderBy('Nombre');
        $asesores = $builder->get();
        return [
            'asesores'=> $asesores
        ];
    }

    public function CargarConceptosDocumentos(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $conceptos =  Conceptos::select('conceptos.IdConcepto','NmConcepto')
                        ->leftjoin('conceptosdocumentos','conceptosdocumentos.IdConcepto','=','conceptos.IdConcepto')
                        ->where("conceptosdocumentos.IdDocumento",'=',$request->IdDoc)
                        ->where('conceptos.Inactivo',0)->get();
        return [
            'conceptos'=>$conceptos
        ];
    }

    public function CargarFormasDePago(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $FormasPago =  DB::select("select * from formaspago");
        return [
            'formas_pago'=>$FormasPago
        ];
    }

    public function DashboardHome(Request $request)
    {
        if(!$request->ajax())  return  redirect('/'); 
        $anio = date("Y");
        $IdTercero =  \Auth::user()->IdTercero;
        $Sql = "select MONTH(movimientos.Fecha) as mes, YEAR(movimientos.Fecha) as anio, SUM(movimientos.SubTotal * if(OpValor =0,1,OpValor)) as total   from `movimientos` 
        LEFT JOIN documentos on documentos.IdDocumento = movimientos.IdDocumento
        where Fecha >= date_sub(CURDATE(), interval 6 MONTH) and `IdTercero` = ".$IdTercero." and (movimientos.IdDocumento = 61 or movimientos.`IdDocumento` = 8) and (movimientos.Estado='AUTORIZADA' OR movimientos.Estado='CERRADA' )
         group by MONTH(movimientos.Fecha), YEAR(movimientos.Fecha) ORDER BY YEAR(movimientos.Fecha) ,MONTH(movimientos.Fecha)";
        $pedidos = DB::select($Sql);

        $Sql = "select MONTH(movimientos.Fecha) as mes, YEAR(movimientos.Fecha) as anio,
                SUM(movimientos.SubTotal * if(OpValor =0,1,OpValor)) as total
                from `movimientos`
                LEFT JOIN documentos on documentos.IdDocumento = movimientos.IdDocumento
                LEFT JOIN conceptos on conceptos.IdConcepto = movimientos.IdConcepto
                where Fecha >= date_sub(CURDATE(), interval 6 MONTH)
                and `IdTercero` = ".$IdTercero." AND `movimientos`.`Impresion` > 0
                and (movimientos.TpDocumento = 5)
                and (movimientos.Estado='AUTORIZADA' OR movimientos.Estado='CERRADA' )
                group by MONTH(movimientos.Fecha), YEAR(movimientos.Fecha) ORDER BY YEAR(movimientos.Fecha) ,MONTH(movimientos.Fecha)";
        $ventas=DB::select($Sql); 

        $remisiones = DB::select("select MONTH(FhAutoriza) as mes ,YEAR(FhAutoriza) as anio,sum(Total) as total from movimientos where IdDocumento = 11 and IdTercero =".$IdTercero." and Fecha >='2019-09-01' group by MONTH(FhAutoriza) ,YEAR(FhAutoriza)");
        return [
            "pedidos"=>$pedidos,
            "ventas"=>$ventas,
            'remisiones'=>$remisiones,
            "anio"=>$anio
        ];
    }

    public function ObtenerDireccion(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $Datos = Direcciones::with('tipo')->where("IdDireccion",$request->IdDir)->get();
        if(is_countable($Datos) && count($Datos)>0){
            return[
                'direccion'=>$Datos
            ];
        }
        else{
            return[
                'direccion'=>[]
            ];
        }
    }

    public function LogMovimientos(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $IdMovimiento = $request->nIdMovimiento;
        $IdDocumento = $request->nIdDocumento;
        $sql ='';
        switch($IdDocumento){
            case 93;
            $sql = "select log_plantillas.*,NmAccion from log_plantillas
                    LEFT JOIN acciones on acciones.IdAccion = log_plantillas.IdAccion
                    where IdPlantilla =".$IdMovimiento;
            break;

            case 2;
            $sql = "select log_cotizaciones.*,NmAccion from log_cotizaciones
                    LEFT JOIN acciones on acciones.IdAccion = log_cotizaciones.IdAccion
                    where IdCotizacion =".$IdMovimiento;
            break;

            default :
                $sql = "select log.*, acciones.NmAccion
                    FROM log LEFT JOIN acciones ON log.IdAccion = acciones.IdAccion
                    WHERE IdMovimiento=" . $IdMovimiento;
                break;
            

        }
        $Datos = DB::select($sql);
        return [
            'logs'=>$Datos
        ];
    }

    public function CargarCamposTablas(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $Tabla = $request->tabla;
        $sql = "SHOW FULL columns from ".$Tabla;
        $Datos = DB::select($sql);
        return [
            'campos'=>$Datos
        ];
    }

    public function getLineasGrupos(Request $request)
    {
       $idLinea = $request->idLinea;
       $builder = Lineas::with('grupos');
       $builder->when($idLinea,function($builder,$idLinea){
            return $builder->where('IdLinea',$idLinea);
       });
       return $builder->orderBy('NmLinea')->get();
    }

    public function getSubGruposGrupos(Request $request)
    {
        $request->validate([
            'idLinea'=>'required',
            'idGrupo'=>'required',
        ]);
        $idLinea = $request->idLinea;
        $idGrupo = $request->idGrupo;
        $builder = SubGrupos::query();
        $builder->when($idLinea,function($builder,$idLinea){
            return $builder->where('IdLinea',$idLinea);
        })->when($idGrupo,function($builder,$idGrupo){
            return $builder->where('IdGrupo',$idGrupo);
        });
        return $builder->orderBy('NmSubGrupo')->get();
    }


}
