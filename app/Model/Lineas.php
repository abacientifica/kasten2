<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class Lineas extends Model
{
    protected $table ="lineas";
    protected $primaryKey ="IdLinea";
    public $incrementing = false;

    protected $keyType ="string";

    public function grupos()
    {
        return $this->hasMany('\App\Model\Grupos','IdLinea','IdLinea')->orderBy('NmGrupo');
    }
}
