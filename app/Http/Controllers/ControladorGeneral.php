<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Conceptos;
use App\Model\Direcciones;
class ControladorGeneral extends Controller
{
    
    public function ObtenerAsesores(Request $request)
    {
        if(!$request->ajax())  return  redirect('/'); 
        $Asesores = DB::select("select * from asesores where Inactivo =0");
        return [
            'asesores'=>$Asesores
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
        $Sql = "select MONTHNAME(movimientos.Fecha) as mes, YEAR(movimientos.Fecha) as anio, SUM(movimientos.Total) as total from `movimientos` 
            where Fecha >= date_sub(CURDATE(), interval 6 MONTH) and `IdTercero` = ".$IdTercero." and (IdDocumento = 61 or `IdDocumento` = 8)
            group by MONTHNAME(movimientos.Fecha), YEAR(movimientos.Fecha) ORDER BY YEAR(movimientos.Fecha) ,MONTHNAME(movimientos.Fecha)";
        $pedidos = DB::select($Sql); 
        $Sql = "select MONTHNAME(movimientos.Fecha) as mes, YEAR(movimientos.Fecha) as anio, SUM(movimientos.Total) as total from `movimientos` 
            where Fecha >= date_sub(CURDATE(), interval 6 MONTH) and `IdTercero` = ".$IdTercero." and (IdDocumento = 3 or `IdDocumento` = 11)
            group by MONTHNAME(movimientos.Fecha), YEAR(movimientos.Fecha) ORDER BY YEAR(movimientos.Fecha) ,MONTHNAME(movimientos.Fecha)";
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
        $sql = "select log.*, acciones.NmAccion
                FROM log LEFT JOIN acciones ON log.IdAccion = acciones.IdAccion
                WHERE IdMovimiento=" . $IdMovimiento;
        $Datos = DB::select($sql);
        return [
            'logs'=>$Datos
        ];
    }
}
