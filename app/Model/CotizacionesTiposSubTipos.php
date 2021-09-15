<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CotizacionesTiposSubTipos extends Model
{
    protected $table="cotizaciones_tipos_subtipos";

    public function tipos(){
        return $this->hasMany('App\Model\CotizacionesTipo','IdTipoCotizacion','IdTipoCotizacion');
    }

    public function subtipos(){
        return $this->hasMany('App\Model\CotizacionesSubTipo','IdSubTipoCotizacion','IdSubTipoCotizacion');
    }
}
