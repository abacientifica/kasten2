<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey ='Id_Item';
    public $timestamps = false;
    protected $fillable = [
        'Id_Item',
        'Descripcion',
        'Descripcion2',
        'IdLinea',
        'IdGrupo',
        'IdSubgrupo',
        'CCostos',
        'IdDepartamento',
        'UMM',
        'CostoPrimer',
        'CostoReferencia',
        'CostoUlt',
        'CostoProm',
        'CostoPred',
        'StockMin',
        'StockMax',
        'DiasStockMin',
        'IdUbicacion',
        'IdProveedor',
        'Cod_Barras',
        'GeneraSticker',
        'Maneja_Lote',
        'Por_Iva',
        'DctoPromocion',
        'CUM',
        'PesoKilos',
        'PesoVolumen',
        'ExComodato',
        'ExInventario',
        'Existencia',
        'Remisionada',
        'Reserva',
        'CantOC',
        'CantPedido',
        'Disponible',
        'Inactivo',
        'IdOrigen',
        'Comentarios',
        'AfectaInventario',
        'RequiereAutFV',
        'ManStock',
        'CtaInventario',
        'CtaCosto',
        'CtaIngreso',
        'CtaDevolucion',
        'CtaCompras',
        'CtaDevolucionCompras',
        'Medicamento',
        'IdListaCostosDetItem',
        'EnNovedad',
        'PosicionArancelaria',
        'FechaUltimaFactura',
        'NoValidarVigenciaPrecio',
        'ProvisionCliente',
        'Kit',
        'BajaRotacion',
        'NoAsignarPrecioImpresionFactura',
        'Contrato',
        'AplicaForecast',
        'ConsComodato',
    ];

    public function listacostosdet(){
        return $this->hasOne('App\Model\ListaCostosProvDet','IdListaCostosProvDet','IdListaCostosDetItem');
    }
}
