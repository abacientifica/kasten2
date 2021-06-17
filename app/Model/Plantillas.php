<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Plantillas extends Model
{
    protected $table = "plantillas";

    protected $primaryKey ='IdPlantilla';

    public $timestamps = false;
}
