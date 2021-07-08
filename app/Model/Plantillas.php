<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Plantillas extends Model
{
    protected $table = "plantillas";

    protected $primaryKey ='IdPlantilla';

    public $timestamps = false;

    public function tercero(){
        return $this->hasOne('App\Model\Terceros','IdTercero','IdTerceroPlant');
    }
    
    public function direccion(){
        return $this->hasOne('App\Model\Direcciones','IdDireccion','IdDireccionPlant');
    }

    public function plantillasdet(){
        return $this->hasMany('App\Model\PlantillasDet','IdPlantilla','IdPlantilla');
    }
}
