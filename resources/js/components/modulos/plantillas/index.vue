<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Plantillas</h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item active">Plantillas Clientes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <template v-if="listPermisosFilterByRolUser.includes('plantillas_clientes.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                        <div class="card-tools">
                            <div class="row">
                                <router-link class="btn btn-info btn-sm" :to="'/plantillas/clientes/crear'" style="margin-right: 1rem">
                                <i class="fas fa-plus-square"></i> Nueva Plantilla
                                </router-link>
                                <modal :titulo="'Ayudas Plantillas'" :iddoc="this.OpPedido" :url="'pedidos.crear'"></modal>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card card-info">
                        <div class="card-header bg-head ">
                            <h3 class="card-title">Criterios de Busqueda</h3>
                        </div>
                        <div class="card-info">
                            <div class="card-body">
                            <form role="form">
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Id Plantilla</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" v-model="fillPlantilla.nNroDocumento" @keyup.enter="ListarPlantillas()"/>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Estado</label>
                                    <div class="col-md-9">
                                        <el-select v-model="fillPlantilla.cEstado" placeholder="Seleccione un estado" clearable @keyup.enter="ListarPlantillas()">
                                        <el-option v-for="item in listEstados" :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                        </el-select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Cliente</label>
                                    <div class="col-md-9">
                                        <v-select
                                            @search="selectTerceros"
                                            label="NombreCorto"
                                            :options="arrayTerceros"
                                            placeholder="Buscar Tercero..."
                                            @input="getDatosTercero"   
                                        >
                                        </v-select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Rago Fecha</label>
                                    <div class="col-md-9">
                                        <el-date-picker
                                            v-model="fillPlantilla.oRangoFechas"
                                            class="form-control"
                                            type="daterange"
                                            align="right"
                                            unlink-panels
                                            range-separator="A"
                                            start-placeholder="Desde"
                                            end-placeholder="Hasta"
                                            :picker-options="pickerOptions"
                                            format="yyyy-MM-dd"
                                            value-format="yyyy-MM-dd"
                                            @keyup.enter="ListarPlantillas()">
                                        </el-date-picker>
                                    </div>
                                    </div>
                                </div>
                                
                                </div>
                            </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                            <div class="col-md4 offset-4">
                                <button class="btn btn-flat btn-info" @click.prevent="ListarPlantillas()" v-loading.fullscreen.lock="fullscreenLoading">
                                Buscar
                                </button>
                                <button class="btn btn-flat btn-default" @click.prevent="LimpiarFiltro()">
                                Limpiar
                                </button>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="card-info">
                        <div class="card-body">
                            <div class="card-header">
                            <h3 class="card-title">Bandeja Resultados</h3>
                            <div class="card-body table-responsive">
                                <template v-if="ListarPlantillasPaginate.length <= 0">
                                <div class="callout callout-info">
                                    <h5>Sin Resultados</h5>
                                </div>
                                </template>
                                <table class="table table-hover table-bordered table-striped table-sm" v-else>
                                <thead class="bg-info">
                                    <tr>
                                    <th class="texto-centrado">Fecha</th>
                                    <th class="texto-centrado">Tercero</th>
                                    <th class="texto-centrado">Dirección</th>
                                    <th class="texto-centrado">Nombre</th>
                                    <th class="texto-centrado">Entrega Propuesta</th>
                                    <th class="texto-centrado">Periodo</th>
                                    <th class="texto-centrado">Estado</th>
                                    <th class="texto-centrado">Usuario</th>
                                    <th class="texto-centrado">Comentarios</th>
                                    <th class="texto-centrado">Opcion</th>
                                    </tr>
                                </thead>
                                <tbody v-if="ListarPlantillasPaginate.length >0">
                                    <tr v-for="det in ListarPlantillasPaginate" :key="det.IdPlantilla">
                                        <td>{{moment(det.FhPlantilla).format('MMMM DD YYYY, h:mm:ss a')}}</td>
                                        <td>{{det.tercero.NombreCorto}}</td>
                                        <td v-text="det.direccion.Direccion"></td>
                                        <td v-text="det.NmPlantilla"></td>
                                        <td>{{moment(det.FhEntregaPropuesta).format('MMMM DD YYYY')}}</td>
                                        <td v-text="det.Periodo"></td>
                                        <td v-text="det.Estado"></td>
                                        <td v-text="det.Usuario"></td>
                                        <td v-text="det.Comentarios"></td>
                                        <td>
                                            <router-link  :to="'/plantillas/clientes/ver/'+det.IdPlantilla" class="btn btn-info btn-sm" v-if="listPermisosFilterByRolUser.includes('plantillas_clientes.ver') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                                <div class="card-footer clearfix">
                                    <ul class="pagination pagination-sm m-0 float-rigth">
                                        <li class="page-item" v-if="this.pageNumber > 0">
                                        <a href="#" class="page-link" @click.prevent="pagePrev()" >Ant</a>
                                        </li>

                                        <li class="page-item" v-for="(page, index) in pagesList" :key="index" :class="page == pageNumber ? 'active' : ''">
                                        <a href="#" class="page-link" @click.prevent="selectPage(page)">{{ page + 1 }}</a>
                                        </li>

                                        <li class="page-item" v-if="pageNumber < pageCount - 1">
                                        <a href="#" class="page-link" @click.prevent="nextPage()">Sig</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import vSelect from "vue-select";
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            //Al cambiar a true muestra el loading
            fullscreenLoading:false,
            listPermisosFilterByRolUser:[],
            moment:moment,
            fillPlantilla:{
                nNroDocumento:'',
                nIdMovimiento:'',
                nIdTercero:'',
                cEstado:'DIGITADA',
                nIdDireccion:'',
                cFechaDesde:'',
                cFechaHasta:'',
                oRangoFechas:''
            },
            OpPedido:8,
            //Contiene la lista de usuarios recuperada
            listPlantillas: [],
            //LLena el combo de estados en el filtro
            listEstados: [
                { value: 'DIGITADA', label: "DIGITADA" },
                { value: 'AUTORIZADA', label: "AUTORIZADA" },
                { value: 'CERRADA', label: "CERRADA" },
                { value: 'ANULADA', label: "ANULADA" },
            ],
            listRoles:[],

            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 15,
            //Fin variables paginacion
            tituloModal: "",
            accionModal: 0,
            showModal:false,
            mensajeError:[],
            mostrarModal: {
                display: "block",
                background: "#0000006b",
            },
            ocultarModal: {
                display: "none",
            },
            usuario:[],
            moment:moment,
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
            value1: '',
            value2: '',
            arrayTerceros: []
        }
    },
    components:{
        "v-select": vSelect
    },
    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.listPlantillas.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarPlantillasPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.listPlantillas.slice(inicio, fin);
        },
        pagesList() {
            let a = this.listPlantillas.length;
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
    },
    methods: {

        ListarPlantillas(IdTercero= this.fillPlantilla.nIdtercero){
            this.fullscreenLoading = true;
            let url ="/plantillas/clientes/lista";
            axios.get(url,{params:{
                'Id' : this.fillPlantilla.nIdMovimiento,
                'IdTercero' : this.fillPlantilla.nIdTercero,
                'Estado' : this.fillPlantilla.cEstado,
                'oRangoFechas' : this.fillPlantilla.oRangoFechas
            }}).then(response=>{    
                this.inicializarPagination();
                if(response.data.plantillas.length){
                    this.listPlantillas = response.data.plantillas;
                    /*this.listPlantillas.map(function(e){
                        console.log(e.tercero.NombreCorto)
                    })*/
                }
                else{
                    this.listPlantillas = [];
                }
                this.fullscreenLoading = false;
            }).catch(error =>{
                console.log(error)
                this.fullscreenLoading = false;
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
                me.fillPlantilla.nIdTercero = val1.IdTercero;
            }
            catch(error){
                this.fillPlantilla.nIdTercero = 0;
            }
        },

        LimpiarFiltro(){
            this.fillPlantilla.cNombre ='';
            this.fillPlantilla.cUsuario ='';
            this.fillPlantilla.cCorreo ='';
            this.fillPlantilla.cEstado =1;
            this.ListarPlantillas();
        },

        /*Inicio Metodos Paguinacion*/
        inicializarPagination() {
            this.pageNumber = 0;
        },
        nextPage() {
            this.pageNumber++;
        },
        pagePrev() {
            this.pageNumber--;
        },
        selectPage(page) {
            this.pageNumber = page;
        },    
        /*Fin Metodos Paginacion*/

       
        FormatoMoneda(amount=0, decimals) {
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
    },
    mounted() {
       
        this.listPermisosFilterByRolUser = localStorage.getItem('listPermisosFilterByRolUser');
        this.listPermisosFilterByRolUser == null ? [] : this.listPermisosFilterByRolUser;
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
        if(this.usuario.Tipo == 2){
            this.OpPedido = 61;
            this.fillPlantilla.nIdtercero = this.usuario.IdTercero;
            if(this.usuario.IdDireccion >0){
                this.fillPlantilla.nIdDireccion = this.usuario.IdDireccion;
            }
        }
        else{
            this.OpPedido = 8;
        }
        this.ListarPlantillas(this.fillPlantilla.nIdtercero);
        
    },

}
</script>