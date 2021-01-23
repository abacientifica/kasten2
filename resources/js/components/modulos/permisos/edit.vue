<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Editar Permiso</h1>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <router-link class="btn btn-info btn-sm" :to="'/permisos'">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </router-link>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Formulario Registrar Editar</h3>
                                </div>
                                <div class="card-body">
                                    <form role="form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Nombre</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" v-model="fillEditarPermiso.cNombre" @keyup.enter="setEditarPermiso">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Url Amigable</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" v-model="fillEditarPermiso.cUrl" @keyup.enter="setEditarPermiso">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-4 offset-4">
                                            <button class="btn btn-flat btn-info btnWidth" @click.prevent="setEditarPermiso" v-loading.fullscreen.lock="fullscreenLoading">Actualizar</button>
                                            <button class="btn btn-flat btn-default btnWidth" @click.prevent="limpiarCriterios">Limpiar</button>
                                        </div>
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
import Swal from 'sweetalert2'
    export default {
        data(){
            return {
                fillEditarPermiso: {
                    nIdPermiso:0,
                    cNombre: '',
                    cUrl: '',
                },
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
        
        methods: {
            setEditarPermiso(){
                if(this.validarRegistroPermiso()){
                    this.modalShow = true;
                    return;
                }
                this.fullscreenLoading = true;
                let url = "/permisos/editar";
                axios.post(url, {
                    'nIdPermiso' :this.fillEditarPermiso.nIdPermiso,
                    'cNombre': this.fillEditarPermiso.cNombre,
                    'cUrl': this.fillEditarPermiso.cUrl,
                })
                .then((response) => {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Permiso Actualizado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.fullscreenLoading = false;
                    this.$router.push('/permisos');
                });
            },

            validarRegistroPermiso(){
                this.error =0;
                this.mensajeError =[];
                let Datos = this.fillEditarPermiso;
                if(!Datos.cNombre){
                    this.mensajeError.push("El campo nombre es obligatorio");
                }
                if(!Datos.cUrl){
                    this.mensajeError.push("El campo Url/Amigable  es obligatorio");
                }
               
                if(this.mensajeError.length){
                    this.error =1;
                }
                return this.error;
            },

            getPermiso(Id){
                let url = "/permisos/get/"+this.$attrs.id;
                axios.get(url)
                .then((response) => {
                    let Permiso = response.data.permiso;
                    this.fillEditarPermiso.nIdPermiso = Permiso.id;
                    this.fillEditarPermiso.cNombre = Permiso.nombre;
                    this.fillEditarPermiso.cUrl = Permiso.slug;
                });
            },

            abrirModal(){
                this.modalShow = !this.modalShow;
                this.mensajeError = [];
            },

        },

        mounted() {
            //console.log(this.$attrs.id)
            this.getPermiso();
        },
    }
</script>

<style>

</style>
