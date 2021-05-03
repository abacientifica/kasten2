<template v-else-if="listado == 0">
        <div class="form-group row border">
            <div class="col-md-3" v-if="usuario.Tipo != 2 && (Editar == false || Editar == undefined)">
                <div class="form-group">
                    <label>Tercero </label>
                   <!--  :change="setVariables('idtercero',idtercero)"     -->
                    <span style="color:red" v-show="idtercero ==0">(Seleccione *)</span>
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

            <div class="col-md-3" v-if="ValidarDato('IdDireccion') != false">
                <div class="form-group">
                    <label >Direccion</label>
                    <span style="color:red" v-show="id_direccion ==0">(Seleccione *)</span>
                    <select class="form-control" v-model="id_direccion">
                        <option value="0" selected>( Seleccione )</option>
                        <option v-for="dir in arrayDirecciones" :key="dir.IdDireccion" :value="dir.IdDireccion" v-text="dir.NmDireccion"></option>
                    </select>                                   
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('IdTercero2') !=false">
                <div class="form-group">
                    <label v-text="ValidarDato('IdTercero2')"> </label>
                    <span style="color:red" v-show="idtercero2 ==0">(Seleccione *)</span>
                    <v-select
                        @search="selectTerceros2"
                        label="NombreCorto"
                        :options="arrayTerceros2"
                        placeholder="Buscar Tercero..."
                        @input="getDatosTercero2"  
                        v-model="idtercero2"                                
                    >
                    </v-select>
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('Fecha1') !=false">
                <div class="form-group">
                    <label>Fecha Minima Entrega</label><span style="color:red" v-show="fecha_minima ==''">(Seleccione *)</span>
                    <input type="date"  class="form-control" v-model="fecha_minima"/>      
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('Fecha2') !=false">
                <div class="form-group">
                    <label >Fecha Maxima Entrega</label><span style="color:red" v-show="fecha_maxima ==''">(Seleccione *)</span>
                    <input type="date"  class="form-control" v-model="fecha_maxima"/>  
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('Soporte') !=false">
                <div class="form-group">
                    <label >Soporte</label>
                    <input type="text" v-model="num_orden" class="form-control" placeholder=" # Orden"> 
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('IdCondEntrega') !=false">
                <div class="form-group">
                    <label >Condicion Entrega</label><span style="color:red" v-show="condicion_entrega ==''">(Seleccione *)</span>
                    <select class="form-control" v-model="condicion_entrega">
                        <option value="0">SIN DEFINIR</option>
                        <option value="1">VENTANILLA</option>
                        <option value="2">DOMICILIO CLIENTE</option>
                    </select>            
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('Prioridad') !=false">
                <div class="form-group">
                    <label >Prioridad</label><span style="color:red" v-show="prioridad ==''">(Seleccione *)</span>
                    <select class="form-control" v-model="prioridad">
                        <option value="0">ALTA</option>
                        <option value="1">NORMAL</option>
                        <option value="2">MEDIA</option>
                        <option value="3">BAJA</option>
                    </select>              
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('IdAsesor') !=false">
                <div class="form-group">
                    <label >Asesor</label><span style="color:red" v-show="asesor ==''">(Seleccione *)</span>
                    <select class="form-control" v-model="asesor">
                        <option v-for="asesoraba in arrayAsesores" :key="asesoraba.IdAsesor" :value="asesoraba.IdAsesor" v-text="asesoraba.Nombre" ></option>
                    </select>              
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('IdConcepto') !=false">
                <div class="form-group">
                    <label >Conceptos</label><span style="color:red" v-show="idconcepto ==''">(Seleccione *)</span>
                    <select class="form-control" v-model="idconcepto">
                        <option v-for="concepto in arrayConceptos" :key="concepto.IdConcepto" :value="concepto.IdConcepto" v-text="concepto.NmConcepto" ></option>
                    </select>              
                </div>
            </div>

            <div class="col-md-3" v-if="ValidarDato('VrFletes') !=false">
                <div class="form-group">
                    <label >Fletes</label>
                    <input type="text" v-model="flete" class="form-control" placeholder=""> 
                </div>
            </div>

            <div class="col-md-6" v-if="ValidarDato('Comentarios') !=false">
                <div class="form-group">
                    <label >Comentarios</label>
                    <textarea v-model="comentarios" class="form-control">
                    </textarea>  
                </div>
            </div>

            <div class="col-md-6" v-if="ValidarDato('ComentariosInternos') !=false">
                <div class="form-group">
                    <label >Comentarios Internos</label>
                    <textarea v-model="comentarios_internos" class="form-control">
                    </textarea>  
                </div>
            </div>

            <div class="col-md-12">
                <div v-show="errorIngreso" class="form-group">
                    <div class="text-center col-md-12 alert alert-danger">
                        <ul v-for="error in errorMsgIngreso" :key="error">
                            <li v-text="error"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
import {mapState, mapActions} from 'vuex'
export default {
    props:{
        IdDocSeleccionado : 0,
        Editar:false,
        IdMovimiento:0

    },
    data() {
        return {
            //Almacena los campos que puede diligenciar en un documento
            arrayCampos:[],
            
            
            arrayTerceros :[],
            arrayTerceros2 :[],
            arrayCondicionEntrega:[],
            arrayDirecciones: [],
            arrayAsesores:[],
            arrayConceptos:[],

            errorIngreso:0,//Valida si al crear una roles los formularios le falta un dato
            errorMsgIngreso:[],//Validamos el campo categoria    

         /* Comentarios:false,
            ComentariosInternos:false,
            Fecha1:false,
            Fecha2:false,
            Fecha3:false,
            IdAsesor:false,
            IdConcepto:false,
            IdContrato:false,
            IdDireccion:false,
            IdFormaPago:false,
            IdTercero:false,
            IdTercero2:false,
            Prioridad:false,
            Soporte:false,
            VrFletes:false, */
            campos:[],

        }
    },

    computed: {
        //Se debe crear el get y set para trabajar con v-model de lo contrario no lo permite
        id_direccion:{
            get(){ 
                return this.$store.state.NuevoMovimiento.id_direccion
            },
            set(value){
                this.SET_VARIABLES({'id_direccion':value})
            }
        },

        idtercero:{
            get(){ 
                return this.$store.state.NuevoMovimiento.idtercero
            },
            set(value){
                this.SET_VARIABLES({'idtercero':value})
            }
        },

        idtercero2:{
            get(){ 
                return this.$store.state.NuevoMovimiento.idtercero2
            },
            set(value){
                this.SET_VARIABLES({'idtercero2':value})
            }
        },

        fecha_minima:{
            get(){ 
                return this.$store.state.NuevoMovimiento.fecha_minima
            },
            set(value){
                this.SET_VARIABLES({'fecha_minima':value})
            }
        },

        fecha_maxima:{
            get(){ 
                return this.$store.state.NuevoMovimiento.fecha_maxima
            },
            set(value){
                this.SET_VARIABLES({'fecha_maxima':value})
            }
        },

        num_orden:{
            get(){ 
                return this.$store.state.NuevoMovimiento.num_orden
            },
            set(value){
                this.SET_VARIABLES({'num_orden':value})
            }
        },

        condicion_entrega:{
            get(){ 
                return this.$store.state.NuevoMovimiento.condicion_entrega
            },
            set(value){
                this.SET_VARIABLES({'condicion_entrega':value})
            }
        },

        prioridad:{
            get(){ 
                return this.$store.state.NuevoMovimiento.prioridad
            },
            set(value){
                this.SET_VARIABLES({'prioridad':value})
            } 
        },

        asesor:{
            get(){ 
                return this.$store.state.NuevoMovimiento.asesor
            },
            set(value){
                this.SET_VARIABLES({'asesor':value})
            } 
        },

        idconcepto:{
            get(){ 
                return this.$store.state.NuevoMovimiento.idconcepto
            },
            set(value){
                this.SET_VARIABLES({'idconcepto':value})
            } 
        },

        comentarios:{
            get(){ 
                return this.$store.state.NuevoMovimiento.comentarios
            },
            set(value){
                this.SET_VARIABLES({'comentarios':value})
            } 
        },

        comentarios_internos:{
            get(){ 
                return this.$store.state.NuevoMovimiento.comentarios_internos
            },
            set(value){
                this.SET_VARIABLES({'comentarios_internos':value})
            } 
        },

        usuario:{
            get(){ 
                return this.$store.state.NuevoMovimiento.comentarios_internos
            },
        },

    }, 

    methods: {
        ocultarDetalle(){
            this.listado = 1;
            this.arrayDetalle =[];
            this.idmovimiento= 0;
            this.nro_documento=0;
            this.idtercero=0;
            this.nombre = '';
            this.fecha = '';
            this.fecha1 = '';
            this.fecha2 = '';
            this.condicion_entrega ='2';
            this.estado='';
            this.soporte='';
            this.id_direccion='0';
            this.direccion='';
            this.asesor='';
            this.forma_pago='';
            this.total_iva=0;
            this.sub_total=0;
            this.total=0;
            this.comentarios='';
            this.prioridad='1';
            this.fecha_minima='';
            this.fecha_maxima='';
            this.num_orden='';
            this.arrayDetalle =[];
            this.paginacionArt=0;
            this.impuesto=0;
        },

        selectTerceros(search,loading){
            let me=this;
            loading(true);
            var url= '/terceros/ObtenerTerceros?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search;
                me.arrayTerceros = respuesta.terceros;
                loading(false);
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        getDatosTercero(val1){
            let me = this;
            try{
                me.loading = true;
                //me.idtercero = val1.IdTercero;
                me.SET_VARIABLES({'idtercero':val1.IdTercero});
                me.obtenerDirecciones();
                me.CargarAsesores();
            }
            catch(error){
                me.idtercero = 0;
                me.obtenerDirecciones();
            }
        },

        selectTerceros2(search,loading){
            let me=this;
            loading(true);
            var url= '/terceros/ObtenerTerceros?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search;
                me.arrayTerceros2 = respuesta.terceros;
                loading(false);
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        getDatosTercero2(val1){
            let me = this;
            try{
                me.loading = true;
                me.SET_VARIABLES({'idtercero2':val1.IdTercero});
                //me.idtercero2 = val1.IdTercero;
            }
            catch(error){
                //me.nuevomov[0].idtercero2 = null;
            }
        },

        obtenerDirecciones(){
            let me = this;
            if(me.idtercero !=''){
                var url = '/direcciones/tercero/'+me.idtercero;
            }
            else{
                var url = '/direcciones/tercero/'+me.idtercero;
            }
            axios.get(url).then(function (response) {
                //Asi le asignamos al array categoria los datos de la respuesta
                var respuesta = response.data;
                me.arrayDirecciones = respuesta.direcciones;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });
        },

        registrarIngreso(){//Registra un nuevo pedido
                if(this.ValidarIngreso()){
                    return false;
                } 
                Swal.fire({
                    title: 'Estas seguro(a) de Autorizar el pedido ?',
                    text: "Si lo autorizas no podras modificar ningún dato de los ingresados.",
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-warning'
                    },
                    buttonsStyling: false,
                    cancelButtonText: 'Validar',
                    confirmButtonText: 'Registrar !',
                    showLoaderOnConfirm: true,
                    }).then((result) => {
                        if (result.value) {
                            this.isLoading = true;
                            let me =this;
                            axios.post('/pedido/registrar', {
                                'idtercero':this.idtercero,
                                'fecha_minima': this.fecha_minima,
                                'fecha_maxima': this.fecha_maxima,
                                'id_direccion': this.id_direccion,
                                'condicion_entrega': this.condicion_entrega,
                                'prioridad':this.prioridad,
                                'comentarios':this.comentarios,
                                'sub_total':this.total,
                                'total':this.TotalNeto,
                                'total_iva':this.impuesto,
                                'data':this.arrayDetalle,
                            })
                            .then(function (response) {
                                var IdMovRegistrado = response.data.movimiento;
                                me.idmovimiento= 0;
                                me.nro_documento=0;
                                me.idtercero=0;
                                me.nombre = '';
                                me.fecha = '';
                                me.fecha1 = '';
                                me.fecha2 = '';
                                me.condicion_entrega ='2';
                                me.estado='';
                                me.soporte='';
                                me.id_direccion='0';
                                me.direccion='';
                                me.asesor='';
                                me.forma_pago='';
                                me.total_iva=0;
                                me.sub_total=0;
                                me.total=0;
                                me.comentarios='';
                                me.prioridad='1';
                                me.fecha_minima='';
                                me.fecha_maxima='';
                                me.num_orden='';
                                me.arrayDetalle =[];


                                me.CerrarModal();
                                me.verPedido(IdMovRegistrado);
                                me.isLoading = false;
                            })
                            .catch(function (error) {
                                me.isLoading = false;
                                //console.log(error);
                                console.log(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Ocurrio un error al crear el pedido!'
                                })
                                console.log(error);
                            });
                        }
                    })
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
                console.log(error);
            });
        },

        CargarConceptosDoc(){
            let me = this;
            axios.get('/conceptos/lista/'+this.IdDocSeleccionado).then(function (response) {
                //Asi le asignamos al array categoria los datos de la respuesta
                var respuesta = response.data;
                me.arrayConceptos = respuesta.conceptos;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });
        },

        CargarCamposDocumento(){
            let me = this;
            if(me.IdDocSeleccionado >0){
                var url = '/documentos/campos?IdDoc='+me.IdDocSeleccionado+"&Exist=true";
                var arrayDatos =[];
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayCampos = respuesta.campos;
                    let i =0;
                    for(i; i< me.arrayCampos.length;i++){
                        me.campos.push({'nombre': me.arrayCampos[i].IdCampo,'alias': me.arrayCampos[i].Existe})
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
                me.arrayCampos = arrayDatos;
            }
        },

        ValidarDato: function(Dato){
            let filtro = this.campos.find(element=>element.nombre ==Dato)
            //console.log(filtro)
            if(filtro != undefined){
                console.log(filtro.alias)
                return filtro.alias
            }
            else{
                return false;
            }
        },
        ...mapActions('NuevoMovimiento',['SET_VARIABLES']),

        cargarDatosMovimiento(){
            
        }

    },

    
    mounted() {
        this.CargarConceptosDoc();
        this.CargarCamposDocumento();
        if(this.Editar == true && this.IdMovimiento >0){
            this.obtenerDirecciones();
            this.CargarAsesores();
            this.cargarDatosMovimiento();
        }
    },

}
</script>