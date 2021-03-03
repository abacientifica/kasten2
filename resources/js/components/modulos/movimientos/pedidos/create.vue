<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Crear Pedido</h1>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <router-link class="btn btn-info btn-sm" :to="'/pedidos/index'">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </router-link>
                    </div>
                </div>
                <div class="card-body">
                    <NuevoMovimiento :IdDoc="IdDocPed"></NuevoMovimiento>
                    <template v-if="DatosEncabezado">
                        <AgregarProductosPedido :IdTercero="fillNuevoMovimiento.nIdTercero" :IdDireccion="fillNuevoMovimiento.nIdDireccion"></AgregarProductosPedido>
                    </template>
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
                fillNuevoMovimiento:[],
                arraryDetallesMovimiento:[],
                DatosEncabezado :false,
                fillCrearPermiso: {
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
                abrirModal:false,
                error: 0,
                mensajeError: [],
                IdDocPed:8,
            }
        },
        computed: {
        },
        mounted() {
            EventBus.$on('fillNuevoMovimiento',data =>{
                this.fillNuevoMovimiento = data;
                this.abrirPanelProductos();
            });
            EventBus.$on('arraryDetallesMovimiento',data =>{
                this.arraryDetallesMovimiento = data;
                this.RegistrarMovimiento();
            });
            this.IdDocPed = this.$attrs.iddoc;
        },
        methods: {
            RegistrarMovimiento(){
                let me = this;
                axios.post('/movimiento/nuevo',{
                    params:{
                        'fillNuevoMovimiento':this.fillNuevoMovimiento,
                        'arraryDetallesMovimiento':this.arraryDetallesMovimiento
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: respuesta.msg,
                        showConfirmButton: false,
                        timer: 1300
                    });
                    me.$router.push('/pedidos/ver/'+respuesta.movimiento)
                })
                .catch(function (error) {
                    console.log(error);
                    if (error.response.data.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                    if(error.response.data.status == 500){
                        Swal.fire({
                            icon :'danger',
                            type :'danger',
                            title :'',
                            text:response.data.error
                        })
                    }
                });
            },

            abrirPanelProductos(){
                this.DatosEncabezado = true;
            }
        }
    }
</script>

<style>

</style>
