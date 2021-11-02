<?php
namespace App\Helpers;
  
use Illuminate\Support\Facades\DB;
use App\Model\Cotizaciones;
use App\Model\CotizacionesDet;
use App\Model\FiltrosCotizaciones;
use App\Task\ConvertirFiltrosCotizaciones;
use Illuminate\Pipeline\Pipeline;

class FuncionesCotizaciones {

    /**
     * Obtiene el listado de cotizaciones
     * @fiterdata filtros enviados desde el formulario
     * @limiteDatos el limite de datos establecido
     */
    public static function ObtenerListaCotizaciones($fiterdata=null,$limiteDatos=100){
        $Cotizaciones = Cotizaciones::select('cotizaciones.*')
        ->with('cliente','direccion','direccion.listaprecios','tipocot','subtipocot','segactual','segactual.tipo')
        ->LEFTJOIN('terceros','terceros.IdTercero','=','cotizaciones.IdTerceroCotizacion')
        ->LEFTJOIN('grupos_cotizaciones','cotizaciones.IdGrupoCotizacion','=','grupos_cotizaciones.IdGrupoCotizaciones')
        ->LEFTJOIN('cotizaciones_tipos','cotizaciones_tipos.IdCotizacionTipo','=','cotizaciones.IdCotizacionTipo')
        ->LEFTJOIN('cotizaciones_subtipos','cotizaciones_subtipos.IdSubTipoCotizacion','=','cotizaciones.IdCotizacionSubTipo')
        ->LEFTJOIN('comentarios_seguimientoscot','cotizaciones.IdSeguimientoActual','=','comentarios_seguimientoscot.IdComentario','cotizaciones.IdCotizacion','=','comentarios_seguimientoscot.IdCotizacion')
        ->LEFTJOIN('direcciones','direcciones.IdDireccion','=','cotizaciones.IdDireccionCotizacion')
        ->LEFTJOIN('lista_precios','lista_precios.IdListaPrecios','=','direcciones.IdListaPreciosDireccion')
        ->LEFTJOIN('tipos_seguimientos_cotizacion','tipos_seguimientos_cotizacion.IdTipoSeguimiento','=','comentarios_seguimientoscot.TipoSeguimiento');
        $FiltrosK = FiltrosCotizaciones::find(\Auth::user()->Usuario);
        $pipe = app(Pipeline::class);
        $Cotizaciones = $pipe->send([$fiterdata,$Cotizaciones])->through([
            ConvertirFiltrosCotizaciones::class
        ])->thenReturn();
        $Cotizaciones = $Cotizaciones->OrderBy('NmTipoSeguimiento','ASC')->OrderBy('FechaCotizacion','DESC')->take($limiteDatos)->get();
        return $Cotizaciones ? $Cotizaciones : [];
    }

    /**
     * Obtiene los datos de la cotizacion
     * @IdCot id cotizacion como parameto
     */
    public static function ObtenerCotizacion($IdCot){
        try{
            $Cotizacion = Cotizaciones::select('cotizaciones.*','NmCiudad')
            ->with('cliente','cliente.asesorservcliente','cliente.asesor','direccion','direccion.listaprecios','tipocot','subtipocot','segactual','segactual.tipo')
            ->LEFTJOIN('terceros','terceros.IdTercero','=','cotizaciones.IdTerceroCotizacion')
            ->LEFTJOIN('grupos_cotizaciones','cotizaciones.IdGrupoCotizacion','=','grupos_cotizaciones.IdGrupoCotizaciones')
            ->LEFTJOIN('cotizaciones_tipos','cotizaciones_tipos.IdCotizacionTipo','=','cotizaciones.IdCotizacionTipo')
            ->LEFTJOIN('cotizaciones_subtipos','cotizaciones_subtipos.IdSubTipoCotizacion','=','cotizaciones.IdCotizacionSubTipo')
            ->LEFTJOIN('comentarios_seguimientoscot','cotizaciones.IdSeguimientoActual','=','comentarios_seguimientoscot.IdComentario','cotizaciones.IdCotizacion','=','comentarios_seguimientoscot.IdCotizacion')
            ->LEFTJOIN('direcciones','direcciones.IdDireccion','=','cotizaciones.IdDireccionCotizacion')
            ->LEFTJOIN('lista_precios','lista_precios.IdListaPrecios','=','direcciones.IdListaPreciosDireccion')
            ->LEFTJOIN('ciudades','ciudades.IdCiudad','direcciones.IdCiudad')
            ->where('cotizaciones.IdCotizacion',$IdCot)->first();
            return $Cotizacion;
        }
        catch(Exception $e){
            return null;
        }
    }

    public static function ObtenerCotizacionDet($IdCot){
        try{
            $Sql = "SELECT  cotizaciones_det.IdCotizacionDet, cotizaciones_det.CodCliente,cotizaciones_det.GrupoPlantilla,cotizaciones_det.ItemCliente,cotizaciones_det.DescripcionCliente, cotizaciones_det.MarcaSugerida, cotizaciones_det.UmCompraCliente,
                    cotizaciones_det.Consumo,cotizaciones_det.MesesConsumo,(cotizaciones_det.Consumo / cotizaciones_det.MesesConsumo ) as ConsumoMes,cotizaciones_det.FactorCliente,cotizaciones_det.PrecioTecho,(cotizaciones_det.PrecioCotizacion / cotizaciones_det.FactorCliente) as PrecioOfertaUmCli,
                    cotizaciones_det.ReqMuestras,cotizaciones_det.ReqMuestras,cotizaciones_det.CantMuestras,cotizaciones_det.ComentariosMuestras,'' as ComentariosHm,cotizaciones_det.Alternativa,'' as DctoFinCliente,'' as ClteAplicaFro,cotizaciones_det.Opcion,
                    terceros.NombreCorto as NmProveedor,lineas.NmLinea,grupos.NmGrupo,subgrupos.NmSubGrupo,cotizaciones_det.DescuentoFcieroCot,(CCGenon.CantMinimaCompra * CCGenon.CostoUMM) as MontoMinOCProv,item.Id_Item,marcas.NmMarca,item.Descripcion,
                    ListaDet.CodProveedor,ListaDet.IvaLC,ListaDet.RefFabricante,if(item.Contrato = 1,'SI','NO') as Contrato,'' as VigContrato,if(ListaDet.HabCotizar = 1,'SI','NO') as HabCotizar,ListaDet.CategoriaPortafolio,tiposcompras.Alias as TipoCompra,
                    lista_costos_prov_det.Presentacion,lista_costos_prov_det.UMC,lista_costos_prov_det.FactorCompra,item.UMM,lista_costos_prov_det.CantMinimaVenta,'' as VentaPromMesCliente,'' as VentaProMesAba,item.Disponible,item.Reserva,item.CantOC,item.CantPedido,cotizaciones_det.CantidadCotizacion,
                    CONCAT(lista_costos_prov.NmListaCostos,': ',cotizaciones_det.FhDesdeLista,' - ',cotizaciones_det.FhHastaLista) as NmListaCostos,CCGenon.CostoUMM as CostoGen,'' as CostoEspVig,FORMAT(((CCGenon.CostoUMM - cotizaciones_det.CostoCotizacion) / CCGenon.CostoUMM ) * 100 ,2)as VarCostoGen,
                    cotizaciones_det.CostoCotizacion,cotizaciones_det.PrecioTecho,(((PrecioTecho-CostoCotizacion)/PrecioTecho)*100) as UtilidadVsTecho,cotizaciones_det.Margen,cotizaciones_det.DctoCotizacion,cotizaciones_det.PrecioCotizacion,(((PrecioCotizacion-CostoCotizacion)/PrecioCotizacion)*100) as Utilidad,
                    CONCAT(cotizaciones_det.FhDesdePrecioCot,' - ',cotizaciones_det.FhHastaPrecioCot) as VigenciaOferta, FORMAT(((cotizaciones_det.PrecioCotizacion - cotizaciones_det.PrecioUltimaCot ) / cotizaciones_det.PrecioUltimaCot ) * 100, 2) VarUltCot,cotizaciones_det.PrecioVigCliente,
                    FORMAT(((cotizaciones_det.PrecioCotizacion - cotizaciones_det.PrecioVigCliente ) / cotizaciones_det.PrecioVigCliente ) * 100, 2) VarPrecioVig,cotizaciones_det.VigActListaCliente,cotizaciones_det.PrecioProximo,
                    FORMAT(((cotizaciones_det.PrecioCotizacion - cotizaciones_det.PrecioProximo ) / cotizaciones_det.PrecioProximo ) * 100, 2) VarPrecioProx,cotizaciones_det.VigPrecioProximo,cotizaciones_det.PrecioUltFactura,cotizaciones_det.FhUltimaFactura,
                    FORMAT(((cotizaciones_det.PrecioCotizacion - cotizaciones_det.PrecioUltFactura ) / cotizaciones_det.PrecioUltFactura ) * 100, 2) VarPrecioUltFact,(CantidadCotizacion * lista_costos_prov_det.CostoUMM ) as SubTotalCosto,(CantidadCotizacion * PrecioCotizacion) as SubTotalVenta,
                    '' as ValorIva,'' as TotalIva,if(cotizaciones_det.Revisado = 1,'SI','NO') as Revisado,if(cotizaciones_det.Habilitado = 1,'SI','NO') as Habilitado,cotizaciones_det.ComentarioCotDet
                    FROM cotizaciones_det 
                    LEFT JOIN cotizaciones on cotizaciones.IdCotizacion = cotizaciones_det.IdCotizacion
                    LEFT JOIN lista_costos_prov_det on cotizaciones_det.IdListaCostosDetCot=lista_costos_prov_det.IdListaCostosProvDet
                    LEFT JOIN lista_costos_prov_det as ListaDet on ListaDet.IdListaCostosProvDet = lista_costos_prov_det.IdListaDetReferencia
                    LEFT JOIN tiposcompras on tiposcompras.IdTipoCompra=lista_costos_prov_det.TipoCompra 
                    LEFT JOIN marcas on lista_costos_prov_det.IdMarca=marcas.IdMarca 
                    LEFT JOIN lista_costos_prov on lista_costos_prov.IdListaCostosProv = lista_costos_prov_det.IdListaCostosProv
                    LEFT JOIN lista_costos_prov as ListaProv on ListaProv.IdListaCostosProv = ListaDet.IdListaCostosProv
                    LEFT JOIN terceros on terceros.IdTercero = if(lista_costos_prov_det.IdListaDetReferencia is NULL ,lista_costos_prov.IdTercero,ListaProv.IdTercero)
                    LEFT JOIN item on cotizaciones_det.IdItemCotizacion=item.Id_Item
                    LEFT JOIN lista_costos_prov_det as CCGenon  on CCGenon.IdListaCostosProvDet = item.IdListaCostosDetItem
                    LEFT JOIN lineas on lineas.IdLinea = lista_costos_prov_det.IdLineaLC
                    LEFT JOIN grupos on grupos.IdLinea = lineas.IdLinea and grupos.IdGrupo = lista_costos_prov_det.IdGrupoLC
                    LEFT JOIN subgrupos on subgrupos.IdGrupo = grupos.IdGrupo and subgrupos.IdLinea = lineas.IdLinea and subgrupos.IdSubgrupo = lista_costos_prov_det.IdSubGrupoLC
                    LEFT JOIN sql_precio_cotizacion_item on sql_precio_cotizacion_item.Id_Item = item.Id_Item and sql_precio_cotizacion_item.IdDireccion = cotizaciones.IdDireccionCotizacion
                    LEFT JOIN sql_escalas_cotizaciones on sql_escalas_cotizaciones.IdListacostosDet = if(lista_costos_prov_det.IdListaDetReferencia is not null,lista_costos_prov_det.IdListaDetReferencia,lista_costos_prov_det.IdListaCostosProvDet) and sql_escalas_cotizaciones.FhHasta >= CURDATE()
                    LEFT JOIN kits_det on kits_det.IdKitDet = cotizaciones_det.IdKitCot
                    WHERE cotizaciones.IdCotizacion=" . $IdCot;
            return DB::select($Sql);
        }
        catch(Exception $e){
            return null;
        }
    }

    /**
     * Retorna los tipos de cotizaciones
     */
    public static function TiposCotizaciones(){
        return  DB::select("select * from cotizaciones_tipos order by NmCotizacionTipo");
    }

    /**
     * Retorna los sub-tipos de cotizaciones
     */
    public static function SubTiposcotizaciones(){
        return  DB::select("select * from cotizaciones_subtipos where AplicaHistorico = 1 order by NmSubTipoCotizacion");
    }

    /**
     * Retorna los tipos de seguimiento cotizaciones
     */
    public static function TiposSeguimientos(){
        return  DB::select("select * from tipos_seguimientos_cotizacion order by NmTipoSeguimiento");
    }

    /**
     * Retorna los filtros guardados cuando un usuario filtra en cotizaciones
     */
    public static function ObtenerFiltrosCotizaciones(){
        $FiltrosK = FiltrosCotizaciones::find(\Auth::user()->Usuario);
        return [
            'filtros'=>$FiltrosK
        ];
    }
}