<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
    public $timestamps = false;
    protected $primaryKey ='IdDocumento';
    protected $fillable = [
        'IdDocumento',
        'Tp',
        'Modulo',
        'Operacion',
        'OpValor',
        'Nombre',
        'DocControl',
        'AfectaCantRef',
        'AfectaCantDev',
        'NmCantRef',
        'AfectaBodLote',
        'AfectaBodDestino',
        'AfectaCant',
        'AfectaInventarios',
        'AfectaCostos',
        'AfectaPrecios',
        'AfectaReserva',
        'AfectaCantDespachadasContrato',
        'AfectaCantFacturadasContrato',
        'ExigeTercero',
        'ExigeProveedor',
        'ExigeCliente',
        'Consecutivo',
        'ModificaTotales',
        'NmCantSopAfecta',
        'AfectaReservaEnlace',
        'AfectaTraslado',
        'ControlConsecutivos',
        'TransaccionIntercambio',
        'TpAsiento',
        'TpAsientoRet',
        'TrDestino',
        'PrefijoIlimitada',
        'Comprobante',
        'DocComodato',
        'TipoSeguridad',
        'DocumentoLogistica',
        'ValidaSaldoCartera',
        'AplicaEstadoCuenta',
        'AbreviaturaDocumento',
        'ControlInterno',
        'GeneraCostoPromedio',
        'AsignarCostoPromedio',
        'ValidaContratoObligatorio',
        'ImprimePrefijo',
    ];
}
