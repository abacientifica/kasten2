<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CotizacionesSubTipo extends Model
{
    public $timestamps = false;
    protected $table ='cotizaciones_subtipos';
    protected $primaryKey ='IdSubTipoCotizacion';
}
