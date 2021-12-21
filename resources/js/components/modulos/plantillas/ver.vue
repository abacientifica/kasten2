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
                    <b class="opcion-sel" v-if="nmOpcionSeleccionada" v-text="'Configuración Seleccionada : '+nmOpcionSeleccionada"></b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <template v-if="ValidarPermiso('crear')">
                                    <router-link class="btn btn-info btn-sm" :to="{name:'plantillas_clientes.crear'}">
                                        <i class="fas fa-plus-square"></i> Nueva Plantilla
                                    </router-link>
                                </template>
                                <novedades></novedades>
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
                        <button class="btn btn-dark btn-sm btn-margin-left" @click.prevent="AbriModalListaChequeo = true"><i class="fas fa-check-square"></i> Chequeo Procesos</button>
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
                                    <vs-tooltip>
                                        <template #tooltip>
                                            Edita los datos del encabezado.
                                        </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('editar') && fillPlantilla.Estado=='DIGITADA') ? false : true"  @click.prevent="abrirModalEditar">
                                        <i class="fas fa-edit"></i> Editar
                                        </el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>
                                            Autoriza  plantilla pero no los detalles.
                                        </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('autorizar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="Autorizar"><i class="fas fa-check-circle"></i> Autorizar</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Desautoriza  plantilla pero no los detalles. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('desautorizar') && fillPlantilla.Estado=='AUTORIZADA') ? false : true" @click.prevent="DesAutorizar"> <i class="fas fa-minus-circle"></i> Des Autorizar</el-button>
                                    </vs-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">2</th>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Anula la plantilla actual. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('anular') && fillPlantilla.Estado=='AUTORIZADA')? false : true" @click.prevent="Anular"> <i class="fas fa-ban"></i> Anular</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para homologar datos de una cotización o plantilla anterior. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('homologar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalHomologar = true"> <i class="fas fa-link"></i> Homologar</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para enlazar los detalles a la lista principal del proveedor o pasar de una lista general a la especial del cliente</template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('restablecercostos') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalRestablecerCostos = true"> <i class="fas fa-retweet"></i>Restablecer Costos</el-button>
                                    </vs-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">3</th>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para crear una plantilla con los mismos datos. (La plantilla debe estar autorizada)</template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('duplicar')) && fillPlantilla.Estado =='AUTORIZADA' ? false : true"   @click.prevent="duplicarPlantilla()"><i class="fas fa-clone"></i> Duplicar Plantilla</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para eliminar items de la plantilla. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('eliminar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="EliminarDetallesSel" ><i class="fas fa-trash-alt"></i>Eliminar Items</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para crear o enviar items a una cotización </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('crearcot') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalCotizacion = true"><i class="fas fa-align-justify"></i> Crear Cotización</el-button>
                                    </vs-tooltip>
                                </td>
                                <tr>
                                <th scope="row">4</th>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para autorizar masivamente items de la plantilla. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('autorizaritems') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AutorizarDetalles()"><i class="fas fa-edit"></i> Autorizar Items</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para editar los datos principales de la plantilla. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('desautorizaritems') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="DesAutorizarDetalles()"><i class="fas fa-minus-circle"></i>Desautorizar Items</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para marcar los items que se han vendido en un rango de fecha. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('vendidos') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click="AbriModalItemsVendidos = true"><i class="fas fa-chart-line"></i>Marcar Items Vendidos</el-button>
                                    </vs-tooltip>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row">5</th>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Sirve para importar una lista en formato cvs </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('importar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalImportarItems = true"><i class="fas fa-align-justify"></i> Importar Listado</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Calcula el factor </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('calcularfactor') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalCalcFactor = true"><i class="fas fa-calculator"></i> Calcular Factor</el-button>
                                    </vs-tooltip>
                                </td>
                                <td>
                                    <vs-tooltip>
                                        <template #tooltip>Corre factores de otras plantillas donde coincida la unidad. </template>
                                        <el-button type="primary" round :disabled="(ValidarPermiso('correrfactores') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbriModalCorrerFactores = true"><i class="fas fa-running"></i> Correr Factores</el-button>
                                    </vs-tooltip>
                                </td>
                               
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Traer items vendidos en un rango de fecha no homologados</template>
                                            <el-button type="primary" round :disabled="(ValidarPermiso('itemsnohomologados') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalItemsHm = true"><i class="fas fa-clipboard-list"></i> Items no Homologados</el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Actualizar los totales de la plantilla</template>
                                            <el-button type="primary" round :disabled="(ValidarPermiso('actualizar') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="ActualizarValoresPlantilla()"><i class="fas fa-retweet"></i> Actualizar </el-button>
                                        </vs-tooltip>
                                    </td>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Configuraciones predeterminadas de columnas tabla</template>
                                            <el-button type="primary" round :disabled="(ValidarPermiso('configurar_grilla') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalOpcionesGrilla = true"><i class="fas fa-sort-amount-up-alt"></i> Opciones Tabla Dinamica </el-button>
                                        </vs-tooltip>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>
                                        <vs-tooltip>
                                            <template #tooltip>Aplica la opción de licitación a los items seleccionados</template>
                                            <el-button type="primary" round :disabled="(ValidarPermiso('aplicaropcion') && fillPlantilla.Estado=='DIGITADA') ? false : true" @click.prevent="AbrirModalOpciones = true"><i class="fas fa-check-square"></i> Aplicar Opción</el-button>
                                        </vs-tooltip>
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
                                        :on-progress="ProgresoSubidaArchivo"
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

                    <el-dialog title="Correr Factores" :visible.sync="AbriModalCorrerFactores">
                        <div class="form-group row border" >
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Opciones</label><br>
                                    <el-radio v-model="fillCorrerFactores.OpFactores" :label="1">Ultima Plantilla</el-radio>
                                    <el-radio v-model="fillCorrerFactores.OpFactores" :label="null">N/A</el-radio><br>

                                    <label>Id Plantilla</label>
                                    <input type="number" :disabled="fillCorrerFactores.OpFactores  ? true:false" v-model="fillCorrerFactores.nIdPlantilla" class="form-control" placeholder="# Plantilla"><br>

                                    <label>Opciones Items</label><br>
                                    <el-radio v-model="fillCorrerFactores.OpItems" :label="1">Todos</el-radio>
                                    <el-radio v-model="fillCorrerFactores.OpItems" :label="2">Seleccionados</el-radio><br>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbriModalCorrerFactores = false">Cancelar</el-button>
                            <el-button type="primary" :disabled="(fillCorrerFactores.nIdPlantilla !='' ||  fillCorrerFactores.OpFactores) &&  ((fillCorrerFactores.OpItems == 2 && ItemsSeleccionados.length >0) || fillCorrerFactores.OpItems == 1) ? false:true" v-model="fillCorrerFactores.nIdPlantilla" @click="AplicarCorrerFactores()">Aplicar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Lista de Chequeo " :visible.sync="AbriModalListaChequeo" width="65%">
                       <el-table
                            :data="ListaCheck"
                            border
                            :row-class-name="tableClassListaCheck"
                            style="width: 100%">

                            <el-table-column type="expand">
                                <template slot-scope="props">
                                    <p>Descripción de las actividades</p>
                                    <p v-html="props.row.Descripcion"></p>
                                </template>
                            </el-table-column>
                            <el-table-column
                                prop="NmCheck"
                                label="Actividad Check"
                                width="180">
                            </el-table-column>

                            <el-table-column
                                prop="Usuario"
                                label="Usuario"
                                width="70">
                            </el-table-column>

                            <el-table-column
                                prop="FhCheck"
                                label="Fecha Check">
                            </el-table-column>

                            <el-table-column
                                prop="Comentarios"
                                label="Comentario">
                            </el-table-column>

                            <el-table-column
                                width="120"
                                label="Opciones">
                                <template slot-scope="scope">
                                    <el-button
                                        v-if=" !scope.row.Usuario && ValidarPermiso(scope.row.Permiso) && ValidarCheck(scope.$index, scope.row)"
                                        size="mini"
                                        type="success"
                                        @click="AplicarCheckPlantilla(scope.$index, scope.row)"><i class="fas fa-check"></i> Chequear</el-button>
                                    </template>
                            </el-table-column>
                        </el-table>
                    </el-dialog>

                    <el-dialog title="Retablecer Costos " :visible.sync="AbriModalRestablecerCostos" width="65%">
                   
                    <div class="form-group row border" >
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Aplicar Lista General</label><br>
                                <el-button type="primary" @click.prevent="RestablecerCostos" :disabled="ListasGenerales.length<=0">Restablecer Todos</el-button><hr>

                                <label>Desde Lista</label><br>
                                <el-select v-model="restablecerCostos.idListaDesde" clearable placeholder="Select">
                                    <el-option
                                    v-for="item in ListasGenerales"
                                    :key="item.IdListaCostosProv"
                                    :label="item.NmListaCostos"
                                    :value="item.IdListaCostosProv">
                                    </el-option>
                                </el-select>

                                <label>A lista</label>
                                <el-select  clearable v-model="restablecerCostos.idListaHasta" placeholder="Select">
                                    <el-option
                                    v-for="item in ListasEspeciales"
                                    :key="item.IdListaCostosProv"
                                    :label="item.NmListaCostos"
                                    :value="item.IdListaCostosProv">
                                    </el-option>
                                </el-select>

                                <label>Opciones Items</label><br>
                                <el-radio v-model="restablecerCostos.opItems" label="opTodos">Todos</el-radio>
                                <el-radio v-model="restablecerCostos.opItems" label="opSel">Seleccionados</el-radio><br>
                                <label>Aplicar Costos Proximos</label><br>
                                <el-switch
                                    v-model="restablecerCostos.costosProximos"
                                    active-text="Aplicar"
                                    inactive-text="No Aplicar">
                                </el-switch><br>
                                <el-button type="primary" @click.prevent="CorrerCostos()" :disabled="!restablecerCostos.idListaDesde && !restablecerCostos.idListaHasta && (!restablecerCostos.opTodos || !restablecerCostos.opSeleccionados)">Aplicar</el-button><br>
                            </div>
                        </div>
                    </div>
                    </el-dialog>

                    <el-dialog title="Agregar Items No Homologados" :visible.sync="AbrirModalItemsHm">
                        <div class="form-group row border" >
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Selecione un rango de fechas:</label>
                                    <el-date-picker
                                            v-model="oRangoFechasItemsNoHm"
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
                            <el-button @click="AbrirModalItemsHm = false">Cancelar</el-button>
                            <el-button type="success" @click="AgregarItemsNoHomologados()">Aplicar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Opciones Tabla Dinamica" :visible.sync="AbrirModalOpcionesGrilla">
                        <div class="form-group row border" >
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Opciones</label>
                                    <el-select v-model="grillaSeleccionada" placeholder="Seleccione" clearable>
                                        <el-option :key="0" :label="'Predeterminada'" :value="0"></el-option>
                                        <el-option
                                            v-for="item in configuracionesGrilla"
                                            :key="item.id"
                                            :label="item.Descripcion"
                                            :value="item.id"
                                            >
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbrirModalOpcionesGrilla = false">Cancelar</el-button>
                            <el-button type="primary" :disabled="grillaSeleccionada == null" @click="cambiarGrilla()">Aplicar</el-button>
                        </span>
                    </el-dialog>

                    <el-dialog title="Aplicar Opcion" :visible.sync="AbrirModalOpciones">
                        <div class="form-group row border" >
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Opciones</label>
                                    <el-select v-model="opcionLicitaction" placeholder="Seleccione" clearable>
                                        <el-option :key="1" :label="'1'" :value="1"></el-option>
                                        <el-option :key="2" :label="'2'" :value="2"></el-option>
                                        <el-option :key="3" :label="'3'" :value="3"></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AbrirModalOpciones = false">Cancelar</el-button>
                            <el-button type="primary" :disabled="opcionLicitaction == null || ItemsSeleccionados.length <=0" @click="aplicarOpcionlic()">Aplicar</el-button>
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
                                <label class='label-strong margen-label-encabezado'>Periodo Año-Mes</label>
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
                                @cell-value-changed="onCellValueChanged"
                                @row-value-changed="onRowValueChanged"
                                @selection-changed="onSelectionChanged">
                            </ag-grid-vue>
                        </div>
                    </div>
                    <el-dialog :title="TituloModalNovedad" :visible.sync="dialogNovedades">
                        <el-table :data="Novedades">
                            <el-table-column property="FhNovedad" label="Fecha" width="150"></el-table-column>
                            <el-table-column property="Comentarios" label="Descripción" width="350"></el-table-column>
                            <el-table-column property="FhPosibleSolucion" label="Fh. Posible Solucion" width="150"></el-table-column>
                        </el-table>
                    </el-dialog>
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
import Novedades from '../../plantilla/plantillasclientes/Novedades.vue';
export default {
    components: {
        AgGridVue,
        "v-select": vSelect,
        'novedades':Novedades
    },
    data() {
        return {
            stateLoader:null,
            //Parametros de configuracion de la grilla
            columnDefs: [],
            rowData: [],
            paginationPageSize:100,
            //gridOptions: null,
            gridOptions :{
                onFilterChanged:this.CambioFiltros,
                onColumnMoved:this.GuardarOrdenColumnas,
                onColumnVisible:this.ColumnasVisibles,
                onSortChanged:this.GuardarOrdenColumnas,
                onColumnPinned:this.GuardarOrdenColumnas,
                onPaginationChanged:this.cambioPagina
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
            refsOpcion:{
                null:'',
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
            opDetallesHm:null,
            oFechasCot:[],
            fillHm:{
                NroCot:'',
                IdPlantilla:'',
                Grupo:''
            },
            //Cotizacion
            AbrirModalCotizacion: false,
            
            //Restablecer Costos
            AbriModalRestablecerCostos:false,
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
            },

            //Variables Correr Factores
            AbriModalCorrerFactores:false,
            fillCorrerFactores:{
                OpItems:1,
                nIdPlantilla:'',
                OpFactores: null,
            },

            //Variables lista chequeo
            AbriModalListaChequeo:false,
            ListaCheck:[],

            //Novedades
            dialogNovedades:false,
            Novedades:[],
            TituloModalNovedad:'',

            //Correr Costos
            ListasGenerales:[],
            ListasEspeciales:[],
            restablecerCostos:{
                IdPlantilla:this.$attrs.id,
                idListaDesde:null,
                idListaHasta:null,
                opTodos:false,
                opSeleccionados:false,
                opItems:['opTodos','opSel'],
                costosProximos:false
            },
            //Items no Homologados
            AbrirModalItemsHm:false,
            oRangoFechasItemsNoHm:false,

            //Configuraciones grilla
            configuracionesGrilla:null,
            configuracionesGrillaDet:null,
            AbrirModalOpcionesGrilla:false,
            grillaSeleccionada:null,
            nmOpcionSeleccionada:null,
            //
            AbrirModalOpciones:false,
            opcionLicitaction:null,
            pageActual:this.gridOptions
        }
    },
    watch:{
        EditarDet(){

        },
        AbriModalListaChequeo(){
            this.AbriModalListaChequeo ? this.ObtenerListaChequeo() : '';
        },
        dialogNovedades(){
            !this.dialogNovedades ? this.Novedades = [] : '';
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
        cambioPage(){
            if(this.pageActual){
                console.log(this.gridOptions.api.paginationGetCurrentPage(),localStorage.getItem('paginaActual'))
                console.log("Cambio")
            }
            return true;
        }
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
            const loaders = this.loaderk();
            axios.get(url,{params:{
                'filtros':Filtros
            }}).then(response=>{    
                this.fillPlantilla = response.data.plantilla;
                this.fillDetallesPlantilla = response.data.plantillas_det;
                this.ListasGenerales = response.data.datos_listas.listas;
                this.ListasEspeciales = response.data.datos_listas.listaesp;
                this.configuracionesGrilla = response.data.configuraciones;
                this.configuracionesGrillaDet = response.data.configuraciones_det;
                
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
                this.CargarColumnas(response);
                this.rowData = this.fillDetallesPlantilla;
                this.FillCrearCot.ItemsPlantilla = this.rowData;
            }
            let pinnedButtomData = this.generatePinnedButtomData();
            this.gridApi.setPinnedBottomRowData([pinnedButtomData]);
            /*let filtros = JSON.parse(localStorage.getItem('filtros'));
            this.gridApi.setFilterModel(filtros);*/
            //Refrescamos los botones renderizados en la grilla
            this.gridApi.refreshCells({ force: true })
            this.OpcionAccionDets = null;
            if(localStorage.getItem('grillaSel') && localStorage.getItem('grillaSel') > 0 && this.configuracionesGrilla){
                let NmOpcionSel = this.configuracionesGrilla.filter(filtro => filtro.id == parseInt(localStorage.getItem('grillaSel')));
                NmOpcionSel = NmOpcionSel[0];
                this.nmOpcionSeleccionada = NmOpcionSel.Descripcion;
            }
            else{
                this.nmOpcionSeleccionada = 'Predeterminada ';
            }
            loaders.close();
            
            }).catch(error =>{
                loaders.close();
                console.log(error)
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
                coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
                if(coderror == 401 || coderror == 419){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
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
                this.$confirm('Esto eliminará permanentemente '+this.ItemsSeleccionados.length+' items seleccionados . ¿Continuar?', 'Warning', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
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
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Transacción cancelada.'
                    });          
                });
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
            /*if(plantilla.dPeriodoAnio == null || plantilla.dPeriodoAnio == 0){
                this.arrMensajeError.push("Ingresa un periodo Año");
            }
            if(plantilla.dPeriodoMes == null || plantilla.dPeriodoMes == 0){
                this.arrMensajeError.push("Ingresa un periodo mes");
            }*/
            
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
            this.pageActual = this.gridOptions.api.paginationGetCurrentPage();
            this.gridOptions.api.paginationGoToPage(parseInt(localStorage.getItem('paginaActual')));
            if(localStorage.getItem('itemHmActual')){
                let index = parseInt(localStorage.getItem('itemHmActual'))
                this.gridOptions.api.forEachNode(node=>{
                    if(node.rowIndex === index)  node.setSelected(true)
                })
                this.gridOptions.api.setFocusedCell(index,'Opciones');
                localStorage.removeItem('itemHmActual')
            }
        },

        AbrirHomologacion(params){
            let me = this;
            me.datosfiltrados = [];
            this.gridApi.forEachNodeAfterFilterAndSort(function(node,index){
                me.datosfiltrados.push(node);
            });
            localStorage.setItem('itemHmActual',params.rowIndex);
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
                this.DatoEditado.push({'Columna':Columna,'DatoNuevo':DatoNuevo,'DatoAnt':DatoAnterior,'index':event.rowIndex});
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

        getData(){
            let rowData =[];
            this.gridApi.forEachNodeAfterFilterAndSort(node=> rowData.push(node.data));
            console.log(rowData)
            return rowData;
        },

        setItemGrilla(index,data){
            console.log(index,data)
            let rowData =[];
            this.gridApi.forEachNode(node=> {
                if(node.rowIndex == index){
                    node.data = data;
                    rowData = node.data;
                }
            });
            return rowData;
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
                this.AlertMensaje(respuesta.msg,1,1200);
                let rowDataUpdate = [];
                rowDataUpdate.push(me.setItemGrilla(Datos[0].index , respuesta.detalle[0]));
                this.gridApi.applyTransaction({ update: rowDataUpdate });
                let pinnedButtomData = me.generatePinnedButtomData();
                me.gridApi.setPinnedBottomRowData([pinnedButtomData]);
            }).catch(error=>{
                this.MantenerFiltros = true;
                this.listarPlantilla(true);
                console.log(error)
                this.AlertMensaje('Ocurrio un error al editar ',3);
            })
        },

        CambioFiltros(params){
            localStorage.removeItem('filtros');
            let DatosFiltro = JSON.stringify(params.api.getFilterModel());
            localStorage.setItem('filtros',DatosFiltro);
            this.GuardarFiltros();
            let pinnedButtomData = this.generatePinnedButtomData();
            this.ItemsSeleccionados.length >0 ? this.ItemsSeleccionados = [] : '';
            if(!localStorage.getItem('itemHmActual')) this.gridOptions.api.deselectAll();
            this.gridApi.setPinnedBottomRowData([pinnedButtomData]);
        },

        generatePinnedButtomData(){
            let result = {};
            this.gridColumnApi.getAllGridColumns().forEach(item =>{
                result[item.colId] = null;
            });
            return this.calculatedPinnedButtomData(result);
        },

        calculatedPinnedButtomData(target){
            let columnWithAggregation = ['IdPlantillaDet','SubTotal','SubTotalVenta','SubTotalConsumo'];
            let me = this;
            columnWithAggregation.forEach(element=>{
                if(element != 'IdPlantillaDet'){
                    me.gridApi.forEachNodeAfterFilter(function (rowNode, index){
                        target[element] += Number(typeof rowNode.data[element] =='number' && rowNode.data[element].toFixed(2));
                    });
                    if(target[element]){
                        target[element] = `${target[element].toFixed(2)}`;
                    }
                }
                else{
                    target[element] = 'Totales :'
                }
            })
            return target;
        },

        GuardarOrdenColumnas(params){
            localStorage.removeItem('columnas');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas',DatosFiltro);
            this.GuardarFiltros();
        },

        ColumnasVisibles(params){
            localStorage.removeItem('columnas');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas',DatosFiltro);
            this.GuardarFiltros();
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

        AlertMensaje(Msg,Tipo = '',time=12000){
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
                duration:time,
                progress: 'auto',
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
            if(this.PermisosUser.includes('plantillas_clientes.'+permiso) || this.PermisosUser.includes(permiso) || this.PermisosUser.includes('administrador.sistema') || this.PermisosUser.includes('plantillas_cliente_det.'+permiso)){
                return true;
            }
            else{
                return false;
            }
        },

        OcultarModalHM(){
            this.MantenerFiltros = true;
            this.showHomologar = false;
            //this.listarPlantilla(true);
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
            this.stateLoader.close();
            this.listarPlantilla();
            this.AlertMensaje(res.msg,1);
            if(this.rowData.length <=0){
                //window.location.reload();
            }
        },

        ArchivoImportadoError(res){
            console.log(res)
            this.stateLoader.close();
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
                let columnas = [];
                me.grillaSeleccionada = me.grillaSeleccionada ? me.grillaSeleccionada : localStorage.getItem('grillaSel');
                me.grillaSeleccionada = me.grillaSeleccionada ?  parseInt(me.grillaSeleccionada) : null;
                if((me.grillaSeleccionada && me.grillaSeleccionada >0) && me.configuracionesGrillaDet){
                    var grilla = me.configuracionesGrillaDet.filter(filter=>{ return filter.id == me.grillaSeleccionada})
                    grilla.map((e)=>{
                        if(e.IdCampo){
                            columnas.push({
                                'columna':e.IdCampo,
                                'alias':e.AliasCampo,
                                'pinned':e.pinned,
                                'FormatoCelda':e.FormatoCelda,
                                'ancho':e.Ancho,
                                'edit':e.editable,
                                'visible':e.visible,
                                'filtro':e.filtro,
                                'permiso':e.PermisoEditar
                            })
                        }
                    });
                    columnas.length >0 ? columnas.push({'columna':'Opciones' ,'alias':'HM','pinned':'right','edit':'false'}) : '';
                }
                this.fillColumnas = columnas.length >0 ? columnas : response.data.columnas;
                this.fillColumnas.map(function(x,y){
                    if(x.columna != null && (x.permiso_ver && me.ValidarPermiso(x.permiso_ver) || !x.permiso_ver)){
                        let Edit = (x.edit == 'true' && me.fillPlantilla.Estado =='DIGITADA' && (me.ValidarPermiso('editardetallles')  || (x.permiso && me.ValidarPermiso(x.permiso)) )) ? true: false;
                        let filtro = x.filtro == 'true' ? true : x.filtro;
                        let colVisible = x.visible =='false' ? true: false;
                        
                        if(x.columna =='AceptaAlternativa'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:filtro, 
                                hide:colVisible,
                                editable: params => params.data.Autorizado != 1 && !params.node.rowPinned ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        '0',
                                        '1'
                                    ]
                                },
                                refData:me.refsOpcion,
                                cellClassRules:validarClaseCelda,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                         return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                filter:filtro, 
                                hide:colVisible,
                                editable: params => params.data.Autorizado != 1 && !params.node.rowPinned ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        0,
                                        1
                                    ]
                                },
                                refData:me.refsOpcion,
                                cellClassRules:validarClaseCelda,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                         return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                filter:filtro, 
                                hide:colVisible,
                                editable: params => params.data.Autorizado != 1 && !params.node.rowPinned ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        '',
                                        0,
                                        1
                                    ]
                                },
                                refData:me.refsOpcion,
                                cellClassRules:validarClaseCelda,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                         return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                filter:filtro, 
                                hide:colVisible,
                                editable: params => (me.fillPlantilla.Estado =='DIGITADA' && !params.node.rowPinned && params.data.IdListaCostosDetPlantDet > 0) ? Edit :  false, 
                                width : 147 ,
                                cellEditor:'select',
                                cellEditorParams:{
                                    values:[
                                        '',
                                        '0',
                                        '1'
                                    ]
                                },
                                refData:me.refsOpcion,
                                cellClassRules:validarClaseCelda,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
                            });
                        }
                        else if(x.columna =='VendidoAnterioridad'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                sortable: true,
                                filter:filtro, 
                                editable: me.Edit,
                                refData:me.ValAceptaAlt,
                                cellClassRules:validarClaseCelda,
                                hide:colVisible,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                        if(!params.node.rowPinned){
                                            if(Homologado){
                                                tempDiv.innerHTML = '<span  class="btn btn-success btn-sm"><i class="fas fa-search"></span>';
                                            }else{
                                                tempDiv.innerHTML = '<span  class="btn btn-primary btn-sm"><i class="fas fa-search"></span>';
                                            }
                                        }
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1){
                                        me.AbrirHomologacion(params);
                                    }
                                },
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                    if(params.data.Autorizado != 1 && !params.node.rowPinned){
                                        tempDiv.innerHTML =
                                        '<span class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></span>';
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1 && !params.node.rowPinned){
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
                                    if(params.data.Autorizado != 1 && !params.node.rowPinned){
                                        tempDiv.innerHTML = '<span class="btn btn-info btn-sm"><i class="fas fa-edit"></span>';
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    if(params.data.Autorizado != 1 && !params.node.rowPinned){
                                        me.EditarDetalle(params);
                                    }
                                },

                            });
                        }
                        else if(x.columna =='IdListaCostosDetPlantDet'){
                            me.columnDefs.push({
                                headerName: 'Id Lista C.',
                                headerClass:'bg-info',
                                field : x.columna,
                                resizable: true, 
                                filter:filtro, 
                                width : 50,
                                hide:true,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
                            });
                        }
                        else if(x.columna =='IdPlantillaDet'){
                            me.columnDefs.push({
                                headerClass:'bg-info',
                                headerName: x.alias,
                                pinned: 'left',
                                resizable: true, 
                                field : x.columna, 
                                cellRendererFramework: 'novedades',
                                headerCheckboxSelection:true,
                                checkboxSelection:true,
                                valueFormatter: (x.FormatoCelda == 'FormatoMoneda') ? FormatoMoneda: '',
                                sortable: true,
                                filter:filtro, 
                                editable: Edit, 
                                tooltipField: x.columna,
                                cellClassRules:validarClaseCeldaItemNovedad,
                                width : 147,
                                cellStyle:(params)=>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }
                                }
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
                                filter:filtro, 
                                hide:colVisible,
                                editable: params => params.data.Autorizado != 1 && !params.node.rowPinned ? Edit :  false, 
                                cellStyle:params =>{
                                    if(params.node.rowPinned){
                                        return  { 'font-style': 'italic','background-color':'#87a1ea','font-weight': 'bold','text-align': 'right' } ;
                                    }

                                    if(x.columna == 'ItemAba' && params.data.Autorizado && params.data.VendidoAnterioridad ){
                                        return {
                                            'background-color': '#7fd47f'
                                        }
                                    }
                                    if(typeof params.value === 'number' || me.Is_Float(params.value) || !isNaN(params.value)){
                                        return { 'text-align': 'right' }
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
                                cellRendererSelector:(params)=>{
                                    if(params.node.rowPinned){
                                        return {
                                            params: { style: { 'font-style': 'italic','background-color':'red','font-weight': 'bold','text-align': 'right'} },
                                        };
                                    }
                                }
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
        },

        GuardarFiltros(){
            let me = this;
            let url ="/plantillas/clientes/GuardarFiltro";
            axios.post(url,{
                params:{
                    'filtros':localStorage.getItem('filtros'),
                    'columnas':localStorage.getItem('columnas'),
                }
            }).then(response=>{    
                let respuesta = response.data;
            }).catch(error =>{
                console.log(error)
            })
        },

        ObtenerFiltros(){
            let me = this;
            let url ="/plantillas/clientes/ObtenerFiltro";
            axios.get(url).then(response=>{    
                let respuesta = response.data;
                if(respuesta.filtros !=''){
                    localStorage.setItem('filtros',(respuesta.filtros));
                }
                if(respuesta.columnas !=''){
                    localStorage.setItem('columnas',(respuesta.columnas));
                }
            }).catch(error =>{
                console.log(error)
            })
        },

        ProgresoSubidaArchivo(event, file, fileList){
            console.log(event)
            const loader = this.loaderk();
            this.stateLoader = loader;
        },

        AplicarCorrerFactores(){
            console.log([this.fillCorrerFactores])
            let me = this;
            let url ="/plantillas/clientes/CorrerFactores";
            const loader = this.loaderk();
            axios.put(url,{
                'IdPlantilla':me.fillPlantilla.IdPlantilla,
                'nIdPlantilla':me.fillCorrerFactores.nIdPlantilla,
                'OpPlantilla':me.fillCorrerFactores.OpFactores,
                'OpItems':me.fillCorrerFactores.OpItems,
                'ItemsSeleccionados':me.ItemsSeleccionados
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                let codmsg = respuesta.status == 500 ? 3 : 1;
                me.AlertMensaje(respuesta.msg,codmsg);
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
        },

        ObtenerListaChequeo(){
            let me = this;
            let url ="/plantillas/listachequeo/"+ me.fillPlantilla.IdPlantilla;
            axios.get(url).then(response=>{    
                let respuesta = response.data;
                me.ListaCheck = respuesta.lista_chequeo;
                console.log(respuesta)
            }).catch(error =>{
                console.log(error)
            })
        },

        tableClassListaCheck({row, rowIndex}) {
            if(row.Usuario){
                return 'success-row';
            }
            return '';
        },

        AplicarCheckPlantilla(index,row){
            let me = this;
            this.$prompt('Ingrese un comentario ', 'Estas seguro(a) de aplicar check '+row.NmCheck+' !!', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                inputPattern:/^[^]+$/,
                inputErrorMessage: 'El comentario es obligatorio.',
                type: 'warning'
            }).then(({value}) => {
                const loader = me.loaderk();
                let url ="/plantillas/listachequeo/chequear";
                axios.put(url,{
                    'IdPlantilla':me.fillPlantilla.IdPlantilla,
                    'IdCheck':row.Id_Check,
                    'Comentarios':value
                }).then(response=>{    
                    let respuesta = response.data;
                    me.ListaCheck = respuesta.lista_chequeo;
                    loader.close();
                    me.$message({
                        type: 'success',
                        message: row.NmCheck + ' Chequeada correctamente'
                    });
                }).catch(error =>{
                    loader.close();
                    me.AlertMensaje(error,3);
                    console.log(error);
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Operacion Cancelada'
                });          
            });
            //console.log({index,row})
        },

        ValidarCheck(index,row){
            let me = this;
            let CheckAnt = this.ListaCheck[index -1];
            if(CheckAnt){
                if(!CheckAnt.Usuario && CheckAnt.Obligatorio){
                    return false
                }
            }
            return true;
        },

        CorrerCostos(){
            let me = this;
            let url = "/plantillas/clientes/CorrerCostos";
            const loaders = this.loaderk();
            this.MantenerFiltros = true;
            axios.put(url,{
                'restablecerCostos':me.restablecerCostos,
                'ItemsSeleccionados':me.ItemsSeleccionados
            }).then(response=>{
                let respuesta = response.data;
                me.AlertMensaje(respuesta.msg,1)
                loaders.close();
                respuesta.items_actualizados > 0 ? me.listarPlantilla() : this.MantenerFiltros = false;
                me.restablecerCostos.idListaDesde = null;
                me.restablecerCostos.idListaHasta = null;
                me.restablecerCostos.opTodos=false;
                me.restablecerCostos.opSeleccionados=false;
                me.restablecerCostos.costosProximos=false;
            }).catch(error=>{
                loaders.close();
                me.AlertMensaje(error,3)
            })
        },

        AgregarItemsNoHomologados(){
            let me = this;
            let url ="/plantillas/clientes/AgregarNoHomologados";
            const loader = me.loaderk();
            axios.put(url,{
                params:{
                    'IdPlantilla':me.fillPlantilla.IdPlantilla,
                    'oRangoFecha':me.oRangoFechasItemsNoHm,
                }
            }).then(response=>{    
                let respuesta = response.data;
                me.AlertMensaje(respuesta.msg,1);
                me.MantenerFiltros = false;
                me.listarPlantilla();
                loader.close();
                console.log(respuesta)
            }).catch(error =>{
                me.AlertMensaje("Ocurrio un error al generar los no homologados",3);
                loader.close();
                console.log(error)
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
                coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
                if(coderror == 401 || coderror == 419){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                }
            })
        },

        ActualizarValoresPlantilla(){
            let me = this;
            let url ="/plantillas/clientes/Actualizar";
            const loader = me.loaderk();
            axios.put(url,{
                params:{
                    'IdPlantilla':me.fillPlantilla.IdPlantilla,
                }
            }).then(response=>{    
                let respuesta = response.data;
                me.AlertMensaje(respuesta.msg,1);
                me.MantenerFiltros = true;
                me.listarPlantilla();
                loader.close();
            }).catch(error =>{
                me.AlertMensaje("Ocurrio un error al generar los no homologados",3);
                loader.close();
                console.log(error)
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
                coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
                if(coderror == 401 || coderror == 419){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                }
            })
        },

        cambiarGrilla(){
            if(this.grillaSeleccionada || this.grillaSeleccionada == 0){
                this.MantenerFiltros = false;
                localStorage.removeItem('columnas');
                this.listarPlantilla();
                localStorage.setItem('grillaSel',Number(this.grillaSeleccionada));
                let filtros = JSON.parse(localStorage.getItem('filtros'));
                this.gridApi.setFilterModel(filtros);
                let NmOpcionSel = this.configuracionesGrilla.filter(filtro => filtro.id == this.grillaSeleccionada);
                NmOpcionSel = NmOpcionSel[0];
                this.nmOpcionSeleccionada = this.grillaSeleccionada == 0 ? 'Predeterminada' : NmOpcionSel.Descripcion;
                this.AlertMensaje('Has seleccionado la configuración :' + this.nmOpcionSeleccionada +", recuerda que esta trae unas columnas configuradas por defecto.",1);
                this.AbrirModalOpcionesGrilla = false;
                this.dialogAcciones = false;
            }
            else{
                this.AlertMensaje('Debes Seleccionar una opcion de grilla',3);
            }
        },


        aplicarOpcionlic(){
            if(this.ItemsSeleccionados.length >0){
                let url ="/plantillas/clientes/AplicarOpcionLicitacion"
                let me = this;
                axios.put(url,{params:{
                    'IdPlantilla':me.fillPlantilla.IdPlantilla,
                    'ItemsSel':me.ItemsSeleccionados,
                    'Opcion':me.opcionLicitaction
                }}).then(response=>{
                    let respuesta = response.data;
                    me.MantenerFiltros = true;
                    me.listarPlantilla();
                    me.AlertMensaje(respuesta.msg, 1);
                }).catch(error=>{
                    let codError = this.serviceApp.ObtenerTpError(error);
                    if(codError){
                        if(coderror == 401 || coderror == 419){
                            this.$router.push({name: 'login'})
                            location.reload();
                            sessionStorage.clear();
                        }
                    }
                    console.log(error);
                    this.AlertMensaje('Ocurrio un error al aplicar la opción ', 3)
                })
            }
            else{
                this.AlertMensaje("Debes seleccionar al menos 1 registro",2);
            }
        },

        duplicarPlantilla(){
            this.$prompt('Estas seguro(a) de duplicar esta plantilla. Continuar? Ingresa un comentario ', 'Alerta', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                type: 'warning'
            }).then(({value}) => {
                let me = this;
                const loader = me.loaderk();
                me.dialogAcciones = !me.dialogAcciones;
                axios.put('/plantillas/clientes/duplicar',{
                    'IdPlantilla':this.$attrs.id,
                    'comentario': value
                }).then(response=>{
                    let respuesta = response.data;
                    if(respuesta.status === 201){
                        me.AlertMensaje(`${respuesta.msg}, lo estamos redirigiendo ...`,1);
                        setTimeout(()=>{
                            loader.close();
                            me.$router.push('/plantillas/clientes/ver/'+respuesta.id);
                            location.reload();
                        },6000)
                    }
                }).catch(error=>{
                    me.AlertMensaje(`${respuesta.msg},intenta nuevamente ...`,3);
                })
            }).catch(()=>{
                this.$message({
                    type: 'info',
                    message: 'Transacción cancelada'
                });  
            })
        },

        cambioPagina(params){
            params.newPage ? localStorage.setItem('paginaActual',this.gridApi.paginationGetCurrentPage()) : '';
        }
    },

    
    beforeMount() {
        //Carga de datos del grid
        try{
            let url ="/plantillas/clientes/ObtenerPlantilla/"+ this.$attrs.id;
            let me = this;
            axios.get(url).then(response=>{    
                this.fillPlantilla = response.data.plantilla;
                this.CargarColumnas(response);
            });
        }
        catch(error){
            let msgerror = error.message.split(" ");
            let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
            coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
            if(coderror == 401 || coderror == 419){
                this.$router.push({name: 'login'})
                location.reload();
                sessionStorage.clear();
            }
        }
        
        //Fin carga de datos
    },
    mounted() {
        //this.ObtenerPlantilla(this.$attrs.id);
        EventBus.$on('ItemHomologado',data=>{
            //console.log("Item Homologado",data)
            this.setItemGrilla(data.index , data.data);
            this.rowData = this.getData();
            this.gridApi.applyTransaction({ update: this.rowData });
            let pinnedButtomData = this.generatePinnedButtomData();
            this.gridApi.setPinnedBottomRowData([pinnedButtomData]);
        });

        EventBus.$on('ItemDesHomologado',data=>{
            //console.log("Item Des Homologado",data)
            this.setItemGrilla(data.index , data.data);
            this.rowData = this.getData();
            this.gridApi.applyTransaction({ update: this.rowData });
            let pinnedButtomData = this.generatePinnedButtomData();
            this.gridApi.setPinnedBottomRowData([pinnedButtomData]);
        })

        EventBus.$on('cambioPageHomologar',data=>{
            let page = data.index <=99 ? 0 : Math.floor((data.index) / 100)
            localStorage.setItem('paginaActual',page);
            localStorage.setItem('itemHmActual',data.index);
            this.gridOptions.api.deselectAll();
            this.gridOptions.api.forEachNode(node=>{
                if(node.rowIndex === data.index) node.setSelected(true)
            })
            this.gridOptions.api.paginationGoToPage(page);
        })

        try{
            this.PermisosUser = localStorage.getItem('listPermisosFilterByRolUser');
            this.gridApi = this.gridOptions.api;
            this.gridColumnApi = this.gridOptions.columnApi;
            this.gridApi.closeToolPanel();
            let PanelOculto = localStorage.getItem('panelOculto');
            if(PanelOculto){
                this.OcultarPanel = PanelOculto
            }
            this.ObtenerFiltros();
            EventBus.$on('arrNovedades',data =>{
                this.dialogNovedades = !this.dialogNovedades
                this.TituloModalNovedad = 'Novedades Item',
                this.Novedades = data;
            });
        }
        catch(error){
            let msgerror = error.message.split(" ");
            let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
            coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
            if(coderror == 401 || coderror == 419){
                this.$router.push({name: 'login'})
                location.reload();
                sessionStorage.clear();
            }
        }
    },
    
}

var validarClaseCelda = {
    'rag-green': function (params) {
        return params.value == 1 ;
    },

    'item-act-hm':(params)=>{
        if(localStorage.getItem('itemHmActual')) return params.rowIndex === localStorage.getItem('itemHmActual')
    }

}
var validarClaseCeldaItemNovedad = {
    'item-novedad': function (params) {
        return params.data.EnNovedad;
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
.item-act-hm{
    background-color: lightgreen;
}
.rag-green {
    background-color: lightgreen;
}
.item-novedad {
    background-color: #fbc9c9;
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
.aling-number{
    text-align: right;
}
.el-table .warning-row {
    background: oldlace;
}

.el-table .success-row {
    background: #f0f9eb;
}
.opcion-sel{
    margin-left: 30%;
    color: rgb(199, 178, 153);;
}
.ag-theme-alpine .ag-icon-desc:before , .ag-theme-alpine .ag-icon-asc:before, .ag-theme-alpine .ag-icon-menu:before{
    color:white;
}
</style>