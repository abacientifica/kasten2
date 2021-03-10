<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ConfigDocumentosDet extends Model
{
    protected $table="config_documentos_det";
    public $timestamps = false;
    public $primarykey = ['Id','IdConfig'];
}
