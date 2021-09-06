<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CotizacionesTipo extends Model
{
    public $timestamps = false;
    protected $table ='cotizaciones_tipos';
    protected $primaryKey ='IdCotizacionTipo';
}
