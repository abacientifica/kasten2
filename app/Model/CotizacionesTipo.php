<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\CotizacionesTiposSubTipos;
class CotizacionesTipo extends Model
{
    public $timestamps = false;
    protected $table ='cotizaciones_tipos';
    protected $primaryKey ='IdCotizacionTipo';

    public function tiposubtipo(){
        return $this->hasMany(CotizacionesTiposSubTipos::class,'IdTipoCotizacion','IdCotizacionTipo');
    }
}
