<template>
    <div class="content-header margen-ruta">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Mapa de Estado Inventario </h1>
            </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item"><router-link to="/utilidades/inventario">Inventario</router-link></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content container-fluid">
            <div class="card">
                <div class="card-header bg-info">
                    <b class="aling-left"> Mapa Inventario</b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <router-link class="btn btn-info btn-sm" :to="{name:'home.index'}">
                                    <i class="fas fa-arrow-left"></i> Regresar
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <el-button type="primary" icon="el-icon-refresh" @click.prevent="obtenerDatos()" circle></el-button>
                                <input type="text" v-model="filtros" @keyup.enter="filtrarDatos()" class="form-control" placeholder="Ingrese valor del criterio">
                                <button type="submit" @click="filtrarDatos()" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                   
                    <hr>
                    <el-table
                        ref="filterTable"
                        :data="tableData"
                        style="width: 100%"
                        :max-height="height"
                        border>

                    <el-table-column label="Piso" width="80" fixed>
                        <template slot-scope="scope">
                            <label v-text="scope.row[0].Piso" ></label>
                        </template>
                    </el-table-column>


                    <el-table-column label="Sector" width="80">
                        <template slot-scope="scope">
                            <label v-text="scope.row[0].Sector" ></label>
                        </template>
                    </el-table-column>

                    <el-table-column label="A">
                        <template slot-scope="scope" >
                            <a v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'A').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'A').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'A').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>
                                <template  v-else-if="obtenerEstadoSeccion(scope,'A').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                            
                        </template>
                    </el-table-column>

                    <el-table-column label="B">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'B').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'B').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'B').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'B').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column  label="C">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'C').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'C').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'C').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'C').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="D">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'D').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'D').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'D').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'D').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="E">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'E').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'E').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'E').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'E').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="F">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'F').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'F').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'F').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'F').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="G">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'G').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'G').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'G').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'G').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="H">
                        <template slot-scope="scope" >
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoSeccion(scope,'H').estado ==='sin-iniciar'" >
                                    <el-alert  :title="`Sin Ini.`"   show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'H').estado ==='finalizado'" >
                                    <el-alert  :title="`Fin.`"  type="success" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'H').estado ==='capturando'" >
                                    <el-alert  :title="`Capturando`"  type="warning" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoSeccion(scope,'H').estado ==='no-encontrado'" >
                                    <vs-button shadow disabled>
                                        No Existe
                                    </vs-button>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="Conteo 2">
                        <template slot-scope="scope">
                            <a  v-on:click="verConteos(scope)">
                                <template  v-if="obtenerEstadoConteo2(scope.row,'C2') ==='sin-iniciar'" >
                                    <el-alert  title="Sin ini." type="error" effect="dark" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoConteo2(scope.row,'C2') ==='generado'" >
                                    <el-alert  title="Gen."  type="warning" effect="dark" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoConteo2(scope.row,'C2') ==='finalizado'" >
                                    <el-alert  title="Fin."  type="success" effect="dark" show-icon :closable="false">
                                    </el-alert>
                                </template>

                                <template  v-else-if="obtenerEstadoConteo2(scope.row,'C2') ==='sin-conteo'" >
                                    <el-alert  title="Sin C2"  type="info" effect="dark" show-icon :closable="false">
                                    </el-alert>
                                </template>
                            </a>
                        </template>
                    </el-table-column>

                    <el-table-column label="Conteo 3">
                            <template slot-scope="scope" >
                                <a  v-on:click="verConteos(scope)">
                                    <template  v-if="obtenerEstadoConteo3(scope.row,'C3') ==='sin-iniciar'" >
                                        <el-alert  title="Sin ini." type="error" effect="dark" show-icon :closable="false">
                                        </el-alert>
                                    </template>

                                    <template  v-else-if="obtenerEstadoConteo3(scope.row,'C3') ==='generado'" >
                                        <el-alert  title="Gen."  type="warning" effect="dark" show-icon :closable="false">
                                        </el-alert>
                                    </template>

                                    <template  v-else-if="obtenerEstadoConteo3(scope.row,'C3') ==='finalizado'" >
                                        <el-alert  title="Fin."  type="success" effect="dark" show-icon :closable="false">
                                        </el-alert>
                                    </template>

                                    <template  v-else-if="obtenerEstadoConteo3(scope.row,'C3') ==='sin-conteo'" >
                                        <el-alert  title="Sin C3"  type="info" effect="dark" show-icon :closable="false">
                                        </el-alert>
                                    </template>
                                </a>
                            </template>
                       
                    </el-table-column>
                   
                </el-table>
                </div>
            </div>
        </div>
        <!--Modal ver conteos -->
        <el-dialog title="Conteos" :visible.sync="abrirModalConteos" width="90%">
            <div class="form-group row border" >
                <el-table
                    ref="filterTable"
                    :data="listarCapturasPaginate"
                    size="small"
                    style="width: 100%">
                    <el-table-column
                        prop="Id_Item"
                        label="Item"
                        sortable >
                    </el-table-column>

                    <el-table-column
                        prop="Descripcion"
                        label="Descripcion">
                    </el-table-column>

                    <el-table-column
                        prop="UMC"
                        label="UMC">
                    </el-table-column>

                    <el-table-column
                        prop="FactorCompra"
                        label="Factor">
                    </el-table-column>

                    <el-table-column
                        prop="UMM"
                        label="UMM">
                    </el-table-column>

                    <el-table-column
                        prop="Lote"
                        label="Lote">
                    </el-table-column>

                    <el-table-column
                        prop="NmUbicacion"
                        label="Ubicacion">
                    </el-table-column>

                    <el-table-column
                        prop="Consecutivo"
                        label="Cons.">
                    </el-table-column>

                    <el-table-column
                        prop="Usuario"
                        label="Usr C1" >
                    </el-table-column>

                    <el-table-column
                        prop="Conteo1"
                        label="C1">
                    </el-table-column>

                    <el-table-column
                        prop="UsuarioConteo2"
                        label="Usr C2" >
                    </el-table-column>

                    <el-table-column
                        prop="Conteo2"
                        label="C2">
                    </el-table-column>

                    <el-table-column
                        prop="UsuarioConteo3"
                        label="Usr C3" >
                    </el-table-column>

                    <el-table-column
                        prop="Conteo3"
                        label="C3">
                    </el-table-column>
                </el-table>
               
                
            </div>
            <span slot="footer" class="dialog-footer">
                <paginador  v-if="listarCapturasPaginate && listarCapturasPaginate.length > 0 &&  datosConteos.length > perPage " :totalItems="datosConteos.length" :itemPorPagina="perPage" v-on:paginaSelect="selectPage"></paginador>
                <hr>
                <el-button @click="abrirModalConteos = false">Cerrar</el-button>
            </span>
        </el-dialog>

    </div>
</template>
<script>
import Paginador  from  '../../../plantilla/paginador';
const groupSecciones = (objectArray,property)=>{
    return objectArray?.reduce((acc,obj)=>{
        let key  = obj[property];
        if(!acc[key]){
            acc[key]=[]
        }
        acc[key] = [...acc[key],obj];
        return acc;
    })
}

export default {
    components:{
        Paginador
    },
    
    data() {
        return {
            height:((window.innerHeight / 2) + 90),
            tableData: [],
            backData:[],
            prueba:null,
            filtros:null,
            obtenerEstadoSeccion (scope,letra){
                let estado = null;
                let seccion = null;
                if(scope){
                    let estadoLetra = '';
                    let datoFiltrado = Object.values(scope.row)?.filter(e=>{ return e.Seccion === letra})[0];
                    if(datoFiltrado){
                        seccion = datoFiltrado.NmUbicacion;
                        if(datoFiltrado.FhHrFin && datoFiltrado.InvCerrado){
                            estado = 'finalizado'
                        }
                        else if(datoFiltrado.FhHrInicio && !datoFiltrado.FhHrFin && !datoFiltrado.InvCerrado){
                            estado = 'capturando'
                        }
                        else if(!datoFiltrado.FhHrInicio && !datoFiltrado.InvCerrado){
                            estado = 'sin-iniciar'
                        }
                        if(!estado){
                            console.log(seccion,datoFiltrado.FhHrInicio ,datoFiltrado.InvCerrado)
                        }
                    }
                    else{
                        estado = 'no-encontrado'
                    }
                    
                }
                return {
                    'estado':estado,
                    'seccion':seccion
                };
            },
            obtenerEstadoConteo2 (datosRow,letra){
                let estado =  null;
                if(datosRow){
                    let estadoLetra = '';
                    let datoFiltrado = datosRow[0]
                    if(datoFiltrado){
                        if(!datoFiltrado.Conteo2 && !datoFiltrado.Generado  && !datoFiltrado.Finalizado  && !datoFiltrado.Cerrado ){
                            estado = 'sin-iniciar'
                        }
                        else if(datoFiltrado.Generado  && !datoFiltrado.Finalizado && !datoFiltrado.Cerrado ){
                            estado = 'generado'
                        }
                        else if(datoFiltrado.Conteo2){
                            estado = 'finalizado'
                        }
                        else{
                            estado = 'sin-conteo'
                        }
                    }
                    else{
                        estado = 'no-encontrado'
                    }
                }
                return estado;
            },
            obtenerEstadoConteo3 (datosRow,letra){
                let estado =  null;
                if(datosRow){
                    let estadoLetra = '';
                    let datoFiltrado = datosRow[0]
                    if(datoFiltrado){
                        if(datoFiltrado.Generado  && !datoFiltrado.Cerrado){
                            estado = 'sin-iniciar'
                        }
                        else if(datoFiltrado.Cerrado){
                            estado = 'generado'
                        }
                        else if(datoFiltrado.Finalizado ){
                            estado = 'finalizado'
                        }
                        else{
                            estado = 'sin-conteo'
                        }
                    }
                    else{
                        estado = 'no-encontrado'
                    }
                }
                return estado;
            },
            abrirModalConteos:false,
            datosConteos:null,

            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 8,
            //Fin variables paginacion

        }
    },

    computed:{
        listarCapturasPaginate() {
            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.datosConteos ? this.datosConteos.slice(inicio, fin):[];
        },

        limpiarConteos(){
            this.abrirModalConteos === false ? this.datosConteos = null:'';
            return true;
        }
    },

    methods: {
        
        obtenerDatos(){
            let me  = this;
            me.tableData = [];
            const load = this.loader('Un momento, estamos cargando los datos ... ');
            axios.get('/inventario/conteos').then(response=>{
                let respuesta = response.data.datos;
                respuesta.push(response.data.datos[0]);
                let groupData = groupSecciones(respuesta,'Sector')
                me.prueba = Object.values(groupData).map(e=>{
                    if(Array.isArray(e)){
                        me.tableData = [...me.tableData,e]
                    }
                });
                me.backData = me.tableData;
                load.close();
            }).catch(error=>{
                load.close();
                me.abrirModalConteos = false;
                console.log(error);
            })
        },

        verConteos(scope){
            const load = this.loader();
            let me=this,seccion=scope.column.label,sector=scope.row[0].Sector,conteo2= scope.column.label.length > 1 ? true :false,conteo3=scope.column.label.length > 1 ? true :false;
            axios.get('/inventario/obtenerConteos',{
                params:{seccion,sector,conteo2,conteo3
            }}).then(response=>{
                me.datosConteos = response.data.conteos
                me.abrirModalConteos = true;
                load.close();
            }).catch(error=>{
                load.close();
                this.$notify.error({
                    title: 'Error',
                    message: 'Ocurrio un error al obtener los datos, intenta de nuevo.'
                });
                me.abrirModalConteos = false;
                console.log(error);
            })
        },

        selectPage(page) {
            this.pageNumber = page;
        },    

        filtrarDatos(){ 
            const load = this.loader('Filtrando datos ... ');
            if(this.filtros){
                let arrFiltros = this.filtros.split(',');
                if(arrFiltros.length === 1){
                    if(!isNaN(arrFiltros)){
                        this.tableData = this.backData[this.filtros-1] ? [this.backData[this.filtros-1]] :this.tableData;
                    }
                }
                else{
                    let datoInit = false;
                    for (let i = 0; i < arrFiltros.length; i++) {
                        if(!isNaN(arrFiltros[i])){
                            if(i===0 || !datoInit){
                                if(this.backData[arrFiltros[i]-1]){
                                    this.tableData = [this.backData[arrFiltros[i]-1]];
                                    datoInit = true;
                                }
                            }
                            else{
                                if(this.backData[arrFiltros[i]-1]){
                                    this.tableData.push(this.backData[arrFiltros[i]-1]);
                                }
                            }
                        }
                    }
                }
            }
            else{
                this.tableData = this.backData;
            }
            load.close();
        },

        loader(msg='Cargando...') {
            return this.$vs.loading({
                type: 'square',
                background: '#babaea',
                color: '#fff',
                text: msg
            });
        },
    },
    mounted() {
        this.obtenerDatos();
    },
}

</script>
<style>
.el-dialog{
    margin-top: 5vh !important
}
</style>