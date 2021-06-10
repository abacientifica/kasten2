<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Usuarios</h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Usuarios</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <template v-if="listPermisosFilterByRolUser.includes('usuario.crear')">
                        <div class="card-tools">
                            <button class="btn btn-info btn-sm" @click.prevent="abrirModal(1)" data-toggle="modal">
                            <i class="fas fa-plus-square"></i> Nuevo Usuario
                            </button>

                            <button class="btn btn-success btn-sm" @click.prevent="actualizarContrasenas()" data-toggle="modal">
                            <i class="fas fa-user-edit"></i> Actualizar Contraseñas
                            </button>

                            <button class="btn btn-info btn-sm" @click="ValidarFinalizacionSesion(),CerrarTodos = true">
                                <i class="fas fa-user-clock"> </i>Cerrar Sesion Todos
                            </button>
                        </div>
                    </template>
                </div>
                <div class="card-body">
                <div class="container-fluid">
                    <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Criterios de Busqueda</h3>
                    </div>
                    <div class="card-info">
                        <div class="card-body">
                        <form role="form">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="filtUsuario.cNombre" @keyup.enter="listarUsuarios()"/>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Usuario</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="filtUsuario.cUsuario" @keyup.enter="listarUsuarios()"/>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Email</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" v-model="filtUsuario.cCorreo" @keyup.enter="listarUsuarios()"/>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Estado</label>
                                <div class="col-md-9">
                                    <el-select v-model="filtUsuario.cEstado" placeholder="Seleccione un estado" clearable @keyup.enter="listarUsuarios()">
                                    <el-option v-for="item in listEstados" :key="item.value" :label="item.label" :value="item.value">
                                    </el-option>
                                    </el-select>
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
                            <button class="btn btn-flat btn-info" @click.prevent="listarUsuarios()" v-loading.fullscreen.lock="fullscreenLoading">
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
                        <div class="card-body table-responsive" style="height: 700px;">
                            <template v-if="listarUserPaginate.length <= 0">
                            <div class="callout callout-info">
                                <h5>Sin Resultados</h5>
                            </div>
                            </template>
                            <table class="table table-hover table-head-fixed text-nowrap projects"
                            v-else
                            >
                            <thead class="sticky-header">
                                <tr>
                                <th>Foto</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Usuario</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user) in listarUserPaginate" :key="user.id">
                                <td v-if="user.imagen == null">
                                    <img  src="/img/avatar.png"  alt="user.username" class="img-circle elevation-2" style="width:50px"/>
                                </td>
                                <td v-else>
                                    <img  :src="user.imagen"  alt="user.username" class="img-circle elevation-2" style="width:50px"/>
                                </td>
                                <td v-text="user.Nombres"></td>
                                <td v-text="user.Apellidos"></td>
                                <td v-text="user.Usuario"></td>
                                <td v-text="user.IdTercero"></td>
                                <td>
                                    <div v-if="user.Inactivo == 1">
                                    <span class="badge badge-danger">Inactivo</span>
                                    </div>
                                    <div v-else>
                                    <span class="badge badge-success">Activo</span>
                                    </div>
                                </td>
                                <td>
                                    <template v-if="listPermisosFilterByRolUser.includes('usuario.editar') || listPermisosFilterByRolUser.includes('usuario.administrador')">
                                        <button class="btn btn-info btn-sm" @click="abrirModal(1,user)">
                                        <i class="fas fa-pencil-alt"> </i>Editar
                                        </button>
                                    </template>
                                    
                                    <template v-if="listPermisosFilterByRolUser.includes('usuario.asignarpermisos') || listPermisosFilterByRolUser.includes('usuario.administrador')">
                                        <router-link :to="'/usuario/permiso/'+user.Usuario"  class="btn btn-success btn-sm">
                                            <i class="fas fa-key"> </i> Permiso
                                        </router-link>
                                    </template>

                                    <template v-if="user.Inactivo == 1">
                                        <template v-if="listPermisosFilterByRolUser.includes('usuario.activar') || listPermisosFilterByRolUser.includes('usuario.administrador')">
                                            <button class="btn btn-success btn-sm" @click="ActivarUsuario(user)">
                                                <i class="fas fa-check"> </i>Activar
                                            </button>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <template v-if="listPermisosFilterByRolUser.includes('usuario.inactivar') || listPermisosFilterByRolUser.includes('usuario.administrador')">
                                            <button class="btn btn-danger btn-sm" @click="InActivarUsuario(user)">
                                                <i class="fas fa-times-circle"> </i>Inactivar
                                            </button>
                                        </template>
                                    </template>
                                    <router-link :to="{name:'usuario.perfil',params:{id:user.Usuario}}"  class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"> </i> Perfil
                                    </router-link>
                                    <button class="btn btn-info btn-sm" @click="ValidarFinalizacionSesion(user)">
                                        <i class="fas fa-user-clock"> </i>Cerrar Sesion
                                    </button>
                                </td>
                                </tr>
                            </tbody>
                            </table>
                            
                        </div>
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
        <!--Fin Contenido-->
        <div class="modal fade" :class="{ show: showModal }" :style="showModal ? mostrarModal : ocultarModal" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="callout callout-danger" v-for="(errorm, index) in mensajeError" :key="index" v-text="errorm" style="padding: 5px"></div>
                <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" v-text="tituloModal"></h5>
                    <button type="button" class="close" @click.prevent="cerrarModal()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Nombres :</label>
                        <div class="col-md-9">
                        <input type="text"  class="form-control" v-model="fillCrearUsuario.cNombres" placeholder="Nombres " />
                        </div>
                    </div>

                    
                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Apellidos:</label>
                        <div class="col-md-9">
                        <input type="text" class="form-control" v-model="fillCrearUsuario.cApellidos" placeholder="Apellidos"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input" >Identificación:</label>
                        <div class="col-md-9">
                        <input type="number" class="form-control" v-model="fillCrearUsuario.nIdtercero"  placeholder="Nit/CC"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Nombre Usuario:</label>
                        <div class="col-md-9">
                        <input type="text" class="form-control" v-model="fillCrearUsuario.cUsuario" placeholder="Usuario" v-if="accionModal==2?disalbed:''"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Email :</label>
                        <div class="col-md-9">
                        <input  type="email" v-model="fillCrearUsuario.cCorreo" class="form-control" placeholder="Email"/>
                        </div>
                    </div>

                    <div class="form-group row" v-if="accionModal != 3">
                        <label class="col-md-3 form-control-label" for="text-input">Contraseña:</label>
                        <div class="col-md-9">
                        <el-input placeholder="Ingrese Contraseña" v-model="fillCrearUsuario.cContrasena"  show-password></el-input>
                        </div>
                    </div>

                    <div class="form-group row" v-if="accionModal != 3">
                        <label class="col-md-3 form-control-label" for="text-input">Rol:</label>
                        <div class="col-md-9">
                        <el-select  v-model="fillCrearUsuario.nIdRol" placeholder="Seleccione un Rol" clearable>
                            <el-option
                                v-for="item in listRoles"
                                :key="item.id"
                                :label="item.nombre"
                                :value="item.id">
                            </el-option>
                        </el-select>
                        </div>
                    </div>

                    <div class="form-group row" v-if="accionModal != 3">
                        <label class="col-md-3 form-control-label" for="text-input">Cargo:</label>
                        <div class="col-md-9">
                        <input type="text" class="form-control" v-model="fillCrearUsuario.cCargo" placeholder="Cargo"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 form-control-label" for="text-input">Imagen Perfil :</label>
                        <div class="col-md-9">
                        <input type="file" id="btn-file" class="form-control" @change="getFile" />
                        <img :src="ImagenPerfil" class="img-circle elevation-2 img-avatars" alt="">
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click.prevent="cerrarModal()"> Cerrar</button>
                    <button type="button" v-if="accionModal == 0" class="btn btn-success" v-loading.fullscreen.lock="fullscreenLoading" @click.prevent="registraUsuario()">
                    Crear
                    </button>
                    <button type="button" v-if="accionModal == 1" class="btn btn-primary" @click.prevent="ValidarDatos()">
                    Actualizar
                    </button>
                </div>
                </div>
            </div>
        </div>
        <template>
            <div class="center">
            <vs-dialog @close="ValidarFinalizacionSesion('')" v-model="active">
                <template #header>
                <h4 class="not-margin">
                    Hola especifica el motivo.
                </h4>
                </template>


                <div class="con-form">
                    <textarea class="form-control" v-model="msgCerrarSesion" placeholder="Describe el motivo de finalización de sesión...">
                    
                    </textarea>
                </div>
                <vs-button block warn :disabled="msgCerrarSesion!=''?false:true" @click.prevent="CerrarSesionUsuario()">
                    Cerrar
                </vs-button>
            </vs-dialog>
            </div>
        </template>
    </div>
</template>
<script>
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            Form: new FormData(),
            //Al cambiar a true muestra el loading
            fullscreenLoading:false,
            listPermisosFilterByRolUser:[],
            //Objeto usuario para realizar consultas de usuarios.
            filtUsuario:{
                nIdentificacion:0,
                cNombre:'',
                cUsuario:'',
                cCorreo:'',
                cEstado:0
                
            },
            ImagenPerfil:'',
            //Con este objeto enviados los datos del usuarios nuevo o el que vamos a actualizar
            fillCrearUsuario:{
                cUsuario : '',
                nIdtercero: '',
                cContrasena : '',
                cNombres : '',
                cApellidos : '',
                cCargo:'',
                cCorreo:'',
                nIdRol:'',
                cImagen:'',
            },
            //Contiene la lista de usuarios recuperada
            listUsuarios: [],
            //LLena el combo de estados en el filtro
            listEstados: [
                { value: 0, label: "Activo" },
                { value: 1, label: "Inactivo" },
            ],
            listRoles:[],

            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 20,
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
            active:false,
            msgCerrarSesion:'',
            userSelect:[],
            CerrarTodos:false,
        }
    },
    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.listUsuarios.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        listarUserPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.listUsuarios.slice(inicio, fin);
        },
        pagesList() {
            let a = this.listUsuarios.length;
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
        userActive(user){
            /*console.log("Entro"+user)
            if(!this.active){
                this.userSelect = [];
            }
            else{   
                this.userSelect = user;
            }*/
            return true;
        }
    },
    methods: {
        /**
         * Obtenemos el listado de usuarios
         */
        listarUsuarios(){
            let url ="/usuarios/lista";
            axios.get(url,{params:{
                'cNombre' : this.filtUsuario.cNombre,
                'cUsuario' : this.filtUsuario.cUsuario,
                'cCorreo' : this.filtUsuario.cCorreo,
                'cEstado' : this.filtUsuario.cEstado
            }}).then(response=>{    
                this.inicializarPagination();
                if(response.data.usuarios.length){
                    this.listUsuarios = response.data.usuarios;
                }
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        LimpiarFiltro(){
            this.filtUsuario.cNombre ='';
            this.filtUsuario.cUsuario ='';
            this.filtUsuario.cCorreo ='';
            this.filtUsuario.cEstado =1;
            this.listarUsuarios();
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

        ValidarDatos(){
            if(this.validarRegistro()){
                this.ActualizarUsuario();
            }
        },

        actualizarContrasenas(){
            let url = "/usuarios/actualizarpass";
            axios.put(url)
            .then((response) => {
                Swal.fire({
                    icon :'success',
                    type :'success',
                    title :'',
                    text: response.data.msg
                })
            }).catch(error =>{
                Swal.fire({
                    icon :'warning',
                    type :'warning',
                    title :'',
                    text: response.data.msg
                })
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },


        /**
         * Valida los campos del formulario editar o registrar
         */
        validarRegistro() {
            this.error = true;
            this.mensajeError = [];
            let Datos = this.fillCrearUsuario;

            if (!Datos.cUsuario) {
                this.mensajeError.push("El usuario es obligatorio");
            }

            if (!Datos.cNombres) {
                this.mensajeError.push("Los nombres son obligatorios");
            }

            if (!Datos.cApellidos) {
                this.mensajeError.push("Los apellidos son obligatorios");
            }

            if (!Datos.cCorreo) {
                this.mensajeError.push("El correo es obligatorio");
            }

            if (!Datos.cContrasena && this.accionModal ==0) {
                this.mensajeError.push("La contraseña es obligatorio");
            }

            if (!Datos.nIdRol) {
                this.mensajeError.push("El rol es obligatorio");
            }

            if (!Datos.cCargo) {
                this.mensajeError.push("El cargo es obligatorio");
            }

            if (this.mensajeError.length) {
                this.error = false;
            }
            return this.error;
        },

        /**
         * Actualiza el usuario
         */
        ActualizarUsuario() {
            let url = "/usuarios/editar";
            axios.put(url, {
                'fillCrearUsuario' : this.fillCrearUsuario,
            })
            .then((response) => {
                this.listarUsuarios();
                this.cerrarModal();
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        ActivarUsuario(usuario){
            let url = "/usuarios/activar/"+usuario.Usuario;
            axios.put(url)
            .then((response) => {
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: 'Usuario : '+usuario.Nombres+" Activo",
                    showConfirmButton: false,
                    timer: 1500
                });
                this.listarUsuarios();
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        InActivarUsuario(usuario){
            let url = "/usuarios/inactivar/"+usuario.Usuario;
            axios.put(url)
            .then((response) => {
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: 'Usuario : '+usuario.Nombres+" Inactivo",
                    showConfirmButton: false,
                    timer: 1500
                });
                this.listarUsuarios();
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        /**
         * op 1 = usuarios nuevo,2 =editar usuario
         */
        abrirModal(op,data=[]) {
            this.showModal = true;
            if(op = 1){
                this.accionModal = 0;
                this.tituloModal ="Crear Nuevo Usuario";
            }
            if(op = 2){
                this.accionModal = 1;
                this.getListRoles();
                this.tituloModal ="Actualizar Usuario "+ data.Nombres;
                this.fillCrearUsuario.cUsuario = data.Usuario;
                this.fillCrearUsuario.nIdtercero = data.IdTercero;
                this.fillCrearUsuario.cContrasena = data.Contrasena;
                this.fillCrearUsuario.cNombres = data.Nombres;
                this.fillCrearUsuario.cApellidos = data.Apellidos;
                this.fillCrearUsuario.cCargo = data.Cargo;
                this.fillCrearUsuario.cCorreo = data.Email;
                this.fillCrearUsuario.nIdRol = data.IdRol ==0 ? '': data.IdRol;
                this.fillCrearUsuario.cImagen = '';
                this.ImagenPerfil = data.imagen;
            }
        },
        cerrarModal(){
            this.showModal = false;
            this.accionModal = 0;
            this.limpiarFormulario();
        },

        limpiarFormulario(){
            this.fillCrearUsuario.cUsuario = '';
            this.fillCrearUsuario.nIdtercero = '';
            this.fillCrearUsuario.cContrasena = '';
            this.fillCrearUsuario.cNombres = '';
            this.fillCrearUsuario.cApellidos = '';
            this.fillCrearUsuario.cCargo = '';
            this.fillCrearUsuario.cCorreo = '';
            this.fillCrearUsuario.nIdRol = '';
            this.fillCrearUsuario.cImagen = '';
        },

        /**
         * Carga las propiedades del input file
         */
        getFile(e) {
            this.fillCrearUsuario.cImagen = e.target.files[0];
            this.CargarImagen(this.fillCrearUsuario.cImagen);
        },

        /**
         * Obtiene la imagen del input file 
         */
        CargarImagen(file){
            //FileReader me permite leer archivos
            let leerImagen = new FileReader();

            //El evento onload se dispara despues de ejecutar readAsDataURL
            leerImagen.onload = (e)=>{
                this.ImagenPerfil = e.target.result;
                this.fillCrearUsuario.cImagen = this.ImagenPerfil;
            }
            leerImagen.readAsDataURL(file);
        },

        /**
         * Carga los roles del combo rol usuario
         */
        getListRoles() {
            let url = "/roles/lista";
            axios .get(url).then((response) => {
                if(response.data.roles.length >0){
                    this.listRoles = response.data.roles;
                }
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        CerrarSesionUsuario(){
            this.active = false;
            axios.get('/usuarios/cerrarsesion/',{
                params:{
                    'Usuario': this.userSelect.Usuario,
                    'Mensaje' : this.msgCerrarSesion,
                    'Todos': this.CerrarTodos
                }
            }).then((response)=>{
               
                this.userSelect = [];
                this.CerrarTodos = false
            }).catch(error=>{
                this.active = false;
                this.userSelect = [];
                this.CerrarTodos = false
            })
        },

        ValidarFinalizacionSesion(user = []){
            this.userSelect = user;
            if(user){
                this.active = true;
            }
        }
    },
    mounted() {
        this.listarUsuarios();
        this.listPermisosFilterByRolUser = sessionStorage.getItem('listPermisosFilterByRolUser');
    },

}
</script>