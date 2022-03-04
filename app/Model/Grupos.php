<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Lineas;
use App\Model\SubGrupos;
class Grupos extends Model
{
    protected $table ="grupos";
    protected $primaryKey =["IdGrupo","IdLinea"];
    public $incrementing = false;
    protected $keyType ="string";

    public function subgrupos($idLinea)
    {
        return $this->hasMany(SubGrupos::class,'IdGrupo','IdGrupo')->where('IdLinea',$idLinea);
    }
}
