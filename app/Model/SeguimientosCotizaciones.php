<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SeguimientosCotizaciones extends Model
{
    protected $table ="comentarios_seguimientoscot";
    protected $primaryKey ="IdComentario";

    public function tipo(){
        return $this->hasOne('App\Model\TiposSeguimientosCotizacion','IdTipoSeguimiento','TipoSeguimiento');
    }
}
