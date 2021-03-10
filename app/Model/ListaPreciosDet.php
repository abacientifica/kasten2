<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ListaPreciosDet extends Model
{
    protected $table='lista_precios_det';
    protected $fillable = [
        'IdListaPreciosDet',
        'IdListaPrecios',
        'Id_Item',
        'Precio',
        'CodTercero',
        'DescripcionTercero',
        'FhDesde',
        'FhHasta',
        'VencimientoLC',
        'IdUsuario',
        'CantMin',
        'CantMax',
        'Inactivo',
        'FactorVenta',
        'UMV',
        'DctoListaPrecio',
        'IdCotizacionDet',
        'IdListaCostosDet',
        'IdListaPreciosDetRef',
        'IdCategoriaEstado',
        'IdContratoEnlace',
        'PrecioProximo',
        'FhDesdeProximo',
        'FhHastaProximo',
        'IdCotDetProximo',
        'IdEstadoProximo',
        'UMCompraCliente',
        'FCCompraCliente',
        'CantMinVta',
        'IdKit',
    ];
}
