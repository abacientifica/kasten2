<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <button type="button" @click="AbrirModal('usuario','registrar')" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Configuración Documentos
                    </div>
                    <template v-if="listado=1">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterio">
                                            <option value="Nombre" selected>Nombre</option>
                                            <option value="IdDocumento">Id Documento</option>
                                        </select>
                                        <input type="text" id="txtBuscar" v-model="buscar" @keyup.enter="ListarDocumentos(1,buscar,criterio)" v-on:change="ListarDocumentos(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="ListarDocumentos(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Consecutivo</th>
                                        <th>Tipo Documento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="config in ListarDocumentosPaginate" :key="config.IdDocumento">
                                        <td>
                                            <button type="button"  @click="AbrirModal('documento','agregar',config)" class="btn btn-warning btn-sm" >
                                                <i class="fas fa-pencil-alt"></i>
                                            </button> 
                                            <button type="button"  class="btn btn-warning btn-sm" >
                                                <router-link :to="'/configuracion/documentos/edit/'+config.IdDocumento" :Documento="config.Nombre">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </router-link>
                                            </button> 
                                        </td>
                                        <td v-text="config.IdDocumento"></td>
                                        <td v-text="config.Nombre"></td>
                                        <td v-text="config.Consecutivo"></td>
                                        <td v-text="config.Tp"></td>
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
            </div>
         <!--Inicio del modal agregar/actualizar-->

        <div class="modal fade" :class="{ show: showModal }" :style="showModal ? mostrarModal : ocultarModal" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="txtBuscarmodal" v-model="filtcampos" @keyup.enter="CargarCampos()" v-on:change="CargarCampos()" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="CargarCampos()" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Ejemplo</th>
                                        <th>Alias</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="dato in ListarCamposPaginate" :key="dato.IdCampo">
                                        
                                        <td v-text="dato.IdCampo"></td>
                                        <td v-text="dato.NmEjemplo"></td>
                                        <td><input type="text" v-model="dato.NmEjemplo" v-if="dato.Existe == null"><input type="text" v-model="dato.Existe" disabled v-else></td>
                                        <td>
                                            <button type="button"  @click="agregarDetalleModal(dato)" v-if="dato.Existe == null" class="btn btn-success btn-sm" >
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button"  @click="quitarDetalleModal(dato)" v-if="dato.Existe != null" class="btn btn-danger btn-sm" >
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <nav>
                                <div class="card-footer clearfix">
                                    <ul class="pagination pagination-sm m-0 float-rigth">
                                        <li class="page-item" v-if="this.pageDocNumber > 0">
                                        <a href="#" class="page-link" @click.prevent="pagePrevDoc()" >Ant</a>
                                        </li>

                                        <li class="page-item" v-for="(pageDoc, index) in pagesListCampos" :key="index" :class="pageDoc == pageDocNumber ? 'active' : ''">
                                        <a href="#" class="page-link" @click.prevent="selectPageDoc(pageDoc)">{{ pageDoc + 1 }}</a>
                                        </li>

                                        <li class="page-item" v-if="pageDocNumber < pageCountCampos - 1">
                                        <a href="#" class="page-link" @click.prevent="nextPageDoc()">Sig</a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="CerrarModal()" >Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->   
</main>
 <!-- /Fin del contenido principal -->
</template>

<script>
    export default {
        data() {
            return {
                showModal:false,
                listado:1,
                IdDocumento:0,
                Nombre:'',
                Tp:'',
                Consecutivo:0,
                modal:0,

                tituloModal:'',

                listDocumentos:[],
                arrayDetalle:[],
                arrayCampos:[],
                filtcampos:'',

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
            }
        },

        computed: {
            //Obtener el numero de las paginas
            pageCount() {
                let a = this.listDocumentos.length;
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
                return this.listDocumentos.slice(inicio, fin);
            },
            pagesList() {
                let a = this.listDocumentos.length;
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

            inicializarPaginationCampos() {
                this.pageDocNumber = 0;
            },

            nextPageDoc() {
                this.pageDocNumber++;
            },
            pagePrevDoc() {
                this.pageDocNumber--;
            },
            selectPageDoc(page) {
                this.pageDocNumber = page;
            },  
            /*Fin Metodos Paginacion*/

            cambiarPagina(page,buscar,criterio){//Cambia de pagina en el paginador
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.ListarDocumentos(page,buscar,criterio);
            },

           

            CerrarModal(){//Cierra el modal y limpia las variables 
                //Cerramos el modal y limpiamos los datos variantes
                this.showModal =false;
                this.tituloModal ='';
                this.IdDocumento =0;
                this.arrayCampos=[];

            },

            AbrirModal(modelo,accion,IdDoc){//Abre el modal
                switch(modelo){
                    case "documento":
                    {
                        switch(accion){
                            case "agregar":{
                                this.tituloModal ='Registrar Campos '+IdDoc.Nombre;
                                this.showModal = true;
                                this.IdDocumento = IdDoc.IdDocumento;
                                this.CargarCampos();
                                break;
                            }
                        }
                    }
                }
            },

            CargarCampos(){
                let me = this;
                var url = '/documentos/campos?IdDoc='+me.IdDocumento;
                axios.get(url,{params:{
                    'filtro':this.filtcampos
                }}).then(function (response) {
                    me.inicializarPaginationCampos();
                    var respuesta = response.data;
                    me.arrayCampos = respuesta.campos;
                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            agregarDetalleModal(dato){
                let campo = dato.IdCampo;
                let alias = dato.NmEjemplo;
                let me =this;
                axios.post('/documentos/GuardarConfig', {
                    'IdDoc': me.IdDocumento,
                    'IdCampo': campo,
                    'AliasCampo': alias,
                })
                .then(function (response) {
                    me.CargarCampos();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },

            quitarDetalleModal(dato){
                let campo = dato.IdCampo;
                let alias = dato.NmEjemplo;
                let me =this;
                axios.put('/documentos/campos/delete', {
                    'IdDoc': me.IdDocumento,
                    'IdCampo': campo
                })
                .then(function (response) {
                    me.CargarCampos();
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },

        },
        mounted() {//Inicializa el constructor
            this.ListarDocumentos();
        }
    }
</script>

<!--Se va ejecutar este codigo cuando se quiere abrir el modal-->
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background-color: #3c29297a !important;
    }
</style>
