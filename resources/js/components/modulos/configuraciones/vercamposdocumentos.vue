<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Configuración Tabla {{Documento}}</h1>
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
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <template>
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
                                    <el-button class="transfer-footer" slot="right-footer" size="small">Operation</el-button>
                                </el-transfer>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:["Documento"],
    data() {
       /* const generateData = _ => {
            const data = [];
            for (let i = 1; i <= 15; i++) {
                data.push({
                    key: i,
                    label: `Option ${ i }`,
                });
            }
            return data;
        };*/
        
        return {
            data:[],
            value: [1],
            value4: [],
            renderFunc(h, option) {
                return '<span>'+  option.key  + '-' + option.label  + '</span>'
            },
        }
        
    },
    methods: {
        CargarCampos(){
            let me = this;
            axios.get('/documento/campos/').then(function(response){
                let respuesta = response.data.campos;
                for(let i = 0 ; i< respuesta.length ; i++){
                    me.data.push({
                        key: respuesta[i].Field ,
                        label: i+'-'+respuesta[i].Field,
                    });
                }
            }).catch({
                
            })
        },

        

        handleChange(value, direction, movedKeys) {
            console.log(value, direction, movedKeys);
        }
    },
    mounted() {
        this.CargarCampos();
    },
}
</script>
<style>
.el-transfer-panel{
    width: 20rem !important;
    height: 20rem !important;;
}
</style>