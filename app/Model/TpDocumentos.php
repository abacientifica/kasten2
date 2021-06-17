<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TpDocumentos extends Model
{
    protected $table="tpdocumentos";
    protected $primaryKey ='IdTpDocumento';

    public function documentos(){
        return $this->hasMany('App\Model\Documentos','Tp','IdTpDocumento');
    }
}
