<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Direcciones;
use App\Model\ListaPreciosDet;
use App\Model\MovimientosDet;
class ListaPreciosController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return  redirect('/');

        $filtro = $request->filtro;
        if(isset($request->IdDireccion) && $request->IdDireccion >0){
            $IdDir = $request->IdDireccion; 
            $Direccion = Direcciones::findOrFail($IdDir);
            $Sql = "select `lista_precios_det`.`Id_Item` as `Item`, `item`.`Descripcion` as `Descripcion`, `item`.`Disponible` as `Disponible`, `item`.`Inactivo` as `Inactivo`, lista_precios_det.Precio, `item`.`Por_Iva` as `Por_Iva`,'' as Venta,'0' as Cantidad,item.Por_Iva as Iva
                    from  lista_precios_det
                    LEFT JOIN lista_precios on lista_precios.IdListaPrecios = lista_precios_det.IdListaPrecios
                    LEFT JOIN direcciones on direcciones.IdDireccion = ".$IdDir."
                    LEFT JOIN item on item.Id_Item = lista_precios_det.Id_Item where 1";
            if($filtro !=''){
                $Sql.= " and (item.Descripcion like '%".$filtro."%' or item.Id_Item like '%".$filtro."%')";
            }
            $Sql.= " and lista_precios.IdListaPrecios = ".$Direccion->IdListaPreciosDireccion." and item.Inactivo=0 and IdKit =0 order by Venta DESC limit 100";
            $Lista = DB::select($Sql);
        }
        return[
            'productos'=>$Lista
        ];
    }
}
