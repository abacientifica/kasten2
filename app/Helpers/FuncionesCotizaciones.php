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