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
                                <div class="card-footer clearfix">
                                    <ul class="pagination pagination-sm m-0 float-rigth">
                                        <li class="page-item" v-if="this.pageNumber > 0">
                                        <a href="#" class="page-link" @click.prevent="pagePrev()" >Ant</a>
                                        </li>

                                        <li class="page-item" v-for="(page, index) in pagesList" :key="index" :class="page == pageNumber ? 'active' : ''">
                                        <a href="#" class="page-link" @click.prevent="selectPage(page)">{{ page + 1 }}</a>
                                        </li>

                                        <li class="page-item" v-if="pageNumber < pageCount - 1">
                                        <a href="#" class="page-link" @click.prevent="nextPage()">Sig</a>
                                        </li>
                                    </ul>
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
export default {
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
            perPage: 5,
        }
    },

    computed:{
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.arrLogs.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        listarLogpaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

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

    methods: {
        verLog(){
           let me = this;
            axios.get('/log/lista',{params:{
                'nIdMovimiento':this.IdMovimiento,
                'nIdDocumento':this.IdDocumento
            }}).then(function (response) {
                var respuesta = response.data;
                me.arrLogs = respuesta.logs;
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
        nextPage() {
            this.pageNumber++;
        },
        pagePrev() {
            this.pageNumber--;
        },
        selectPage(page) {
            this.pageNumber = page;
        },    
        /*Fin Metodos Paginacion*/

        AbrirModal(){
            this.modalShow = !this.modalShow;
            this.arrLogs =[];
        }
    },
    mounted() {
        this.nIdMovimiento = this.IdMovimiento;
    },
}   
</script>