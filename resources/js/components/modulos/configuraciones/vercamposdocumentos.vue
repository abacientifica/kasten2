<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"></h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link :to="'/'">Home</router-link></li>
                        <li class="breadcrumb-item active">Configuración</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configuración Tabla {{Documento.Nombre}}</h3>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card card-info">
                <div class="card-header bg-info">
                    <h3 class="card-title">Criterios de Busqueda</h3>
                </div>
                    <div class="card-info">
                        <div class="card-body">
                        <form role="form">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Tabla Maestra</label>
                                <div class="col-md-9">
                                    <el-select v-model="tablaMaestra" placeholder="Seleccione una tabla" filterable clearable :disabled="value4.length >0 ? true :false">
                                        <el-option v-for="(item,index) in tablas" :key="index" :label="item.label" :value="item.value">
                                        </el-option>
                                    </el-select>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                <label class="col-md-3 col-form-label">Tabla Detalles</label>
                                <div class="col-md-9">
                                    <el-select v-model="tablaMaestraDetalles" placeholder="Seleccione una tabla" filterable clearable :disabled="value4.length >0 ? true :false" @change="CargarCampos(tablaMaestraDetalles)">
                                        <el-option v-for="(item,index) in tablas" :key="index" :label="item.label" :value="item.value">
                                        </el-option>
                                    </el-select>
                                </div>
                                </div>
                            </div>
                            </div>
                        </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                        <div class="col-md4 offset-4">
                            <button class="btn btn-flat btn-info" @click.prevent="GuardarTablasDocumento()">
                             Guardar
                            </button>
                            <button class="btn btn-flat btn-default">
                            Limpiar
                            </button>
                        </div>
                        </div>
                    </div>
                    </div>
                        <template v-if="Documento['TablaMasterDet'] != null ">
                            <p style="text-align: center; margin: 50px 0 20px">Seleccione los campo que desea configurar en la tabla</p>
                            <div style="text-align: center">
                                <el-transfer
                                    style="text-align: left; display: inline-block ;"
                                    v-model="value4"
                                    filterable
                                    :titles="['Campos BD', 'Campos Configurados']"
                                    :button-texts="['Quitar', 'Agregar']"
                                    :format="{
                                        noChecked: '${total}',
                                        hasChecked: '${checked}/${total}'
                                    }"
                                    @change="handleChange"
                                    :data="data">
                                    <span slot-scope="{ option }">{{ option.label }}</span>
                                    
                                    <el-button class="transfer-footer" slot="left-footer" size="small">Operation</el-button>
                                    <el-button class="transfer-footer btn btn-primary" @click="guardarConfiguracion()" slot="right-footer" size="small">Guardar</el-button>
                                </el-transfer>
                            </div>
                        </template>
                        <template v-else>
                            <vs-alert>Debes configurar primero las tablas maestras del documento</vs-alert>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            Documento:[],
            tablas:[],
            tablaMaestra:'',
            tablaMaestraDetalles:'',
            data:[],
            value: [1],
            value4: [],
            renderFunc(h, option) {
                return '<span>'+  option.key  + '-' + option.label  + '</span>'
            },
            fillNuevaConfig:{
                IdDocumento:this.$attrs.iddoc,
                ConfigInicial :1
            },
            fillDetNuevaConfig:[]
        }
    },
    computed:{
        setCampos(){
            if(this.value4.length > 0){
                let i;
                let NumReg = this.value4.length;
                for(i=0;i<NumReg;i++){
                    console.log("Entro".this.value4[i])
                    this.fillDetNuevaConfig.push({
                        'IdCampo' : this.value4[i],
                        'AliasCampo': null,
                        'IdOrden' : null ,
                    })
                }
            }
            return true;
        }
    },
    methods: {
        CargarCampos(tabla){
            let me = this;
            this.data = [];
            if(tabla !=''){
                axios.get('/documento/campos/?tabla='+tabla).then(function(response){
                    let respuesta = response.data.campos;
                    for(let i = 0 ; i< respuesta.length ; i++){
                        me.data.push({
                            key: respuesta[i].Field ,
                            label: i+'-'+respuesta[i].Field,
                        });
                    }
                }).catch({
                    
                })
            }
        },

        handleChange(value, direction, movedKeys) {
            
        },

        guardarConfiguracion(){
            let me = this;
            axios.put('/documentos/config/save',{
                params:{
                    'IdDocumento':me.fillNuevaConfig.IdDocumento,
                    'ConfigInicial':me.fillNuevaConfig.ConfigInicial,
                    'ConfigDet':me.value4
                }
            }
            ).then(function(response){
                var respuesta = response.data;
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1300
                });
            }).catch({
                
            })
        },

        obtenerConfiguracion(){
            let me = this;
            axios.get('/documentos/config/list/'+me.fillNuevaConfig.IdDocumento
            ).then((response)=>{
                let respuesta = response.data.config.detalles;
                if(respuesta){
                    respuesta.map(function(e){
                        //console.log(e.IdCampo,e.IdOrden);
                        me.value4.push(e.IdCampo)
                    })
                }
            }).catch({
                
            })
        },

        cargarTablas(){
            let me = this;
            axios.get('/documentos/tablas/transacciones'
            ).then((response)=>{
                let respuesta = response.data.tablas;
                if(respuesta){
                    respuesta.map( function(e){
                        me.tablas.push({ value: e.table_name, label: e.table_name })
                    })
                }
            }).catch({
                
            })
        },

        GuardarTablasDocumento(){
            axios.put('/documentos/tablas/transacciones/save',{params:{
                'IdDoc': this.$attrs.iddoc,
                'tablaMaestra' : this.tablaMaestra,
                'tablaMaestraDetalles' : this.tablaMaestraDetalles
            }}
            ).then((response)=>{
                let respuesta = response.data;
                if(respuesta){
                    console.log(respuesta)
                }
            }).catch({
                
            })
            console.log(this.tablaMaestra,this.tablaMaestraDetalles)
        },

        ObtenerDocumento:function(){//Lista documetnos
                let me = this;
                var url = '/documentos/lista';
                axios.get(url,{
                    params:{
                        'buscar':'',
                        'criterio':'',
                        'iddoc' :this.$attrs.iddoc
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    me.Documento = respuesta.documentos;
                    me.tablaMaestra = me.Documento['TablaMaster'];
                    me.tablaMaestraDetalles = me.Documento['TablaMasterDet'];
                    me.CargarCampos(me.tablaMaestraDetalles);
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
    },
    mounted() {
        this.cargarTablas();
        this.obtenerConfiguracion();
        this.ObtenerDocumento();
    },
}
</script>
<style>
.el-transfer-panel{
    width: 20rem !important;
    height: 20rem !important;;
}
</style>