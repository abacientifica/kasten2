<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header bg-head">
                    <i class="fa fa-align-justify"></i> Configuración Lista de items <strong>{{ayuda.TituloAyuda}}</strong>
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
                                    <th>Editar</th>
                                    <th>Titulo</th>
                                    <th>Descripción</th>
                                    <th>Posición</th>
                                    <th>Url</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in ListarAyudasItemPaginate" :key="item.IdDocumento">
                                    <td>
                                        <button type="button"  class="btn btn-info btn-sm" >
                                            <router-link :to="'/configuracion/documentos/edit/'+item.IdDocumento">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                        </button> 
                                    </td>
                                    <td v-text="item.Titulo"></td>
                                    <td v-text="item.Descripcion"></td>
                                    <td v-text="item.Posicion"></td>
                                    <td v-text="item.Imagen"></td>
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
                            <label class="col-md-3 form-control-label" for="text-input">Titulo :</label>
                            <div class="col-md-9">
                            <input type="text"  class="form-control" v-model="fillCrearAyudaDet.Titulo" placeholder="Titulo " required/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Descripción :</label>
                            <div class="col-md-9">
                                <textarea class="form-control" placeholder="Descripción " v-model="fillCrearAyudaDet.Descripcion" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Posición :</label>
                            <div class="col-md-9">
                            <input type="number"  class="form-control" v-model="fillCrearAyudaDet.Posicion" placeholder="Posición " required/>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Imagen :</label>
                            <div class="col-md-9">
                                <input type="file" id="btn-file" class="form-control" @change="getFile"/>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click.prevent="AbrirModal('','')"> Cerrar</button>
                        <button type="button" v-if="accionModal == 0" class="btn btn-success" @click.prevent="registrarAyudaItem()">
                            Crear
                        </button>
                        <button type="button" v-if="accionModal == 1" class="btn btn-primary" @click.prevent="ValidarDatos()">
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
export default {
    data() {
        return {
            modal:0,
            tituloModal:'',
            mensajeError:[],
            ListAyudasItems:[],
            arrayDetalle:[],
            arrayCampos:[],
            filtcampos:'',
            fillCrearAyudaDet:{
                IdAyudaDet: this.$attrs.id,
                Titulo:'',
                Descripcion:'',
                Posicion:'',
                Imagen:''
            },
            Imagen:'',

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
            accionModal:'',
            tituloModal:'',
            ayuda:[]

        }
    },

    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.ListAyudasItems.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarAyudasItemPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.ListAyudasItems.slice(inicio, fin);
        },
        pagesList() {
            let a = this.ListAyudasItems.length;
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
            var url = '/ayuda/ListaItems';
            axios.get(url,{
                params:{
                    'buscar':buscar,
                    'criterio':criterio
                }
            }).then(function (response) {
                var respuesta = response.data;
                me.ListAyudasItems = respuesta.ayudasitem;
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

        /**
         * Carga las propiedades del input file
         */
        getFile(e) {
            this.fillCrearAyudaDet.Imagen = e.target.files[0];
            this.CargarImagen(this.fillCrearAyudaDet.Imagen);
        },

        /**
         * Obtiene la imagen del input file 
         */
        CargarImagen(file){
            //FileReader me permite leer archivos
            let leerImagen = new FileReader();

            //El evento onload se dispara despues de ejecutar readAsDataURL
            let me = this;
            leerImagen.onload = (e)=>{
                me.Imagen = e.target.result;
                me.fillCrearAyudaDet.Imagen = me.Imagen;
            }
            leerImagen.readAsDataURL(file);
        },

        registrarAyudaItem(){
            let url = "/ayudas/registrarAyudaItem";
            axios.post(url, {
                'fillCrearAyudaDet': this.fillCrearAyudaDet
            },)
            .then((response) => {
                let respuesta = response.data;
                console.log(respuesta)
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
        },

        AbrirModal(titulo,opcion){
            this.modal = !this.modal;
           
            if(this.modal !=0){
                if(opcion =='registrar'){
                    this.accionModal = 0;
                    this.tituloModal =" Registrar Ayuda Item";
                }
                else{
                    accionModal = 1;
                    this.tituloModal =" Actualizar Ayuda Item";
                }
            }
        },

        obtenerAyuda(){
            let me = this;
            var url = '/ayuda/ObtenerAyudaDet';
            axios.get(url,{
                params:{
                    'id':this.$attrs.id,
                }
            }).then(function (response) {
                var respuesta = response.data;
                me.ayuda = respuesta.ayudadet;
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
    },

    mounted() {//Inicializa el constructor
        this.obtenerAyuda();
        this.ListarAyudas();
    }
}
</script>