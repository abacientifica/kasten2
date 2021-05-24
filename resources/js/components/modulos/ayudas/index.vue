<template>
    <main class="main">
        
        <div class="container-fluid">
            <div class="content-header margen-ruta">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-left">
                            <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                            <li class="breadcrumb-item active">Ayudas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header bg-head">
                    <i class="fa fa-align-justify"></i> Configuración Ayudas
                    <button type="button" @click="AbrirModal('ayuda','registrar')" class="btn btn-info btn-sm float-right">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    
                </div>
                <template>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                        <option value="Nombre" selected>Nombre</option>
                                        <option value="IdDocumento">Id Documento</option>
                                    </select>
                                    <input type="text" id="txtBuscar" v-model="buscar" @keyup.enter="ListarAyudas(1,buscar,criterio)" v-on:change="ListarAyudas(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="ListarAyudas(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-info">
                                <tr>
                                    <th>Ver</th>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Url</th>
                                    <th>Opcion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="config in ListarDocumentosPaginate" :key="config.IdDocumento">
                                    <td>
                                        <button type="button"  class="btn btn-ligth btn-sm" >
                                            <router-link :to="'/ayudas/kasten/'+config.id">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                        </button> 
                                        <button type="button" @click="AbrirModal('ayuda','actualizar',config)" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" @click="eliminarAyuda(config.id)" class="btn btn-warning btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                    <td v-text="config.id"></td>
                                    <td v-text="config.Nombre"></td>
                                    <td v-text="config.NmDoc" v-if="config.IdDoc"></td>
                                    <td v-text="config.Url" v-else ></td>
                                    <td>
                                        <div v-if="config.IdSlug != null">
                                            <span class="badge badge-success" >Ruta</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-primary" >Documento</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <nav>
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
                        </nav>
                    </div>
                </template>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
            <!--Inicio Modal -->
            <div class="modal fade" :class="{ show: modal }" :style="modal ? mostrarModal : ocultarModal" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="callout callout-danger" v-for="(errorm, index) in mensajeError" :key="index" v-text="errorm" style="padding: 5px"></div>
                    <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title" v-text="tituloModal"></h5>
                        <button type="button" class="close" @click.prevent="AbrirModal('','')" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombre Ayuda :</label>
                            <div class="col-md-9">
                            <input type="text"  class="form-control" v-model="fillCrearAyuda.NmAyuda" placeholder="Nombre " required/>
                            </div>
                        </div>

                        <div class="form-group row" >
                            <label class="col-md-3 form-control-label" for="text-input">Documento :</label>
                            <div class="col-md-9">
                            <el-select  v-model="fillCrearAyuda.IdDoc" placeholder="Seleccione" clearable :disabled="fillCrearAyuda.IdSlug >0 ? true :false">
                                <el-option
                                    v-for="item in listDocumentos"
                                    :key="item.IdDocumento"
                                    :label="item.Nombre"
                                    :value="item.IdDocumento">
                                </el-option>
                            </el-select>
                            </div>
                        </div>

                        <div class="form-group row" >
                            <label class="col-md-3 form-control-label" for="text-input">Ruta :</label>
                            <div class="col-md-9">
                            <el-select  v-model="fillCrearAyuda.IdSlug" placeholder="Seleccione" clearable :disabled="fillCrearAyuda.IdDoc >0 ? true: false">
                                <el-option
                                    v-for="item in ListaRutas"
                                    :key="item.id"
                                    :label="item.nombre"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click.prevent="AbrirModal('','')"> Cerrar</button>
                        <button type="button" v-if="accionModal == 0" class="btn btn-success" @click.prevent="registrarAyuda()">
                            Crear
                        </button>
                        <button type="button" v-if="accionModal == 1" class="btn btn-primary" @click.prevent="actualizarAyuda()">
                            Actualizar
                        </button>
                    </div>
                    </div>
                </div>
            </div>
            <!--Fin del modal-->   
        </div>
    </main>
 <!-- /Fin del contenido principal -->
</template>
<script>
import Swal from 'sweetalert2';
export default {
    data() {
        return {
            modal:0,
            tituloModal:'',
            mensajeError:[],
            ListAyudas:[],
            arrayDetalle:[],
            arrayCampos:[],
            filtcampos:'',
            fillCrearAyuda:{
                Id:0,
                NmAyuda:'',
                IdDoc:'',
                IdSlug:'',
            },

            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 15,

            pageDocNumber: 0,
            perPageDoc: 10,
            //Fin variables paginacion

            criterio : 'Nombres', //Esta variable se inicializa con nombre por que es el primer criterio del combo
            buscar :'',//Esta es la cadena de busqueda
            
            //Clases del modal
            mostrarModal: {
                display: "block",
                background: "#0000006b",
            },
            ocultarModal: {
                display: "none",
            },

            listDocumentos:[],
            ListaRutas:[],
            accionModal:0,
            tituloModal:''

        }
    },

    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.ListAyudas.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarDocumentosPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.ListAyudas.slice(inicio, fin);
        },
        pagesList() {
            let a = this.ListAyudas.length;
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

        //Obtener el numero de las paginas
        pageCountCampos() {
            let a = this.arrayCampos.length;
            let b = this.perPageDoc;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarCamposPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageDocNumber * this.perPageDoc;
            let fin = inicio + this.perPageDoc;
            return this.arrayCampos.slice(inicio, fin);
        },
        pagesListCampos() {
            let a = this.arrayCampos.length;
            let b = this.perPageDoc;
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
        ListarAyudas:function(page,buscar,criterio){//Lista documetnos
            let me = this;
            var url = '/ayudas/lista';
            axios.get(url,{
                params:{
                    'buscar':buscar,
                    'criterio':criterio
                }
            }).then(function (response) {
                var respuesta = response.data;
                me.ListAyudas = respuesta.ayudas;
                me.inicializarPagination();
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

        ListarDocumentos:function(page,buscar,criterio){//Lista documetnos
            let me = this;
            var url = '/documentos/lista';
            axios.get(url,{
                params:{
                    'buscar':buscar,
                    'criterio':criterio
                }
            }).then(function (response) {
                var respuesta = response.data;
                me.listDocumentos = respuesta.documentos;
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

        getListarPermisos() {
            let url = "/permisos/lista";
            axios.get(url, {
                params: {
                    'cNombre': '',
                    'cUrl': '',
                },
            })
            .then((response) => {
                this.inicializarPagination();
                let permisos = response.data.permisos;
                this.ListaRutas = permisos;
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                }
            })
        },

        registrarAyuda(){
            this.validarDatos();
            if(this.mensajeError.length <=0){
                let url = "/ayudas/guardar";
                axios.post(url, {
                    params: {
                        'NmAyuda': this.fillCrearAyuda.NmAyuda,
                        'IdDoc': this.fillCrearAyuda.IdDoc,
                        'IdSlug': this.fillCrearAyuda.IdSlug
                    },
                })
                .then((response) => {
                    let respuesta = response.data;
                    if(respuesta.status == 201){
                        Swal.fire({
                            icon :'success',
                            type :'success',
                            title :'',
                            text:'La ayuda se registro con exito',
                            showConfirmButton: false,
                            timer: 1200
                        });
                        this.AbrirModal('','');
                        this.ListarAyudas();
                    }
                    if(respuesta.status == 500){
                        
                        Swal.fire({
                            icon :'danger',
                            type :'danger',
                            title :'',
                            text:'Ocurrio un error '.respuesta.msg,
                            showConfirmButton: false,
                            timer: 1200
                        });
                    }
                }).catch(error =>{
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                    }
                })
            }
        },

        actualizarAyuda(){
            this.validarDatos();
            if(this.mensajeError.length <=0){
                let url = "/ayuda/ActualizarAyuda";
                axios.put(url, {
                    params: {
                        'id': this.fillCrearAyuda.Id,
                        'NmAyuda': this.fillCrearAyuda.NmAyuda,
                        'IdDoc': this.fillCrearAyuda.IdDoc,
                        'IdSlug': this.fillCrearAyuda.IdSlug
                    },
                })
                .then((response) => {
                    let respuesta = response.data;
                    if(respuesta.status == 201){
                        Swal.fire({
                            icon :'success',
                            type :'success',
                            title :'',
                            text:'La ayuda se actualizo con exito',
                            showConfirmButton: false,
                            timer: 1200
                        });
                        this.AbrirModal('','');
                        this.ListarAyudas();
                    }
                    if(respuesta.status == 500){
                        
                        Swal.fire({
                            icon :'danger',
                            type :'danger',
                            title :'',
                            text:'Ocurrio un error '.respuesta.msg,
                            showConfirmButton: false,
                            timer: 1200
                        });
                    }
                }).catch(error =>{
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                    }
                })
            }
        },

        AbrirModal(titulo,opcion,data=[]){
            this.modal = !this.modal;
                
            if(this.modal !=0){
                this.ListarDocumentos();
                this.getListarPermisos();
                if(opcion =='registrar'){
                    this.accionModal = 0;
                    this.tituloModal =" Registrar Ayuda";
                }
                else{
                    this.accionModal = 1;
                    this.fillCrearAyuda.Id = data.id;
                    this.fillCrearAyuda.NmAyuda = data.Nombre;
                    this.fillCrearAyuda.IdDoc = data.IdDoc;
                    this.fillCrearAyuda.IdSlug = data.IdSlug;
                    this.tituloModal =" Actualizar Ayuda";
                }
            }
            else{
                this.fillCrearAyuda.NmAyuda = '';
                this.fillCrearAyuda.IdDoc = '';
                this.fillCrearAyuda.IdSlug = '';
                this.tituloModal ="";
            }
        },

        validarDatos(){
            let datos = this.fillCrearAyuda;
            this.mensajeError =[];
            if(!datos.NmAyuda){
                this.mensajeError.push("El campo nombre ayuda no puede estar vacio.");
            }
            if(datos.IdDoc =='' && datos.IdSlug ==''){
                this.mensajeError.push("Debes seleccionar un documento o una ruta.");
            }
        },

        eliminarAyuda(id){
            Swal.fire({
                title: 'Estas seguro(a) de eliminar la ayuda ?',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: `Eliminar`,
                confirmButtonColor: '#28a745',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put('/ayuda/EliminarAyuda', {
                        params: {
                            'id': id,
                        },
                    }).then((response) => {
                        let respuesta = response.data;
                        if(respuesta.status == 201){
                            Swal.fire({
                                icon :'success',
                                type :'success',
                                title :'',
                                text:respuesta.msg,
                                showConfirmButton: false,
                                timer: 1600
                            });
                            this.ListarAyudas();
                        }
                        if(respuesta.status == 500){
                            
                            Swal.fire({
                                icon :'error',
                                type :'error',
                                title :'',
                                text:respuesta.msg,
                                showConfirmButton: false,
                                timer: 1600
                            });
                        }
                    }).catch(error =>{
                        console.log(error)
                        if(error.response.status ==401){
                            this.$router.push({name: 'login'})
                            location.reload();
                            sessionStorage.clear();
                        }
                    })
                }
            })
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
    },

    mounted() {//Inicializa el constructor
        this.ListarAyudas();
    }
}
</script>