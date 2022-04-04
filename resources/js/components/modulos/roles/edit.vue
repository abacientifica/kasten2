<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Editar Rol</h1>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <router-link class="btn btn-info btn-sm" :to="'/roles'">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </router-link>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card card-info">
                                    <div class="card-header bg-info">
                                        <h3 class="card-title">Formulario Editar Rol</h3>
                                    </div>
                                    <div class="card-body">
                                        <form role="form">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">Nombre</label>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" v-model="fillEditarRol.cNombre" @keyup.enter="setEditarRolPermisos">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">Url Amigable</label>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" v-model="fillEditarRol.cUrl" @keyup.enter="setEditarRolPermisos">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <button class="btn btn-flat btn-info btnWidth" @click.prevent="setEditarRolPermisos" v-loading.fullscreen.lock="fullscreenLoading">Editar</button>
                                            <button class="btn btn-flat btn-default btnWidth" @click.prevent="limpiarCriterios">Limpiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card card-info">
                                    <div class="card-header bg-info">
                                        <h3 class="card-title">Listar Permisos 
                                        </h3>
                                        <form class="form-inline ml-3 float-right">
                                            <div class="input-group input-group-sm">
                                                <el-input placeholder="Buscar" prefix-icon="el-icon-search"  size="mini" v-model="search" ></el-input>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <template v-if="listarPermisosPaginate.length">
                                            <!--<div>
                                                <b v-text="opsel"></b><input type="checkbox" v-model="sel" @click.prevent="SeleccionarPermisos()">
                                            </div>-->
                                            <div class="scrollTable">
                                                <table class="table table-hover table-head-fixed text-nowrap projects">
                                                    <thead>
                                                        <tr>
                                                            <th><el-checkbox  v-model="checkAll"></el-checkbox> Acción </th>
                                                            <th>Nombre</th>
                                                            <th>Url Amigable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in listarPermisosPaginate" :key="index" @click.prevent="marcarFila(index)">
                                                            <td>
                                                                <el-checkbox v-model="item.checked" :checked="item.checked ? true :false" @click.prevent="marcarFila(index)" ></el-checkbox>
                                                            </td>
                                                            <td v-text="item.nombre"></td>
                                                            <td v-text="item.slug"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="callout callout-info">
                                                <h5>No se encontraron resultados...</h5>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sistema Laravel y Vue</h5>
                        <button class="close" @click="abrirModal"></button>index
                    </div>
                    <div class="modal-body">
                        <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in mensajeError" :key="index" v-text="item"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="abrirModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2'
    export default {
        data(){
            return {
                opsel:'Seleccionar Todos:',
                sel : false,
                filtroPermiso:'',
                fillEditarRol: {
                    nIdRol: this.$attrs.id,
                    cNombre: '',
                    cUrl: '',
                },
                listPermisos: [],
                listPermisosFilter: [],
                listRolPermisosByUsuario: [],
                listRolPermisosByUsuarioFilter: [],
                fullscreenLoading: false,
                modalShow: false,
                mostrarModal: {
                    display: 'block',
                    background: '#0000006b',
                },
                ocultarModal: {
                    display: 'none',
                },
                error: 0,
                mensajeError: [],
                listaPermisosByUser:[],
                listPermisosUserFilter:[],
                oUsuarioAct:[],
                permisosCheck:[],
                search:null,
                backFilter:[],
            }
        },
        computed: {
            //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
            listarPermisosPaginate() {
                return this.listPermisos;
            },
            pagesList() {
                let a = this.listPermisosFilter.length;
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

        watch:{
            search(newVal,oldVal){
                if(!oldVal)this.backFilter = this.listPermisos
                if(!newVal){
                    this.listPermisos = []
                    this.listPermisos  = this.backFilter
                }
                else{
                    this.listPermisos = this.listPermisos.filter(filt => filt.nombre.toLowerCase().includes(newVal.toLowerCase())).sort((a,b)=> { 
                        if(a.checked < b.checked){
                            return 1
                        }
                        if(a.checked > b.checked){
                            return -1
                        }
                        return 0
                    })
                }
                
                this.validCheck();
            },
        },
        mounted() {
            this.getListarRoles();
            this.getListarPermisosByRol();
            this.oUsuarioAct = JSON.parse(sessionStorage.getItem('authUser'));
        },
        methods: {
            limpiarCriterios(){
                this.fillEditarRol.cNombre   = '';
                this.fillEditarRol.cSlug      = '';
            },
            abrirModal(){
                this.modalShow = !this.modalShow;
            },
            getListarRoles(){
                this.fullscreenLoading = true;

                var url = '/roles/lista'
                axios.get(url, {
                    params: {
                        'nIdRol'   :   this.fillEditarRol.nIdRol
                    }
                }).then(response => {
                    this.fillEditarRol.cNombre  =   response.data.roles[0].nombre;
                    this.fillEditarRol.cUrl    =   response.data.roles[0].slug;
                    this.fullscreenLoading = false;
                }).catch(error => {
                    if (error.response.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },
            getListarPermisosByRol(){
                var ruta = '/roles/getListarPermisosByRol'
                axios.get(ruta, {
                    params: {
                        'nIdRol'  :   this.fillEditarRol.nIdRol,
                        'bEdit'   :   true,
                        'cNombre' :this.filtroPermiso,
                    }
                }).then( response => {
                    this.listPermisos = [];
                    this.listPermisos = response.data.permisosbyrol;
                    this.permisosCheck = this.listPermisos.filter(filt => filt.checked)
                }).catch(error => {
                    if (error.response.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },
            setEditarRolPermisos(){
                if (this.validarEditarRolPermisos()) {
                    this.modalShow = true;
                    return;
                }
                this.fullscreenLoading = true;

                var url = '/rol/editar'
                axios.post(url, {
                    'IdRol'                :   this.fillEditarRol.nIdRol,
                    'cNombre'               :   this.fillEditarRol.cNombre,
                    'cUrl'                 :   this.fillEditarRol.cUrl,
                    'listPermisosFilter'    :   this.permisosCheck
                }).then(response => {
                    this.getListarRolPermisosByUser();
                    this.getListarRolPermisosByUsuario();
                    this.fullscreenLoading = false;
                    this.$router.push('/roles');
                }).catch(error => {
                    
                })
            },
            
            validarEditarRolPermisos(){
                this.error = 0;
                this.mensajeError = [];

                if (!this.fillEditarRol.cNombre) {
                    this.mensajeError.push("El Nombre es un campo obligatorio")
                }
                if (!this.fillEditarRol.cUrl) {
                    this.mensajeError.push("La Url Amigable es un campo obligatorio")
                }

                let contador = 0;
                this.listPermisosFilter.map(function(x, y){
                    if (x.checked == true) {
                        contador++;
                    }
                })
                if (this.permisosCheck.length<=0) {
                    this.mensajeError.push("Debe seleccionar al menos un permiso");
                }

                if (this.mensajeError.length) {
                    this.error = 1;
                }
                return this.error;
            },


            getListarRolPermisosByUser(){
                let url = "/permiso/ObtenerPermisosUsuario";
                this.listaPermisosByUser = [];
                axios.get(url,{params:{
                    'cUsuario':this.oUsuarioAct.Usuario,
                    'nIdRol':this.oUsuarioAct.IdRol,
                }}).then((response)=>{
                    let Datos = response.data;
                    this.listaPermisosByUser = Datos.permisos;
                    this.filterListRolPermisosByUsuario();
                })
            },

            filterListRolPermisosByUsuario(){
                if(this.$attrs.id == this.oUsuarioAct.IdRol){
                    let me = this;
                    me.listaPermisosByUser.map(function(x,y){
                        me.listPermisosUserFilter.push(x.slug);
                    });
                    sessionStorage.setItem('listPermisosFilterByRolUser',JSON.stringify(this.listPermisosUserFilter));
                    EventBus.$emit("notififyRolPermisosByUser",me.listPermisosUserFilter);
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Rol Actualizado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                this.fullscreenLoading = false;
            },

            marcarFila(index){
                this.listPermisos[index].checked = !this.listPermisos[index].checked ? true:false;
                this.validarCheck(this.listPermisos[index],index)
            },

            validarCheck(permiso,index){
                const permisosExist = this.permisosCheck.filter(filt => filt.id === permiso.id);
                if(!permiso.checked && permisosExist.length >0){
                    this.permisosCheck.splice((index),1)
                }
                else if (permiso.checked && permisosExist.length <=0){
                    this.permisosCheck.push(permiso)
                }
            },

            validCheck(){
                this.listarPermisosPaginate.map(el=> {
                    if(this.permisosCheck.filter(filt=> filt.id === el.id).length){
                        el.checked = true
                    }
                })
            },


            checkAll(newVal){
                this.listPermisosFilter.map(el=> el.checked = newVal) 
            }
        },
    }
</script>

<style>
.scrollTable{
  max-height: 300px !important;
  overflow: auto !important;
}
</style>