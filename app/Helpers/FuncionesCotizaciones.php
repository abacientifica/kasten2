<?php
namespace App\Helpers;
  
use Illuminate\Support\Facades\DB;
use App\Model\Cotizaciones;
use App\Model\CotizacionesDet;
use App\Model\FiltrosCotizaciones;
use App\Task\ConvertirFiltrosCotizaciones;
use Illuminate\Pipeline\Pipeline;

class FuncionesCotizaciones {

    public static function ObtenerListaCotizaciones($fiterdata=null,$limiteDatos=100){
        $Cotizaciones = Cotizaciones::select('cotizaciones.*','formulario_cotizacion.UsuarioSolicita')
        ->with('cliente','direccion','direccion.listaprecios','tipocot','subtipocot','segactual','segactual.tipo')
        ->LEFTJOIN('terceros','terceros.IdTercero','=','cotizaciones.IdTerceroCotizacion')
        ->LEFTJOIN('grupos_cotizaciones','cotizaciones.IdGrupoCotizacion','=','grupos_cotizaciones.IdGrupoCotizaciones')
        ->LEFTJOIN('cotizaciones_tipos','cotizaciones_tipos.IdCotizacionTipo','=','cotizaciones.IdCotizacionTipo')
        ->LEFTJOIN('cotizaciones_subtipos','cotizaciones_subtipos.IdSubTipoCotizacion','=','cotizaciones.IdCotizacionSubTipo')
        ->LEFTJOIN('comentarios_seguimientoscot','cotizaciones.IdSeguimientoActual','=','comentarios_seguimientoscot.IdComentario','cotizaciones.IdCotizacion','=','comentarios_seguimientoscot.IdCotizacion')
        ->LEFTJOIN('direcciones','direcciones.IdDireccion','=','cotizaciones.IdDireccionCotizacion')
        ->LEFTJOIN('lista_precios','lista_precios.IdListaPrecios','=','direcciones.IdListaPreciosDireccion')
        ->JOIN('formulario_cotizacion' ,'formulario_cotizacion.IdCotizacion','=','cotizaciones.IdCotizacion')
        ->LEFTJOIN('tipos_seguimientos_cotizacion','tipos_seguimientos_cotizacion.IdTipoSeguimiento','=','comentarios_seguimientoscot.TipoSeguimiento');
        $FiltrosK = FiltrosCotizaciones::find(\Auth::user()->Usuario);
        $pipe = app(Pipeline::class);
        $Cotizaciones = $pipe->send([$fiterdata,$Cotizaciones])->through([
            ConvertirFiltrosCotizaciones::class
        ])->thenReturn();
        $Cotizaciones = $Cotizaciones->OrderBy('NmTipoSeguimiento','ASC')->OrderBy('FechaCotizacion','DESC')->take($limiteDatos)->get();
        return $Cotizaciones ? $Cotizaciones : [];
    }

    public static function TiposCotizaciones(){
        return  DB::select("select * from cotizaciones_tipos order by NmCotizacionTipo");
    }

    public static function SubTiposcotizaciones(){
        return  DB::select("select * from cotizaciones_subtipos where AplicaHistorico = 1 order by NmSubTipoCotizacion");
    }

    public static function TiposSeguimientos(){
        return  DB::select("select * from tipos_seguimientos_cotizacion order by NmTipoSeguimiento");
    }
}