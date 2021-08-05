<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ver Plantilla </h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item active">Plantilla</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header bg-info">
                    <b class="aling-left" v-if="fillPlantilla.tercero" v-text="fillPlantilla.tercero.IdTercero+' '+fillPlantilla.tercero.NombreCorto"></b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <template v-if="ValidarPermiso('crear')">
                                    <router-link class="btn btn-info btn-sm" :to="{name:'plantillas_clientes.crear'}">
                                        <i class="fas fa-plus-square"></i> Nueva Plantilla
                                    </router-link>
                                </template>
                                <router-link class="btn btn-info btn-sm" :to="{name:'plantillas_clientes.index'}">
                                    <i class="fas fa-arrow-left"></i> Regresar
                                </router-link>
                                <modal :titulo="'Ayudas Plantillas'" :iddoc="93" :url="'plantillas_clientes.crear'"></modal>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 btn-group-justified"  style="display:flex" >
                        <logacciones class="btn-margin-left" :IdMovimiento ="fillPlantilla.IdPlantilla" :IdDocumento="93"></logacciones>
                        <button class="btn btn-success btn-sm btn-margin-left" v-if="fillPlantilla.Estado!='ANULADA'" @click.prevent="dialogAcciones = !dialogAcciones"><i class="fas fa-grip-horizontal"></i> Menu Acciones</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-if="OcultarPanel" @click.prevent="OcultarMostrarPanel()"><i class="fas fa-eye"></i> Mostrar Encabezado</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-if="!OcultarPanel" @click.prevent="OcultarMostrarPanel()"><i class="fas fa-eye-slash"></i> Ocultar Encabezado</button>
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
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para editar los datos principales de la plantilla.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('editar') && fillPlantilla.Estado=='DIGITADA') ? false : true"  @click.prevent="abrirModalEditar">
                                        <i class="fas fa-edit"></i> Editar
                                        </el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Autoriza  plantilla pero no los detalles.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('autorizar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="Autorizar"><i class="fas fa-check-circle"></i> Autorizar</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Desautoriza  plantilla pero no los detalles.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('desautorizar') && fillPlantilla.Estado=='AUTORIZADA') ? false : true" @click.prevent="DesAutorizar"> <i class="fas fa-minus-circle"></i> Des Autorizar</el-button>
                                    </el-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">2</th>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Anula la plantilla actual.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('anular') && fillPlantilla.Estado=='AUTORIZADA')? false : true" @click.prevent="Anular"> <i class="fas fa-ban"></i> Anular</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para homologar datos de una cotización o plantilla anterior.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('homologar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalHomologar = true"> <i class="fas fa-link"></i> Homologar</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para enlazar los detalles a la lista principal del proveedor</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('restablecercostos') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="RestablecerCostos"> <i class="fas fa-retweet"></i>Restablecer Costos</el-button>
                                    </el-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">3</th>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para crear una plantilla con los mismos datos.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('duplicar')) ? false : true"><i class="fas fa-clone"></i> Duplicar Plantilla</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para eliminar items de la plantilla.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('eliminar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="EliminarDetallesSel" ><i class="fas fa-trash-alt"></i>Eliminar Items</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para crear o enviar items a una cotización</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('crearcot') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalCotizacion = true"><i class="fas fa-align-justify"></i> Crear Cotización</el-button>
                                    </el-tooltip>
                                </td>
                                <tr>
                                <th scope="row">4</th>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para autorizar masivamente items de la plantilla.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('autorizaritems') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AutorizarDetalles()"><i class="fas fa-edit"></i> Autorizar Items</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para editar los datos principales de la plantilla.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('desautorizaritems') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="DesAutorizarDetalles()"><i class="fas fa-minus-circle"></i>Desautorizar Items</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para marcar los items que se han vendido en un rango de fecha.</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('crearcot') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click="AbriModalItemsVendidos = true"><i class="fas fa-chart-line"></i>Marcar Items Vendidos</el-button>
                                    </el-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">4</th>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Sirve para importar una lista en formato cvs</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('importar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalImportarItems = true"><i class="fas fa-align-justify"></i> Importar Listado</el-button>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <el-tooltip placement="right">
                                        <div slot="content">Calcula el factor</div>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('calcularfactor') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalCalcFactor = true"><i class="fas fa-calculator"></i> Calcular Factor</el-button>
                                    </el-tooltip>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="warning" @click="dialogAcciones = false">Cerrar</el-button>
                        </span>
                    </el-dialog>
                    <!--Fin Botones Acciones-->

                    <!--Acciones-->
                    <el-dialog title="Editar Plantilla" :visible.sync="editarPlantilla">
                        <div class="form-group row border" >
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cliente : </label>
                                    <span style="color:red" v-show="fillEditarPlantilla.nIdTercero ==0">(Sel. *)</span>
                                    <v-select
                                        @search="selectTerceros"
                                        label="NombreCorto"
                                        v-model="fillEditarPlantilla.nIdTercero"
                                        :options="arrayTerceros"
                                        placeholder="Buscar Tercero..."
                                        @input="getDatosTercero"   
                                    >
                                    </v-select>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label >Dirección</label>
                                    <span style="color:red" v-show="fillEditarPlantilla.nIdDireccion ==null">(Sel. *)</span>
                                    <select class="form-control" v-model="fillEditarPlantilla.nIdDireccion">
                                        <option value="0" selected>( Seleccione )</option>
                                        <option v-for="dir in arrayDirecciones" :key="dir.IdDireccion" :value="dir.IdDireccion" v-text="dir.NmDireccion+' ('+dir.Direccion+')'"></option>
                                    </select>                                   
                                </div>
                            </div>

                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fh Entrega Pro.</label><span style="color:red" v-show="fillEditarPlantilla.FechaEntregaPropuesta ==null">(Sel. *)</span>
                                    <el-date-picker
                                        v-model="fillEditarPlantilla.FechaEntregaPropuesta"
                                        type="datetime"
                                        placeholder="Seleccione la fecha y hora"
                                        value-format="yyyy-MM-dd HH:mm:ss">
                                    </el-date-picker>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nombre Plantilla <span style="color:red" v-show="fillEditarPlantilla.cNmPlantilla ==null">(Sel. *)</span></label>
                                    <input type="text" v-model="fillEditarPlantilla.cNmPlantilla" class="form-control" placeholder="Nombre de la plantilla"> 
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Meses de Consumo <span style="color:red" v-show="fillEditarPlantilla.nMesesConsumo ==null">(Sel. *)</span></label>
                                    <input type="number" v-model="fillEditarPlantilla.nMesesConsumo" class="form-control" placeholder="# meses"> 
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vigencia Oferta <span style="color:red" v-show="!fillEditarPlantilla.oVigenciaOferta">(Sel. *)</span></label>
                                    <el-date-picker
                                        v-model="fillEditarPlantilla.oVigenciaOferta"
                                        type="daterange"
                                        align="right"
                                        unlink-panels
                                        format="yyyy-MM-dd"
                                        range-separator="A"
                                        start-placeholder="Desde"
                                        end-placeholder="Hasta"
                                        value-format="yyyy-MM-dd">
                                    </el-date-picker>
                                </div>
                            </div>

                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Periodo Año</label><span style="color:red" v-show="fillEditarPlantilla.dPeriodoAnio ==null">(Sel. *)</span>
                                    <select class="form-control" v-model="fillEditarPlantilla.dPeriodoAnio">
                                        <option value="0">Seleccione</option>
                                        <option v-for="(anio,index) in PeriodoAnio" :key="index" :value="anio" v-text="anio"></option>
                                    </select>            
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Periodo Mes</label><span style="color:red" v-show="fillEditarPlantilla.dPeriodoMes ==null">(Sel. *)</span>-   
                                    <select class="form-control" v-model="fillEditarPlantilla.dPeriodoMes">
                                        <option value="0">Seleccione</option>
                                        <option v-for="(mes,index) in PeriodoMes" :key="index" :value="mes" v-text="mes"></option>
                                    </select>             
                                </div>
                            </div>

                            <div class="col-md-6" >
                                <div class="form-group">
                                    <label>Comentarios</label>
                                    <textarea v-model="fillEditarPlantilla.cComentarios" class="form-control">
                                    </textarea>  
                                </div>
                            </div>
                    
                            <!--<div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Alerta !!!</h5>
                                            <button class="close" @click="abrirModalErrorEdit"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in arrayMensajeError" :key="index" v-text="item"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" @click="abrirModalErrorEdit">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="editarPlantilla = false">Cancelar</el-button>
                            <el-button type="primary" @click="actualizarDatosPlantilla()">Actualizar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Marcar Items Vendidos" :visible.sync="AbriModalItemsVendidos">
                        <div class="form-group row border" >
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Selecione un rango de fechas:</label>
                                    <el-date-picker
                                            v-model="oRangoFechasItemsV"
                                            class="form-control"
                                            type="daterange"
                                            align="right"
                                            unlink-panels
                                            range-separator="A"
                                            start-placeholder="Desde"
                                            end-placeholder="Hasta"
                                            :picker-options="pickerOptions"
                                            format="yyyy-MM-dd"
                                            value-format="yyyy-MM-dd">
                                    </el-date-picker>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbriModalItemsVendidos = false">Cancelar</el-button>
                            <el-button type="success" @click="MarcarItemsVendidos()">Aplicar</el-button>
                        </span>
                    </el-dialog>
                    
                    <el-dialog title="Importar Archivo" :visible.sync="AbriModalImportarItems">
                        <div class="form-group row border" >
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Selecione un archivo cvs:</label>
                                    <el-upload
                                        ref="upload"
                                        class="upload-demo"
                                        :action="'/plantillas/clientes/ImportarArchivo/'+ this.$attrs.id"
                                        :headers="{
                                            'X-CSRF-TOKEN': csrf,
                                            'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, DELETE',
                                            'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept, X-File-Name, X-File-Size, X-File-Type'
                                        }"
                                        :on-preview="handlePreview"
                                        :on-remove="handleRemove"
                                        :on-change="AgregarArchivo"
                                        :on-success="ArchivoImportadoExito"
                                        :on-error="ArchivoImportadoError"
                                        :file-list="fileList"
                                        :auto-upload="false"
                                        :before-upload="validarArchivo"
                                        :limit="1"
                                        multiple>
                                    <el-button size="small" type="primary">Clic para subir archivo</el-button>
                                    <div slot="tip" class="el-upload__tip">Solo archivos cvs delimitados por coma</div>
                                    </el-upload>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbriModalImportarItems = false">Cerrar</el-button>
                            <el-button type="success" :disabled="fileList.length > 0?false:true" @click="ImportarArchivo()">Importar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Calcular Factor" :visible.sync="AbriModalCalcFactor">
                        <div class="form-group row border" >
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Ingrese un valor <strong :class="CalculoFactor != null ? 'bg-success': '' " v-text="CalcFactorFin = CalculoFactor"></strong></label>
                                    <input type="number" v-model="CalFactor" class="form-control" placeholder="Ingrese un valor"> 
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbriModalCalcFactor = false">Cerrar</el-button>
                            <el-button type="success"  @click="AplicarFactorMultiple()">Aplicar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Homologar Detalles" :visible.sync="AbrirModalHomologar">
                        <div class="form-group row border" >
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Cotización</label>
                                    <input type="number" :disabled="fillHm.IdPlantilla != '' ? true:false" v-model="fillHm.NroCot" class="form-control" placeholder="# Cot"> 
                                    <label>Plantilla</label>
                                    <input type="number" :disabled="fillHm.NroCot != '' ? true:false" v-model="fillHm.IdPlantilla" class="form-control" placeholder="# Plantilla"> 
                                    <label>Grupo</label>
                                    <input type="text" v-model="fillHm.Grupo" class="form-control" placeholder="Nm Grupo"> 
                                    <label>Fechas Cotizacion</label>
                                    <el-date-picker
                                        v-model="oFechasCot"
                                        type="daterange"
                                        align="right"
                                        unlink-panels
                                        format="yyyy-MM-dd"
                                        range-separator="A"
                                        start-placeholder="Desde"
                                        end-placeholder="Hasta"
                                        value-format="yyyy-MM-dd">
                                    </el-date-picker>
                                    <label>Opción</label><br>
                                    <el-radio v-model="opDetallesHm" label="1">Todos</el-radio>
                                    <el-radio v-model="opDetallesHm" label="2">Seleccionados</el-radio>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbrirModalHomologar = false">Cancelar</el-button>
                            <el-button type="primary" @click="AplicarHomologacion()">Homologar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Crear Cotización" :visible.sync="AbrirModalCotizacion">
                        <div class="form-group row border" >
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Cotización Existente</label>
                                    <input type="number" v-model="FillCrearCot.CotExist" class="form-control" placeholder="# Cot"> 
                                    <label>Opción Detalles</label><br>
                                    <el-radio v-model="FillCrearCot.OpcionItems" label="1">Todos</el-radio>
                                    <el-radio v-model="FillCrearCot.OpcionItems" label="2">Seleccionados</el-radio><br>
                                    <label>Opcion Cotización</label>
                                    <el-select v-model="FillCrearCot.valuecot" :disabled="FillCrearCot.CotExist>0" placeholder="Seleccione">
                                        <el-option
                                            v-for="item in FillCrearCot.optionsCot"
                                            :key="item.valuecot"
                                            :label="item.label"
                                            :value="item.valuecot">
                                        </el-option>
                                    </el-select>
                                    <label>Pertenece a contrato</label><br>
                                    <el-radio v-model="FillCrearCot.perteneceCCto" :disabled="FillCrearCot.CotExist>0" label="1">Si</el-radio>
                                    <el-radio v-model="FillCrearCot.perteneceCCto" :disabled="FillCrearCot.CotExist>0" label="0">No</el-radio>

                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbrirModalCotizacion = false">Cancelar</el-button>
                            <el-button type="primary" @click="ValidarDatosCot()">Crear</el-button>
                        </span>
                    </el-dialog>

                    <!--Fin Acciones-->
                    </div><hr>
                    <div class="form-group row border" v-if="!OcultarPanel">
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Id Plantilla</label>
                                <!--label muestra el objeto o la columna en el select-->
                                <p class="p-encabezado" v-text="fillPlantilla.IdPlantilla"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Nombre Plantilla</label>
                                <p class="p-encabezado" v-text="fillPlantilla.NmPlantilla"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Dirección</label>
                                <p class="p-encabezado" v-if="fillPlantilla.direccion" v-text="fillPlantilla.direccion['Direccion']"></p>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha</label>
                                <p class="p-encabezado">{{moment(fillPlantilla.FhPlantilla).format('MMMM DD YYYY, h:mm:ss a')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Vig. Oferta</label>
                                <p class="p-encabezado">{{moment(fillPlantilla.VigDesde).format('MMMM DD YYYY')}} hasta {{moment(fillPlantilla.VigHasta).format('MMMM DD YYYY')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha Entrega Propuesta</label>
                                <p class="p-encabezado">{{moment(fillPlantilla.FhEntregaPropuesta).format('MMMM DD YYYY')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Estado</label>
                                <p class="p-encabezado" v-text="fillPlantilla.Estado"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Total Consumo Mes </label>
                                <p class="p-encabezado" >{{fillPlantilla.TotalConsumo}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Total Costo Oferta</label>
                                <p class="p-encabezado" >{{FormatoMoneda(fillPlantilla.Total)}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Año</label>
                                <p class="p-encabezado" >{{fillPlantilla.Periodo}}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Meses Consumo</label>
                                <p class="p-encabezado" >{{fillPlantilla.CantidadConsumoMes}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Comentarios</label>
                                <p class="p-encabezado" v-text="fillPlantilla.Comentarios"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <!--<button  v-on:click="autoSizeAll(true)">Auto-Size All</button>-->
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
                                :menuTabs="['filterMenuTab']"
                                :editType="editType"
                                :pagination="true"
                                :sideBar="sideBar"
                                :paginationPageSize="paginationPageSize"
                                :tooltipShowDelay="tooltipShowDelay"
                                @cell-value-changed="onCellValueChanged"
                                @row-value-changed="onRowValueChanged"
                                @selection-changed="onSelectionChanged">
                            </ag-grid-vue>
                        </div>
                    </div>
                    <homologar-plantillas :visible="showHomologar" :datosItem="DatosHomologar" :datos="datosfiltrados" :datosPl="fillPlantilla"  v-on:OcultarModal = "OcultarModalHM()"></homologar-plantillas>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <router-link class="btn btn-secondary" :to="{name:'plantillas_clientes.index'}">
                                <i class="fas fa-arrow-left"></i> Cerrar
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>
<script>
import Swal from 'sweetalert2'
import "ag-grid-community/dist/styles/ag-grid.css";
import "ag-grid-community/dist/styles/ag-theme-alpine.css";
import 'ag-grid-enterprise';
import vSelect from "vue-select";
import serviceApp from "../../../ServicesApp";
const servicesApp = new serviceApp();
import "vue-select/dist/vue-select.css";
import { AgGridVue } from "ag-grid-vue";
export default {
    components: {
        AgGridVue,
        "v-select": vSelect
    },
    data() {
        return {
            //Parametros de configuracion de la grilla
            columnDefs: [],
            rowData: [],
            autoGroupColumnDef: null,
            paginationPageSize:100,
            //gridOptions: null,
            gridOptions :{
                onFilterChanged:this.CambioFiltros,
                onColumnMoved:this.GuardarOrdenColumnas,
                onColumnVisible:this.ColumnasVisibles,
                onSortChanged:this.GuardarOrdenColumnas
            },
            gridApi: null,
            columnApi: null,
            defaultColDef: null,
            //editType: null,
            tooltipShowDelay:null,
            sideBar: null,
            rowSelection:null,
            Columnas:[],
            width:'100%',
            height:(window.innerHeight - 150)+'px',

            
            defaultColDef : {
                flex: 1,
                minWidth: 100,
                enableValue: true,
                enableRowGroup: true,
                enablePivot: true,
                sortable: true,
                filter: true,
            },
            sideBar : {
                toolPanels: [{
                        id: 'columns',
                        labelDefault: 'Columns',
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
                        labelDefault: 'Filters',
                        labelKey: 'filters',
                        iconKey: 'filter',
                        toolPanel: 'agFiltersToolPanel',
                    },
                ],
                position: 'left',
                defaultToolPanel: 'filters',
                hiddenByDefault: false,
            },
            rowSelection : 'multiple',
            editType : 'fullRow',
            //Fin datos configuracion de grilla

            editarPlantilla:false,
            AbriModalItemsVendidos:false,
            pickerOptions: {
                shortcuts: [{
                    text: 'Ult. Semana',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        console.log(start)
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: 'Ult. Mes',
                        onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: 'Ult. 3 Meses',
                        onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            },
            dialogAcciones:false,
            usuario:'',
            direccion:[],
            arrMensajeError:[],
            OpPedido:8,
            accionMovimiento:0,
            PermisosUser:[],
            fillPlantilla:[],
            datosfiltrados:[],
            //Eliminar Items 
            ItemsSeleccionados:[],
            //Fin Eliminar Items

            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
            //Variables Editar
            EditarDet : true,
            fillEditarPlantilla:{
                nIdPlantilla:this.$attrs.id,
                nIdTercero:0,
                nIdDireccion:null,
                FechaEntregaPropuesta:null,
                cNmPlantilla:'',
                nMesesConsumo:null,
                oVigenciaOferta:[],
                dPeriodoAnio:null,
                dPeriodoMes:null,
                cComentarios:null,
            },
            arrayTerceros:[],
            arrayDirecciones:[],
            dPeriodoAnio:[],
            dPeriodoMes:[],
            //Fin variables Editar
            arrayMensajeError:[],
            fillDetallesPlantilla:[],
            fillColumnas:[],
            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 15,
            //Fin variables paginacion
            moment:moment,
            fullscreenLoading:false,
            active:false,
            msgAnulacion:'',
            AptoAnular:true,
            localeText : servicesApp.traducirTextosAggrid(),

            showHomologar:false,
            DatosHomologar:[],
            DatoEditado:[],
            ValAceptaAlt:{
                '1':'SI',
                '0':'NO'
            },
            ValReqMuestras :{
                '1':'SI',
                '0':'NO'
            },
            ValSolCot :{
                '1':'SI',
                '0':'NO'
            },
            ValAut :{
                '1':'SI',
                '0':'NO'
            },
            OcultarPanel:false,
            MantenerFiltros:false,
            OpcionAccionDets:null,
            //Items vendidos
            oRangoFechasItemsV:'',
            AbriModalImportarItems:false,

            //Subida de cvs
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            fileList:[],

            //Calcular factor
            AbriModalCalcFactor : false,
            CalFactor : null,
            CalcFactorFin: null,

            //Variables Homologar
            AbrirModalHomologar:false,
            opDetallesHm:1,
            oFechasCot:[],
            fillHm:{
                NroCot:'',
                IdPlantilla:'',
                Grupo:''
            },
            //Cotizacion
            AbrirModalCotizacion: false,
            
            FillCrearCot : {
                CotExist:'',
                optionsCot: [{
                    valuecot: 'cot-oferta',
                    label: 'Cotización-Oferta'
                }, {
                    valuecot: 'cot-preo',
                    label: 'Cotización-PreOferta'
                }, {
                    valuecot: 'lic-oferta',
                    label: 'Licitación-Oferta'
                },
                {
                    valuecot: 'lic-preo',
                    label: 'Licitación-PreOferta'
                }
                ],
                valuecot: '',
                OpcionItems:null,
                perteneceCCto:null,
                ItemsPlantilla :[],
            }
        }
    },
    watch:{
        EditarDet(){

        }
    },
    computed: {
        CalculoFactor(){
            if(this.CalFactor >0){
                //$this->LblFactor->Text = number_format(1 /$this->TxtCalculo->Text, 4, '.', '');
                var p = Math.pow(10,4);
                return Math.round(((1 / this.CalFactor) * p)) / p;
            }
            else{
                return null;
            }
        },
        
        calcularTotal: function(){
            var resultado =0;
            var porIva = 0;
            if(this.accionMovimiento!=0){
                for(var i=0;i< this.ListarMovimientosDetPaginate.length;i++){
                    var objeto = this.ListarMovimientosDetPaginate[i];
                    porIva = porIva + (((objeto['Cantidad'] * objeto['Precio']) * objeto['PorIva'])/100);
                    resultado = resultado + (objeto['Cantidad'] * objeto['Precio']);
                }
            }
            else{
                var objeto = this.fillPlantilla;
                porIva = objeto['nVrIva'] ;
                resultado = objeto['nSubTotal'];
            }
            for(var i=0;i< this.ListarMovimientosDetPaginate.length;i++){
                var objeto = this.ListarMovimientosDetPaginate[i];
                if((objeto['Cantidad'] - objeto['CantAfectada']) != objeto['Cantidad']){
                    this.AptoAnular = false;
                    break;
                }
            }
            this.fillPlantilla.nVrIva = porIva;
            return resultado;
        },
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.fillDetallesPlantilla.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarMovimientosDetPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.fillDetallesPlantilla.slice(inicio, fin);
        },
        pagesList() {
            let a = this.fillDetallesPlantilla.length;
            let b = this.perPage;
            let PageCoun = Math.ceil(a / b);
            let count = 0;
            let PagesArray = [];
            while (count < PageCoun) {
                PagesArray.push(count);
                count++;
            }
            return PagesArray;
        },
        mensajeAnular(){
            if(!this.active){
                this.msgAnulacion ='';
            }
            return this.msgAnulacion;
        },
        PeriodoAnio(){
            let Anios = [];
            let i = 2018;
            for(i;i<2028;i++){
                Anios.push(i);
            }
            return Anios;
        },
        PeriodoMes(){
            let Meses = [];
            let i = 1;
            for(i;i<7;i++){
                Meses.push(i);
            }
            return Meses;
        },
    },
    methods: {
        DatosEditarPlantilla(Id){
            this.fillEditarPlantilla.nIdTercero =this.fillPlantilla.IdTerceroPlant;
            this.fillEditarPlantilla.nIdDireccion = this.fillPlantilla.IdDireccionPlant
            this.fillEditarPlantilla.FechaEntregaPropuesta = this.fillPlantilla.FhEntregaPropuesta;
            this.fillEditarPlantilla.cNmPlantilla  = this.fillPlantilla.NmPlantilla;
            this.fillEditarPlantilla.nMesesConsumo = this.fillPlantilla.CantidadConsumoMes;
            this.fillEditarPlantilla.oVigenciaOferta = [
                this.fillPlantilla.VigDesde,
                this.fillPlantilla.VigHasta
            ];
            if(this.fillPlantilla.Periodo !=''){
                let periodo = this.fillPlantilla.Periodo.split('-');
                this.fillEditarPlantilla.dPeriodoAnio = periodo[0];
                this.fillEditarPlantilla.dPeriodoMes = periodo[1];
            }
            this.fillEditarPlantilla.cComentarios = this.fillPlantilla.Comentarios;
        },

        listarPlantilla(editPl =false){
            let Filtros = JSON.parse(localStorage.getItem('filtros'));
            let url ="/plantillas/clientes/ObtenerPlantilla/"+this.$attrs.id ;
            let me = this;
            axios.get(url,{params:{
                'filtros':Filtros
            }}).then(response=>{    
                this.fillPlantilla = response.data.plantilla;
                this.fillDetallesPlantilla = response.data.plantillas_det;
                if(this.MantenerFiltros){
                    var itemsToUpdate = [];
                    var itemsToDelete = [];
                    var itemsToInsert = []; 
                    let TotalItems=0;
                    this.gridApi.forEachNodeAfterFilterAndSort(function (rowNode, index) { 
                        TotalItems++;
                    });
                    console.log(TotalItems)
                    if(TotalItems >0 ){ 
                    //Obtenemos los items que esten filtrados
                    this.gridApi.forEachNodeAfterFilterAndSort(function (rowNode, index) {
                        //Llenamos los datos actuales
                        var data = rowNode.data;
                        //Obtenemos los objetos del dato actual osea las columnas.
                        let Objetos = Object.keys(data);

                        //Recorremos cada dato que retorno la consulta
                        me.fillDetallesPlantilla.map(function(x){
                            //Validamos si el dato que retorno actual es igual  al dato que se esta actualizando
                            if(me.OpcionAccionDets !='elim' && me.OpcionAccionDets !='add'){
                                if(x.IdPlantillaDet == data.IdPlantillaDet){
                                    //Si es el mismo dato validamos el objeto entrante vs el anterior si cambio obtiene los cambios
                                    Objetos.map(function(e){
                                        if(data[e] != x[e]){
                                            data[e] = x[e];
                                        }
                                    });
                                }
                            }
                            
                        });

                        if(me.OpcionAccionDets =='elim'){
                            if(!me.validarExist(me.fillDetallesPlantilla,data.IdPlantillaDet,'IdPlantillaDet')){
                                itemsToDelete.push(data);
                            }
                        }

                        else if(me.OpcionAccionDets =='add'){
                            if(!me.validarExist(me.fillDetallesPlantilla,data.IdPlantillaDet,'IdPlantillaDet')){
                                itemsToInsert.push(data);
                            }
                        }
                        itemsToUpdate.push(data);
                    });
                }
                else{
                    this.CargarColumnas(response);
                    if(me.OpcionAccionDets =='add'){
                        itemsToInsert = me.fillDetallesPlantilla;
                    }
                }
                //Aplicamos los cambios a la grilla
                if(me.OpcionAccionDets !='elim' && me.OpcionAccionDets !='add'){
                    console.log("Entro a actualizar")
                    this.gridApi.applyTransaction({ update: itemsToUpdate });
                }
                else if(me.OpcionAccionDets =='elim'){
                    console.log("Entro a eliminar")
                    this.gridApi.applyTransaction({ remove: itemsToDelete });
                }
                else if(me.OpcionAccionDets =='add'){
                    console.log("Entro a insertar")
                    this.gridApi.applyTransaction({ add: itemsToInsert });
                }
            }   
            else{
                this.rowData = this.fillDetallesPlantilla;
                this.FillCrearCot.ItemsPlantilla = this.rowData;
            }
            //Refrescamos los botones renderizados en la grilla
            this.gridApi.refreshCells({ force: true })
            this.OpcionAccionDets = null;
            }).catch(error =>{
                console.log(error)
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },
        
        limpiarDatos(){
        },

        Autorizar(){
            let me = this;
            let url ="/plantillas/clientes/Autorizar";
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'nIdPlantilla':me.$attrs.id
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                this.AlertMensaje(respuesta.msg,1);
                me.fillPlantilla = respuesta.plantilla;
                this.EditarDet = false;
                
            }).catch(error =>{
                loader.close();
                this.AlertMensaje(error.data.msg,3);
                console.log(error)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
                if(error.response.status == 500 || error.response.status == 501){
                    loader.close();
                    let message = error.response.data.message;
                    let linea = error.response.data.line;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                        text :message + 'linea : '+ linea ,
                        showConfirmButton: true
                    });
                }
            })
        },
        DesAutorizar(){
            let me = this;
            let url ="/plantillas/clientes/DesAutorizar";
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'nIdPlantilla':me.$attrs.id
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                this.AlertMensaje(respuesta.msg,1);
                me.fillPlantilla = respuesta.plantilla;
                this.EditarDet = true;
            }).catch(error =>{
                loader.close();
                this.AlertMensaje(error.data.msg,3);
                console.log(error)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
                if(error.response.status == 500 || error.response.status == 501){
                    loader.close();
                    let message = error.response.data.message;
                    let linea = error.response.data.line;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                        text :message + 'linea : '+ linea ,
                        showConfirmButton: true
                    });
                }
            })
        },

        AutorizarDetalles(){
            if(this.ItemsSeleccionados.length >0){
                this.MantenerFiltros = true;
                let me = this;
                let url ="/plantillas/clientes/AutorizarDetalles";
                const loader = this.loaderk();
                axios.put(url,{
                    params:{
                        'arrItems':me.ItemsSeleccionados,
                        'nIdPlantilla':me.$attrs.id
                    }
                }).then(response=>{    
                    let respuesta = response.data;
                    loader.close();
                    me.AlertMensaje(respuesta.msg,1);
                    this.listarPlantilla(true);
                }).catch(error =>{
                    loader.close();
                    this.AlertMensaje(error.data.msg,3);
                    console.log(error)
                    if(error.response.status ==401){
                        me.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        loader.close();
                    }
                    if(error.response.status == 500 || error.response.status == 501){
                        loader.close();
                        let message = error.response.data.message;
                        let linea = error.response.data.line;
                        Swal.fire({
                            position: 'top-center',
                            icon: 'error',
                            title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                            text :message + 'linea : '+ linea ,
                            showConfirmButton: true
                        });
                    }
                })
            }
            else{
                this.AlertMensaje("No hay productos seleccionados para autorizar",2);
            }
        },

        DesAutorizarDetalles(){
            if(this.ItemsSeleccionados.length >0){
                this.MantenerFiltros = true;
                let me = this;
                let url ="/plantillas/clientes/DesAutorizarDetalles";
                const loader = this.loaderk();
                axios.put(url,{
                    params:{
                        'arrItems':me.ItemsSeleccionados,
                        'nIdPlantilla':me.$attrs.id
                    }
                }).then(response=>{    
                    let respuesta = response.data;
                    loader.close();
                    me.AlertMensaje(respuesta.msg,1);
                    this.listarPlantilla(true);
                }).catch(error =>{
                    loader.close();
                    this.AlertMensaje(error.data.msg,3);
                    console.log(error)
                    if(error.response.status ==401){
                        me.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        loader.close();
                    }
                    if(error.response.status == 500 || error.response.status == 501){
                        loader.close();
                        let message = error.response.data.message;
                        let linea = error.response.data.line;
                        Swal.fire({
                            position: 'top-center',
                            icon: 'error',
                            title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                            text :message + 'linea : '+ linea ,
                            showConfirmButton: true
                        });
                    }
                })
            }
            else{
                this.AlertMensaje("No hay productos seleccionados para des-autorizar",2);
            }
        },

        Anular(){
            let me = this;
            let url ="/plantillas/clientes/Anular";
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'nIdPlantilla':me.$attrs.id
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                this.AlertMensaje(respuesta.msg,1);
                me.fillPlantilla = respuesta.plantilla;
            }).catch(error =>{
                loader.close();
                this.AlertMensaje(error.data.msg,3);
                console.log(error)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
                if(error.response.status == 500 || error.response.status == 501){
                    loader.close();
                    let message = error.response.data.message;
                    let linea = error.response.data.line;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                        text :message + 'linea : '+ linea ,
                        showConfirmButton: true
                    });
                }
            })
        },
        onSelectionChanged(){
            var selectedRows = this.gridApi.getSelectedRows();
            this.ItemsSeleccionados = selectedRows;
        },

        EliminarDetallesSel(){
            if(this.ItemsSeleccionados.length >0){
                this.MantenerFiltros = true;
                let me = this;
                let url ="/plantillas/clientes/EliminarDetalles";
                const loader = this.loaderk();
                axios.put(url,{
                    params:{
                        'arrItemsEliminar':me.ItemsSeleccionados,
                        'nIdPlantilla':me.$attrs.id
                    }
                }).then(response=>{    
                    let respuesta = response.data;
                    loader.close();
                    this.ItemsSeleccionados = [];
                    this.MantenerFiltros = true;
                    this.OpcionAccionDets ='elim';
                    this.listarPlantilla();
                    me.AlertMensaje(respuesta.msg,1);
                }).catch(error =>{
                    loader.close();
                    this.AlertMensaje(error.data.msg,3);
                    console.log(error)
                    if(error.response.status ==401){
                        me.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        loader.close();
                    }
                    if(error.response.status == 500 || error.response.status == 501){
                        loader.close();
                        let message = error.response.data.message;
                        let linea = error.response.data.line;
                        Swal.fire({
                            position: 'top-center',
                            icon: 'error',
                            title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                            text :message + 'linea : '+ linea ,
                            showConfirmButton: true
                        });
                    }
                })
            }
            else{
                this.AlertMensaje("No hay productos seleccionados para eliminar",2);
            }
        },

        EliminarDetalle(params){
            this.ItemsSeleccionados = []
            this.ItemsSeleccionados.push(params.data);
            Swal.fire({
                title: 'Estas Seguro(a)',
                text: "De eliminar el detalle seleccionado!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.EliminarDetallesSel();
                }
                else{
                    this.ItemsSeleccionados = []
                }
            })
            
        },

        actualizarDatosPlantilla(){
            let me = this;
            const loader = this.loaderk();
            if(!this.ValidarDatosPlantilla()){
                loader.close();
            }
            if(this.arrMensajeError.length <=0){
                axios.put('/plantillas/clientes/Editar',{
                    params:{
                        'fillEditarPlantilla':this.fillEditarPlantilla
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    loader.close();
                    me.AlertMensaje(respuesta.msg,1);
                    me.fillPlantilla = respuesta.plantilla;
                    me.abrirModalEditar();
                })
                .catch(function (error) {
                    me.AlertMensaje(error.data.msg,1);
                    console.log(error);
                    loader.close();
                    if (error.response.data.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                    if(error.response.data.status == 500){
                        Swal.fire({
                            icon :'danger',
                            type :'danger',
                            title :'',
                            text:response.data.error
                        })
                    }
                });
            }
            else{
                this.modalShow = true;
            }
        },

        ValidarDatosPlantilla(){
            let plantilla = this.fillEditarPlantilla;
            this.arrMensajeError =[];
            if(plantilla.nIdPlantilla == null){
                this.arrMensajeError.push("No se cargo bien los datos de la plantilla, intenta nuevamente");
            }
            if(plantilla.nIdTercero == null || plantilla.nIdTercero == 0){
                this.arrMensajeError.push("Debes seleccionar un tercero");
            }
            if(plantilla.nIdDireccion == null || plantilla.nIdDireccion == 0){
                this.arrMensajeError.push("Debes seleccionar una dirección");
            }
            if(plantilla.FechaEntregaPropuesta == null || plantilla.FechaEntregaPropuesta ==''){
                this.arrMensajeError.push("Debes seleccionar una fecha de entrega de propuesta");
            }
            if(plantilla.cNmPlantilla == null || plantilla.cNmPlantilla ==''){
                this.arrMensajeError.push("Debes ingresar el nombre de la plantilla");
            }
            if(plantilla.nMesesConsumo <=0 || plantilla.nMesesConsumo ==''){
                this.arrMensajeError.push("Ingresa los meses de consumo");
            }
            if(!plantilla.oVigenciaOferta){
                this.arrMensajeError.push("Ingresa la vigencia de la oferta");
            }
            if(plantilla.dPeriodoAnio == null || plantilla.dPeriodoAnio == 0){
                this.arrMensajeError.push("Ingresa un periodo Año");
            }
            if(plantilla.dPeriodoMes == null || plantilla.dPeriodoMes == 0){
                this.arrMensajeError.push("Ingresa un periodo mes");
            }
            
            if(this.arrMensajeError.length==0){
                return true;
            }
            else{
                this.arrMensajeError.forEach(error => {
                    this.AlertMensaje(error,2);
                });
                return false;
            }
        },

        ValidarDatosPlantillaDetalle(){
            let i;
            for(i=0;i<this.fillDetallesPlantilla.length;i++){
                let articulo = this.fillDetallesPlantilla[i];
                if(articulo.Cantidad <=0){
                    this.arrMensajeError.push("La cantidad del cod "+articulo.Id_Item+" debe ser mayor a 0");
                }
                if(articulo.CantMinimaVenta > articulo.Cantidad && this.direccion[0].tipo.NoValidaCantMinVenta  == 0){
                    this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.CantMinimaVenta);
                }
                if(this.Is_Float(articulo.Cantidad)){
                    this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.item.listacostosdet.CantMinimaVenta+", debe ser igual o multiplos de esta");
                }
            }
            if(this.arrMensajeError.length==0){
                return true;
            }
            else{
                return false;
            }
        },

        Is_Float(num){
            return !isNaN(num) && Math.round(num) != num;
        },

        FormatoMoneda(amount=0, decimals=2) {
            amount = (amount == null) ? 0 : amount;
            var sign = (amount.toString().substring(0, 1) == "-");

            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0) 
                return parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

            return  sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
            });
        },

        onGridReady(params) {
            /* this.fillDetallesPlantilla.map(function(x,y){
                me.rowData.push(x);
            })*/
            //this.rowData = this.fillDetallesPlantilla;
            //this.gridApi.setDomLayout("autoHeight");
            let Filtros = JSON.parse(localStorage.getItem('filtros'));
            let Columnas = JSON.parse(localStorage.getItem('columnas'));
            this.gridApi.setFilterModel(Filtros);
            this.gridOptions.columnApi.setColumnState(Columnas)
            this.listarPlantilla();
        },

        onFirstDataRendered(params){
            let Filtros = JSON.parse(localStorage.getItem('filtros'));
            let Columnas = JSON.parse(localStorage.getItem('columnas'));
            this.gridApi.setFilterModel(Filtros);
            this.gridOptions.columnApi.setColumnState(Columnas)
        },

        AbrirHomologacion(params){
            let me = this;
            me.datosfiltrados = [];
            this.gridApi.forEachNodeAfterFilterAndSort(function(node,index){
                me.datosfiltrados.push(node);
            });
            this.DatosHomologar = params.node;
            console.log(params.column.colId);
            this.showHomologar = true;
        },

        EditarDetalle(params){
            if(this.fillPlantilla.Estado == 'DIGITADA'){
                let index = params.node.childIndex;
                let idColumna = params.column.colId;
                this.gridApi.setFocusedCell(index,idColumna);
                this.gridApi.startEditingCell({
                    rowIndex: index,
                    colKey: idColumna,
                });
            }
        },

        onCellValueChanged(event) {
            //console.log(event.data.IdPlantillaDet)
            let Columna = event.colDef.field;
            let DatoNuevo = event.newValue;
            let DatoAnterior = event.oldValue;
            if(DatoNuevo){
                this.DatoEditado.push({'Columna':Columna,'DatoNuevo':DatoNuevo,'DatoAnt':DatoAnterior});
            }
        },

        onRowValueChanged(event) {
            var data = event.data;
            let id = event.data.IdPlantillaDet;
            if(this.DatoEditado.length >0){
                this.guardarDatoEditadoGrid(id,this.DatoEditado);
            }
            this.DatoEditado = [];
        },

        guardarDatoEditadoGrid(id,Datos){
            let me = this;
            axios.put('/plantillas/clientes/GuardarCambiosGrilla',{
                params:{
                    'IdPlantillaDet':id,
                    'arrCambios':Datos,
                }
            }).then((response)=>{
                let respuesta = response.data;
                this.AlertMensaje(respuesta.msg,1);
                this.MantenerFiltros = true;
                this.listarPlantilla(true);
            }).catch(error=>{
                console.log(error)
                this.AlertMensaje('Ocurrio un error al editar ',3);
            })
        },

        CambioFiltros(params){
            if(!this.MantenerFiltros){
                localStorage.removeItem('filtros');
                let DatosFiltro = JSON.stringify(params.api.getFilterModel());
                localStorage.setItem('filtros',DatosFiltro);
            }
        },

        GuardarOrdenColumnas(params){
            localStorage.removeItem('columnas');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas',DatosFiltro);
        },

        ColumnasVisibles(params){
            localStorage.removeItem('columnas');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas',DatosFiltro);
        },

        OcultarMostrarPanel(){
            this.OcultarPanel = !  this.OcultarPanel;
            localStorage.setItem('panelOculto', this.OcultarPanel);
        },

        //Metodos para cargar datos editar
        abrirModalEditar(){
            this.editarPlantilla=!this.editarPlantilla
            if(this.editarPlantilla){
                this.DatosEditarPlantilla();
                this.getDatosTercero(this.fillEditarPlantilla.nIdTercero);
            }
            else{
                this.fillEditarPlantilla.nIdTercero =0;
                this.fillEditarPlantilla.nIdDireccion = 0;
                this.fillEditarPlantilla.FechaEntregaPropuesta = '';
                this.fillEditarPlantilla.cNmPlantilla  = null;
                this.fillEditarPlantilla.nMesesConsumo = null;
                this.fillEditarPlantilla.oVigenciaOferta = [];
                this.fillEditarPlantilla.dPeriodoAnio = null;
                this.fillEditarPlantilla.dPeriodoMes = null;
                this.fillEditarPlantilla.cComentarios = null;
            }
        },
        
        abrirModalErrorEdit(){
            this.modalShow = !this.modalShow;
            if(this.modalShow){
            }
            else{
                this.arrMensajeError=[];
            }
        },

        selectTerceros(search,loading){
            let me=this;
            loading(true);
            var url= '/terceros/lista';
            axios.get(url,{
                params:{
                    'filtro':search
                }
            }).then(function (response) {
                let respuesta = response.data;
                q: search;
                me.arrayTerceros = respuesta.terceros;
                loading(false);
            })
            .catch(function (error) {
                 if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        getDatosTercero(val1){
            let me = this;
            try{
                me.fillEditarPlantilla.nIdTercero = val1.IdTercero ? val1.IdTercero : this.fillEditarPlantilla.nIdTercero;
                this.CargarDirecciones(me.fillEditarPlantilla.nIdTercero);
            }
            catch(error){
                this.fillEditarPlantilla.nIdTercero = 0;
            }
        },

        CargarDirecciones(IdTercero){
            let me = this;
            axios.get('/terceros/lista',{params:{
                'filtro':IdTercero
            }}).then(function (response) {
                var respuesta = response.data.terceros[0];
                me.arrayDirecciones = respuesta.direcciones;
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        AlertMensaje(Msg,Tipo = ''){
            let tipom = "";
            if(Tipo ==1){
                tipom = 'success';
            }
            else if(Tipo == 2){
                tipom = 'warn';
            }
            else if(Tipo == 3){
                tipom = 'danger';
            }
            let position = 'top-center'
            let color = tipom
            const noti = this.$vs.notification({
                flat: true,
                color,
                position,
                title:null,
                text: Msg
            })

            /*this.$message({
                showClose: true,
                message: Msg,
                type: tipom,
                offset:10
            });*/
        },

        ValidarPermiso(permiso){
            if(this.PermisosUser.includes('plantillas.'+permiso) || this.PermisosUser.includes('administrador.sistema')){
                return true;
            }
            else{
                return false;
            }
        },

        OcultarModalHM(){
            this.MantenerFiltros = true;
            this.showHomologar = false;
            this.listarPlantilla(true);
        },

        MarcarItemsVendidos(){
            let url = '/plantillas/clientes/MarcarItemsVendidos';
            let me = this;
            let load = me.loaderk();
            axios.post(url,{params:{
                'arrPlantilla':me.fillPlantilla,
                'oRangoFecha':me.oRangoFechasItemsV
            }}).then(function (response) {
                load.close();
                var respuesta = response.data;
                me.AlertMensaje(respuesta.msg,1);
                me.oRangoFechasItemsV = null
            })
            .catch(function (error) {
                load.close();
                me.AlertMensaje('Ocurrio un error al procesar la solicitud !!',3);
                console.log(error);
                if (error.response.status == 401) {
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },
        //Fin metodos editar

        //Metodos para la importacion de datos
        handleRemove(file, fileList) {
            //console.log(file, fileList);
            this.fileList = [];
        },
        handlePreview(file) {
            //console.log(file);
        },
        validarArchivo(file){
            let cvs = file.type === 'text/csv' || file.type === 'text/comma-separated-values' || file.type === 'application/csv' || file.type === 'application/vnd.ms-excel' || file.type === 'text/anytext'
            console.log(file.type)
            if(!cvs){
                this.AlertMensaje("el archivo no es tipo cvs delimitado por coma",3);
                return false
            }
            
        },

        AgregarArchivo(file, fileList){
            this.fileList = fileList;
        },

        ImportarArchivo(){
            this.$refs.upload.submit();
        },

        ArchivoImportadoExito(res){
            this.MantenerFiltros = true;
            this.OpcionAccionDets ='add';
            this.listarPlantilla();
            this.AlertMensaje(res.msg,1);
            if(this.rowData.length <=0){
                //window.location.reload();
            }
        },

        ArchivoImportadoError(res){
            console.log(res)
            this.AlertMensaje('Ocurrio un error al importar, valida el archivo.',3)
        },
        //Fin metodos importacion de datos

        //Aplicar Factor a items seleccionados
        AplicarFactorMultiple(){
            if(this.ItemsSeleccionados.length >0){
                if(this.CalcFactorFin){
                    this.MantenerFiltros = true;
                    let me = this;
                    let url ="/plantillas/clientes/AplicarCalculoFactor";
                    const loader = this.loaderk();
                    axios.put(url,{
                        params:{
                            'arrItems':me.ItemsSeleccionados,
                            'nIdPlantilla':me.$attrs.id,
                            'nFactor':me.CalcFactorFin
                        }
                    }).then(response=>{    
                        let respuesta = response.data;
                        loader.close();
                        me.AlertMensaje(respuesta.msg,1);
                        this.ItemsSeleccionados = [];
                        this.MantenerFiltros = true;
                        this.listarPlantilla(true);
                    }).catch(error =>{
                        loader.close();
                        this.AlertMensaje(error.data.msg,3);
                        console.log(error)
                        if(error.response.status ==401){
                            me.$router.push({name: 'login'})
                            location.reload();
                            sessionStorage.clear();
                            loader.close();
                        }
                    })
                }
                else{
                    this.AlertMensaje("No se ha calculado un factor",2);
                }
            }
            else{
                this.AlertMensaje("No hay productos seleccionados para eliminar",2);
            }
        },

        //Proceso Homologacion
        AplicarHomologacion(){
            this.MantenerFiltros = true;
            let me = this;
            let url ="/plantillas/clientes/ProcesarHomologacion";
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'IdPlantilla':me.$attrs.id,
                    'opDetalles':me.opDetallesHm,
                    'nNroCot':me.fillHm.NroCot,
                    'nIdPlantilla':me.fillHm.IdPlantilla,
                    'cGrupo':me.fillHm.Grupo,
                    'oFechasCot':me.oFechasCot,
                    'arrDetallesSel':me.ItemsSeleccionados,
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                me.AlertMensaje(respuesta.msg,1);
                this.ItemsSeleccionados = [];
                this.MantenerFiltros = true;
                this.listarPlantilla(true);
            }).catch(error =>{
                loader.close();
                this.AlertMensaje(error.data.msg,3);
                console.log(error)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
            })
        },

        //Restablecer Costos generales
        RestablecerCostos(){
            if(this.ItemsSeleccionados.length >0){
                this.MantenerFiltros = true;
                let me = this;
                let url ="/plantillas/clientes/AsignarCostoActual";
                const loader = this.loaderk();
                axios.put(url,{
                    params:{
                        'IdPlantilla':me.$attrs.id,
                        'arrDetallesSel':me.ItemsSeleccionados,
                    }
                }).then(response=>{    
                    let respuesta = response.data;
                    loader.close();
                    me.AlertMensaje(respuesta.msg,1);
                    this.ItemsSeleccionados = [];
                    this.MantenerFiltros = true;
                    this.listarPlantilla(true);
                }).catch(error =>{
                    loader.close();
                    this.AlertMensaje(error.data.msg,3);
                    console.log(error)
                    if(error.response.status ==401){
                        me.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        loader.close();
                    }
                })
            }
            else{
                this.AlertMensaje("No hay productos seleccionados para restablecer",2);
            }
        },

        CargarColumnas(response){
            let me = this;
            this.columnDefs = [];
            if(response.data.columnas){
                this.fillDetallesPlantilla = response.data.plantillas_det;
                this.fillColumnas = response.data.columnas;
                this.fillColumnas.map(function(x,y){
                    if(x.columna != null){
                        let Edit = (x.edit == 'true' && me.fillPlantilla.Estado =='DIGITADA' && me.ValidarPermiso('editardetallles')) ? true: false;
                        if(x.columna =='AceptaAlternativa'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:true, 
                                editable: params => params.data.Autorizado != 1 ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        '0',
                                        '1'
                                    ]
                                },
                                refData:me.ValAceptaAlt,
                                cellClassRules:validarClaseCelda
                            });
                        }
                        else if(x.columna =='ReqMuestras'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:true, 
                                editable: params => params.data.Autorizado != 1 ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        0,
                                        1
                                    ]
                                },
                                refData:me.ValReqMuestras,
                                cellClassRules:validarClaseCelda
                            });
                        }
                        else if(x.columna =='Revisado'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:true, 
                                editable: params => params.data.Autorizado != 1 ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        0,
                                        1
                                    ]
                                },
                                refData:me.ValSolCot,
                                cellClassRules:validarClaseCelda
                            });
                        }
                        else if(x.columna =='Autorizado'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:true, 
                                editable: params => (me.fillPlantilla.Estado =='DIGITADA') ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        '0',
                                        '1'
                                    ]
                                },
                                refData:me.ValAut,
                                cellClassRules:validarClaseCelda
                            });
                        }
                        else if(x.columna =='Opciones' && me.fillPlantilla.Estado == 'DIGITADA'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna, 
                                width : 50,
                                cellRenderer: function(params){
                                    let Homologado = params.data.NmListaCostos;
                                    var tempDiv = document.createElement('div');
                                    if(params.data.Autorizado != 1 && (params.data.EnlaceCot <=0 || !params.data.EnlaceCot )){
                                        if(Homologado){
                                            tempDiv.innerHTML = '<span  class="btn btn-success btn-sm"><i class="fas fa-search"></span>';
                                        }else{
                                            tempDiv.innerHTML = '<span  class="btn btn-primary btn-sm"><i class="fas fa-search"></span>';
                                        }
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1){
                                        me.AbrirHomologacion(params);
                                    }
                                },
                            });
                        }
                        else if(x.columna =='Eliminar'  && me.fillPlantilla.Estado == 'DIGITADA'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                width : 50,
                                cellRenderer: function(params){
                                    
                                    var tempDiv = document.createElement('div');
                                    if(params.data.Autorizado != 1){
                                        tempDiv.innerHTML =
                                        '<span class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></span>';
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1){
                                        me.EliminarDetalle(params);
                                    }
                                },

                            });
                        }
                        else if(x.columna =='Editar'  && me.fillPlantilla.Estado == 'DIGITADA'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                width : 50,
                                cellRenderer: function(params){
                                    var tempDiv = document.createElement('div');
                                    if(params.data.Autorizado != 1){
                                        tempDiv.innerHTML = '<span class="btn btn-info btn-sm"><i class="fas fa-edit"></span>';
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1){
                                        me.EditarDetalle(params);
                                    }
                                },

                            });
                        }
                        else if(x.columna =='IdListaCostosDetPlantDet'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                field : x.columna,
                                width : 50,
                                hide:true,
                            });
                        }
                        else if(x.columna =='IdPlantillaDet'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: 'left',
                                resizable: true, 
                                field : x.columna, 
                                headerCheckboxSelection:true,
                                checkboxSelection:true,
                                valueFormatter: (x.FormatoCelda == 'FormatoMoneda') ? FormatoMoneda: '',
                                sortable: true,
                                filter:'agTextColumnFilter', 
                                editable: Edit, 
                                tooltipField: x.columna,
                                width : 147,
                            });
                        }
                        else if (x.columna !='Editar' && x.columna !='Eliminar' && x.columna !='Opciones'){ 
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true, 
                                field : x.columna, 
                                valueFormatter: (x.FormatoCelda == 'FormatoMoneda') ? FormatoMoneda: '',
                                sortable: true,
                                filter:'agTextColumnFilter', 
                                editable: params => params.data.Autorizado != 1 ? Edit :  false, 
                                cellStyle:params =>{
                                    if(x.columna == 'ItemAba' && params.data.Autorizado && params.data.VendidoAnterioridad ){
                                        return {
                                            backgroundColor: '#7fd47f'
                                        }
                                    }
                                },
                                tooltipField: x.columna,
                                width : 147,
                                cellClass: params => { 
                                    if(params.data.CategoriaPortafolio === 'DC'){
                                        return ['item-descontinuado'];
                                    }
                                    else if(params.data.EnlaceCot >0 ){
                                        return ['item-enviado-cot'];
                                    }
                                },
                            });
                        }
                    }
                });
            }
        },

        validarExist(arreglo,find,campo){
            const Valid = arreglo.filter((busqueda)=>busqueda[campo] == find);
            return Valid.length > 0;
        },

        ValidarDatosCot(){
            let MensajeErr =[];
            let me = this;
            if(!this.FillCrearCot.OpcionItems){
                MensajeErr.push("Debes seleccionar si todos o seleccionados !");
            }
            if(!this.FillCrearCot.CotExist  && !this.FillCrearCot.valuecot){
                MensajeErr.push("Debes seleccionar un tipo de cotización !");
            }
            if(!this.FillCrearCot.CotExist  && !this.FillCrearCot.perteneceCCto){
                MensajeErr.push("Debes seleccionar si pertenece a contrato o no !");
            }
            if(this.FillCrearCot.OpcionItems == 2 && !this.ItemsSeleccionados){
                MensajeErr.push("Seleccionastes la opcion items seleccionados y no hay  ningún item seleccionado");
            }
            if(MensajeErr.length >0){
                MensajeErr.map(function(e){
                    me.AlertMensaje(e,3);
                })
            }
            else{
                this.CrearCotizacion();
            }
        },

        CrearCotizacion(){
            this.MantenerFiltros = true;
            let me = this;
            let url ="/plantillas/clientes/CrearCotizacion";
            const loader = this.loaderk();
            axios.post(url,{
                params:{
                    'FillCrearCot':me.FillCrearCot,
                    'ItemsSeleccionados':me.ItemsSeleccionados,
                    'Plantilla' : me.fillPlantilla,
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                let codmsg = respuesta.status == 500 ? 3 : 1;
                me.AlertMensaje(respuesta.msg,codmsg);
                this.ItemsSeleccionados = [];
                this.MantenerFiltros = true;
                this.listarPlantilla(true);
            }).catch(error =>{
                loader.close();
                this.AlertMensaje(error,3);
                console.log(error)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
            })
        }
    },

    
    beforeMount() {
        //Carga de datos del grid
        let url ="/plantillas/clientes/ObtenerPlantilla/"+ this.$attrs.id;
        let me = this;
        axios.get(url).then(response=>{    
            this.fillPlantilla = response.data.plantilla;
            this.CargarColumnas(response);
        });

        //Fin carga de datos
    },
    mounted() {
        //this.ObtenerPlantilla(this.$attrs.id);
        this.PermisosUser = localStorage.getItem('listPermisosFilterByRolUser');
        this.gridApi = this.gridOptions.api;
        this.gridColumnApi = this.gridOptions.columnApi;
        this.gridApi.closeToolPanel();
        let PanelOculto = localStorage.getItem('panelOculto');
        if(PanelOculto){
            this.OcultarPanel = PanelOculto
        }
    },
    
}

var validarClaseCelda = {
    
    

    'rag-green': function (params) {
        return params.value == 1 ;
    },

}

window.FormatoMoneda = function FormatoMoneda(params){
    let amount = params.value;
    let decimals = 2;
    amount = (amount == null) ? 0 : amount;
    var sign = (amount.toString().substring(0, 1) == "-");

    amount += ''; // por si pasan un numero en vez de un string
    amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

    decimals = decimals || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimals);

    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimals);

    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

    return  sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
}
</script>
<style>
.rag-green {
    background-color: lightgreen;
}
.item-descontinuado{
    background-color:#efedbb;
}
.item-enviado-cot{
    background-color:#86cfe9;
}
.acciones td  button{
    margin-bottom: 4%;
}
.ag-theme-alpine .ag-ltr .ag-cell {
    border-right: 1px solid #e0e0e0;
}
</style>