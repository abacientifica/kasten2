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
                                    <div class="card-header">
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
                                    <div class="card-header">
                                        <h3 class="card-title">Listar Permisos</h3>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <template v-if="listPermisosFilter.length">
                                            <div class="scrollTable">
                                                <table class="table table-hover table-head-fixed text-nowrap projects">
                                                    <thead>
                                                        <tr>
                                                            <th>Acción</th>
                                                            <th>Nombre</th>
                                                            <th>Url Amigable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in listPermisosFilter" :key="index" @click.prevent="marcarFila(index)">
                                                            <td>
                                                                <!-- Ira el Checkbox para seleccionar los permisos que se le asignaran al rol -->
                                                                <el-checkbox v-model="item.checked"></el-checkbox>
                                                            </td>
                                                            <td v-text="item.name"></td>
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
                        <button class="close" @click="abrirModal"></button>
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
                listPermisosUserFilter:[]
            }
        },
        computed: {
        },
        mounted() {
            this.getListarRoles();
            this.getListarPermisosByRol();
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
                    console.log(response.data);
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
                        'nIdRol'   :   this.fillEditarRol.nIdRol,
                        'bEdit'   :   true
                    }
                }).then( response => {
                    this.listPermisos = response.data.permisosbyrol;
                    this.filterPermisosByRol();
                }).catch(error => {
                    if (error.response.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },
            filterPermisosByRol() {
                let me = this;
                me.listPermisos.map(function(x, y){
                    me.listPermisosFilter.push({
                        'id'        :   x.id,
                        'name'      :   x.name,
                        'slug'      :   x.slug,
                        'checked'   :   (x.checked == 1) ? true : false
                    })
                })
            },
            marcarFila(index){
                this.listPermisosFilter[index].checked  =   !this.listPermisosFilter[index].checked;
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
                    'listPermisosFilter'    :   this.listPermisosFilter
                }).then(response => {
                    //this.getListarRolPermisosByUser();
                    //this.getListarRolPermisosByUsuario();
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
                if (contador == 0) {
                    this.mensajeError.push("Debe seleccionar al menos un permiso");
                }

                if (this.mensajeError.length) {
                    this.error = 1;
                }
                return this.error;
            },


            getListarRolPermisosByUser(){
                let url = "/administracion/permiso/LisRolObtenerPermisosByUsuario/";
                this.listaPermisosByUser = [];
                axios.get(url).then((response)=>{
                    let Datos = response.data;
                    this.listaPermisosByUser = Datos.permisos;
                    this.filterListRolPermisosByUsuario();
                })
            },

            filterListRolPermisosByUsuario(){
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
                this.fullscreenLoading = false;
            },
        }
    }
</script>

<style>
.scrollTable{
  max-height: 300px !important;
  overflow: auto !important;
}
</style>