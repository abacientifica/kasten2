<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ver Cotizacion </h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item"><router-link to="/cotizaciones/index">Cotizaciones</router-link></li>
                        <li class="breadcrumb-item active">Cotizacion {{this.$attrs.id}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid" v-if="fillCotizacion">
            <div class="card">
                <div class="card-header bg-info">
                    <b class="aling-left" v-if="fillCotizacion" v-text="fillCotizacion.cliente.IdTercero+' '+fillCotizacion.cliente.NombreCorto"></b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <template v-if="validarPermiso('crear')">
                                    <nuevacotizacion :titulo="'Nueva Cotización'"></nuevacotizacion>
                                </template>
                                <router-link class="btn btn-info btn-sm" :to="{name:'cotizaciones.index'}">
                                    <i class="fas fa-arrow-left"></i> Regresar
                                </router-link>
                                <modal :titulo="'Ayudas Cotizaciones'" :iddoc="2" :url="'cotizaciones.index'"></modal>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 btn-group-justified"  style="display:flex" >
                        <logacciones class="btn-margin-left" :IdMovimiento ="fillCotizacion.IdCotizacion" :IdDocumento="2"></logacciones>
                        <button class="btn btn-success btn-sm btn-margin-left" v-if="fillCotizacion.Estado!='ANULADA'" @click.prevent="dialogAcciones = !dialogAcciones"><i class="fas fa-grip-horizontal"></i> Menu Acciones</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-if="!ocultarPanel" @click.prevent="ocultarPanelEncabezado()"><i class="fas fa-eye"></i> Mostrar Encabezado</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-else @click.prevent="ocultarPanelEncabezado()"><i class="fas fa-eye-slash"></i> Ocultar Encabezado</button>
                        
                        <!--Botones de acciones--> 
                        <el-dialog
                            title="Acciones Plantillas"
                            :visible.sync="dialogAcciones"
                            center>
                            <span>Recuerda que cada acción que se realice es responsabilidad del usuario logueado y esta queda registrada.</span>
                            <table class="acciones table-responsive bg-3">
                                
                                <tbody>
                                    <tr>
                                    <th scope="row">1</th>
                                    <td>
                                        <editarcotizacion :titulo="'Editar Cotización'" :cotizacion="fillCotizacion"></editarcotizacion>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>
                                                Autoriza  plantilla pero no los detalles.
                                            </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('autorizar') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="Autorizar"><i class="fas fa-check-circle"></i> Autorizar</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Desautoriza  plantilla pero no los detalles. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('desautorizar') && fillCotizacion.Estado=='AUTORIZADA') ? false : true" @click.prevent="DesAutorizar"> <i class="fas fa-minus-circle"></i> Des Autorizar</el-button>
                                        </vs-tooltip>
                                    </td>
                                    </tr>
                                    <tr>
                                    <th scope="row">2</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Anula la plantilla actual. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('anular') && fillCotizacion.Estado=='AUTORIZADA')? false : true" @click.prevent="Anular"> <i class="fas fa-ban"></i> Anular</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para homologar datos de una cotización o plantilla anterior. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('homologar') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalHomologar = true"> <i class="fas fa-link"></i> Homologar</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para enlazar los detalles a la lista principal del proveedor </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('restablecercostos') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="RestablecerCostos"> <i class="fas fa-retweet"></i>Restablecer Costos</el-button>
                                        </vs-tooltip>
                                    </td>
                                    </tr>
                                    <tr>
                                    <!--<th scope="row">3</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para crear una plantilla con los mismos datos. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('duplicar')) ? false : true"><i class="fas fa-clone"></i> Duplicar Plantilla</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para eliminar items de la plantilla. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('eliminar') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="EliminarDetallesSel" ><i class="fas fa-trash-alt"></i>Eliminar Items</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para crear o enviar items a una cotización </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('crearcot') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalCotizacion = true"><i class="fas fa-align-justify"></i> Crear Cotización</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <tr>
                                    <th scope="row">4</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para autorizar masivamente items de la plantilla. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('autorizaritems') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AutorizarDetalles()"><i class="fas fa-edit"></i> Autorizar Items</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para editar los datos principales de la plantilla. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('desautorizaritems') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="DesAutorizarDetalles()"><i class="fas fa-minus-circle"></i>Desautorizar Items</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para marcar los items que se han vendido en un rango de fecha. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('vendidos') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click="AbriModalItemsVendidos = true"><i class="fas fa-chart-line"></i>Marcar Items Vendidos</el-button>
                                        </vs-tooltip>
                                    </td>
                                    </tr>
                                    <tr>
                                    <th scope="row">4</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Sirve para importar una lista en formato cvs </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('importar') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalImportarItems = true"><i class="fas fa-align-justify"></i> Importar Listado</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Calcula el factor </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('calcularfactor') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalCalcFactor = true"><i class="fas fa-calculator"></i> Calcular Factor</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Corre factores de otras plantillas donde coincida la unidad. </template>
                                            <el-button type="primary" round :disabled="(validarPermiso('correrfactores') && fillCotizacion.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalCorrerFactores = true"><i class="fas fa-running"></i> Correr Factores</el-button>
                                        </vs-tooltip>
                                    </td>-->
                                    </tr>
                                </tbody>
                            </table>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="warning" @click="dialogAcciones = false">Cerrar</el-button>
                            </span>
                        </el-dialog>
                        <!--Fin Botones Acciones-->
                        <el-dialog title="Información Ampliada Cotización" :visible.sync="infoAmpliada">
                            <el-collapse>
                                <el-collapse-item title="Información General Cotización" name="general">
                                    <el-descriptions class="margin-top"  :column="2" size="small" border>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Id Cotización
                                            </template>
                                            {{fillCotizacion.IdCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Nro. Cotizacion
                                            </template>
                                            {{fillCotizacion.NroCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Nombre
                                            </template>
                                            {{fillCotizacion.NmCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Tipo
                                            </template>
                                            {{fillCotizacion.tipocot.NmCotizacionTipo+' - '+fillCotizacion.subtipocot.NmSubTipoCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Estado
                                            </template>
                                            {{fillCotizacion.Estado}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Vig. Oferta Hasta
                                            </template>
                                            {{fillCotizacion.FechaOfertaHasta}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Vigencia $ 
                                            </template>
                                            {{moment(fillCotizacion.FhDesde).format('MM DD YYYY')}} hasta {{moment(fillCotizacion.FhHasta).format('MM DD YYYY')}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Creacion
                                            </template>
                                            {{moment(fillCotizacion.FechaCreacion).format('MMMM DD YYYY , h:mm:ss a') +', Usuario :'+ fillCotizacion.Usuario}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Imp.
                                            </template>
                                            {{fillCotizacion.Impreso}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Grupo
                                            </template>
                                            {{fillCotizacion.IdGrupoCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Cond. Devolución
                                            </template>
                                            {{fillCotizacion.CondDevolucion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Soporte
                                            </template>
                                            {{fillCotizacion.Soporte}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                SubTotal
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.SubTotal)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Iva
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.VrIva)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Total
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.Total)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Descuento
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.VrDescuento)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Proyectado
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.TotalProyectado)}}
                                        </el-descriptions-item>
                                    </el-descriptions>
                                </el-collapse-item>
                                <el-collapse-item title="Condiciones de Venta" name="condventa">
                                    <el-descriptions class="margin-top"  :column="2" size="small" border>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Plazo Neto 
                                            </template>
                                            {{fillCotizacion.cliente.PlazoPago +' días'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Plazo Cot 
                                            </template>
                                            {{fillCotizacion.Plazo +' días'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Dcto. Fro. Actual 
                                            </template>
                                            {{dctosFinancierosCliente.PorDcto +' %'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Dcto. Fro. Cot 
                                            </template>
                                            {{fillCotizacion.DctoFin +' %'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Plazo Dcto Act 
                                            </template>
                                            {{dctosFinancierosCliente.DiasPago +' días'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Plazo Dcto Cot 
                                            </template>
                                            {{fillCotizacion.DiasDctoFin +' días'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Cupo Credito
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.cliente.LimiteCredito)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Cupo Extra
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.cliente.LimiteCreditoExtra)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                            Saldo Cartera
                                            </template>
                                            {{servicios.FormatoMoneda(fillCotizacion.DiasDctoFin)}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Otros Dctos. Rtes
                                            </template>
                                            {{(fillCotizacion.cliente.PorRteIva + fillCotizacion.cliente.PorIndCom + fillCotizacion.cliente.PorOtrosRecibos + fillCotizacion.cliente.PorProHospitales + fillCotizacion.cliente.PorProDesarrollo + fillCotizacion.cliente.PorEstampillas + fillCotizacion.cliente.PorProAncianos) + '%'}}
                                        </el-descriptions-item>
                                    </el-descriptions>
                                </el-collapse-item>
                                <el-collapse-item title="Datos Cliente" name="datoscliente">
                                <el-descriptions class="margin-top"  :column="2" size="small" border>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Cliente
                                            </template>
                                            {{fillCotizacion.cliente.IdTercero+' '+fillCotizacion.cliente.NombreCorto}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Dirección
                                            </template>
                                            {{fillCotizacion.direccion.IdDireccion+' '+fillCotizacion.direccion.Direccion+' '+fillCotizacion.direccion.NmDireccion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Ciudad
                                            </template>
                                            {{fillCotizacion.NmCiudad}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Valida Precios
                                            </template>
                                            {{fillCotizacion.cliente.ValidarPrecios == 1 ? 'SI':'NO'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                            Estado 
                                            </template>
                                            {{fillCotizacion.cliente.Inactivo == 1 ? 'Inactivo':'Activo'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Bloqueado x Cartera
                                            </template>
                                            {{fillCotizacion.cliente.BloqueadoCartera == 1 ? 'SI':'NO'}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Contacto
                                            </template>
                                            {{fillCotizacion.ContactoCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Email Cotizacion
                                            </template>
                                            {{fillCotizacion.EmailCotizacion}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                            Asesor 
                                            </template>
                                            {{fillCotizacion.cliente.asesorservcliente.Nombre+' - '+fillCotizacion.cliente.asesor.Nombre}}
                                        </el-descriptions-item>
                                        <el-descriptions-item>
                                            <template slot="label">
                                                Lista Precios
                                            </template>
                                            {{fillCotizacion.direccion.listaprecios.NmListaPrecios}}
                                        </el-descriptions-item>
                                    </el-descriptions>
                                </el-collapse-item>
                            </el-collapse>
                            <span slot="footer" class="dialog-footer">
                                <el-button @click="infoAmpliada = false">Cerar</el-button>
                            </span>
                        </el-dialog>
                        <div>
                            <vs-tooltip>
                                <template #tooltip>
                                    Esta opcion configura las columnas según las acciones del listado.
                                </template>
                                <el-select v-model="grillaSeleccionada" size="mini" placeholder="Predeterminada">
                                <el-option
                                    v-for="item in configuracionesGrilla"
                                    :key="item.id"
                                    :label="item.Descripcion"
                                    :value="item.id"
                                    >
                                </el-option>
                            </el-select>
                            </vs-tooltip>
                        </div>
                        
                    </div><hr>
                    <div class="form-group row border" v-if="ocultarPanel">
                        
                          <el-descriptions class="margin-top"  :column="4" direction="vertical">
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Id Cot
                                    </template>
                                    {{fillCotizacion.IdCotizacion}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Nro. Cot
                                    </template>
                                    {{fillCotizacion.NroCotizacion}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Nombre
                                    </template>
                                    {{fillCotizacion.NmCotizacion}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Dirección
                                    </template>
                                    {{fillCotizacion.direccion['Direccion']}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Fecha
                                    </template>
                                    {{moment(fillCotizacion.FechaCotizacion).format('MMMM DD YYYY, h:mm:ss a')}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Vig. Oferta
                                    </template>
                                    {{moment(fillCotizacion.FhDesde).format('MMM DD YYYY')}} hasta {{moment(fillCotizacion.FhHasta).format('MMM DD YYYY')}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Fh. Oferta Hasta
                                    </template>
                                    {{moment(fillCotizacion.FechaOfertaHasta).format('MMMM DD YYYY')}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Valida Precios
                                    </template>
                                    {{fillCotizacion.cliente.ValidarPrecios == 1 ? 'SI':'NO'}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Estado Cliente
                                    </template>
                                    {{(fillCotizacion.cliente.Inactivo == 1 ? 'Inactivo':'Activo')}}
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Ver mas
                                    </template>
                                    <button class="btn btn-secondary btn-sm" @click.prevent="infoAmpliada=true"><i class="fas fa-external-link-square-alt"></i> Ampliar</button>
                                </el-descriptions-item>
                                <el-descriptions-item label-class-name="titulo-descripcion">
                                    <template slot="label">
                                        Comentarios
                                    </template>
                                    {{fillCotizacion.Comentarios}}
                                </el-descriptions-item>
                          </el-descriptions>
                        <!--<div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Id Cot</label>
                                <p class="p-encabezado" v-text="fillCotizacion.IdCotizacion"></p>
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Nro. Cot</label>
                                <p class="p-encabezado" v-text="fillCotizacion.NroCotizacion"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Nombre Plantilla</label>
                                <p class="p-encabezado" v-text="fillCotizacion.NmCotizacion"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Dirección</label>
                                <p class="p-encabezado" v-if="fillCotizacion.direccion" v-text="fillCotizacion.direccion['Direccion']"></p>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha</label>
                                <p class="p-encabezado">{{moment(fillCotizacion.FechaCotizacion).format('MMMM DD YYYY, h:mm:ss a')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Vig. Oferta</label>
                                <p class="p-encabezado">{{moment(fillCotizacion.FhDesde).format('MMM DD YYYY')}} hasta {{moment(fillCotizacion.FhHasta).format('MMM DD YYYY')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fh. Oferta Hasta</label>
                                <p class="p-encabezado">{{moment(fillCotizacion.FechaOfertaHasta).format('MMMM DD YYYY')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Estado</label>
                                <p class="p-encabezado" v-text="fillCotizacion.Estado"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Valida Precios</label>
                                <p class="p-encabezado" >{{fillCotizacion.cliente.ValidarPrecios == 1 ? 'SI':'NO'}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Estado Cliente</label>
                                <p class="p-encabezado" >{{(fillCotizacion.cliente.Inactivo == 1 ? 'Inactivo':'Activo')}}</p>
                            </div>
                        </div>
                        

                        <div class="col-md-5">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Comentarios</label>
                                <p class="p-encabezado" v-text="fillCotizacion.Comentarios"></p>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-secondary btn-sm" @click.prevent="infoAmpliada=true"><i class="fas fa-external-link-square-alt"></i> Ampliar</button>
                        </div>-->
                    </div>

                    <!--Tabla Cotizaciones-->
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <ag-grid-vue
                                class="ag-theme-alpine"
                                :style="{width,height }"
                                @gridReady="onGridReady"
                                @firstDataRendered="onFirstDataRendered"
                                :gridOptions="gridOptions"
                                :localeText="localeText"
                                :columnDefs="columnDefs"
                                :rowData="rowData"
                                :rowSelection="rowSelection"
                                :autoGroupColumnDef="false"
                                :editType="editType"
                                :pagination="true"
                                :sideBar="sideBar"
                                :paginationPageSize="paginationPageSize"
                                :tooltipShowDelay="tooltipShowDelay"
                                @cell-value-changed="cambioDatosGrilla"
                                @row-value-changed="cambioValorCeldaGRilla"
                                @selection-changed="cambioSeleccionItems">
                            </ag-grid-vue>
                        </div>
                    </div>
                    <!--Fin Tabla Cotizaciones-->
                </div>
            </div>
        </div>
        <cot-homologar :visible="dialogHomologar"></cot-homologar>
    </div>
</template>
<script>
//Componente grilla
import "ag-grid-community/dist/styles/ag-grid.css";
import "ag-grid-community/dist/styles/ag-theme-alpine.css";
import 'ag-grid-enterprise';
import { AgGridVue } from "ag-grid-vue";
//Servicios
import serviceApp from "../../../ServicesApp";
import serviciosCotizaciones from "../../servicios/serviciosCotizaciones";
const SERVICIOS_REST = new serviceApp();
const SERVICIOS_REST_COT = new serviciosCotizaciones();
//Componentes
import Novedades from '../../plantilla/plantillasclientes/Novedades.vue';
import vSelect from "vue-select";
import { mapActions, mapGetters, mapState } from 'vuex'
export default {
    components: {
        AgGridVue,
        "v-select": vSelect,
        'novedades':Novedades
    },
    data() {
        return {
            //Methodos de servicioos
            serviciosCot:SERVICIOS_REST_COT,
            //Configuraciones Grilla
            width:'100%',
            height:(window.innerHeight - 150)+'px',
            localeText : SERVICIOS_REST.traducirTextosAggrid(),
            gridOptions :{
                onFilterChanged:this.cambioFiltros,
                onColumnMoved:this.cambioColumnas,
                onColumnVisible:this.cambioColumnas,
                onSortChanged:this.cambioColumnas,
                onColumnPinned:this.cambioColumnas,
            },
            gridApi:null,
            gridColumnApi:null,
            defaultColDef : {
                flex: 1,
                minWidth: 100,
                enableValue: true,
                enableRowGroup: true,
                enablePivot: true,
                sortable: true,
                filter: true,
            },
            mantenerFiltros:false,
            configuracionesGrilla:null,
            configuracionesGrillaDet:null,
            configuracionPredeterminada:null,
            grillaSeleccionada:localStorage.getItem('grilla_sel_cot') ? parseInt(localStorage.getItem('grilla_sel_cot')) :null,
            columnDefs:[],
            rowData:[],
            rowSelection : 'multiple',
            editType : 'fullRow',
            sideBar : {
                toolPanels: [{
                        id: 'columns',
                        labelDefault: 'Columnas',
                        labelKey: 'columns',
                        iconKey: 'columns',
                        toolPanel: 'agColumnsToolPanel',
                        toolPanelParams: {
                            suppressPivotMode: true,
                            suppressRowGroups: true,
                            suppressValues: true,
                        }
                    },
                    {
                        id: 'filters',
                        labelDefault: 'Filtros',
                        labelKey: 'filters',
                        iconKey: 'filter',
                        toolPanel: 'agFiltersToolPanel',
                    },
                ],
                position: 'left',
                defaultToolPanel: 'filters',
                hiddenByDefault: false,
            },
            paginationPageSize:100,
            tooltipShowDelay:null,
            DatoEditado:[],
            itemsSeleccionados:[],
            respuestaCargaInicial:null,
            //Fin Configuracion grilla
            permisosUsuario:[],
            fillCotizacion:null,
            dctosFinancierosCliente:null,
            servicios:SERVICIOS_REST,
            ocultarPanel:false,
            dialogAcciones:false,
            moment:moment,
            //Variables dialogs
            infoAmpliada:false,
            dialogHomologar:null,
            datosHomologar:null,
        }
    },

    watch:{
        grillaSeleccionada(newval,old){
            const nmConfig = (id) =>{
                if(!id) return 'Predeterminada';
                let desc = this.configuracionesGrilla.find(el => el.id === id);
                return desc.Descripcion;
            }
            const convertColumns = (arrColumnas)=>{
                let arrColumnasNew = []
                arrColumnas.map(el=>{
                    arrColumnasNew.push(
                        {
                            'columna':el.IdCampo,
                            'alias':el.AliasCampo,
                            'pinned':el.pinned,
                            'FormatoCelda':el.FormatoCelda,
                            'ancho':el.Ancho,
                            'edit':el.editable,
                            'visible':el.Visible,
                            'filtro':el.filtro,
                            'permiso':el.PermisoEditar,
                            'permiso_ver':el.PermisoVer,
                            'float':el.Float,
                            'tipoEdicion':el.TipoEdicion,
                            'tipoFiltro':el.TipoFiltro
                        }
                    )
                })
                return arrColumnasNew;
            }
            if(old || old === null){
                this.$confirm(`Estas seguro(a) de cambiar la configuración ${nmConfig(old)} por ${nmConfig(newval)}`, 'Warning', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    this.grillaSeleccionada = newval;
                    localStorage.removeItem('columnas_cot_det');
                    localStorage.setItem('grilla_sel_cot',Number(this.grillaSeleccionada));
                    let columnas = []
                    columnas = this.configuracionesGrillaDet.filter(el => {return  el.id === newval });
                    columnas =  convertColumns(columnas.sort((a,b)=>{ return  a.IdOrden - b.IdOrden}));
                    this.cargarColumnas(columnas);
                    this.$message({
                        type: 'success',
                        message: 'Configuración aplicada, valida las columnas.'
                    });
                }).catch((error) => {
                    console.log(error)
                    //this.grillaSeleccionada = old;
                });
            }
        }
    },

    

    methods: {
        ...mapActions('OpcionesModalCot', ['abrirModal','cerrarModal']),

        abrirModalHomologar(params){
            let datosfiltrados = [];
            this.gridApi.forEachNodeAfterFilterAndSort(function(node, index) {
                datosfiltrados.push(node)
            })
            this.abrirModal([params,datosfiltrados])
            console.log(this.$store.state.OpcionesModalCot.modalHomologarCotDet)
        },

        accionesDetalle(params){
            //console.log(params);
            this.abrirModalHomologar(params);
            /*this.dialogHomologar = !this.dialogHomologar
            this.datosHomologar = params.node;*/
        },

        cargarCotizacion(before=false){
            const loader = this.loaderk();
            let me = this;
            const datos = this.serviciosCot.getCotizacion(this.$attrs.id);
            datos.then(response =>{
                let respuesta = response.data
                let Dctos = respuesta.dctos_fin;
                me.rowData = respuesta.cotizaciones_det;
                me.fillCotizacion = respuesta.cotizacion;
                me.dctosFinancierosCliente = Dctos;
                me.configuracionesGrilla = respuesta.config_especiales_agrupadas;
                me.configuracionesGrillaDet = respuesta.config_especiales;
                me.configuracionPredeterminada = respuesta.config_predeterminada;
                if(before) me.cargarColumnas(respuesta.config_predeterminada);
                loader.close()
            }).catch(error=>{
                loader.close();
                console.log(error)
                let coderror = me.servicios.ObtenerTpError(error);
                if(coderror == 401 || coderror == 419){
                    this.$router.push({name: 'login'})
                    location.reload();
                    localStorage.clear();
                    sessionStorage.clear();
                }
            });
        },

        cargarColumnas(arrColumnas){
            console.log(arrColumnas)
            let me = this;
            this.columnDefs = [];
            if(arrColumnas){
                me.fillColumnas = arrColumnas;
                me.fillColumnas.map(function(col,index){
                    let confCol = SERVICIOS_REST_COT.configuracionColumna(col,me.permisosUsuario,me.fillCotizacion.Estado,me);
                    me.columnDefs.push(confCol);
                })
            }
        },

        cambioFiltros(params){
            this.serviciosCot.cambioFiltros(this,params);
        },

        cambioColumnas(params){
            this.serviciosCot.cambioColumnas(this.gridOptions.columnApi.getColumnState());
        },

        cambioDatosGrilla(event){
            let Columna = event.colDef.field;
            let DatoNuevo = event.newValue;
            let DatoAnterior = event.oldValue;
            if(DatoNuevo){
                this.DatoEditado.push({'Columna':Columna,'DatoNuevo':DatoNuevo,'DatoAnt':DatoAnterior});
            }
        },

        cambioValorCeldaGRilla(event) {
            var data = event.data;
            let id = event.data.IdPlantillaDet;
            if(this.DatoEditado.length >0){
                this.guardarDatoEditadoGrid(id,this.DatoEditado);
            }
            this.DatoEditado = [];
        },

        cargarFiltros(){
            let me = this;
            let url ="/cotizaciones/ObtenerFiltro";
            axios.get(url).then(response=>{    
                let respuesta = response.data;
                if(respuesta.filtros){
                    localStorage.setItem('filtros_cot_det',(respuesta.filtros));
                }
                if(respuesta.columnas){
                    localStorage.setItem('columnas_cot_det',(respuesta.columnas));
                }
            }).catch(error =>{
                console.log(error)
            })
        },

        cambioPlantilla(id){
            console.log(id)
        },

        cambioSeleccionItems(){
            this.itemsSeleccionados = this.gridApi.getSelectedRows();
        },

        onGridReady(params) {
            let filtros = JSON.parse(localStorage.getItem('filtros_cot_det'));
            let columnas = JSON.parse(localStorage.getItem('columnas_cot_det'));
            this.gridOptions.api.setFilterModel(filtros);
            this.gridOptions.columnApi.setColumnState(columnas)
            this.gridApi = params;
            this.gridApi = this.gridOptions.api;
            this.gridApi.closeToolPanel();
        },

        onFirstDataRendered(params){
            /*let Filtros = JSON.parse(localStorage.getItem('filtros'));
            let Columnas = JSON.parse(localStorage.getItem('columnas'));
            this.gridApi.setFilterModel(Filtros);
            this.gridOptions.columnApi.setColumnState(Columnas)*/
        },

        ocultarPanelEncabezado(){
            this.ocultarPanel = !  this.ocultarPanel;
            localStorage.setItem('panelOculto', this.ocultarPanel);
        },


        validarPermiso(permiso){
            if(this.permisosUsuario.includes('cotizaciones.'+permiso) || this.permisosUsuario.includes(permiso) || this.permisosUsuario.includes('administrador.sistema')){
                return true;
            }
            else{
                return false;
            }
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
            });
        },

    },

    beforeMount(){
        this.cargarCotizacion(true);
    },

    mounted() {
        console.log(this.$el)
        try{
            EventBus.$on('CotizacionEdit',data=>{
                console.log(data)
                this.fillCotizacion = data;
            });
            this.gridColumnApi = this.gridOptions.columnApi;
            this.cargarFiltros();
            this.permisosUsuario = localStorage.getItem('listPermisosFilterByRolUser');
            let PanelOculto = localStorage.getItem('panelOculto') ? this.ocultarPanel = PanelOculto :false;
        }
        catch(error){
            const err = this.serviceApp.ObtenerTpError(error);
            if(err == 401 || err == 419){
                this.$router.push({name: 'login'})
                location.reload();
                sessionStorage.clear();
            }
        }
    },
}

</script>
<style>
.titulo-descripcion{
    font-weight: bold !important;
}

.ag-theme-alpine .ag-icon-desc:before , .ag-theme-alpine .ag-icon-asc:before, .ag-theme-alpine .ag-icon-menu:before{
    color:white;
}
</style>