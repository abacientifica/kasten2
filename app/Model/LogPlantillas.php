<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogPlantillas extends Model
{
    protected $table="log_plantillas";
    public $timestamps = false;
    public $primarykey = 'Id';
}