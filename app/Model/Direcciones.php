<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Direcciones extends Model
{
    protected $primaryKey ='IdDireccion';
    
    protected $fillable = [
        'IdDireccion',
        'IdTercero',
        'NmDireccion',
        'Direccion',
        'IdListaPreciosDireccion',
        'IdRutaDireccion',
        'IdCiudad',
        'IdZona',
        'IdBarrio',
        'Cuadra',
        'Manzana',
        'Contacto1',
        'Contacto2',
        'Tel1',
        'Tel2',
        'Email1',
        'Email2',
        'Comentarios',
        'Orden',
        'Inactiva',
        'Tipo',
        'CobraFlete',
        'HoraRecibo',
    ];
}
