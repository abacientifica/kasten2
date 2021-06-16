<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionColumasDocumentos extends Model
{
    protected $table="configuraciones_columnas_documentos";

    protected $primaryKey ="IdConfiguracion";

    public function detalles(){
        return $this->hasMany('App\Model\ConfiguracionColumasDocumentosDet','IdConfiguracion','IdConfiguracion');
    }
}
