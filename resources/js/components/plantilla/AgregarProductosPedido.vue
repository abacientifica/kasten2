<template>
    <div class="container-fluid">
        <div class="form-group row border">
            <div class="col-md-9">
                <div class="form-group">
                    <label>Artículo <span style="color:red" v-show="fillArticulosMov.nIdItem ==0">(Seleccione *)</span></label>
                    <div class="form-inline">
                            <input type="text" class="form-control" v-model="fillProdNuevo.nIdItem" @keyup.enter="buscarArticuloIndividual()" placeholder="Ingrese Des,Cod,Ref...">
                            <button class="btn btn-primary" @click="AbrirModal()" value="">...</button>
                            <input type="text" readonly="" class="form-control col-md-8" v-model="fillProdNuevo.cDescripcion">
                    </div>                                    
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label>Precio <span style="color:red" v-show="fillProdNuevo.precio ==0">(Ingrese *)</span></label>
                    <input type="number" value="0" step="any" class="form-control" v-model="fillProdNuevo.nPrecio" v-text="FormatoMoneda(fillProdNuevo.nPrecio,1)" disabled>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label>Cantidad <span style="color:red" v-show="fillProdNuevo.cantidad ==0">(Ingrese *)</span></label>
                    <input type="number" value="0" class="form-control" v-model="fillProdNuevo.nCantidad">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label>Agregar</label>
                    <button @click="agregarDetalle()" class="btn btn-success form-control"><i class="fas fa-plus-square"></i></button>
                </div>
            </div>
            
        </div>
        <div class="form-group row border">
            <div class="col-md-12">
                <div class="form-group btnagregar ">
                    <button class="btn btn-success form-control col-md-2" v-if="arraryDetallesMovimiento.length >0" @click.prevent="EmitirEventoProductos"><i class="fas fa-plus-square"></i> Crear Pedido</button>
                </div>
            </div>
        </div>
        <!--Listado de Productos-->
        <div class="form-group row border">
            <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="bg-info">
                        <tr>
                            <th class="texto-centrado">Opción</th>
                            <th class="texto-centrado">Codigo Aba</th>
                            <th class="texto-centrado">Codigo Cliente</th>
                            <th class="texto-centrado">Descripción</th>
                            <th class="texto-centrado">Marca</th>
                            <th class="texto-centrado">Referencia</th>
                            <th class="texto-centrado">UMV</th>
                            <th class="texto-centrado">Cant. Min.</th>
                            <th class="texto-centrado">Cantidad</th>
                            <th class="texto-centrado">Precio</th>
                            <th class="texto-centrado">Iva</th>
                            <th class="texto-centrado">Subtotal</th>
                            
                        </tr>
                    </thead>
                    <tbody v-if="arraryDetallesMovimiento.length">
                        <tr v-for="(detalle,index) in arraryDetallesMovimiento" :key="detalle.id" >
                            <td>
                                <button type="button" @click="eliminarDetalle(index)" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </td>
                            <td class="texto-derecha" v-text="detalle.Id_Item"></td>
                            <td v-text="detalle.CodTercero"></td>
                            <td v-text="detalle.Descripcion"></td>
                            <td v-text="detalle.NmMarca"></td>
                            <td v-text="detalle.Referencia"></td>
                            <td v-text="detalle.UMV"></td>
                            <td class="texto-derecha" v-text="detalle.CantMinimaVenta"></td>
                            <td>
                                <input type="number" v-model="detalle.Cantidad" class="form-control" :style="detalle.Cantidad <= 0 || detalle.Cantidad < 1 ? 'border: 2px solid red;':''">
                            </td>
                            <td class="texto-derecha" v-text="FormatoMoneda(detalle.Precio,2)"></td>
                            <td class="texto-derecha" v-text="FormatoMoneda(detalle.Iva,2)"></td>
                            
                            <td class="texto-derecha" v-text="FormatoMoneda((detalle.Precio * detalle.Cantidad),2)"> </td>
                            
                        </tr>
                        
                        <tr style="background-color: rgb(196 196 245);">
                            <td colspan="11" align="right"><strong>Sub Total:</strong></td>
                            <td class="texto-derecha">${{FormatoMoneda(SubTotal,2)}}</td>
                        </tr>
                        <tr style="background-color: rgb(196 196 245);">
                            <td colspan="11" align="right"><strong>Total Iva:</strong></td>
                            <td class="texto-derecha">${{FormatoMoneda(TotalIva,2)}}</td>
                        </tr>
                        <tr style="background-color: rgb(196 196 245);">
                            <td colspan="11" align="right"><strong>Total Neto:</strong></td>
                            <td class="texto-derecha">${{FormatoMoneda(Total = calcularTotal,2)}} </td>
                        </tr>
                    </tbody>  
                    <tbody v-else>
                        <tr>
                            <td colspan="11">No hay articulos</td>
                        </tr>
                    </tbody>                                 
                </table>
            </div>
        </div>
        <!--Fin listado de productos-->

        <!--Inicio Modal-->
        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
                <div class="modal-dialog modal-primary modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <vs-alert warn>
                            <strong>Alerta!!!</strong> Los productos de color amarillo en el precio, tiene la vigencia vencida por lo tanto al finalizar el pedido un asesor se comunicará en caso de haber cambios.
                        </vs-alert>
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
                            <div class="table-responsive" style="height: 500px;">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="bg-info">
                                        <tr>
                                            <th class="texto-centrado">Código Cliente</th>
                                            <th class="texto-centrado">Código Aba</th>
                                            <th class="texto-centrado">Descripción</th>
                                            <th class="texto-centrado">Referencia</th>
                                            <th class="texto-centrado">Marca</th>
                                            <th class="texto-centrado">UMV</th>
                                            <th class="texto-centrado">Precio</th>
                                            <th class="texto-centrado">Cant. Min.</th>
                                            <th class="texto-centrado">Opción</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="articulo in listarArticulosPaginate" :key="articulo.id"  >
                                            <td v-text="articulo.CodTercero"></td>
                                            <td class="texto-derecha" v-text="articulo.Item"></td>
                                            <td v-text="articulo.Descripcion"></td>
                                            <td v-text="articulo.RefFabricante"></td>
                                            <td v-text="articulo.NmMarca"></td>
                                            <td v-text="articulo.UMM"></td>
                                            <td class="texto-derecha" :class="{'prod-vencido' : articulo.FhHasta < moment().format('YYYY-MM-DD')}" v-text="FormatoMoneda(articulo.Precio,2)">
                                                
                                            </td>
                                            <td class="texto-derecha" v-text="articulo.CantMinimaVenta"></td>
                                            <td>
                                                <button type="button"  @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm" flat >
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
    props:['IdTercero','IdDireccion'],
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
                nIdItem:null,
                cDescripcion:'',
                cCodTercero:'',
                cNmMarca:'',
                nPrecio:0,
                nIva:0,
                cUMV:'',
                cUMM:'',
                cReferencia:'',
                nCantidad:0,
                nCantMinimaVenta:0,
                nIdLista:''
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
            perPage: 10,
            //Fin variables paginacion

            //Variables Totales
            SubTotal:0,
            TotalIva:0,
            Total:0,
            active:false,
            moment:moment

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
                if(response.data.productos.length >0){
                    me.fillProdNuevo.nIdItem = respuesta.Item;
                    me.fillProdNuevo.cDescripcion = respuesta.Descripcion;
                    me.fillProdNuevo.cCodTercero = respuesta.CodTercero;
                    me.fillProdNuevo.nPrecio = respuesta.Precio;
                    me.fillProdNuevo.nIva = respuesta.Por_Iva;
                    me.fillProdNuevo.cUMV = respuesta.UMM;
                    me.fillProdNuevo.cUMM = respuesta.UMM;
                    me.fillProdNuevo.cReferencia = respuesta.RefFabricante;
                    me.fillProdNuevo.cNmMarca = respuesta.NmMarca;
                    me.fillProdNuevo.nCantMinimaVenta = respuesta.CantMinimaVenta;
                    me.fillProdNuevo.nCantidad = 1;
                    me.fillProdNuevo.nIdLista = respuesta.IdListaPreciosDet
                }
                else{
                    me.$vs.notification({
                        position : 'top-center',
                        color: 'warning',
                        title: 'Producto no encontrado',
                        text: 'No se encontro ningun producto relacionado con : <strong>'+me.fillProdNuevo.nIdItem+"</strong>, intenta ingresar una descripción diferente."
                    });
                    //alert("No se encontro ningun dato con "+me.fillProdNuevo.nIdItem)
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
                    Referencia:articulo.RefFabricante,
                    NmMarca : articulo.NmMarca,
                    CantidadAba: 1 / articulo.FactorVenta,
                    Precio:articulo.Precio,
                    Iva:articulo.Iva,
                    UMM:articulo.UMM,
                    UMV:articulo.UMV,
                    IdLista : articulo.IdListaPreciosDet
                });
                Swal.fire({
                    icon :'success',
                    type :'success',
                    title :'',
                    text:'El articulo '+articulo.Descripcion+' se agrego"',
                    showConfirmButton: false,
                    timer: 1200
                })
            }
        },

        ValidarDatos(articulo){
            if(articulo.Cantidad <=0){
                this.arrMensajeError.push("La cantidad del cod "+articulo.IId_Item+" debe ser mayor a 0");
            }
            if(articulo.CantMinimaVenta > articulo.Cantidad && this.direccion[0].tipo.NoValidaCantMinVenta  == 0){
                this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.IId_Item+" es "+articulo.CantMinimaVenta);
            }
            if(this.Is_Float(articulo.Cantidad)){
                this.arrMensajeError.push("La cantidad minima de venta es "+articulo.CantMinimaVenta+", debe ser igual o multiplos de esta");
            }
            if(articulo.Cantidad >0 && this.direccion[0].tipo.NoValidaCantMinVenta  == 0 && this.Is_Float((articulo.Cantidad / articulo.CantMinimaVenta))){
                this.arrMensajeError.push("La cantidad minima de venta es "+articulo.CantMinimaVenta+", debe ser igual o multiplos de esta");
            }
            if(articulo.IdLista == '' || articulo.IdLista == 0 ){
                this.arrMensajeError.push("El cod "+articulo.IId_Item+" no quedo bien enlazado, debes quitarlo y volverlo agregar si el problema continúa comunicarse con el areá de soporte");
            }
        },

        eliminarDetalle(index){
            let producto = this.arraryDetallesMovimiento[index]['Descripcion'];
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
            if(this.fillProdNuevo.nIdItem != 0  && !this.ValidarProductoExiste(this.fillProdNuevo.nIdItem)){
                this.arraryDetallesMovimiento.push({
                    'Id_Item':this.fillProdNuevo.nIdItem,
                    'Descripcion':this.fillProdNuevo.cDescripcion,
                    'CodTercero':this.fillProdNuevo.cCodTercero,
                    'NmMarca':this.fillProdNuevo.cNmMarca,
                    'Referencia':this.fillProdNuevo.cReferencia,
                    'UMV':this.fillProdNuevo.cUMV,
                    'UMM':this.fillProdNuevo.cUMM,
                    'Precio':this.fillProdNuevo.nPrecio,
                    'Iva':this.fillProdNuevo.nIva,
                    'Cantidad':this.fillProdNuevo.nCantidad,
                    'CantMinimaVenta':this.fillProdNuevo.nCantMinimaVenta,
                    'IdLista':this.fillProdNuevo.nIdLista,
                });

                let prodNuevo = this.fillProdNuevo;
                prodNuevo.nIdItem=null;
                prodNuevo.cDescripcion='';
                prodNuevo.cCodTercero='';
                prodNuevo.cNmMarca='';
                prodNuevo.nPrecio=0;
                prodNuevo.nIva=0;
                prodNuevo.cUMV='';
                prodNuevo.cUMM='';
                prodNuevo.cReferencia='';
                prodNuevo.nCantidad=0;
                prodNuevo.nCantMinimaVenta=0,
                prodNuevo.nIdLista=''
            }
        },

        ValidarProductoExiste(Item){
            var Existe = false;
            var Producto = '';
            this.arraryDetallesMovimiento.map(function(x,y){
                if(x['Id_Item'] == Item){
                    Existe = true;
                    Producto = x['Descripcion'];
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
            this.$confirm('Estas seguro(a) de crear el pedido ?', 'Warning', {
                confirmButtonText: 'Crear',
                cancelButtonText: 'Cancelar',
                type: 'warning'
            }).then(() => {
                let i
                for(i =0;i<this.arraryDetallesMovimiento.length;i++){
                    this.ValidarDatos(this.arraryDetallesMovimiento[i]);
                }
                if(this.arrMensajeError.length == 0){
                    EventBus.$emit('arraryDetallesMovimiento',this.arraryDetallesMovimiento);
                    console.log("Se emitio el evento registrar detalles");
                }
                else{
                    this.modalShowErr = true;
                }
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Operacion cancelada'
                });          
            });
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
        console.log(moment().format('YYYY-MM-DD'))
    },
}
</script>
