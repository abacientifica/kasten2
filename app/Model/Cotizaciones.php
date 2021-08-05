<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    protected $table = "cotizaciones";
    protected $primaryKey ="IdCotizacion";
    public $timestamps = false;
}
