<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Crear Rol</h1>
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
                                        <h3 class="card-title">Formulario Registrar Rol</h3>
                                    </div>
                                    <div class="card-body">
                                        <form role="form">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">Nombre</label>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" v-model="fillCrearRol.cNombre" @keyup.enter="setRegistrarRolPermisos">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">Url Amigable</label>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" v-model="fillCrearRol.cUrl" @keyup.enter="setRegistrarRolPermisos">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <button class="btn btn-flat btn-info btnWidth" @click.prevent="setRegistrarRolPermisos" v-loading.fullscreen.lock="fullscreenLoading">Registrar</button>
                                            <button class="btn btn-flat btn-default btnWidth" @click.prevent="limpiarCriterios">Limpiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Listar Permisos <input class="form-control form-control-navbar" type="search" v-model="filtroPermiso" @keyup.enter="getListarPermisosByRol()" placeholder="Buscar " aria-label="Search"></h3>
                                        
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
    export default {
        data(){
            return {
                fillCrearRol: {
                    cNombre: '',
                    cUrl: '',
                },
                listPermisos: [],
                listPermisosFilter: [],
                filtroPermiso:'',
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
                mensajeError: []
            }
        },
        computed: {
        },
        mounted() {
            this.getListarPermisosByRol();
        },
        methods: {
            setRegistrarRolPermisos(){
                if(this.validarRegistroRolPermisos()){
                    this.modalShow = true;
                    return;
                }
                this.fullscreenLoading = true;
                let url = "/rol/crear";
                axios.post(url, {
                    'cNombre': this.fillCrearRol.cNombre,
                    'cUrl': this.fillCrearRol.cUrl,
                    'listPermisosFilter': this.listPermisosFilter,
                })
                .then((response) => {
                    this.fullscreenLoading = false;
                    this.$router.push('/roles');
                });
            },

            validarRegistroRolPermisos(){
                this.error =0;
                this.mensajeError =[];
                let Datos = this.fillCrearRol;
                if(!Datos.cNombre){
                    this.mensajeError.push("El campo nombre es obligatorio");
                }
                if(!Datos.cUrl){
                    this.mensajeError.push("El campo Url/Amigable  es obligatorio");
                }
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

            filterPermisosByRol(){
                let me = this;
                me.listPermisosFilter =[];
                me.listPermisos.map(function(x,y){
                    me.listPermisosFilter.push({
                        'id':x.id,
                        'nombre':x.nombre,
                        'slug':x.slug,
                        'checked': false,
                    });
                })
            },

            getListarPermisosByRol(){
                let url = "/roles/getListarPermisosByRol";
                axios.get(url,{
                    params:{
                        cNombre:this.filtroPermiso
                    }
                })
                .then((response) => {
                    this.listPermisos = response.data.permisosbyrol;
                    if(response.data.permisosbyrol.length >0){
                        this.filterPermisosByRol();
                    }
                    else{
                        this.listPermisosFilter =[];
                    }
                });
            },

            abrirModal(){
                this.modalShow = ! this.modalShow;
                this.mensajeError = [];
                this.error =0;
            },

            marcarFila(index){
                this.listPermisosFilter[index].checked = !this.listPermisosFilter[index].checked;
            }
        }
    }
</script>

<style>

</style>
