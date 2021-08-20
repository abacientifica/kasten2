<template>
    <div>
        <button class="btn btn-secondary btn-sm" @click="verLog()">
            <li type="button" class="fas fa-eye"></li> Log 
        </button>
        <!--Modal Error-->
        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="font-size:12px">
                    <div class="modal-header">
                        <h5 class="modal-title">Registro de acciones</h5>
                        <button class="close" @click="AbrirModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="text" v-model="filtros" @keyup.enter="filtrarRegistros(true)"  class="form-control sm" placeholder="Ingrese valor del criterio">
                                <button type="submit" @click="filtrarRegistros(true)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                        <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Accion</th>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Cod Aba</th>
                                            <th v-if="IdDocumento == 93">Id Plant. Det</th>
                                            <th>Comentarios</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="log in listarLogpaginate" :key="log.id">
                                            
                                            <td v-text="log.NmAccion"></td>
                                            <td v-text="log.Fecha"></td>
                                            <td v-text="log.Usuario"></td>
                                            <td v-text="log.Id_Item"></td>
                                            <td v-if="IdDocumento == 93" v-text="log.IdPlantillaDet"></td>
                                            <td v-text="log.Comentarios"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div>
                                    <paginador  v-if="listarLogpaginate.length > 0 &&  arrLogs.length > perPage " :totalItems="arrLogs.length" :itemPorPagina="perPage" v-on:paginaSelect="selectPage"></paginador>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="AbrirModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Paginador  from  '../paginador.vue';
export default {
    components:{
        Paginador
    },
    props:['IdMovimiento','IdDocumento'],
    data() {
        return {
            nIdMovimiento:this.IdMovimiento,
            nIdDocumento:this.IdDocumento,
            arrLogs:[],
            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
            pageNumber: 0,
            perPage: 10,
            filtros:'',
            arrLogsBack:[]
        }
    },

    computed:{
        
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        listarLogpaginate() {
            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.arrLogs.slice(inicio, fin);
        },
        pagesList() {
            let a = this.arrLogs.length;
            let b = this.perPage;
            let PageCoun = Math.ceil(a / b);
            let count = 0;
            let PagesArray = [];
            while (count < PageCoun) {
                PagesArray.push(count);
                count++;
            }
            return PagesArray;
        },
    },

    watch:{
        filtros(){
            this.filtrarRegistros();
        }
    },

    methods: {
        verLog(){
           let me = this;
            axios.get('/log/lista',{params:{
                'nIdMovimiento':this.IdMovimiento,
                'nIdDocumento':this.IdDocumento
            }}).then(function (response) {
                var respuesta = response.data;
                me.arrLogs = respuesta.logs;
                me.arrLogsBack = respuesta.logs;
                me.inicializarPagination();
                me.modalShow = true;
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
        /*Inicio Metodos Paguinacion*/
        inicializarPagination() {
            this.pageNumber = 0;
        },
        selectPage(page) {
            console.log(page)
            this.pageNumber = page;
        },    
        /*Fin Metodos Paginacion*/

        AbrirModal(){
            this.modalShow = !this.modalShow;
            this.arrLogs =[];
        },

        filtrarRegistros(){
            if(this.filtros){
                let me = this;
                let DataFilter = this.arrLogs.filter(function(e){
                    let filter  = e.NmAccion.concat(' ', e.Comentarios,' ', e.Usuario,' ',e.IdPlantillaDet,' ',e.IdItem);
                    if(filter.toLowerCase().includes(me.filtros.toLowerCase())){
                        return true;
                    }
                });
                this.arrLogs = DataFilter;
            }
            else{
                this.arrLogs = this.arrLogsBack;
            }
        }
    },
    mounted() {
        this.nIdMovimiento = this.IdMovimiento;
    },
}   
</script>