<template>
    <div class="container-fluid">
        <div class="form-group row border">
            <div class="col-md-6">
                <div class="col-md-6">
                    <div class="form-group btnagregar">
                        <button @click="AbrirModal()" class="btn btn-success form-control col-md-6"><i class="fas fa-plus-square"></i> Agregar Productos</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Modal-->
        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
                <div class="modal-dialog modal-primary modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="AbrirModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                            <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" v-model="filtroProd" @keyup.enter="buscarArticulo()" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="buscarArticulo()" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="bg-info">
                                        <tr>
                                            <th class="texto-centrado">Codigo Cliente</th>
                                            <th class="texto-centrado">Codigo Aba</th>
                                            <th class="texto-centrado">Descripción</th>
                                            <th class="texto-centrado">Referencia</th>
                                            <th class="texto-centrado">Marca</th>
                                            <th class="texto-centrado">UMV</th>
                                            <th class="texto-centrado">Precio</th>
                                            <th class="texto-centrado">Cant. Min. Venta</th>
                                            <th class="texto-centrado">Opción</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="articulo in listarArticulosPaginate" :key="articulo.id" :class="{'vendidos' : articulo.Venta >0}">
                                            <td v-text="articulo.CodTercero"></td>
                                            <td class="texto-derecha" v-text="articulo.Item"></td>
                                            <td v-text="articulo.Descripcion"></td>
                                            <td v-text="articulo.RefFabricante"></td>
                                            <td v-text="articulo.NmMarca"></td>
                                            <td v-text="articulo.UMM"></td>
                                            <td class="texto-derecha" v-text="FormatoMoneda(articulo.Precio,2)"></td>
                                            <td class="texto-derecha" v-text="articulo.CantMinimaVenta"></td>
                                            <td>
                                                <button type="button"  @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm" >
                                                <i class="fas fa-check"></i>
                                                </button>
                                            </td>
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
                            <button type="button" class="btn btn-secondary" @click="AbrirModal()">Cerrar</button>
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
                        <button class="close" @click="abrirModalErr"></button>
                    </div>
                    <div class="modal-body">
                        <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in arrMensajeError" :key="index" v-text="item"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="abrirModalErr">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Swal from 'sweetalert2'
export default {
    props:['IdTercero','IdDireccion','IdMovimiento','arrayDetallesMovimientoAct'],
    data() {
        return {
            direccion:[],
            arrayArticulos:[],
            arrMensajeError:[],
            articulo:'',
            fillArticulosMov:{
                nIdItem:0,
                cDescripcion:'',
                nPrecio:0,
                nIva:0,
                nCantidad:0,
            },
            arraryDetallesMovimiento:[],
            fillProdNuevo:{
                nIdItem:0,
                cDescripcion:'',
                nPrecio:0,
                nIva:0,
                nCantidad:0,
            },
            filtroProd:'',
            tituloModal:'',
            modalShow: false,
            modalShowErr:false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
            mostrarModalErr: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModalErr: {
                display: 'none',
            },
            IdDir:0,
            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 9,
            //Fin variables paginacion

            //Variables Totales
            SubTotal:0,
            TotalIva:0,
            Total:0

        }
    },

    methods: {
        buscarArticulo(){
            let me = this;
            var url = '/listaprecios/lista';
            axios.get(url,{params:{
                'IdDireccion':this.IdDireccion,
                'filtro':this.filtroProd
            }}).then(function (response) {
                let respuesta = response.data;
                me.arrayArticulos = respuesta.productos;
                me.inicializarPagination();
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        buscarArticuloIndividual(){
            let me = this;
            var url = '/listaprecios/lista';
            axios.get(url,{params:{
                'IdDireccion':this.IdDireccion,
                'filtro':this.fillProdNuevo.nIdItem,
                'limit':1
            }}).then(function (response) {
                let respuesta = response.data.productos[0];
                if(respuesta != ''){
                    me.fillProdNuevo.nIdItem = respuesta.Item;
                    me.fillProdNuevo.cDescripcion = respuesta.Descripcion;
                    me.fillProdNuevo.nPrecio = respuesta.Precio;
                    me.fillProdNuevo.nIva = respuesta.Por_Iva;
                    me.fillProdNuevo.nCantidad = 1;
                }
                else{
                    alert("No se encontro ningun dato con "+me.fillProdNuevo.nIdItem)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        
        ListarArticulo(filtroProd,criterioArt,page=1){
            this.obtenerDirecciones();
            let me = this;
            var url = '/listaprecios/lista?Id='+this.id_direccion+'&filtro='+filtroProd+'&criterio='+criterioArt+'&page='+page;
            axios.get(url).then(function (response) {
                //Asi le asignamos al array categoria los datos de la respuesta
                var respuesta = response.data;
                me.arrayArticulos = respuesta.productos.data;
                me.pagination = respuesta.pagination;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
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
            if(this.modalShow){
                this.buscarArticulo();
            }
            else{
                this.arrayArticulos=[];
            }
        },

        abrirModalErr(){
            this.modalShowErr = !this.modalShowErr;
            if(this.modalShowErr){
                //this.buscarArticulo();
            }
            else{
                this.arrMensajeError=[];
            }
        },

        agregarDetalleModal(articulo){
            if(!this.ValidarProductoExiste(articulo.Item)){
                this.arraryDetallesMovimiento.push({
                    Id_Item:articulo.Item,
                    CodTercero:articulo.CodTercero,
                    Descripcion:articulo.Descripcion,
                    FactorVenta:articulo.FactorVenta,
                    CantMinimaVenta:articulo.CantMinimaVenta,
                    Cantidad:articulo.CantMinimaVenta,
                    CantidadAba: 1 / articulo.FactorVenta,
                    Precio:articulo.Precio,
                    Iva:articulo.Iva,
                    UMM:articulo.UMM,
                    UMV:articulo.UMV
                });
                let me = this;
                axios.put('/movimiento/agregarProducto',{params:{
                    'arraryDetalleMovimiento':me.arraryDetallesMovimiento,
                    'nIdMovimiento':me.IdMovimiento
                }}).then(function (response) {
                    me.EmitirEventoProductos();
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

        ValidarDatos(articulo){
            if(articulo.Cantidad <=0){
                this.arrMensajeError.push("La cantidad del cod "+articulo.Id_Item+" debe ser mayor a 0");
            }
            if(articulo.CantMinimaVenta > articulo.Cantidad && this.direccion[0].tipo.NoValidaCantMinVenta  == 0){
                this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.CantMinimaVenta);
            }
            if(this.Is_Float(articulo.Cantidad)){
                this.arrMensajeError.push("La cantidad minima de venta es "+articulo.CantMinimaVenta+", debe ser igual o multiplos de esta");
            }
        },

        eliminarDetalle(index){
            let producto = this.arraryDetallesMovimiento[index]['articulo'];
            Swal.fire({
                title: '',
                text: "Estas seguro de eliminar "+producto+" de la lista ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Eliminar !',
                }).then((result) => {
                if (result.value) {
                    let me = this;
                    me.arraryDetallesMovimiento.splice(index,1);

                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Producto Eliminado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
            
        },

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

        agregarDetalle(){
            if(!this.ValidarProductoExiste(this.fillProdNuevo.nIdItem)){
                this.arraryDetallesMovimiento.push({
                    'Id_Item':this.fillProdNuevo.nIdItem,
                    'Descripcion':this.fillProdNuevo.cDescripcion,
                    'Precio':this.fillProdNuevo.nPrecio,
                    'Iva':this.fillProdNuevo.nIva,
                    'Cantidad':this.fillProdNuevo.nCantidad,
                });
            }
        },

        ValidarProductoExiste(Item){
            var Existe = false;
            var Producto = '';
            this.arrayDetallesMovimientoAct.map(function(x,y){
                if(x['Id_Item'] == Item){
                    Existe = true;
                    Producto = x.item.Descripcion;
                };
            });
            if(Existe){
                Swal.fire({
                    icon :'error',
                    type :'danger',
                    title :'',
                    text:'El articulo '+Producto+' ya existe en el pedido"'
                })
                return true;
            }
            else{
                return false;
            }
        },

        CargarDireccion(IdDireccion){
            let me = this;
            axios.get('/direcciones/obtenerDireccion',{params:{
                'IdDir':IdDireccion
            }}).then(function (response) {
                var respuesta = response.data;
                me.direccion = respuesta.direccion;
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

        Is_Float(num){
            return !isNaN(num) && Math.round(num) != num;
        },

        EmitirEventoProductos(){
            let i
            for(i =0;i<this.arraryDetallesMovimiento.length;i++){
                this.ValidarDatos(this.arraryDetallesMovimiento[i]);
                
            }
            if(this.arrMensajeError.length == 0){
                EventBus.$emit('agregarDetalleMovimiento',this.arraryDetallesMovimiento);
                console.log("Se emitio el evento agregar detalles");
                this.arraryDetallesMovimiento =[];
            }
            else{
                this.modalShowErr = true;
                this.arraryDetallesMovimiento =[];
            }
        }

    },

    computed: {
        calcularTotal:function(){
            this.TotalIva = 0;
            this.SubTotal = 0;
            for(var i=0;i<this.arraryDetallesMovimiento.length;i++){
                var objeto = this.arraryDetallesMovimiento[i];
                this.TotalIva = this.TotalIva + (((objeto['Cantidad'] * objeto['Precio']) * objeto['Iva'])/100);
                this.SubTotal = this.SubTotal + (objeto['Cantidad'] * objeto['Precio']);
            }
            
            return (this.SubTotal + this.TotalIva);
        },

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

    mounted() {
        this.IdDir = this.IdDireccion;
        this.CargarDireccion(this.IdDireccion);
        
    },
}
</script>
