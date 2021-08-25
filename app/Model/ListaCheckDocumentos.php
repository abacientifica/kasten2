<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ListaCheckDocumentos extends Model
{
    protected $table = 'lista_check_documentos';
    protected $primaryKey ='Id_Check';
    public $timestamps = false;
}
