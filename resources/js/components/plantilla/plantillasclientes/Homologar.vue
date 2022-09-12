<template>
    <div>
    <div class="modal fade" v-if="modalShow = this.visible" :class="{ show: this.visible }" :style=" this.visible ? mostrarModal : ocultarModal" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-primary modal-xl font" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <el-alert
                        :title="tituloModal"
                        :closable="false"
                        type="success">
                        <button type="button" v-if="ItemSel && ItemSel.IdListaCostosDetPlantDet && (ItemSel && ItemSel.Autorizado != 1)" class="btn btn-danger btn-sm" @click.prevent="desligarItem()" >
                            <i class="fas fa-minus-circle"></i>
                        </button>
                    </el-alert>
                    <button type="button" class="btn btn-info" v-if="indexItem >0" @click="seleccionarItem('ant')" >
                        <i class="fas fa-arrow-alt-circle-left"></i>
                    </button>

                    <button type="button" class="btn btn-info" v-if="indexItem < indexFin && indexFin > 1" @click="seleccionarItem('sig')" >
                        <i class="fas fa-arrow-alt-circle-right"></i>
                    </button>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <el-select
                                    v-model="value"
                                    multiple
                                    allow-create
                                    class="selector"
                                    placeholder="Seleccione los filtros">
                                    <el-option
                                    v-for="item in criterios"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                                    </el-option>
                                </el-select>
                                <input type="text" v-model="filtros" @keyup.enter="listarDatos(true)" class="form-control" placeholder="Ingrese valor del criterio">
                                <button type="submit" @click="listarDatos(true)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="height: 500px;">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-info">
                                <tr>
                                    <th class="texto-centrado">Item</th>
                                    <th class="texto-centrado">Lista</th>
                                    <th class="texto-centrado">Vigencia</th>
                                    <th class="texto-centrado">Referencia</th>
                                    <th class="texto-centrado">Cod</th>
                                    <th class="texto-centrado">Descripción</th>
                                    <th class="texto-centrado">Marca</th>
                                    <th class="texto-centrado">Presentación</th>
                                    <th class="texto-centrado">UMM</th>
                                    <th class="texto-centrado">FC</th>
                                    <th class="texto-centrado">CP</th>
                                    <th class="texto-centrado">Hab. Cotizar</th>
                                    <th class="texto-centrado">Inact</th>
                                    <th class="texto-centrado">Disp</th>
                                    <!--<th class="texto-centrado">Costo UMM</th>-->
                                    <th class="texto-centrado" v-if="ItemSel != null && ItemSel.Autorizado != 1" >Opción</th>
                                </tr>
                            </thead>

                            <tbody v-if="listarArticulosPaginate.length >0 && (ItemSel && ItemSel.Autorizado != 1)">
                                <tr v-for="(articulo,index) in listarArticulosPaginate" :key="index"  >
                                    <td v-text="articulo.Id_Item"></td>
                                    <td v-text="articulo.NmListaCostos"></td>
                                    <td v-text="articulo.FhDesde+' hasta '+ articulo.FhHasta"></td>
                                    <td v-text="articulo.RefFabricante"></td>
                                    <td v-text="articulo.CodProveedor"></td>
                                    <td v-text="articulo.Descripcion"></td>
                                    <td v-text="articulo.NmMarca"></td>
                                    <td v-text="articulo.Presentacion"></td>
                                    <td v-text="articulo.UMM"></td>
                                    <td v-text="articulo.FactorCompra"></td>
                                    <td v-text="articulo.CategoriaPortafolio"></td>
                                    <td v-text="articulo.HabCotizar == 1 ? 'SI':'NO'"></td>
                                    <td v-text="articulo.Inactivo== 1 ? 'SI':'NO'"></td>
                                    <td v-text="articulo.Disponible ? articulo.Disponible : 0"></td>
                                    <!--<td class="texto-derecha" v-text="FormatoMoneda(articulo.CostoUMM,2)"></td>-->
                                    <!--<td class="texto-derecha" :class="{'prod-vencido' : articulo.FhHasta < moment().format('YYYY-MM-DD')}" v-text="FormatoMoneda(articulo.Precio,2)"></td>-->
                                    <td v-if="ItemSel != null && ItemSel.Autorizado != 1">
                                        <button type="button"  @click="enlazarLista(index)"  class="btn btn-success btn-sm" flat >
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <!--<vs-tooltip warn>
                                            <button type="button"  @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm" flat >
                                            <i class="fas fa-check"></i>
                                            </button>
                                            <template #tooltip>
                                                Este producto tiene el precio vencido
                                            </template>
                                        </vs-tooltip>-->
                                        
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="16">
                                        <vs-alert v-if="ItemSel && ItemSel.Autorizado != 1">
                                            <template #title>
                                                Alerta !!
                                            </template >
                                                No se encontraron registros con los filtros : {{this.filtros}}
                                        </vs-alert>
                                        <vs-alert v-else>
                                            <template #title>
                                                Alerta !!
                                            </template >
                                                El item se encuentra autorizado por lo cual no puedes cambiar la lista homologada.
                                        </vs-alert>
                                    </td>
                                </tr>
                            </tbody>   
                        </table>
                    </div>
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
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
        </div>
        <!--Modal Error-->
        <div class="modal fade" :class="{ show: modalShowErr }" :style=" modalShowErr ? mostrarModalErr : ocultarModalErr">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alerta !!!</h5>
                        <button class="close" @click="cerrarModalErr"></button>
                    </div>
                    <div class="modal-body">
                        <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in MensajeError" :key="index" v-text="item"></div>
                        <strong>Recuerda que los filtros seleccionados deben coincidir con los datos digitados separados por ',' por cada filtro.</strong>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="cerrarModalErr">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { Environment } from 'ag-grid-community';
export default {
    props:['visible','datosItem','datos','datosPl'],
    data() {
        return {
            tituloModal: '',
            modalShow: this.visible,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 10,
            arrayArticulos:[],
            //Fin variables paginacion
            filtroProd:null,
            criterios: [{
                value: 'lista_costos_prov_det.Id_Item',
                label: 'Item'
            }, {
                value: 'lista_costos_prov_det.RefFabricante',
                label: 'Referencia'
            }, {
                value: 'lista_costos_prov_det.CodProveedor',
                label: 'Cod. Prov'
            }, {
                value: 'lista_costos_prov_det.DescripcionProv',
                label: 'Descripcion Proveedor'
            },

            {
                value: 'marcas.NmMarca',
                label: 'Marca'
            },

            {
                value: 'NmListaCostos',
                label: 'Lista Costos'
            },

            {
                value: 'lista_costos_prov_det.Inactivo',
                label: 'Inactivo'
            },
            {
                value: 'lista_costos_prov_det.CategoriaPortafolio',
                label: 'Categoria Portafolio'
            },
            {
                value: 'lista_costos_prov_det.HabCotizar',
                label: 'Hab. Cotizar'
            }
            
            ],
            value: [],
            filtros:null,
            moment:moment,
            MensajeError:[],
            modalShowErr:false,
            mostrarModalErr: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModalErr: {
                display: 'none',
            },
            indexIni:0,
            indexFin:0,
            indexItem : 0,
            ItemSel: null,
        }
    },
    watch:{
        modalShow(val){
            this.indexFin = this.datos.length;
            this.ItemSel = this.datosItem.data;
            this.indexItem = this.datosItem.childIndex;
            if(val){
                this.listarDatos();
            }
        }
    },
    computed:{
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.arrayArticulos.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        listarArticulosPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.arrayArticulos.slice(inicio, fin);
        },
        pagesList() {
            let a = this.arrayArticulos.length;
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
        listarDatos(filtro=false){
            let me = this;
            let url = '/plantillas/ObtenerDatosHomolgar';
            const loader = this.loaderk();
            if(!filtro){
                //console.log(this.datos[this.datosItem.])
                if(this.ItemSel.IdListaCostosDetPlantDet >0){
                    this.filtros = null;
                    this.value = [];
                    let Enlace = this.ItemSel.IdListaCostosDetPlantDet >0 ? `SI /Cod Aba :  ${this.ItemSel.ItemAba}` :'NO' ;
                    this.tituloModal = this.ItemSel.DescripcionCliente +'/ Enlazado : '+Enlace  ;
                    this.filtros = this.ItemSel.DescripcionAba ;
                    this.value.push("lista_costos_prov_det.DescripcionProv");
                }
                else{
                    this.filtros = null;
                    this.value = [];
                    let Enlace = this.ItemSel.IdListaCostosDetPlantDet >0 ? `SI /Cod Aba :  ${this.ItemSel.ItemAba}` :'NO' ;
                    this.tituloModal = this.ItemSel.DescripcionCliente +'/ Enlazado : '+Enlace  ;
                    this.filtros = this.ItemSel.DescripcionCliente;
                    this.value.push("lista_costos_prov_det.DescripcionProv");
                }
            }
            this.ValidarFiltro();
            if(this.MensajeError.length >0){
                loader.close();
                return false;
            }
            axios.get(url,{
                params:{
                    'IdTercero':this.datosPl.IdTerceroPlant,
                    'Filtros':this.filtros,
                    'Criterios':this.value
                }
            }).then((response)=>{
                me.arrayArticulos = response.data.listas_det;
                this.inicializarPagination();
                loader.close();
            }).catch(error=>{
                console.log(error)
                loader.close();
            })
        },
        /*Inicio Metodos Paguinacion*/
        inicializarPagination() {
            this.pageNumber = 0;
        },
        nextPage() {
            this.pageNumber++;
            console.log(this.ItemSel)
        },
        pagePrev() {
            this.pageNumber--;
        },
        selectPage(page) {
            this.pageNumber = page;
        },    
        /*Fin Metodos Paginacion*/
        FormatoMoneda(amount=0, decimals) {
            var sign = (amount.toString().substring(0, 1) == "-");

            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0) 
                return parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

            return  sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
        },

        cerrarModal(){
            this.filtros = null;
            this.value = [];
            this.$emit('OcultarModal',false);
        },

        OrganizarFiltro(){
            this.filtros = this.filtros ? this.filtros+',':'';
        },

        ValidarFiltro(){
            if(this.filtros != null){
                let criterios = this.value.length;
                let filtros = this.filtros.split(',').length;
                if(criterios != filtros){
                    this.MensajeError.push("Los filtros seleccionados no coinciden con los digitados.");
                }
            }
            else{
                this.MensajeError.push("Debes ingresar un dato a filtrar");
            }

            if(this.MensajeError.length >0){
                this.modalShowErr = true;
            }
        },

        cerrarModalErr(){
            this.modalShowErr = !this.modalShowErr;
            this.MensajeError=[];
        },

        seleccionarItem(op){
            this.filtros = null;
            this.value = [];
            if(op=='sig'){
                this.indexItem = this.indexItem + 1;
                this.ItemSel = this.datos[this.indexItem].data;
                EventBus.$emit('cambioPageHomologar',{index:this.indexItem});
                this.listarDatos();
            }
            else{
                this.indexItem = this.indexItem - 1;
                this.ItemSel = this.datos[this.indexItem].data;
                EventBus.$emit('cambioPageHomologar',{index:this.indexItem});
                this.listarDatos();
            }
        },

        enlazarLista(index){
            let Item = this.listarArticulosPaginate[index];
            let me = this;
            const load = this.loaderk();
            axios.put('/plantillas/clientes/AsignarLista',{
                params:{
                    'IdPlantillaDet':me.ItemSel.IdPlantillaDet,
                    'IdLista':Item.IdListaCostosProvDet,
                    'Item':Item.Id_Item,
                }
            }).then((response)=>{
                let respuesta = response.data;
                me.ItemSel.IdListaCostosDetPlantDet = Item.IdListaCostosProvDet;
                me.ItemSel.DescripcionAba = Item.Descripcion;
                me.ItemSel.ItemAba = Item.Id_Item;
                load.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1300
                });
                let id = me.ItemSel.IdPlantillaDet;
                EventBus.$emit('ItemHomologado',{index:this.indexItem,id,'data':respuesta.detalle});
                this.listarDatos();
                
            }).catch(error=>{
                console.log(error)
                load.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'warning',
                    title: 'Ocurrio un error al enlazar ',
                    showConfirmButton: false,
                    timer: 1300
                });
            })
        },

        desligarItem(){
            let me = this;
            const load = this.loaderk();
            axios.put('/plantillas/clientes/DesligarLista',{
                params:{
                    'IdPlantillaDet':me.ItemSel.IdPlantillaDet,
                    'IdItem':me.ItemSel.ItemAba,
                }
            }).then((response)=>{
                let respuesta = response.data;
                me.ItemSel = respuesta.detalle;
                load.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1300
                });
                let id = me.ItemSel.IdPlantillaDet;
                EventBus.$emit('ItemDesHomologado',{index:this.indexItem,id,'data':respuesta.detalle});
                this.listarDatos();
            }).catch(error=>{
                console.log(error)
                load.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'warning',
                    title: 'Ocurrio un error al quitar enlace ',
                    showConfirmButton: false,
                    timer: 1300
                });
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
       
    },
}
</script>
<style >
.selector{
    width: 20% !important;
}
.font{
    font-size: 14px;
}
</style>