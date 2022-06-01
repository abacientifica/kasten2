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
        $filtro = str_replace(' ','%',$filtro);
        if(isset($request->IdDireccion) && $request->IdDireccion >0){
            $IdDir = $request->IdDireccion; 
            $limit = $request->limit;
            $Direccion = Direcciones::findOrFail($IdDir);
            $MarcasTercero = DB::select("select * from marcas_terceros where IdTercero=".$Direccion->IdTercero);
            $Sql = "select `lista_precios_det`.`Id_Item` as `Item`, `item`.`Descripcion` as `Descripcion`, `item`.`Disponible` as `Disponible`, `item`.`Inactivo` as `Inactivo`, lista_precios_det.Precio,
                    `item`.`Por_Iva` as `Por_Iva`,'' as Venta,'0' as Cantidad,item.Por_Iva as Iva,lista_precios_det.FhHasta, `lista_precios_det`.`DescripcionTercero` as `DescripcionTercero`,
                    NmMarca,CodTercero,lista.CantMinimaVenta,if(FCCompraCliente >0 , FCCompraCliente, FactorVenta) as FactorVenta,lista_precios_det.UMV,item.UMM,lista.RefFabricante,lista_precios_det.IdListaPreciosDet,lista_precios_det.NoValidaCantMinVenta
                    from  lista_precios_det
                    LEFT JOIN lista_precios on lista_precios.IdListaPrecios = lista_precios_det.IdListaPrecios
                    LEFT JOIN direcciones on direcciones.IdDireccion = ".$IdDir."
                    LEFT JOIN item on item.Id_Item = lista_precios_det.Id_Item
                    LEFT JOIN lista_costos_prov_det as lista on lista.IdListaCostosProvDet = item.IdListaCostosDetItem
                    LEFT JOIN marcas on marcas.IdMarca = lista.IdMarca
                    where 1";
            if($filtro !=''){
                $Sql.= " and ( lista_precios_det.CodTercero like '%".$filtro."%' or item.Descripcion like '%".$filtro."%' or item.Id_Item like '%".$filtro."%' or marcas.NmMarca like '%".$filtro."%' or lista.RefFabricante like '%".$filtro."%' or lista_precios_det.DescripcionTercero like '%".$filtro."%')";
            }
            if(is_countable($MarcasTercero) && count($MarcasTercero)>0){
                $Sql.= " and  marcas.IdMarca in (select marcas_terceros.IdMarca from marcas_terceros where IdTercero = '".$Direccion->IdTercero."' and marcas_terceros.Inactiva =0 )";
            }
            if($limit != ''){
                $Sql.= " and lista_precios.IdListaPrecios = ".$Direccion->IdListaPreciosDireccion." and  lista_precios_det.FhHasta >= DATE_SUB(CURDATE(),INTERVAL 24 MONTH) and item.Inactivo=0 and IdKit =0 order by Venta DESC limit 1";
            }
            else{
                $Sql.= " and lista_precios.IdListaPrecios = ".$Direccion->IdListaPreciosDireccion." and  lista_precios_det.FhHasta >= DATE_SUB(CURDATE(),INTERVAL 24 MONTH) and item.Inactivo=0 and IdKit =0 order by Venta DESC limit 100";
            }
            $Lista = DB::select($Sql);
        }
        return[
            'productos'=>$Lista,
            'sql'=>$Sql
        ];
    }
}
