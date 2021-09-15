<template>
    <div>
        <a @click="outerVisible = true,getTiposCot(),listarAsesores();" class="btn btn-info btn-sm"><i class="fas fa-plus-square"></i> Nueva </a>
        <el-dialog :title="titulo" :visible.sync="outerVisible" :top="'4vh'" width="40%">
            <el-form :model="formNewCot" :rules="rules" ref="formNewCot" label-width="160px" class="demo-formNewCot">
                <el-form-item label="Cliente" prop="IdTercero">
                <el-select
                        v-model="formNewCot.IdTercero"
                        :multiple="false"
                        :clearable="true"
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
                <el-form-item label="Dirección" prop="IdDireccion">
                    <el-select v-model="formNewCot.IdDireccion" :clearable="true" filterable placeholder="Seleccione">
                        <el-option
                            v-for="item in arrayDirecciones"
                            :key="item.IdDireccion"
                            :label="item.IdDireccion +' - '+item.NmDireccion"
                            :value="item.IdDireccion">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Tipo" prop="IdTipo">
                    <el-select v-model="formNewCot.IdTipo" @change="ListarSubtipos()" placeholder="Seleccione">
                        <el-option
                            v-for="item in arrTiposCot"
                            :key="item.IdCotizacionTipo"
                            :label="item.NmCotizacionTipo"
                            :value="item.IdCotizacionTipo">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Sub Tipo" prop="IdSubTipo">
                    <el-select v-model="formNewCot.IdSubTipo" placeholder="Seleccione">
                        <el-option
                            v-for="item in arrSubTipos"
                            :key="item.IdSubTipoCotizacion"
                            :label="item.NmSubTipoCotizacion"
                            :value="item.IdSubTipoCotizacion">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Vigencia Cotización" prop="vigenciaCot">
                    <el-date-picker
                        class="form-control"
                        type="daterange"
                        align="right"
                        size="small"
                        unlink-panels
                        range-separator="A"
                        v-model="formNewCot.vigenciaCot"
                        start-placeholder="Desde"
                        end-placeholder="Hasta"
                        format="yyyy-MM-dd"
                        value-format="yyyy-MM-dd"
                        @change="getFechas()">
                    </el-date-picker>
                </el-form-item>

                <el-form-item label="Soporte"  prop="soporteCot">
                    <el-input v-model="formNewCot.soporteCot"></el-input>
                </el-form-item>
                <el-form-item label="Nombre Cotización"  prop="nombreCot">
                    <el-input v-model="formNewCot.nombreCot"></el-input>
                </el-form-item>
                <el-form-item label="Contacto Cotización"  prop="contactoCot">
                    <el-input v-model="formNewCot.contactoCot"></el-input>
                </el-form-item>
                <el-form-item label="Email"  prop="emailCot">
                    <el-input v-model="formNewCot.emailCot"></el-input>
                </el-form-item>

                <el-form-item label="Tiempo entrega" prop="tpEntrega">
                    <el-input type="number" v-model="formNewCot.tiempoEntrega" placeholder="Ingrese tiempo de entrega"></el-input>
                    <el-radio-group v-model="formNewCot.tpEntrega">
                        <el-radio label="Horas" value="horas"></el-radio>
                        <el-radio label="Días" value="dias"></el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="Comentarios Devoluciones " prop="comentariosDev">
                    <el-input type="textarea" v-model="formNewCot.comentarioDev"></el-input>
                </el-form-item>
                <el-form-item label="Comentarios Cotizacion " prop="comentariosCot">
                    <el-input type="textarea" v-model="formNewCot.comentarioCot"></el-input>
                </el-form-item>
                <el-form-item label="Asesor" prop="IdAsesor">
                    <el-select v-model="formNewCot.IdAsesor" filterable placeholder="Select">
                        <el-option
                            v-for="item in arrAsesores"
                            :key="item.IdAsesor"
                            :label="item.Nombre"
                            :value="item.IdAsesor">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="CrearCotizacion('formNewCot')">Crear Cotizacion</el-button>
                    <el-button type="info" @click="outerVisible=false">Cerrar</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props:['titulo'],
    data() {
        return {
            outerVisible:false,
            formNewCot:{
                IdTercero:'',
                IdDireccion:'',
                IdTipo:'',
                IdSubTipo:'',
                vigenciaCot:null,
                VigDesde:this.vigenciaCot ? this.vigenciaCot[0]:'' ,
                VigHasta:this.vigenciaCot ? this.vigenciaCot[1]:'',
                soporteCot:'',
                nombreCot:'',
                contactoCot:'',
                emailCot:'',
                tiempoEntrega:'',
                tpEntrega:'',
                comentarioDev:'No se aceptan devoluciones por baja rotacion',
                comentarioCot:'La cantidad cotizada esta ajustada a la unidad minima de venta',
                IdAsesor:''
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
        CargarDatos(){
            if(this.outerVisible){
                this.getTiposCot();
                this.listarAsesores();
            }
            return true;
        },

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
                    console.log(respuesta.terceros)
                    me.selectTercero.loading = false;
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
            let IdTercero = this.formNewCot.IdTercero;
            if(IdTercero){
                axios.get('/terceros/lista',{params:{
                    'filtro':IdTercero
                }}).then(function (response) {
                    var respuesta = response.data.terceros[0];
                    me.arrayDirecciones = respuesta.direcciones.sort();
                    me.formNewCot.IdAsesor = respuesta.asesor.IdAsesor;
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

        ListarSubtipos(){
            this.arrSubTipos = []
            if(this.formNewCot.IdTipo){
                let Tipo = this.formNewCot.IdTipo;
                const SubTipos = this.arrTiposCot.filter(function(e){
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
            if(this.formNewCot.vigenciaCot){
                this.formNewCot.VigDesde = this.formNewCot.vigenciaCot[0]
                this.formNewCot.VigHasta = this.formNewCot.vigenciaCot[1]
            }
            else{
                this.formNewCot.VigDesde = ''
                this.formNewCot.VigHasta = ''
            }
            console.log(this.formNewCot.vigenciaCot)
            return true;
        },

        CrearCotizacion(formName) {
            this.$refs[formName].validate((valid) => {
            if (valid) {
                let me = this;
                const loader = me.loaderk();
                axios.post('/cotizaciones/crear',{params:{
                    'formNewCot' : me.formNewCot
                }}).then(response=>{
                    loader.close();
                    let respuesta = response.data;
                    me.AlertMensaje(respuesta.msg,1);
                    me.AlertMensaje('Te estamos redirigiendo a la cotizacion ...',1);
                    me.limpiarForm();
                    this.$router.push({name:'cotizaciones.ver', params: { 'id':respuesta.IdCotizacion }});
                }).catch(error=>{
                    loader.close();
                    console.log(error);
                    me.AlertMensaje(3,respuesta.msg);
                })
            } else {
                console.log('error submit!!');
                return false;
            }
            });
        },

        limpiarForm() {
            let formName ='formNewCot';
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
    },

    mounted() {
        //this.getTiposCot();
        //this.listarAsesores();
    },
}
</script>