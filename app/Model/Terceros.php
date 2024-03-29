<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Terceros extends Model
{
    protected $table="terceros";
    protected $primaryKey="IdTercero";
    
    protected $fillable = [
        'IdTercero',
        'Identificacion',
        'TpIdentificacion',
        'DigitoVerificacion',
        'FhCreacion',
        'NombreCorto',
        'NombreExtendido',
        'Abreviatura',
        'Nombre',
        'Nombre2',
        'Apellido1',
        'Apellido2',
        'Direccion',
        'Telefono',
        'Fax',
        'Email',
        'IdCiudad',
        'TpRelacion',
        'IdListaPrecios',
        'IdListaPreciosAlternativa',
        'Id_Lista_Costos_Prov',
        'Proveedor',
        'Proveedor1',
        'Cliente',
        'Contacto',
        'CargoContacto',
        'IdAsesor',
        'IdAsesorServicliente',
        'LimiteCredito',
        'LimiteCreditoExtra',
        'PlazoPago',
        'PlazoPagoProveedor',
        'IdFormaPago',
        'IdFormaPagoProveedor',
        'DctoFijo',
        'PorDctoFin',
        'DiasDctoFin',
        'DctoFijoCompra',
        'AdmitePrestamos',
        'AdmiteListaPreciosGeneral',
        'TiempoEntregaMin',
        'CambiarPrecio',
        'HorarioRecibo',
        'Inactivo',
        'BloqueadoCartera',
        'Autoretenedor',
        'Comentarios',
        'NotPedidoNuevo',
        'Husi',
        'IdCondicionEntrega',
        'NoRequiereAlistamiento',
        'SaldoCartera',
        'LiquidaRteFte',
        'PorRteIva',
        'PorIndCom',
        'DctoFijoRecibos',
        'PorOtrosRecibos',
        'PorProHospitales',
        'PorProDesarrollo',
        'PorEstampillas',
        'PorProAncianos',
        'TiempoEntregaProv',
        'VrCompraMinima',
        'PresupuestoCliente',
        'IdTpOrden',
        'IdClasificacionTributaria',
        'RteFteVenta',
        'RteFteVentaSinBase',
        'DctoFroPredProveedor',
        'CanalSISMED',
        'TpCliente',
        'ExentoIva',
        'AplicaGenerarRequerimientoCompras',
        'ImpresionFacturaDescripcionCliente',
        'ValidarPrecios',
        'ValidarContrato',
        'PedidosPendientes',
        'IdActividadEconomica',
        'ProveedorTop',
        'EximeBloqueoAutomatico',
        'UsuarioCrea',
        'PlazoAviso',
        'OrdenarImpresionFacturaDescripcion',
        'ConceptoCartera',
        'ParetoCartera',
        'EspectativaRecaudo',
        'TipoBloqueo',
        'ComentariosBloqueoComercial',
        'ImprimeRemisionConPrecios',
        'DespachoMinimo',
        'AbaEnvioInfoFraElectronica',
        'LinkFraElectronica',
        'UsuarioFraElectronica',
        'PaswFraElectronica',
        'EmailRecepcionFraElectronica',
        'ContactoRecepcionFraElectronica',
        'EnviarFraFisica',
        'AplicaRequerimiento',
        'ImprimirFacNeto',
    ];

    public function direcciones(){
        return $this->hasMany('App\Model\Direcciones','IdTercero','IdTercero');
    }

    public function asesorservcliente(){
        return $this->hasOne('App\Model\Asesores','IdAsesor','IdAsesorServicliente');
    }

    public function asesor(){
        return $this->hasOne('App\Model\Asesores','IdAsesor','IdAsesor');
    }
}
