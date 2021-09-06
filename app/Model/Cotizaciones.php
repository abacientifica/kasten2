<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    protected $table = "cotizaciones";
    protected $primaryKey ="IdCotizacion";
    public $timestamps = false;

    public function cliente(){
        return $this->hasOne('App\Model\Terceros','IdTercero','IdTerceroCotizacion');
    }

    public function direccion(){
        return $this->hasOne('App\Model\Direcciones','IdDireccion','IdDireccionCotizacion');
    }

    public function tipocot(){
        return $this->hasOne('App\Model\CotizacionesTipo','IdCotizacionTipo','IdCotizacionTipo');
    }

    public function subtipocot(){
        return $this->hasOne('App\Model\CotizacionesSubTipo','IdSubTipoCotizacion','IdCotizacionSubTipo');
    }

    public function segactual(){
        return $this->hasOne('App\Model\SeguimientosCotizaciones','IdComentario','IdSeguimientoActual');
    }
}
