<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ListaCostosProvDet extends Model
{
    protected $table ="lista_costos_prov_det";
    protected $primaryKey ='IdListaCostosProvDet';
    public $timestamps = false;

    public function marca(){
        return $this->hasOne('App\Model\Marcas','IdMarca','IdMarca');
    }
}
