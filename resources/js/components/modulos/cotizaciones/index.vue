<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cotizaciones</h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item active">Cotizaciones Clientes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-info">
            <div class="card-header bg-head ">
                <h3 class="card-title">Criterios de Busqueda</h3>
                <div class="card-tools">
                    <div class="row">
                        <div class="btn-group">
                            <template v-if="ValidarPermiso('crear')">
                                <nuevacotizacion :titulo="'Nueva Cotización'"></nuevacotizacion>
                            </template>
                            <router-link class="btn btn-info btn-sm" to='/tpdocumento/lista/12'>
                                <i class="fas fa-arrow-left"></i> Regresar
                            </router-link>
                            <modal :titulo="'Ayudas Cotizaciones'" :iddoc="2" :url="'cotizaciones.index'"></modal>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-info">
                <div class="card-body">
                    <div style="margin-top: 15px;">
                        <el-input placeholder="Busqueda de cotizaciones "  v-model="filtros.FiltroGeneral" clearable @change="filtrarDatos()">
                            <el-button slot="prepend" icon="el-icon-search" @click="filtrarDatos()"></el-button>
                            
                            <el-button slot="append" icon="el-icon-s-operation" :style="ValidarFiltros() ? 'border: 1px solid red': '' " @click.prevent="centerDialogVisible=true"></el-button>
                        </el-input>
                    </div>
                </div>
            </div>
            <el-dialog
                title="Busqueda Avanzada"
                :visible.sync="centerDialogVisible"
                width="30%"
                center>
                
                <el-form ref="form" :model="filtros" label-width="120px">
                    <el-form-item label="Id / #">
                        <el-input
                            size="small"
                            placeholder="Ingrese # o Id"
                            v-model="filtros.NroCotizacion" @change="filtrarDatos()"></el-input>
                    </el-form-item>

                    <el-form-item label="Estado">
                        <el-select v-model="filtros.fEstado" size="small" placeholder="Seleccione un estado" clearable @change="filtrarDatos()">
                            <el-option label="DIGITADA" value="DIGITADA"></el-option>
                            <el-option label="AUTORIZADA" value="AUTORIZADA"></el-option>
                            <el-option label="CERRADA" value="CERRADA"></el-option>
                            <el-option label="ANULADA" value="ANULADA"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Tipo">
                        <el-select v-model="filtros.fTipo" size="small" placeholder="Seleccione un tipo" clearable @change="filtrarDatos()">
                            <el-option  v-for="(tipo) in arrCotizaciones.tiposCot" :key="tipo.IdCotizacionTipo" :label="tipo.NmCotizacionTipo" :value="tipo.IdCotizacionTipo"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Sub Tipo">
                        <el-select v-model="filtros.fSubTipo" size="small" placeholder="Seleccione un sub tipo" clearable @change="filtrarDatos()">
                            <el-option  v-for="(subtipo) in arrCotizaciones.subTiposCot" :key="subtipo.IdSubTipoCotizacion" :label="subtipo.NmSubTipoCotizacion" :value="subtipo.IdSubTipoCotizacion"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Seguimiento">
                        <el-select v-model="filtros.fSeguimiento" size="small" placeholder="Seleccione un seguimiento" clearable @change="filtrarDatos()">
                            <el-option label="Sin Seguimiento" :value="0"></el-option>
                            <el-option  v-for="(seguimiento) in arrCotizaciones.seguimientosCot" :key="seguimiento.IdTipoSeguimiento" :label="seguimiento.NmTipoSeguimiento" :value="seguimiento.IdTipoSeguimiento"></el-option>
                        </el-select>
                    </el-form-item>


                    <el-form-item label="Fecha Cotización">
                       <el-date-picker
                            class="form-control"
                            type="daterange"
                            align="right"
                            size="small"
                            unlink-panels
                            range-separator="A"
                            v-model="filtros.fRangoFecha"
                            start-placeholder="Desde"
                            end-placeholder="Hasta"
                            :picker-options="pickerOptions"
                            format="yyyy-MM-dd"
                            value-format="yyyy-MM-dd"
                            @keyup.enter="listarDatos()">
                        </el-date-picker>
                    </el-form-item>

                    <el-form-item label="Limite Datos">
                        <el-input
                            type="text"
                            size="small"
                            placeholder="Limite datos"
                            v-model="filtros.LimiteDatos"
                            maxlength="4"
                            show-word-limit
                        ></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="filtrarDatos()">Filtrar</el-button>
                        <el-button @click.prevent="centerDialogVisible=false">Cancelar</el-button>
                    </el-form-item>
                    </el-form>
            </el-dialog>
        </div>
        <hr>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <ag-grid-vue
                class="ag-theme-alpine"
                :style="{width,height }"
                @gridReady="onGridReady"
                @firstDataRendered="onFirstDataRendered"
                :gridOptions="gridOptions"
                :localeText="localeText"
                :columnDefs="columnDefs"
                :rowData="rowData"
                :pagination="true"
                :paginationPageSize="paginationPageSize"
                :tooltipShowDelay="tooltipShowDelay">
            </ag-grid-vue>
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
        "v-select": vSelect,
    },
    data() {
        return {
            width:'100%',
            height:(window.innerHeight - 150)+'px',
            columnDefs: [],
            rowData:[],
            paginationPageSize:100,
            gridOptions :{
                onFilterChanged:this.CambioFiltros,
                onColumnMoved:this.GuardarOrdenColumnas,
                onColumnVisible:this.ColumnasVisibles,
                onSortChanged:this.GuardarOrdenColumnas,
                onColumnPinned:this.GuardarOrdenColumnas,
            },
            localeText : servicesApp.traducirTextosAggrid(),
            tooltipShowDelay:null,
            arrCotizaciones:[],
            centerDialogVisible:false,
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
            PermisosUser:[],
            filtros: {
                FiltroGeneral:  '' ,
                NroCotizacion:'',
                fEstado: '',
                fTipo: '',
                fSubTipo: '',
                fSeguimiento: '',
                fRangoFecha:null,
                LimiteDatos:500,
            },
            FColumnas:null,
            FFiltros:null,
            arrCotizaciones:{
                fillCotizaciones:[],
                tiposCot:[],
                subTiposCot:[],
                seguimientosCot:[],
               
            },
            
            
            
        }
    },
    computed:{

    },
    methods:{
        
        cargarColumnas(){
            let me = this;
            this.columnDefs =[];
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'ID',
                pinned: 'left',
                resizable: true,
                field : 'IdCotizacion',
                sortable: true,
                filter:true, 
                width : 100 
            });
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Nro',
                resizable: true,
                field : 'NroCotizacion',
                sortable: true,
                filter:true, 
                width : 100 
            });
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Cliente',
                resizable: true,
                field : 'cliente.NombreCorto',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Estado',
                resizable: true,
                field : 'Estado',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Lista Precios',
                resizable: true,
                field : 'direccion.listaprecios.NmListaPrecios',
                sortable: true,
                filter:true, 
            });
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Tipo',
                resizable: true,
                field : 'tipocot.NmCotizacionTipo',
                sortable: true,
                filter:true, 
            });
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Sub Tipo',
                resizable: true,
                field : 'subtipocot.NmSubTipoCotizacion',
                sortable: true,
                filter:true, 
            });
            
            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Usuario Solicita',
                resizable: true,
                field : 'UsuarioSolicitud',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Seguimiento',
                resizable: true,
                field : 'segactual.tipo.NmTipoSeguimiento',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Fh. Seguimiento',
                resizable: true,
                field : 'segactual.FechaSeguimiento',
                valueFormatter: FormatoFecha,
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Comentario Seg.',
                resizable: true,
                field : 'segactual.Comentario',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Nombre',
                resizable: true,
                field : 'NmCotizacion',
                sortable: true,
                filter:true, 
            });

            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Usuario',
                resizable: true,
                field : 'Usuario',
                sortable: true,
                filter:true, 
            });


            me.columnDefs.push({
                headerClass:'bg-info',
                headerName: 'Ver',
                resizable: true,
                cellRenderer: function(params){
                    if(me.ValidarPermiso('ver')){
                        var tempDiv = document.createElement('div');
                        tempDiv.innerHTML = '<span class="btn btn-info btn-sm"><i class="fas fa-eye"></span>';
                        return tempDiv;
                    }
                    return null;
                },
                onCellClicked : function(params){
                    if(me.ValidarPermiso('ver')){
                        me.VerCotizacion(params);
                    }
                },
                pinned: 'right',
                width : 90 ,
            });
        },

        listarDatos(filtrar=false){
            let url ="/cotizaciones/lista";
            let me = this;
            const load = this.loaderk();
            axios.post(url,{
                params:{
                    'limite':me.filtros.LimiteDatos,
                    'filtros':me.filtros,
                    'set_filtro':filtrar
                }
            }).then(response=>{    
                let respuesta = response.data;
                this.arrCotizaciones.fillCotizaciones = response.data.cotizaciones;
                this.arrCotizaciones.tiposCot = respuesta.datos_filtros.tipos;
                this.arrCotizaciones.subTiposCot = respuesta.datos_filtros.subtipos;
                this.arrCotizaciones.seguimientosCot = respuesta.datos_filtros.seguimientos;
                this.rowData = this.arrCotizaciones.fillCotizaciones;
                load.close();
            }).catch(error =>{
                load.close();
                console.log(error)
                me.AlertMensaje('Upss.. ocurrio un error al cargar los datos',3);
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

        filtrarDatos(){
            this.listarDatos(true);
        },

        FiltrosGuardados(return_filtros=false){
            let url = "/cotizaciones/filtros/usuario"
            axios.get(url).then(response => {
                let respuesta = response.data.filtros;
                if(respuesta.FiltrosIndexK2 !=''){
                    localStorage.setItem('filtros_index_cot',(respuesta.FiltrosIndexK2));
                    this.FFiltros = respuesta.FiltrosIndexK2;
                }
                if(respuesta.ColumnasIndexK2 !=''){
                    localStorage.setItem('columnas_index_cot',(respuesta.ColumnasIndexK2));
                    this.FColumnas = respuesta.ColumnasIndexK2;
                }
                this.filtros.FiltroGeneral = respuesta.FiltrosGeneralK2 ? respuesta.FiltrosGeneralK2 :'' ;
                this.filtros.NroCotizacion =  respuesta.IdCotizacion ? respuesta.IdCotizacion :'';
                this.filtros.fEstado = respuesta.Estado ? respuesta.Estado :'';
                this.filtros.fTipo = respuesta.Tipo ? respuesta.Tipo :'';
                this.filtros.fSubTipo = respuesta.SubTipo ? respuesta.SubTipo :'';
                this.filtros.fSeguimiento = respuesta.IdSeguimiento ? respuesta.IdSeguimiento :'';
                this.filtros.fRangoFecha = respuesta.RangoFecha1 ? [respuesta.RangoFecha1,respuesta.RangoFecha2] : null;
                this.filtros.LimiteDatos = respuesta.Limite ? respuesta.Limite :'' ;
            });
        },

        ValidarFiltros(){
            let me = this;
            let Valid = false;
            Object.keys(me.filtros).map(function(e){
                if(me.filtros[e] && e != 'LimiteDatos'){
                    Valid =true;
                }
            });
            return Valid;
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
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
                duration:12000,
                progress: 'auto',
                flat: true,
                color,
                position,
                title:null,
                text: Msg
            })
        },

        //Metodos columnas y filtros tabla
        onFirstDataRendered(params){
            let Filtros = JSON.parse(localStorage.getItem('filtros_index_cot'));
            let Columnas = JSON.parse(localStorage.getItem('columnas_index_cot'));
            this.gridApi.setFilterModel(Filtros);
            this.gridOptions.columnApi.setColumnState(Columnas)
        },

        CambioFiltros(params){
            if(!this.MantenerFiltros){
                localStorage.removeItem('filtros_index_cot');
                let DatosFiltro = JSON.stringify(params.api.getFilterModel());
                localStorage.setItem('filtros_index_cot',DatosFiltro);
                this.GuardarFiltros();
            }
        },

        GuardarOrdenColumnas(params){
            localStorage.removeItem('columnas_index_cot');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas_index_cot',DatosFiltro);
            this.GuardarFiltros();
        },

        ColumnasVisibles(params){
            localStorage.removeItem('columnas_index_cot');
            let DatosFiltro = JSON.stringify(this.gridColumnApi.getColumnState());
            localStorage.setItem('columnas_index_cot',DatosFiltro);
            this.GuardarFiltros();
        },

        GuardarFiltros(){
            let me = this;
            let url ="/cotizaciones/index/filtros";
            axios.post(url,{
                params:{
                    'filtros':localStorage.getItem('filtros_index_cot'),
                    'columnas':localStorage.getItem('columnas_index_cot'),
                }
            }).then(response=>{    
                let respuesta = response.data;
            }).catch(error =>{
                console.log(error)
            })
        },
        onGridReady(params) {
            let me = this;
            if(!localStorage.getItem('filtros_index_cot') && !localStorage.getItem('columnas_index_cot')){
                let url = "/cotizaciones/filtros/usuario"
                axios.get(url).then(response => {
                    let respuesta = response.data.filtros;
                    if(respuesta.FiltrosIndexK2 !=''){
                        localStorage.setItem('filtros_index_cot',(respuesta.FiltrosIndexK2));
                        me.FFiltros = respuesta.FiltrosIndexK2;
                    }
                    if(respuesta.ColumnasIndexK2 !=''){
                        localStorage.setItem('columnas_index_cot',(respuesta.ColumnasIndexK2));
                        me.FColumnas = respuesta.ColumnasIndexK2;
                    }
                    let Filtros = JSON.parse(me.FFiltros);
                    let Columnas = JSON.parse(me.FColumnas);
                    this.gridApi.setFilterModel(Filtros);
                    this.gridOptions.columnApi.setColumnState(Columnas);
                })
            }
            else{
                let Filtros = JSON.parse(localStorage.getItem('filtros_index_cot'));
                let Columnas = JSON.parse(localStorage.getItem('columnas_index_cot'));
                this.gridApi.setFilterModel(Filtros);
                this.gridOptions.columnApi.setColumnState(Columnas);
            }
            
        },
        // Fin metodos grilla
        VerCotizacion(params){
            let IdCot=params.data.IdCotizacion;
            this.$router.push({name:'cotizaciones.ver', params: { 'id':IdCot }})
        },

        ValidarPermiso(permiso){
            if(this.PermisosUser.includes('cotizaciones.'+permiso) || this.PermisosUser.includes(permiso) || this.PermisosUser.includes('administrador.sistema')){
                return true;
            }
            else{
                return false;
            }
        },

    },

    mounted() {
        this.PermisosUser = localStorage.getItem('listPermisosFilterByRolUser');
        this.gridApi = this.gridOptions.api;
        this.gridColumnApi = this.gridOptions.columnApi;
        this.FiltrosGuardados();
    },

    beforeMount() {
        //Carga de datos del grid
        this.cargarColumnas();
        this.listarDatos();
        //Fin carga de datos
    },
}

window.FormatoFecha = function FormatoFecha(params){
    return  params.value ?  moment(params.value).format('MMMM DD YYYY, h:mm:ss a') : null
}
</script>
<style>
</style>