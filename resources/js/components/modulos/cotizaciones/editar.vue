<template>
    <div>
        <el-button type="primary"  round @click="modalEditar = true,CargarDatosInicio()" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Editar </el-button>
        <el-dialog :title="titulo" :visible.sync="modalEditar" :top="'4vh'" width="40%" append-to-body>
            <el-form :model="formEditCot" :rules="rules" ref="formEditCot" label-width="160px" class="demo-formEditCot">
                <el-form-item label="Cliente" prop="IdTercero" size="small">
                <el-select
                        v-model="formEditCot.IdTercero"
                        :multiple="false"
                        :clearable="true"
                        size="small"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Digite  un cliente"
                        :remote-method="buscarCliente"
                        :loading="selectTercero.loading"
                        @change="ListarDirecciones()">
                        <el-option
                            v-for="item in selectTercero.options"
                            :key="item.IdTercero"
                            :label="item.IdTercero +' - '+item.NombreCorto"
                            :value="item.IdTercero"
                            >
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Dirección" prop="IdDireccion" size="small">
                    <el-select v-model="formEditCot.IdDireccion"   :clearable="true" filterable placeholder="Seleccione">
                        <el-option
                            v-for="item in arrayDirecciones"
                            :key="item.IdDireccion"
                            :label="item.IdDireccion +' - '+item.NmDireccion"
                            :value="item.IdDireccion"
                            size="small">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Tipo" prop="IdTipo" size="small">
                    <el-select v-model="formEditCot.IdTipo" @change="ListarSubtipos(arrTiposCot,true)" placeholder="Seleccione">
                        <el-option
                            v-for="item in arrTiposCot"
                            :key="item.IdCotizacionTipo"
                            :label="item.NmCotizacionTipo"
                            :value="item.IdCotizacionTipo"
                            size="small">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Sub Tipo" prop="IdSubTipo" size="small">
                    <el-select v-model="formEditCot.IdSubTipo" placeholder="Seleccione">
                        <el-option
                            v-for="item in arrSubTipos"
                            :key="item.IdSubTipoCotizacion"
                            :label="item.NmSubTipoCotizacion"
                            :value="item.IdSubTipoCotizacion"
                            size="small">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Vigencia Cotización" prop="vigenciaCot" size="small">
                    <el-date-picker
                        class="form-control"
                        type="daterange"
                        align="right"
                        size="small"
                        unlink-panels
                        range-separator="A"
                        v-model="formEditCot.vigenciaCot"
                        start-placeholder="Desde"
                        end-placeholder="Hasta"
                        format="yyyy-MM-dd"
                        value-format="yyyy-MM-dd"
                        @change="getFechas()">
                    </el-date-picker>
                </el-form-item>

                <el-form-item label="Soporte"  prop="soporteCot" size="small">
                    <el-input v-model="formEditCot.soporteCot" size="small"></el-input>
                </el-form-item>
                <el-form-item label="Nombre Cotización"  prop="nombreCot" size="small">
                    <el-input v-model="formEditCot.nombreCot" size="small"></el-input>
                </el-form-item>
                <el-form-item label="Contacto Cotización"  prop="contactoCot" size="small">
                    <el-input v-model="formEditCot.contactoCot" size="small"></el-input>
                </el-form-item>

                <el-form-item label="Email"  prop="emailCot" size="small">
                    <el-input v-model="formEditCot.emailCot" size="small"></el-input>
                </el-form-item>

                <el-form-item label="Plazo"  size="small">
                    <el-input v-model="formEditCot.Plazo" size="small"></el-input>
                </el-form-item>

                <el-form-item label="Dcto. Fro"  size="small">
                    <el-input v-model="formEditCot.DctoFro" size="small"></el-input>
                </el-form-item>

                <el-form-item label="Días Dcto"  size="small">
                    <el-input v-model="formEditCot.DiasDcto" size="small"></el-input>
                </el-form-item>

                <el-form-item label="Pertenece CCto"  size="small">
                    <el-select v-model="formEditCot.PerteneceCCto">
                        <el-option label ="NO" :value="0"></el-option>
                        <el-option label ="SI" :value="1"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Tipo Precio"  size="small">
                    <el-select v-model="formEditCot.TpPrecio">
                        <el-option label ="SIN DEFINIR" :value="0"></el-option>
                        <el-option label ="PRECIO GENERAL" :value="1"></el-option>
                        <el-option label ="PRECIO ESPECIAL" :value="2"></el-option>
                        <el-option label ="PRECIO LICITACION" :value="3"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Requiere Gestión" prop="tpEntrega" size="small">
                    <el-radio-group v-model="formEditCot.RequiereGestion">
                        <el-radio label="Si" :value="1" size="small"></el-radio>
                        <el-radio label="No" :value="0" size="small"></el-radio>
                    </el-radio-group>
                    <el-date-picker
                        v-model="formEditCot.PlazoGestion"
                        type="date"
                        placeholder="Seleccione una fecha plazo">
                    </el-date-picker>
                    
                </el-form-item>

                <el-form-item label="Tiempo entrega" prop="tpEntrega" size="small">
                    <el-input type="number" v-model="formEditCot.tiempoEntrega" placeholder="Ingrese tiempo de entrega" size="small"></el-input>
                    <el-radio-group v-model="formEditCot.tpEntrega">
                        <el-radio label="Horas" value="horas" size="small"></el-radio>
                        <el-radio label="Días" value="dias" size="small"></el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="Comentarios Devoluciones " prop="comentariosDev" size="small">
                    <el-input type="textarea" v-model="formEditCot.comentarioDev"></el-input>
                </el-form-item>
                <el-form-item label="Comentarios Cotizacion " prop="comentariosCot" size="small">
                    <el-input type="textarea" v-model="formEditCot.comentarioCot"></el-input>
                </el-form-item>
                <el-form-item label="Asesor" prop="IdAsesor" size="small">
                    <el-select v-model="formEditCot.IdAsesor" filterable placeholder="Select">
                        <el-option
                            v-for="item in arrAsesores"
                            :key="item.IdAsesor"
                            :label="item.Nombre"
                            :value="item.IdAsesor"
                            size="small">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="ActualizarCotizacion('formEditCot')">Actualizar</el-button>
                    <el-button type="info" @click="modalEditar=false">Cerrar</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props:['titulo','cotizacion'],
    data() {
        return {
            modalEditar:false,
            formEditCot:{
                IdCotizacion:this.cotizacion.IdCotizacion,
                IdTercero:this.cotizacion.IdTerceroCotizacion,
                IdDireccion:this.cotizacion.IdDireccionCotizacion,
                IdTipo:this.cotizacion.IdCotizacionTipo,
                IdSubTipo:this.cotizacion.IdCotizacionSubTipo,
                vigenciaCot:[this.cotizacion.FechaDesde,this.cotizacion.FechaHasta],
                VigDesde:this.cotizacion.FechaDesde ,
                VigHasta:this.cotizacion.FechaHasta,
                soporteCot:this.cotizacion.Soporte,
                nombreCot:this.cotizacion.NmCotizacion,
                contactoCot:this.cotizacion.ContactoCotizacion,
                emailCot:this.cotizacion.EmailCotizacion,
                tiempoEntrega:this.cotizacion.TiempoEntrega,
                tpEntrega:this.cotizacion.TipoTiempoEntrega,
                comentarioDev:this.cotizacion.CondDevolucion,
                comentarioCot:this.cotizacion.Comentarios,
                IdAsesor:this.cotizacion.AsesorCotizacion,
                //Campos nuevos en editar
                Plazo:this.cotizacion.Plazo,
                DctoFro:this.cotizacion.DctoFin,
                DiasDcto:this.cotizacion.DiasDctoFin,
                PerteneceCCto:this.cotizacion.PerteneceContrato,
                TpPrecio:this.cotizacion.TpPrecio,
                RequiereGestion:this.cotizacion.RequiereGestion,
                PlazoGestion:this.cotizacion.PlazoGestion
            },
            rules: {
                IdTercero: [
                    { required: true, message: 'Seleccione un cliente', trigger: 'change'}
                ],
                IdDireccion: [
                    { required: true, message: 'Seleccione una dirección', trigger: 'change'}
                ],
                IdTipo: [
                    { required: true, message: 'Seleccione un tipo', trigger: 'blur'}
                ],
                IdSubTipo: [
                    { required: true, message: 'Seleccione un subtipo', trigger: 'blur'}
                ],
                vigenciaCot: [
                    { type: 'array', required: true, message: 'Selecione una vigencia', trigger: 'blur' }
                ],
                soporteCot: [
                    { required: true, message: 'Ingrese un soporte', trigger: 'change' }
                ],
                nombreCot: [
                    { required: true, message: 'Ingrese un nombre cotización', trigger: 'change' }
                ],
                contactoCot: [
                    { required: true, message: 'Ingrese un contacto cotización', trigger: 'change' }
                ],
                emailCot: [
                    { required: true, message: 'Ingrese un email cotización', trigger: 'change' },
                    { type: 'email', message: 'Ingrese un email valido.', trigger: ['blur', 'change'] }
                ],
                tiempoEntrega: [
                    { min: 1, max: 2, message: 'Digite 1 o 2 numeros maximo', trigger: 'change' },
                    { type:'number', required: true, message: 'Ingrese un email cotización', trigger: 'change' }
                ],
                comentarioDev: [
                    { required: true, message: 'Ingrese un comentario devolución', trigger: 'change' }
                ],
                comentarioCot: [
                    { required: true, message: 'Ingrese un comentario', trigger: 'change' }
                ],
                IdAsesor: [
                    { required: true, message: 'Seleccione un asesor', trigger: 'change' }
                ]
            },
            selectTercero:{
                options: [],
                value: [],
                list: [],
                loading: false,
                terceros:[],
            },
            arrayDirecciones:[],
            arrTiposCot:[],
            arrSubTipos:[],
            arrAsesores:[]
            
        }
    },
    computed:{

    },

    methods: {
        buscarCliente(search){
            if(search){
                this.selectTercero.loading = true;
                let me =this;
                var url= '/terceros/lista';
                axios.get(url,{
                    params:{
                        'filtro':search
                    }
                }).then(function (response) {
                    let respuesta = response.data;
                    me.selectTercero.options = respuesta.terceros;
                    me.selectTercero.loading = false;
                    me.ListarDirecciones();
                })
                .catch(function (error) {
                    console.log(error)
                    if (error.response.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                });
            }
        },

        ListarDirecciones(){
            let me = this;
            let IdTercero = this.formEditCot.IdTercero;
            if(IdTercero){
                axios.get('/terceros/lista',{params:{
                    'filtro':IdTercero
                }}).then(function (response) {
                    var respuesta = response.data.terceros[0];
                    me.arrayDirecciones = respuesta.direcciones.sort();
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

        getTiposCot(){
            let me = this;
            axios.get('/cotizaciones/getTiposCotizaciones').then(function (response) {
                var respuesta = response.data;
                me.arrTiposCot = respuesta.tipos_cot;
                me.ListarSubtipos(respuesta.tipos_cot);
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    me.fullscreenLoading = false;
                }
            });
        },

        ListarSubtipos(ArrTipos,Changed=false){
            this.arrSubTipos = [];
            let me = this;
            Changed ? this.formEditCot.IdSubTipo = '' : false;
            if(this.formEditCot.IdTipo){
                console.log("Entro")
                let Tipo = me.formEditCot.IdTipo;
                const SubTipos = ArrTipos.filter(function(e){
                    return e.IdCotizacionTipo == Tipo;
                });

                let arrSubTipos = SubTipos.map(function(e){
                    return e.tiposubtipo.map(function(y){
                        return y.subtipos[0];
                    })
                });
                this.arrSubTipos = arrSubTipos = arrSubTipos ?  arrSubTipos[0]:[];
            }
        },

        listarAsesores(){
            let url ="/asesores/lista";
            let me = this;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrAsesores = respuesta.asesores;
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    me.fullscreenLoading = false;
                }
            });
        },

        getFechas(){
            if(this.formEditCot.vigenciaCot){
                this.formEditCot.VigDesde = this.formEditCot.vigenciaCot[0]
                this.formEditCot.VigHasta = this.formEditCot.vigenciaCot[1]
            }
            else{
                this.formEditCot.VigDesde = ''
                this.formEditCot.VigHasta = ''
            }
            console.log(this.formEditCot.vigenciaCot)
            return true;
        },

        ActualizarCotizacion(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                let me = this;
                const loader = me.loaderk();
                axios.put('/cotizaciones/actualizar',{params:{
                    'formEditCot' : me.formEditCot
                }}).then(response=>{
                    loader.close();
                    let respuesta = response.data;
                    EventBus.$emit('CotizacionEdit',respuesta.Cotizacion);
                    me.limpiarForm();
                    me.AlertMensaje(respuesta.msg,1);
                    me.modalEditar = false;

                }).catch(error=>{
                    loader.close();
                    console.log(error);
                    me.AlertMensaje(respuesta.msg,3);
                })
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },

        limpiarForm() {
            let formName ='formEditCot';
            this.$refs[formName].resetFields();
        },

        AlertMensaje(Msg,Tipo = ''){
            let tipom = "";
            if(Tipo ==1){
                tipom = 'success';
            }
            else if(Tipo == 2){
                tipom = 'warn';
            }
            else if(Tipo == 3){
                tipom = 'danger';
            }
            let position = 'top-center'
            let color = tipom
            const noti = this.$vs.notification({
                duration:12000,
                progress: 'auto',
                flat: true,
                color,
                position,
                title:null,
                text: Msg
            })
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
            });
        },

        CargarDatosInicio(){
            this.getTiposCot();
            this.listarAsesores();
            this.buscarCliente(this.cotizacion.IdTerceroCotizacion);
        }
    },

    mounted() {
        
    },
}
</script>