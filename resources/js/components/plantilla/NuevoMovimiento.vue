<template>
    <div class="container-fluid">
        <div class="form-group row border" v-if="arrayCampos.length > 0">
            <template v-if="ValidarCampoVisible('IdTercero') !='' && usuario.Tipo != 2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ValidarCampoVisible('IdTercero')}} </label>
                        <span style="color:red" v-show="fillNuevoMovimiento.nIdTercero ==0">(Seleccione *)</span>
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
            </template>
            
            <template v-if="ValidarCampoVisible('IdDireccion') !=''">
                <div class="col-md-3" >
                    <div class="form-group">
                        <label >{{ValidarCampoVisible('IdDireccion')}}</label>
                        <span style="color:red" v-show="fillNuevoMovimiento.nIdDireccion ==0">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdDireccion" :disabled="usuario.IdDireccion >0 ? true:false">
                            <option value="0" selected>( Seleccione )</option>
                            <option v-for="dir in arrayDirecciones" :key="dir.IdDireccion" :value="dir.IdDireccion" v-text="dir.NmDireccion+' ('+dir.Direccion+')'"></option>
                        </select>                                   
                    </div>
                </div>
            </template>
            
            <template v-if="ValidarCampoVisible('IdTercero2') !=''">
                <div class="col-md-3" >
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('IdTercero2')"> </label>
                        <span style="color:red" v-show="fillNuevoMovimiento.nIdTercero2 ==0">(Seleccione *)</span>
                        <v-select
                            @search="selectTerceros2"
                            label="NombreCorto"
                            :options="arrayTerceros2"
                            placeholder="Buscar Tercero..."
                            @input="getDatosTercero2"                                
                        >
                        </v-select>
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('Fecha1') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('Fecha1')"></label><span style="color:red" v-show="fillNuevoMovimiento.dFecha1 ==''">(Seleccione *)</span>
                        <input type="date"  class="form-control" v-model="fillNuevoMovimiento.dFecha1"/>      
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('Fecha2') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('Fecha2')"></label><span style="color:red" v-show="fillNuevoMovimiento.dFecha2 ==''">(Seleccione *)</span>
                        <input type="date"  class="form-control" v-model="fillNuevoMovimiento.dFecha2"/>  
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('Soporte') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('Soporte')"></label>
                        <input type="text" v-model="fillNuevoMovimiento.cSoporte" class="form-control" placeholder=" # Orden"> 
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('IdFormaPago') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('IdFormaPago')"></label><span style="color:red" v-show="fillNuevoMovimiento.nIdFormaPago ==0">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdFormaPago" :disabled="usuario.Tipo != 2 ? false:true">
                            <option value="0">Seleccione</option>
                            <option v-for="fpago in arrFormasPago" :key="fpago.IdFormaPago" :value="fpago.IdFormaPago" v-text="fpago.FormaPago"></option>
                        </select>              
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('IdCondEntrega') !=''">
                <div class="col-md-3" >
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('IdCondEntrega')"></label><span style="color:red" v-show="fillNuevoMovimiento.nIdCondicionEntrega ==''">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdCondicionEntrega" >
                            <option value="0">SIN DEFINIR</option>
                            <option value="1">VENTANILLA</option>
                            <option value="2">DOMICILIO CLIENTE</option>
                        </select>            
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('Prioridad') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('Prioridad')"></label><span style="color:red" v-show="fillNuevoMovimiento.nIdPrioridad ==''">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdPrioridad">
                            <option value="0">Seleccione</option>
                            <option value="1">ALTA</option>
                            <option value="2">NORMAL</option>
                            <option value="3">MEDIA</option>
                            <option value="4">BAJA</option>
                        </select>              
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('IdAsesor') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('IdAsesor')"></label><span style="color:red" v-show="fillNuevoMovimiento.nIdAsesor ==''">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdAsesor" :disabled="usuario.Tipo != 2 ? false:true">
                            <option value="0">Seleccione</option>
                            <option v-for="asesoraba in arrayAsesores" :key="asesoraba.IdAsesor" :value="asesoraba.IdAsesor" v-text="asesoraba.Nombre" ></option>
                        </select>              
                    </div>
                </div>
            </template>

            <template  v-if="ValidarCampoVisible('IdConcepto') !=''">
                <div class="col-md-3">
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('IdConcepto')"></label><span style="color:red" v-show="fillNuevoMovimiento.nIdConcepto ==''">(Seleccione *)</span>
                        <select class="form-control" v-model="fillNuevoMovimiento.nIdConcepto">
                            <option v-for="concepto in arrayConceptos" :key="concepto.IdConcepto" :value="concepto.IdConcepto" v-text="concepto.NmConcepto" ></option>
                        </select>              
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('VrFletes') !=''">
                <div class="col-md-3" >
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('VrFletes')"></label>
                        <input type="text" v-model="fillNuevoMovimiento.nFlete" class="form-control" placeholder=""> 
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('Comentarios') !=''">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('Comentarios')"></label>
                        <textarea v-model="fillNuevoMovimiento.cComentarios" class="form-control">
                        </textarea>  
                    </div>
                </div>
            </template>

            <template v-if="ValidarCampoVisible('ComentariosInternos') !=''">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label v-text="ValidarCampoVisible('ComentariosInternos')"></label>
                        <textarea v-model="fillNuevoMovimiento.cComentariosInt" class="form-control">
                        </textarea>  
                    </div>
                </div>
            </template>
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
        
        <div v-else>
            <div class="callout callout-info">No se encontro una configuración para el documento</div>
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
    props: ['IdDoc'],
    data(){
        return{
            fillNuevoMovimiento:{
                nIdTercero:0,
                nIdDireccion:0,
                nIdDocumento:0,
                nIdTercero2:0,
                dFecha1:'',
                dFecha2:'',
                cSoporte:'',
                nIdFormaPago:0,
                nIdCondicionEntrega:0,
                nIdPrioridad:0,
                nIdAsesor:0,
                nIdConcepto:0,
                nFlete:0,
                cComentarios:'',
                cComentariosInt:''
            },
            EncabezadoVisible:true,
            arrayTerceros:[],
            arrayTerceros2:[],
            arrayDirecciones:[],
            arrayAsesores:[],
            arrayConceptos:[],
            arrayCampos:[],
            arrFormasPago:[],
            NmAliasCampo:'',
            arrayMensajeError:[],
            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
            usuario:[],
            tercero:[]
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

        selectTerceros2(search,loading){
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
                me.arrayTerceros2 = respuesta.terceros;
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
                me.fillNuevoMovimiento.nIdTercero = val1.IdTercero;
                me.arrayDirecciones = val1.direcciones;
                me.fillNuevoMovimiento.nIdAsesor = val1.IdAsesor;
                me.CargarAsesores();
            }
            catch(error){
                this.fillNuevoMovimiento.nIdTercero = 0;
                me.fillNuevoMovimiento.nIdAsesor = 0;
                me.arrayDirecciones = [];
            }
        },

        getDatosTercero2(val1){
            let me = this;
            try{
                me.fillNuevoMovimiento.nIdTercero2 = val1.IdTercero;
            }
            catch(error){
                me.fillNuevoMovimiento.nIdTercero2 = 0;
            }
        },

        CargarAsesores(){
            let me = this;
            axios.get('/asesores/lista').then(function (response) {
                //Asi le asignamos al array categoria los datos de la respuesta
                var respuesta = response.data;
                me.arrayAsesores = respuesta.asesores;
            })
            .catch(function (error) {
                // handle error
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        CargarConceptosDoc(){
            let me = this;
            axios.get('/conceptos/lista/'+this.IdDoc).then(function (response) {
                //Asi le asignamos al array categoria los datos de la respuesta
                var respuesta = response.data;
                me.arrayConceptos = respuesta.conceptos;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },

        CargarCamposDocumento(IdDoc){
            let me = this;
            if(IdDoc>0){
                var url = '/documentos/campos';
                var arrayDatos =[];
                axios.get(url,{
                    params:{
                        'IdDoc':IdDoc,
                        "Exist":true
                    }
                }).then(function (response) {
                    var respuesta = response.data.campos;
                    respuesta.map(function(x,y){
                        me.arrayCampos.push({'Campo': x.IdCampo,'Alias': x.Existe});
                    })
                    
                    /*let i =0;
                    for(i; i< me.arrayCampos.length;i++){
                        me.campos.push({'nombre': me.arrayCampos[i].IdCampo,'alias': me.arrayCampos[i].Existe})
                    }*/
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
                me.arrayCampos = arrayDatos;
            }
        },

        CargarFormasPago(){
            let me = this;
            axios.get('/formaspago/lista').then(function (response) {
                var respuesta = response.data;
                me.arrFormasPago = respuesta.formas_pago;
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });

        },

        CargarAsesores(){
            let me = this;
            axios.get('/asesores/lista').then(function (response) {
                var respuesta = response.data;
                me.arrayAsesores = respuesta.asesores;
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

        ValidarDatos(){
            var datos = this.fillNuevoMovimiento;
            let NmAliasCampo ='';
            NmAliasCampo  = this.ValidarCampoVisible('IdTercero');
            if(NmAliasCampo !='' && datos.nIdTercero <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('IdDireccion');
            if(NmAliasCampo !='' && datos.IdDireccion <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('IdTercero2');
            if(NmAliasCampo !='' && datos.nIdTercero2 <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('Fecha1');
            if(NmAliasCampo !='' && datos.dFecha1 ==''){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('Fecha2');
            if(NmAliasCampo !='' && datos.dFecha2 ==''){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }

            NmAliasCampo  = this.ValidarCampoVisible('IdFormaPago');
            if(NmAliasCampo !='' && datos.nIdFormaPago <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es obligatorio")
            }

            NmAliasCampo  = this.ValidarCampoVisible('IdCondEntrega');
            if(NmAliasCampo !='' && datos.nIdCondEntrega <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('Prioridad');
            if(NmAliasCampo !='' && datos.nIdPrioridad <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('Asesor');
            if(NmAliasCampo !='' && datos.nIdAsesor <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            NmAliasCampo  = this.ValidarCampoVisible('IdConcepto');
            if(NmAliasCampo !='' && datos.nIdConcepto <=0){
                this.arrayMensajeError.push("El campo "+NmAliasCampo+" es Obligatorio")
            }
            if(this.arrayMensajeError.length >0 ){
                this.modalShow = true;
            }
            else{
                this.EncabezadoVisible = false;
                this.EnviarDatosMovimiento();
            }
        },

        EnviarDatosMovimiento(){
            //Este evento lo escuchara el componente create 
            EventBus.$emit('fillNuevoMovimiento',this.fillNuevoMovimiento);
            console.log("Se emitio el evento encabezado");
        },

        abrirModal(){
            this.modalShow = !this.modalShow;
            this.arrayMensajeError = [];
        },

        ValidarCampoVisible(Campo){
            var Alias ='';
            this.arrayCampos.map(function(x,y){
                if(x.Campo === Campo){
                    Alias = x.Alias;
                }
            })
            return Alias;
        },

        CargarDirecciones(IdTercero){
            let me = this;
            axios.get('/terceros/lista',{params:{
                'filtro':IdTercero
            }}).then(function (response) {
                var respuesta = response.data.terceros[0];
                me.tercero = response.data.terceros[0];
                me.fillNuevoMovimiento.nIdAsesor = respuesta.IdAsesor;
                me.fillNuevoMovimiento.nIdFormaPago = respuesta.IdFormaPago;
                me.fillNuevoMovimiento.nIdCondicionEntrega = 2;
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
        }

    },

    mounted() {
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
        if(this.usuario.Tipo ==2){
            this.fillNuevoMovimiento.nIdTercero = this.usuario.IdTercero;
            this.CargarDirecciones(this.usuario.IdTercero);
            console.log(this.usuario.IdDireccion)
            if(this.usuario.IdDireccion >0){
                this.fillNuevoMovimiento.nIdDireccion = this.usuario.IdDireccion;
            }
        }
        this.fillNuevoMovimiento.nIdDocumento = this.IdDoc;
        this.CargarCamposDocumento(this.fillNuevoMovimiento.nIdDocumento);
        this.CargarConceptosDoc(this.IdDoc);
        this.CargarFormasPago();
        this.CargarAsesores();
        
    },
}
</script>