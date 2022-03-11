<template>
    <main class="main">
        <ol class="breadcrumb">
        </ol>
        <div class="container-fluid"> 
        <div class="card">
            <div class="card-header">
                Reportes <span style="color:red;text-aling:center" v-if="getLabelFilters">Filtros Avanzados {{getLabelFilters}}</span>
            </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="form-group row border">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class='label-strong' >Cliente </label>
                                    <span style="color:red" v-show="idtercero ==0">(Seleccione *)</span>
                                    <v-select
                                        @search="selectTerceros"
                                        label="NombreCorto"
                                        :options="arrayTerceros"
                                        placeholder="Buscar Tercero..."
                                        @input="getDatosTercero"   
                                        no-match-text="No se encontro datos."
                                        :disabled="usuario.Tipo == 2 ?true:false"
                                    >
                                    </v-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class='label-strong' >Año </label>
                                    <span style="color:red" v-show="!anio_filtro">(Seleccione *)</span>
                                    <select class="form-control" v-model="anio_filtro">
                                        <option  v-for="anio in getYears" :key="anio" :value="anio" v-text="anio" ></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label class='label-strong' > Buscar</label>
                                    <button type="submit" @click="getVentas()" class="form-control btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class='label-strong' > Mas Filtros</label><br>
                                    <el-button slot="append" icon="el-icon-s-operation"  @click.prevent="dialogFiltros=true"></el-button>
                                    <el-button type="danger" v-if="validarFiltros()" icon="el-icon-delete" circle @click="limpiarFiltros()"></el-button>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Año {{anio_filtro -1}} </h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ventasant">                                                
                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas {{anio_filtro -1}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="card card-chart" >
                                <div class="card-header">
                                    <h4> Año {{anio_filtro}}</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <div class="chart-container">
                                            <canvas id="ventas">                                                
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas {{anio_filtro}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4> Año Anterior vs Actual</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <div class="chart-container">
                                            <canvas id="ventasvs">                                                
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas {{anio_filtro}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
                </div>
            </div>
        </div>
        <el-dialog
            title="Busqueda Avanzada"
            :visible.sync="dialogFiltros"
            width="50%"
            center>
            
            <el-form ref="formFiltros" :model="filtros" label-width="100px">
                <el-form-item label="Proveedor" prop="idterceroProv" >
                    <el-select
                        v-model="filtros.idterceroProv"
                        filterable
                        remote
                        size="small"
                        :reserve-keyword="true"
                        clearable
                        placeholder="Ingrese un nombre proveedore"
                        :remote-method="getProveedores"
                        :loading="loading">
                        <el-option
                            v-for="item in arrayTerceros"
                            :key="item.IdTercero"
                            :label="item.NombreCorto"
                            :value="item.IdTercero">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Marca" prop="nmMarca">
                     <el-input
                            size="small"
                            placeholder="Ingrese una marca valida"
                            v-model="filtros.nmMarca" ></el-input>
                </el-form-item>

                 <el-form-item label="Linea" prop="idLinea">
                    <el-select v-model="filtros.idLinea" size="small" placeholder="Seleccione una Linea" clearable :disabled="arrLineas.length ? false :true">
                        <el-option  v-for="(linea) in arrLineas " :key="linea.IdLinea" :label="linea.NmLinea" :value="linea.IdLinea"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Grupo" prop="idGrupo">
                    <el-select v-model="filtros.idGrupo" size="small" placeholder="Seleccione un grupo" clearable :disabled="arrGrupos.length ? false :true">
                        <el-option  v-for="(grupo) in arrGrupos" :key="grupo.IdGrupo" :label="grupo.NmGrupo" :value="grupo.IdGrupo"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Sub Grupo" prop="idSubGrupo">
                    <el-select v-model="filtros.idSubGrupo" size="small" :placeholder="filtros.idGrupo && !arrSubGrupos.length ? 'Estamos cargando las opciones' : 'Seleccione un seguimiento'" :disabled="arrSubGrupos.length ? false :true">
                        <el-option  v-for="(subgrupo) in arrSubGrupos" :key="subgrupo.IdSubgrupo" :label="subgrupo.NmSubgrupo" :value="subgrupo.IdSubgrupo"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Asesor" :prop="!isAsesor?'idAsesor':''">
                    <el-select v-model="filtros.idAsesor" size="small" placeholder="Seleccione un asesor" clearable :disabled="!isAsesor ? false :true">
                        <el-option  v-for="(asesor) in arrAsesores" :key="asesor.IdAsesor" :label="asesor.Nombre" :value="asesor.IdAsesor"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Descripción" prop="descripcion">
                    <el-input size="small"  v-model="filtros.descripcion" value="string" placeholder="Ingrese una descripción de producto"></el-input>
                </el-form-item>

                <el-form-item label="Item" prop="idItem">
                    <el-input  size="small" v-model="filtros.idItem" value="number" placeholder="Ingrese # idItem"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="getVentas(),dialogFiltros=false">Filtrar</el-button>
                    <el-button @click.prevent="dialogFiltros=false">Cancelar</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </main>
</template>
<script>
import Chart from 'chart.js';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import servicesVentas from '../helpers/getVentasOpciones'
export default {
    components:{
        "v-select": vSelect
    },
    data() {
        return{
            usuario:[],
            ventas:[],
            ventasAnt:[],
            VarMeses : [],
            Anio : [],
            VarTotales:[],
            VarTotales2:[],

            arrMesesAnioAnt : [],
            totalAnioAnt:[],

            //Filtros
            loading: false,
            aniosFiltro:[],
            arrayTerceros:[],
            idtercero:0,
            anio_filtro:null,
            myChart:'',
            myChartAnt:'',
            myChartVsAnios:'',
            isLoading:false,
            dialogFiltros:false,
            isAsesor:false,
            arrLineas:[],
            arrGrupos:[],
            arrSubGrupos:[],
            arrAsesores:[],
            filtros:{
                idterceroProv : '',
                nmMarca : '',
                idLinea : '',
                idGrupo : '',
                idSubGrupo : '',
                idAsesor : '',
                descripcion : '',
                idItem : '',
            },
            ventasVsAnios:null,
            arrVentasVs:[
                {
                    label:'Jan-2021',
                    data:[4564565855456],
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                },
                {
                    label:'Jan-2022',
                    data:[4564565855456],
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                }
            ]
            
        }
    },
    methods: {

        limpiarDatos(){
            var me =this;
            me.VarMeses =[]
            me.Anio =[]
            me.VarTotales=[]
            me.arrMesesAnioAnt = []
            me.totalAnioAnt=[]
            me.ventasVsAnios=[]
        },

        limpiarFiltros() {
            let formName ='formFiltros';
            this.$refs[formName].resetFields();
            this.getVentas();
        },

        async getVentas() {
            const loader = servicesVentas.loader(this,`Hola ${this.usuario.Nombres} estamos cargando los datos ...`);
            let me = this,anioInicial = this.anio_filtro, anioAnterior = anioInicial -1,nitCliente = this.idtercero;
            const params = {anioInicial,anioAnterior,nitCliente , ...this.filtros}
            me.limpiarDatos();
            const [ventasAnioAct,ventasAnioAnt,ventasVsAnios] = []
            [ventasAnioAct,ventasAnioAnt,ventasVsAnios] = await servicesVentas.ventasGrafica([params])
            me.ventasVsAnios = ventasVsAnios
            ventasAnioAct.map((el)=>{
                me.VarMeses.push(el.Mes);
                me.VarTotales.push(el.Total);
                me.VarTotales2.push(el.Total);
            })
            this.loadVentas();

            ventasAnioAnt.map((el)=>{
                me.arrMesesAnioAnt.push(el.Mes);
                me.totalAnioAnt.push(el.Total);
            })
            this.loadVentasAnt();

            this.loadVsVentas();
            loader.close();
        },

        selectTerceros(search,loading){
            let me=this;
            loading(true);
            servicesVentas.getTercerosFilter(search).then((resp)=>{
                me.arrayTerceros = resp.terceros
                loading(false);
            })
            
        },

        getProveedores(search){
            let me =this
            this.loading = true;
            servicesVentas.getTercerosFilter(search).then((resp)=>{
                me.arrayTerceros = resp.terceros
                this.loading = false;
            })
        },

        getDatosTercero(tercero){
            this.idtercero = tercero ? tercero.IdTercero: 0;
        },

        getDatosTerceroProv(tercero){
            this.filtros.idterceroProv = tercero.IdTercero;
        },

        loadVentas(){
            let me=this;
            if(me.myChart  instanceof Chart){
                me.myChart.destroy();
            }
            let graficsVentas = document.getElementById('ventas').getContext('2d');
            me.myChart = new Chart(graficsVentas, {
                type: 'bar',
                data: {
                    labels: me.VarMeses,
                    datasets: [{
                        label: 'Año Actual',
                        data: me.VarTotales,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(258, 99, 132, 0.2)',
                            'rgba(53, 162, 235, 0.2)',
                            'rgba(257, 206, 86, 0.2)',
                            'rgba(65, 192, 192, 0.2)',
                            'rgba(157, 102, 255, 0.2)',
                            'rgba(251, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(258, 99, 132, 0.2)',
                            'rgba(53, 162, 235, 0.2)',
                            'rgba(257, 206, 86, 0.2)',
                            'rgba(65, 192, 192, 0.2)',
                            'rgba(157, 102, 255, 0.2)',
                            'rgba(251, 159, 64, 0.2)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(parseInt(value) >= 1000){
                                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var valor = tooltipItem.yLabel
                                valor = new Intl.NumberFormat('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                }).format(valor)

                                return data.datasets[tooltipItem.datasetIndex].label+": "+ valor;
                            }
                        }
                    }
                }
                
            });
        },

        loadVentasAnt(){
            var me = this;
            if(me.myChartAnt  instanceof Chart) me.myChartAnt.destroy();
            
            var graficsVentas = document.getElementById('ventasant').getContext('2d');
            me.myChartAnt = new Chart(graficsVentas, {
                type: 'bar',
                data: {
                    labels: me.arrMesesAnioAnt,
                    datasets: [{
                        label: 'Año Anterior',
                        data: me.totalAnioAnt,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(258, 99, 132, 0.2)',
                            'rgba(53, 162, 235, 0.2)',
                            'rgba(257, 206, 86, 0.2)',
                            'rgba(65, 192, 192, 0.2)',
                            'rgba(157, 102, 255, 0.2)',
                            'rgba(251, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(value > 1000){
                                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var valor = tooltipItem.yLabel
                                valor = new Intl.NumberFormat('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                }).format(valor)

                                return data.datasets[tooltipItem.datasetIndex].label+": "+ valor;
                            }
                        }
                    }
                }
            });
        },

        loadVsVentas(){
            let me=this;
            if(me.myChartVsAnios  instanceof Chart){
                me.myChartVsAnios.destroy();
            }
            
            let monthsAnioAct = me.ventasVsAnios.filter(el=> el.Anio == this.anio_filtro ),arrVentas =[]
            monthsAnioAct.forEach(el=>{
                me.ventasVsAnios.map(fil => {
                    if(fil.Mes === el.Mes) return arrVentas.push(fil)
                })
            })
            me.ventasVsAnios = arrVentas;
            const colors = ()=>{
                let objColors = []
                this.ventasVsAnios.map(el=>{
                    if((el.Anio % 2) === 0 ){
                        objColors.push('rgba(255, 99, 132, 0.5)')
                    }
                    else{
                        objColors.push('rgba(54, 162, 235, 0.5)')
                    }
                })
                return objColors
            }
            let graficsVentas = document.getElementById('ventasvs').getContext('2d');
            let delayed;
            this.myChartVsAnios = new Chart(graficsVentas, {
                type: 'bar',
                data: {
                    labels:[...me.ventasVsAnios.map(el=>{return `${el.Mes}-${el.Anio}`})],
                    datasets:[{
                        label:'Comparativo',
                        data: [...me.ventasVsAnios.map(el=>{return el.Total})],
                        backgroundColor:colors,
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(value > 1000){
                                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var valor = tooltipItem.yLabel
                                valor = new Intl.NumberFormat('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                }).format(valor)

                                return data.datasets[tooltipItem.datasetIndex].label+": "+ valor;
                            }
                        }
                    },
                    animation: {
                        onComplete: () => {
                            delayed = true;
                        },
                        delay: (context) => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                            delay = context.dataIndex * 300 + context.datasetIndex * 100;
                            }
                            return delay;
                    },
    },
                }
            })
        },


        validarFiltros(){
            let filt = this.filtros
            return (
                filt.idterceroProv ||
                filt.nmMarca ||
                filt.idLinea ||
                filt.idGrupo ||
                filt.idSubGrupo ||
                filt.idAsesor ||
                filt.descripcion ||
                filt.idItem
            )
        },

        async validarAsesor(){
            const asesor  = await servicesVentas.getAsesor()
            if(asesor){
                this.filtros.idAsesor =  asesor.IdAsesor;
                this.isAsesor = true;
            }
            else{
                this.isAsesor = false;
                this.filtros.idAsesor = '';
            }
            return this.filtros.idAsesor
        }
    },

    watch:{
        async dialogFiltros(newVal,oldVal){
            if(newVal){
                const arrPromises = [
                    servicesVentas.getLineasGrupos(),
                    servicesVentas.getAsesores()
                ]
                const [arrLineas,arrAsesores] = await Promise.all(arrPromises)
                this.arrLineas = arrLineas 
                this.arrAsesores = arrAsesores
            }
            else{
                this.arrLineas = [] 
                this.arrAsesores = []
            }
        },

        'filtros.idLinea':function(newVal,oldVal){
            if(newVal){
                let grupos = this.arrLineas.filter(filt => filt.IdLinea === newVal)[0].grupos;
                this.arrGrupos = grupos ? grupos : null
            }
        },

        'filtros.idGrupo':function(newVal,oldVal){
            if(newVal){
                servicesVentas.getSubGrupos(this.filtros.idLinea,newVal).then(resp=>{
                    this.arrSubGrupos = resp
                })
            }
        },
    },


    computed:{
        getYears(){
            let i =0, AnioActual = new Date(),AnioAct = AnioActual.getFullYear(),years=[];
            for(i=2010;i<=AnioAct;i++){
                years.push(i)
            }
            this.anio_filtro = AnioAct
            return years
        },

        getLabelFilters(){
            let labelFilter = ''
            if(this.filtros){
                this.filtros.idterceroProv ? labelFilter = labelFilter + `[ Proveedor: ${this.filtros.idterceroProv}] ` : ''
                this.filtros.nmMarca ? labelFilter = labelFilter + `[ Marca: ${this.filtros.nmMarca}]` : ''
                this.filtros.idLinea ? labelFilter = labelFilter + `[ Linea: ${this.filtros.idLinea}]` : ''
                this.filtros.idGrupo ? labelFilter = labelFilter + `[ Grupo: ${this.filtros.idGrupo}]` : ''
                this.filtros.idSubGrupo ? labelFilter = labelFilter + `[ Sub Grupo: ${this.filtros.idSubGrupo}]` : ''
                this.filtros.idAsesor ? labelFilter = labelFilter + `[ Asesor: ${this.filtros.idAsesor}]` : ''
                this.filtros.descripcion ? labelFilter = labelFilter + `[ Descripcion: ${this.filtros.descripcion}]` : ''
                this.filtros.idItem ? labelFilter = labelFilter + `[ Item: ${this.filtros.idItem}]` : ''
            }
            return labelFilter
        }
    },


    mounted() {
        this.usuario = this.$store.getters['Usuario/getUsuario']
        if(this.usuario && this.usuario.Tipo ==2){
            this.idtercero = this.usuario.IdTercero;
        }
        this.validarAsesor().then(resp=>{
            this.getVentas()
        })
        
    },
}
</script>