<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Asignar Permisos</h1>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <router-link class="btn btn-info btn-sm" :to="'/usuarios'">
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
                                        <h3 class="card-title">Permisos Rol {{NmRol}}</h3>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <template v-if="listPermisosRolAsignado.length">
                                            <div class="scrollTable">
                                                <table class="table table-hover table-head-fixed text-nowrap projects">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Url Amigable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in listPermisosRolAsignado" :key="index">
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
                            <div class="col-md-7">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Listar Permisos Usuario</h3>
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
                <div class="card-footer">
                    <button class="btn btn-success" @click.prevent="setRegistrarPermisosUsuario()">Actualizar Permisos</button>
                </div>
            </div>
        </div>

        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alerta !!!</h5>
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
                fillPermiso: {
                    cNombre: '',
                    cUrl: '',
                },
                listPermisos: [],
                listPermisosFilter: [],
                listPermisosRolAsignado:[],
                listaPermisosByUser:[],
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
                NmRol:'',
                mensajeError: [],
                nUserId:0,
                listPermisosFilterUser:[],    
                listaPermisosByUserRol:[],        
                listPermisosUserFilter:[],
                IdUsuario:'',
                IdRol:0,
            }
        },
        computed: {
        },
        
        methods: {
            setRegistrarPermisosUsuario(){
                /*if(this.validarPermisosUsuario()){
                    return;
                }*/
                
                this.fullscreenLoading = true;
                let url = "/permiso/setActualizarPermisosByUsuario";
                axios.post(url, {
                    'cUsuario': this.IdUsuario,
                    'listPermisosFilter': this.listPermisosFilter,
                })
                .then((response) => {
                    this.getAllPermisosUsuario();
                    this.fullscreenLoading = false;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Permisos Actualizados',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.$router.push('/usuarios');
                }).catch(error =>{
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                });
                
            },

            validarPermisosUsuario(){
                this.error =0;
                this.mensajeError =[];
                let contador =0;
                this.listPermisosFilter.map(function(x,y){
                    if(x.checked == true){
                        contador++;
                    }
                })
                if(contador == 0){
                    this.mensajeError.push("Debes seleccionar almenos un permiso");
                }
                if(this.mensajeError.length){
                    this.error =1;
                }
                
                return this.error;
            },

            getListarPermisosByRol(){
                let url = "/roles/getListarPermisosByRol";
                axios.get(url,{params: {
                    'nIdRol'  :   this.nIdRol,
                    'bEdit'   :   true,
                }},)
                .then((response) => {
                    this.listPermisos = response.data.permisosbyrol;
                }).catch(error =>{
                    if(error.response.status != undefined && error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                });
            },

            getListarPermisosByRolAsignado(IdRol){
                var ruta = '/roles/getListarPermisosRol'
                axios.get(ruta, {
                    params: {
                    'nIdRol'   :   IdRol >0 ?  IdRol : 0
                    }
                }).then( response => {
                    this.listPermisosRolAsignado = response.data.permisosbyrol;
                }).catch(error =>{
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },

            getRolByUser(Id){
                let url = "/roles/ObtenerRolByUsuario/"+Id;
                axios.get(url).then((response)=>{
                    if(response.data.rol.length >0){
                        let Datos = response.data.rol[0];
                        this.NmRol = Datos.nombre;
                        this.nUserId = Datos.user_id;
                        this.IdRol = Datos.IdRol;
                        this.getPermisosByUser();
                        this.getListarPermisosByRolAsignado(Datos.id);
                    }
                }).catch(error =>{
                    if(error.response.status != undefined && error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },

            getPermisosByUser(){
                let url = "/permiso/ObtenerPermisosByUsuario";
                axios.get(url,{
                    params:{
                        'IdRol':this.IdRol,
                        'Usuario':this.IdUsuario
                    }
                }).then((response)=>{
                    let Datos = response.data;
                    this.listaPermisosByUser = Datos.permisos;
                    this.filterPermisosByUsuario();
                }).catch(error =>{
                    console.log(error)
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },

            filterPermisosByUsuario(){
                let me = this;
                me.listaPermisosByUser.map(function(x,y){
                    me.listPermisosFilter.push({
                        'id':x.id,
                        'nombre':x.nombre,
                        'slug':x.slug,
                        'checked': (x.checked == 1) ? true : false
                    });
                })
            },

            abrirModal(){
                this.modalShow = ! this.modalShow;
                this.mensajeError = [];
                this.error =0;
            },

            marcarFila(index){
                this.listPermisosFilter[index].checked = !this.listPermisosFilter[index].checked;
            },

            filterListRolPermisosByUsuario(){
                let usrActual = JSON.parse(sessionStorage.getItem('authUser'));
                console.log(usrActual.Usuario)
                if(usrActual.Usuario == this.IdUsuario){
                    
                    let me = this;
                    me.listaPermisosByUser.map(function(x,y){
                        if(x.slug != null){
                            me.listPermisosUserFilter.push(x.slug);
                        }
                    });
                    sessionStorage.setItem('listPermisosFilterByRolUser',JSON.stringify(this.listPermisosUserFilter));
                    localStorage.setItem('listPermisosFilterByRolUser',JSON.stringify(this.listPermisosUserFilter));
                    EventBus.$emit("notififyRolPermisosByUser",me.listPermisosUserFilter);
                }
            },

            getAllPermisosUsuario(){
                let url = "/permiso/ObtenerPermisosUsuario";
                axios.get(url,{params:{
                    'cUsuario':this.IdUsuario,
                    'nIdRol':this.IdRol
                }}).then((response)=>{
                    let Datos = response.data;
                    this.listaPermisosByUser = Datos.permisos;
                    this.filterListRolPermisosByUsuario();
                }).catch(error =>{
                    if(error.response.status ==401){
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                })
            },
        },

        mounted() {
            this.IdUsuario = this.$attrs.id;
            this.getListarPermisosByRol();
            this.getRolByUser(this.IdUsuario);
        },
    }
</script>

<style>

</style>
