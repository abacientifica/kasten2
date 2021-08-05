<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogCotizaciones extends Model
{
    protected $table = "log_cotizaciones";
    protected $primaryKey ="IdLog";
    public $timestamps = false;
}
