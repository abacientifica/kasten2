<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Crear Plantilla</h1>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <div class="row">
                            <router-link class="btn btn-info btn-sm" :to="'/plantillas/clientes/index'">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </router-link>
                            <modal :titulo="'Ayudas Plantillas'" :iddoc="85" :url="'plantillas.crear'"></modal>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="form-group row border" >
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cliente : </label>
                                <span style="color:red" v-show="fillNuevaPlantilla.nIdTercero ==0">(Seleccione *)</span>
                                <v-select
                                    @search="selectTerceros"
                                    label="NombreCorto"
                                    :options="arrayTerceros"
                                    placeholder="Buscar Tercero..."
                                    @input="getDatosTercero"   
                                >
                                </v-select>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label >Dirección</label>
                                <span style="color:red" v-show="fillNuevaPlantilla.nIdDireccion ==null">(Seleccione *)</span>
                                <select class="form-control" v-model="fillNuevaPlantilla.nIdDireccion">
                                    <option value="0" selected>( Seleccione )</option>
                                    <option v-for="dir in arrayDirecciones" :key="dir.IdDireccion" :value="dir.IdDireccion" v-text="dir.NmDireccion+' ('+dir.Direccion+')'"></option>
                                </select>                                   
                            </div>
                        </div>

                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Entrega Propuesta</label><span style="color:red" v-show="fillNuevaPlantilla.FechaEntregaPropuesta ==null">(Seleccione *)</span>
                                <el-date-picker
                                    v-model="fillNuevaPlantilla.FechaEntregaPropuesta"
                                    type="datetime"
                                    placeholder="Seleccione la fecha y hora"
                                    value-format="yyyy-MM-dd HH:mm:ss">
                                </el-date-picker>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nombre Plantilla <span style="color:red" v-show="fillNuevaPlantilla.cNmPlantilla ==null">(Seleccione *)</span></label>
                                <input type="text" v-model="fillNuevaPlantilla.cNmPlantilla" class="form-control" placeholder="Nombre de la plantilla"> 
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Meses de Consumo <span style="color:red" v-show="fillNuevaPlantilla.nMesesConsumo ==null">(Seleccione *)</span></label>
                                <input type="number" v-model="fillNuevaPlantilla.nMesesConsumo" class="form-control" placeholder="# meses"> 
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Vigencia Oferta <span style="color:red" v-show="!fillNuevaPlantilla.oVigenciaOferta">(Seleccione *)</span></label>
                                <el-date-picker
                                    v-model="fillNuevaPlantilla.oVigenciaOferta"
                                    type="daterange"
                                    align="right"
                                    unlink-panels
                                    format="yyyy-MM-dd"
                                    range-separator="A"
                                    start-placeholder="Desde"
                                    end-placeholder="Hasta"
                                    value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </div>
                        </div>

                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Periodo Año</label><span style="color:red" v-show="fillNuevaPlantilla.dPeriodoAnio ==null">(Seleccione *)</span>
                                <select class="form-control" v-model="fillNuevaPlantilla.dPeriodoAnio">
                                    <option value="0">Seleccione</option>
                                    <option v-for="(anio,index) in PeriodoAnio" :key="index" :value="anio" v-text="anio"></option>
                                </select>            
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Periodo Mes</label><span style="color:red" v-show="fillNuevaPlantilla.dPeriodoMes ==null">(Seleccione *)</span>-   
                                <select class="form-control" v-model="fillNuevaPlantilla.dPeriodoMes">
                                    <option value="0">Seleccione</option>
                                    <option v-for="(mes,index) in PeriodoMes" :key="index" :value="mes" v-text="mes"></option>
                                </select>             
                            </div>
                        </div>

                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>Comentarios</label>
                                <textarea v-model="fillNuevaPlantilla.cComentarios" class="form-control">
                                </textarea>  
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
                                        <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in arrayMensajeError" :key="index" v-text="item"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" @click="abrirModal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12" v-if="EncabezadoVisible">
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary" @click="ValidarDatos">
                                    Continuar
                                </button>
                                <router-link class="btn btn-sm btn-warning" :to="'/pedidos/index'" style="float:right">
                                    Cancelar
                                </router-link>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
export default {
    components:{
        "v-select": vSelect
    },
    data() {
        return {
            arrayMensajeError:[],
            EncabezadoVisible:true,
            modalShow:false,
            fillNuevaPlantilla:{
                nIdTercero:0,
                nIdDireccion:null,
                FechaEntregaPropuesta:null,
                cNmPlantilla:'',
                nMesesConsumo:null,
                oVigenciaOferta:[],
                dPeriodoAnio:null,
                dPeriodoMes:null,
                cComentarios:null,
            },
            arrayTerceros:[],
            arrayDirecciones:[],
            dPeriodoAnio:[],
            dPeriodoMes:[],
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
        }
    },

    computed:{
        PeriodoAnio(){
            let Anios = [];
            let i = 2018;
            for(i;i<2028;i++){
                Anios.push(i);
            }
            return Anios;
        },

        PeriodoMes(){
            let Meses = [];
            let i = 1;
            for(i;i<13;i++){
                Meses.push(i);
            }
            return Meses;
        }
    },

    methods: {
        selectTerceros(search,loading){
            let me=this;
            loading(true);
            var url= '/terceros/lista';
            axios.get(url,{
                params:{
                    'filtro':search
                }
            }).then(function (response) {
                let respuesta = response.data;
                q: search;
                me.arrayTerceros = respuesta.terceros;
                loading(false);
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

        getDatosTercero(val1){
            let me = this;
            try{
                me.fillNuevaPlantilla.nIdTercero = val1.IdTercero;
                this.CargarDirecciones(me.fillNuevaPlantilla.nIdTercero);
            }
            catch(error){
                this.fillNuevaPlantilla.nIdTercero = 0;
            }
        },

        CargarDirecciones(IdTercero){
            let me = this;
            axios.get('/terceros/lista',{params:{
                'filtro':IdTercero
            }}).then(function (response) {
                var respuesta = response.data.terceros[0];
                me.arrayDirecciones = respuesta.direcciones;
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        abrirModal(){
            this.modalShow = !this.modalShow;
        },

        ValidarDatos(){
            let Datos = this.fillNuevaPlantilla;
            this.arrayMensajeError=[];
            if(Datos.nIdTercero==0){
                this.arrayMensajeError.push("Debes seleccionar un cliente ");
            }
            if(Datos.nIdDireccion==null){
                this.arrayMensajeError.push("Debes seleccionar una dirección");
            }
            if(Datos.FechaEntregaPropuesta==null){
                this.arrayMensajeError.push("Debes seleccionar una fecha de propuesta ");
            }
            if(Datos.cNmPlantilla==''){
                this.arrayMensajeError.push("Debes ingresar el nombre de la plantilla");
            }
            if(Datos.nMesesConsumo==null){
                this.arrayMensajeError.push("Debes ingresar los meses de consumo ");
            }
            if(Datos.oVigenciaOferta.length <=0){
                this.arrayMensajeError.push("Debes seleccionar la vigencia de la oferta");
            }
            if(Datos.dPeriodoAnio==null){
                this.arrayMensajeError.push("Debes seleccionar un año de periodo ");
            }
            if(Datos.dPeriodoMes==null){
                this.arrayMensajeError.push("Debes seleccionar un mes de periodo ");
            }
            if(Datos.cComentarios==null){
                this.arrayMensajeError.push("Debes ingresar un comentario/Observación ");
            }

            if(this.arrayMensajeError.length >0){
                this.abrirModal();
            }
            else{
                this.crearPlantilla();
            }
        },

        crearPlantilla(){
            let me = this;
            axios.post('/plantillas/clientes/nueva',{
                params:{
                    'fillNuevaPlantilla':this.fillNuevaPlantilla,
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
                me.$router.push('/plantillas/clientes/ver/'+respuesta.plantilla);
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
    },
    
}
</script>