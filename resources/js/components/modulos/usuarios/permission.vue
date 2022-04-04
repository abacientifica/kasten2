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
                <div class="card-header bg-info">
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
                                    <div class="card-header bg-info">
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
                                    <div class="card-header bg-info">
                                        <h3 class="card-title">Listar Permisos Usuario</h3>
                                        <form class="form-inline ml-3 float-right">
                                            <div class="input-group input-group-sm">
                                                <el-input placeholder="Buscar" prefix-icon="el-icon-search"  size="mini" v-model="search" ></el-input>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <template v-if="listarPermisosPaginate.length">
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
                                            <paginador :totalItems="listPermisosFilter.length" :itemPorPagina="10" v-on:paginaSelect="selectPage"></paginador>
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
import Paginador from '../../plantilla/paginador.vue';
import serviceApp from "../../../ServicesApp";
const servicesApp = new serviceApp();
export default {
    components:{
        Paginador
    },
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
            search:null,
            checkAll:false,
            permisosCheck:[],
            pageNumber: 0,
            perPage: 6,
            services:servicesApp,
            backFilter:[]
        }
    },
    computed: {
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        listarPermisosPaginate() {
            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.listPermisosFilter.slice(inicio, fin);
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
            if(!oldVal)this.backFilter = this.listPermisosFilter
            if(!newVal){
                this.listPermisosFilter = []
                this.listPermisosFilter  = this.backFilter
            }
            else{
                this.listPermisosFilter = this.listPermisosFilter.filter(filt => filt.nombre.toLowerCase().includes(newVal.toLowerCase())).sort((a,b)=> { 
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
            this.listarPermisosPaginate
        },

        checkAll(newVal){
            this.listPermisosFilter.map(el=> el.checked = newVal) 
        }

    },
    
    methods: {
        setRegistrarPermisosUsuario(){
            const loader = this.services.loader(this)
            this.fullscreenLoading = true;
            let url = "/permiso/setActualizarPermisosByUsuario";
            axios.post(url, {
                'cUsuario': this.IdUsuario,
                'listPermisosFilter': this.permisosCheck,
            })
            .then((response) => {
                this.getAllPermisosUsuario();
                this.fullscreenLoading = false;
                loader.close()
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: 'Permisos Actualizados',
                    showConfirmButton: false,
                    timer: 1500
                })
                this.$router.push('/usuarios');
            }).catch(error =>{
                loader.close()
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
            const loader = this.services.loader(this)
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
                loader.close()
            }).catch(error =>{
                loader.close()
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
                this.listPermisosFilter = Datos.permisos;
                this.permisosCheck = Datos.permisos.filter(filt => filt.checked)
                
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

        abrirModal(){
            this.modalShow = ! this.modalShow;
            this.mensajeError = [];
            this.error =0;
        },

        marcarFila(index){
            let inicio = (this.pageNumber * this.perPage)
            index = (inicio + index )
            this.listPermisosFilter[index].checked = !this.listPermisosFilter[index].checked ? true:false;
            this.validarCheck(this.listPermisosFilter[index],index)
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

        validarCheck(permiso,index){
            console.log({permiso,index})
            const permisosExist = this.permisosCheck.filter(filt => filt.id === permiso.id);
            if(!permiso.checked && permisosExist.length >0){
                this.permisosCheck.splice((index),1)
            }
            else if (permiso.checked && permisosExist.length <=0){
                this.permisosCheck.push(permiso)
            }
        },

        selectPage(page) {
            this.pageNumber = page;
        },   

        validCheck(){
            this.listarPermisosPaginate.map(el=> {
                if(this.permisosCheck.filter(filt=> filt.id === el.id).length){
                    el.checked = true
                }
            })
        }
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
