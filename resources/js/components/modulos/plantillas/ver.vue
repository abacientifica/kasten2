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
                    <b class="aling-left" v-if="fillPlantilla.tercero" v-text="fillPlantilla.tercero.NombreCorto"></b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <template v-if="listPermisosFilterByRolUser.includes('plantillas_clientes.crear')">
                                    <template v-if="listPermisosFilterByRolUser.includes('plantillas_clientes.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                            <router-link class="btn btn-info btn-sm" :to="{name:'plantillas_clientes.crear'}">
                                                <i class="fas fa-plus-square"></i> Nueva Plantilla
                                            </router-link>
                                    </template>
                                </template>
                                <router-link class="btn btn-info btn-sm" :to="{name:'plantillas_clientes.index'}">
                                    <i class="fas fa-arrow-left"></i> Regresar
                                </router-link>
                                <modal :titulo="'Ayudas Plantillas'" :iddoc="85" :url="'plantillas_clientes.crear'"></modal>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 btn-group-justified"  style="display:flex" >
                        <logacciones :IdMovimiento ="fillPlantilla.IdPlantilla"></logacciones>
                    </div><hr>
                    <div class="form-group row border">
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
                                <p class="p-encabezado" >{{fillPlantilla.Total}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Periodo</label>
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
                                style="width: 100%; height: 700px;"
                                class="ag-theme-alpine"
                                @gridReady="onGridReady"
                                :gridOptions="gridOptions"
                                :localeText="localeText"
                                :columnDefs="columnDefs"
                                :rowData="fillDetallesPlantilla"
                                :menuTabs="['filterMenuTab']"
                                :editType="editType"
                                :pagination="true"
                                :paginationPageSize="paginationPageSize"
                                @cell-value-changed="onCellValueChanged"
                                @row-value-changed="onRowValueChanged">
                            </ag-grid-vue>
                        </div>
                    </div>
                    <homologar-plantillas :visible="showHomologar" :datosItem="DatosHomologar" :datos="datosfiltrados" :datosPl="fillPlantilla"  v-on:OcultarModal = "showHomologar = false"></homologar-plantillas>
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
import { AgGridVue } from "ag-grid-vue";
export default {
    components: {
        AgGridVue
    },
    data() {
        return {
            columnDefs: [],
            rowData: [],
            autoGroupColumnDef: null,
            paginationPageSize:100,
            gridOptions: null,
            gridApi: null,
            columnApi: null,
            defaultColDef: null,
            editType: null,
            Columnas:[],

            usuario:'',
            direccion:[],
            arrMensajeError:[],
            OpPedido:8,
            accionMovimiento:0,
            listPermisosFilterByRolUser:[],
            fillPlantilla:[],
            datosfiltrados:[],

            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },

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
            localeText :{
                // for filter panel
                page: 'Pagina',
                more: 'Mas',
                to: 'a',
                of: 'de',
                next: 'Siguente',
                last: 'Último',
                first: 'Primero',
                previous: 'Anteror',
                loadingOoo: 'Cargando...',
                
                // for set filter
                selectAll: 'Seleccionar Todo',
                searchOoo: 'Buscar...',
                blanks: 'En blanco',

                // for number filter and text filter
                filterOoo: 'Filtrar',
                applyFilter: 'Aplicar Filtro...',
                equals: 'Igual',
                notEqual: 'No Igual',

                // for number filter
                lessThan: 'Menos que',
                greaterThan: 'Mayor que',
                lessThanOrEqual: 'Menos o igual que',
                greaterThanOrEqual: 'Mayor o igual que',
                inRange: 'En rango de',

                // for text filter
                contains: 'Contiene',
                notContains: 'No contiene',
                startsWith: 'Empieza con',
                endsWith: 'Termina con',

                // filter conditions
                andCondition: 'Y',
                orCondition: 'O',

                // the header of the default group column
                group: 'Grupo',

                // tool panel
                columns: 'Columnas',
                filters: 'Filtros',
                valueColumns: 'Valos de las Columnas',
                pivotMode: 'Modo Pivote',
                groups: 'Grupos',
                values: 'Valores',
                pivots: 'Pivotes',
                toolPanelButton: 'BotonDelPanelDeHerramientas',

                // other
                noRowsToShow: 'No hay filas para mostrar',

                // enterprise menu
                pinColumn: 'Columna Pin',
                valueAggregation: 'Agregar valor',
                autosizeThiscolumn: 'Autoajustar esta columna',
                autosizeAllColumns: 'Ajustar todas las columnas',
                groupBy: 'agrupar',
                ungroupBy: 'desagrupar',
                resetColumns: 'Reiniciar Columnas',
                expandAll: 'Expandir todo',
                collapseAll: 'Colapsar todo',
                toolPanel: 'Panel de Herramientas',
                export: 'Exportar',
                csvExport: 'Exportar a CSV',
                excelExport: 'Exportar a Excel (.xlsx)',
                excelXmlExport: 'Exportar a Excel (.xml)',


                // enterprise menu pinning
                pinLeft: 'Pin Izquierdo',
                pinRight: 'Pin Derecho',


                // enterprise menu aggregation and status bar
                sum: 'Suman',
                min: 'Minimo',
                max: 'Maximo',
                none: 'nada',
                count: 'contar',
                average: 'promedio',

                // standard menu
                copy: 'Copiar',
                copyWithHeaders: 'Copiar con cabeceras',
                paste: 'Pegar' 
            },

            showHomologar:false,
            DatosHomologar:[],
            DatoEditado:[]

        }
    },
    computed: {
        
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
        }

    },
    methods: {

        /**
         * Obtenemos el listado de detalles
         */
        ListarPlantilla(IdPlantilla){
            let url ="/plantillas/clientes/ObtenerPlantilla/"+IdPlantilla;
            let me = this;
            const loader = this.loaderk();
            axios.get(url).then(response=>{    
                if(response.data.plantillas_det.length){
                    this.fillDetallesPlantilla = response.data.plantillas_det;
                    this.fillColumnas = response.data.columnas;
                    loader.close();
                }
                else{
                    loader.close();
                    this.listMovimientos = [];
                }
            }).catch(error =>{
                loader.close();
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        limpiarDatos(){
            this.fillPlantilla.nIdMovimiento = 0;
            this.fillPlantilla.nNroDocumento = 0;
            this.fillPlantilla.dFecha = '';
            this.fillPlantilla.dFecha1 = '';
            this.fillPlantilla.dFecha2 = '';
            this.fillPlantilla.cEstado = '';
            this.fillPlantilla.cSoporte = '';
            this.fillPlantilla.cNmDireccion = '';
            this.fillPlantilla.cNmCondEntrega = '';
            this.fillPlantilla.cNmFormaPago = '';
            this.fillPlantilla.cNmAsesor = '';
            this.fillPlantilla.cComentarios = '';
            this.fillPlantilla.nVrIva = '';
            this.fillPlantilla.nSubTotal = '';
            this.fillPlantilla.nTotal = '';
            this.fillPlantilla.cNmCliente = '';
            this.fillDetallesPlantilla = [];
        },

        Autorizar(){
            let me = this;
            let url ="/movimiento/autorizar";
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'nIdMovimiento':me.fillPlantilla.nIdMovimiento
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1500
                });

                me.ListarPlantilla(me.fillPlantilla.nIdMovimiento,me.fillPlantilla.nIdDocumento);
                

            }).catch(error =>{
                console.log(error.response.data.line)
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

        Editar(){
            this.accionMovimiento = !this.accionMovimiento;
        },

        ActualizarDatos(){
            let me = this;
            const loader = this.loaderk();
            this.ValidarDatos();
            if(this.arrMensajeError.length <=0){
                axios.put('/movimiento/editar',{
                    params:{
                        'nIdMovimiento':this.fillPlantilla.nIdMovimiento,
                        'arraryDetallesMovimiento':this.fillDetallesPlantilla
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    loader.close();
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: respuesta.msg,
                        showConfirmButton: false,
                        timer: 1300
                    });
                    me.ListarPlantilla(me.fillPlantilla.nIdMovimiento,me.fillPlantilla.nIdDocumento);
                    me.Editar();
                })
                .catch(function (error) {
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

        ValidarDatos(){
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


        AbrirModal(){
            this.modalShow = !this.modalShow;
            if(this.modalShow){
            }
            else{
                this.arrMensajeError=[];
            }
        },


        FormatoMoneda(amount=0, decimals) {
            
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
            //autoSizeColumns()
        },
        autoSizeAll(skipHeader) {
            var allColumnIds = [];
            this.gridColumnApi.getAllColumns().forEach(function (column) {
                console.log(column.colId)
                allColumnIds.push(column.colId);
            });
            this.gridColumnApi.autoSizeColumns(allColumnIds, skipHeader);
        },

        EliminarDetalle(params){
            //console.log(params.node)
            console.log(params.column.colId);
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
            console.log(params.node.childIndex)
            let index = params.node.childIndex;
            let idColumna = params.column.colId;
            this.gridApi.setFocusedCell(index,idColumna);
            this.gridApi.startEditingCell({
                rowIndex: index,
                colKey: idColumna,
            });
        },

        onCellValueChanged(event) {
            //console.log(event.data.IdPlantillaDet)
            let Columna = event.colDef.field;
            let DatoNuevo = event.newValue;
            let DatoAnterior = event.oldValue;
            if(DatoNuevo){
                this.DatoEditado.push({'Columna':Columna,'DatoNuevo':DatoNuevo,'DatoAnt':DatoAnterior});
            }
            /*console.log(
                'onCellValueChanged: ' + Columna + ' = ' + DatoNuevo + ' anterior =  '+DatoAnterior
            );*/
        },

        onRowValueChanged(event) {
            var data = event.data;
            let id = event.data.IdPlantillaDet;
            if(this.DatoEditado.length >0){
                this.guardarDatoEditadoGrid(id,this.DatoEditado);
            }
            this.DatoEditado = [];
            //console.log(event)
            /*console.log(
                'onRowValueChanged: ('+this.fillColumnas.map(function(x){ 
                    let col = x.columna;
                    return 'Columna: '+col +' Dato '+ data[col]+' cambio '+event.newValue
                }) +')'
            );*/
            /*console.log(event.data.IdPlantillaDet)
            let Columna = event.colDef.field;
            let DatoNuevo = event.newValue;
            let DatoAnterior = event.oldValue;

            console.log(
                'onCellValueChanged: ' + Columna + ' = ' + DatoNuevo + ' anterior =  '+DatoAnterior
            );*/
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
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1300
                });
                me.listarPlantilla(true);
            }).catch(error=>{
                console.log(error)
                Swal.fire({
                    position: 'top-center',
                    icon: 'warning',
                    title: 'Ocurrio un error al enlazar ',
                    showConfirmButton: false,
                    timer: 1300
                });
                me.listarPlantilla(true);
            })
        },

        listarPlantilla(edit =false){
            let url ="/plantillas/clientes/ObtenerPlantilla/"+ this.$attrs.id;
            let me = this;
            //me.columnDefs = [];
            axios.get(url).then(response=>{    
                this.fillPlantilla = response.data.plantilla;
                if(response.data.plantillas_det.length){
                    this.fillDetallesPlantilla = response.data.plantillas_det;
                    this.fillColumnas = response.data.columnas;
                    me.columnDefs = [];//Cuando recarga el  grid debemos borrar los anteriores
                    this.fillColumnas.map(function(x,y){
                        if(x.columna !='Opciones' && x.columna !='Eliminar' && x.columna !='Editar' && x.columna !='IdListaCostosDetPlantDet'){
                            let Edit = x.edit == 'true'? true: false;
                            me.columnDefs.push({headerClass:'bg-info',headerName: x.alias,pinned: x.pinned  ,resizable: true, field : x.columna, sortable: true,filter:true, editable: Edit , width : 147 });
                        }
                        if(x.columna =='Opciones'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna, 
                                width : 50,
                                cellRenderer: function(params){
                                    let Homologado = params.data.NmListaCostos;
                                    var tempDiv = document.createElement('div');
                                    if(Homologado){
                                        tempDiv.innerHTML = '<span  class="btn btn-success btn-sm"><i class="fas fa-search"></span>';
                                    }else{
                                        tempDiv.innerHTML = '<span  class="btn btn-primary btn-sm"><i class="fas fa-search"></span>';
                                    }
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    me.AbrirHomologacion(params);
                                }
                            });
                        }
                        if(x.columna =='Eliminar'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                width : 50,
                                cellRenderer: function(params){
                                    var tempDiv = document.createElement('div');
                                    tempDiv.innerHTML =
                                    '<span class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></span>';
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    me.EliminarDetalle(params);
                                },

                            });
                        }

                        if(x.columna =='Editar'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                pinned: x.pinned,
                                resizable: true,
                                field : x.columna,
                                width : 50,
                                cellRenderer: function(params){
                                    var tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = '<span class="btn btn-info btn-sm"><i class="fas fa-edit"></span>';
                                    return tempDiv;
                                },
                                onCellClicked : function(params){
                                    me.EditarDetalle(params);
                                },

                            });
                        }

                        if(x.columna =='IdListaCostosDetPlantDet'){
                            me.columnDefs.push({
                                headerName: x.alias,
                                field : x.columna,
                                width : 50,
                                hide:true,
                            });
                        }

                    });
                    
                    this.fillDetallesPlantilla.map(function(x,y){
                        me.rowData.push(x);
                    })
                }
            }).catch(error =>{
                console.log(error)
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        }
    },

    
    beforeMount() {
        this.gridOptions = {};
        this.listarPlantilla();
        this.editType = 'fullRow';
    },
    mounted() {
        //this.ListarPlantilla(this.$attrs.id);
        this.listPermisosFilterByRolUser = localStorage.getItem('listPermisosFilterByRolUser');
        this.gridApi = this.gridOptions.api;
        this.gridColumnApi = this.gridOptions.columnApi;
    },
    
}
</script>