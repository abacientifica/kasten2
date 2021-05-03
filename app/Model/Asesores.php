<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Asesores extends Model
{
    protected $table='asesores';
    public $timestamps = false;
    public $primarykey = 'IdAsesor';
    protected $fillable = [
        'Identificacion',
        'Nombre',
        'Telefono',
        'Email',
        'UsuarioAsesor',
        'PorComVenta',
        'PorComRecaudo',
        'AsesorServicioCliente',
        'Inactivo',
    ];
}
