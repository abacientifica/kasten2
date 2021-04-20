<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ver Pedido </h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pedidos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header bg-info">
                    <template v-if="listPermisosFilterByRolUser.includes('pedidos.crear')">
                        <div class="card-tools">
                            <template v-if="listPermisosFilterByRolUser.includes('pedidos.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                <div class="card-tools">
                                    <router-link class="btn btn-info btn-sm" :to="'/pedidos/crear/'+this.OpPedido">
                                        <i class="fas fa-plus-square"></i> Nuevo Pedido
                                    </router-link>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div class="card-tools">
                        <router-link class="btn btn-info btn-sm" :to="'/pedidos/index'">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </router-link>
                    </div>
                    <b v-text="fillMovimiento.cNmCliente"></b>
                    
                </div>
                <div class="card-body">
                    <div class="col-md-12 btn-group-justified"  style="display:flex" v-if="fillMovimiento.cEstado == 'DIGITADA' || fillMovimiento.cEstado == 'AUTORIZADA' || fillMovimiento.cEstado == 'CERRADA'">
                        <button class="btn btn-success " v-if="fillMovimiento.cEstado =='AUTORIZADA'" @click.prevent="NotificarPedido()"><i class="fas fa-bell"></i> Enviar Alerta Servicio Cliente</button>
                        <button class="btn btn-success " v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==0" @click.prevent="Autorizar()"><i class="fas fa-check"></i> Autorizar</button>
                        <button class="btn btn-primary " v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==0" @click.prevent="Editar()"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-success " v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==1" @click.prevent="ActualizarDatos()"><i class="fas fa-check"></i> Guardar Cambios</button>
                        <button class="btn btn-warning " v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==1" @click.prevent="Editar()"><i class="fas fa-times-circle"></i> Cancelar Edición</button>
                        <logacciones :IdMovimiento ="this.fillMovimiento.nIdMovimiento"></logacciones>
                    </div><hr>
                    <div class="form-group row border">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class='label-strong'>IdMovimiento</label>
                                <!--label muestra el objeto o la columna en el select-->
                                <p v-text="fillMovimiento.nIdMovimiento"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="" class='label-strong'>Nro Documento</label>
                                <p v-text="fillMovimiento.nNroDocumento"></p>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Fecha Creación</label>
                                <p v-text="fillMovimiento.dFecha"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Condición Entrega</label>
                                <p v-text="fillMovimiento.cNmCondEntrega"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Fecha Minima Entrega </label>
                                <p v-text="fillMovimiento.dFecha1"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Fecha Maxima Entrega </label>
                                <p v-text="fillMovimiento.dFecha2"></p>
                            </div>
                        </div>

                        

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Estado</label>
                                <p v-text="fillMovimiento.cEstado"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Soporte</label>
                                <p v-text="fillMovimiento.cSoporte"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Centro Costos / Dirección</label>
                                <p v-text="fillMovimiento.cNmDireccion"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Asesor</label>
                                <p v-text="fillMovimiento.cNmAsesor"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Forma Pago</label>
                                <p v-text="fillMovimiento.cNmFormaPago"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class='label-strong'>Comentarios</label>
                                <p v-text="fillMovimiento.cComentarios"></p>
                            </div>
                        </div>
                    </div>
                    <AgregarProductosMovimiento v-if="fillMovimiento.cEstado == 'DIGITADA'" :IdTercero="fillMovimiento.nIdTercero" :IdDireccion="fillMovimiento.nIdDireccion" :IdMovimiento="fillMovimiento.nIdMovimiento" :arrayDetallesMovimientoAct="ListarMovimientosDetPaginate"></AgregarProductosMovimiento>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm" >
                                <thead>
                                    <tr>
                                        <th v-if="accionMovimiento==1">Opción</th>
                                        <th>Codigo</th>
                                        <th>Artículo</th>
                                        <th>Referencia</th>
                                        <th>Marca</th>
                                        <th>Invima</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Iva</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="ListarMovimientosDetPaginate.length">
                                    <tr v-for="(detalle) in ListarMovimientosDetPaginate" :key="detalle.id">
                                        <td v-if="accionMovimiento==1">
                                            <button type="button" @click="eliminarDetalle(detalle)" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </td>
                                        <td v-text="detalle.Id_Item"></td>
                                        <td v-text="detalle.item.Descripcion"></td>
                                        <td v-text="detalle.item.listacostosdet.RefFabricante"></td>
                                        <td v-text="detalle.item.listacostosdet.marca.NmMarca"></td>
                                        <td v-text="detalle.item.listacostosdet.RegInvima"></td>
                                        <td style="text-align:right;" v-text="FormatoMoneda(detalle.Precio,2)"></td>
                                        <td style="text-align:right;" v-text="detalle.Cantidad" v-if="accionMovimiento==0"></td>
                                        <td v-else><input type="number" v-model="detalle.Cantidad" class="form-control" :style="detalle.Cantidad <= 0 || detalle.Cantidad < 1 ? 'border: 2px solid red;':''"></td>
                                        <td style="text-align:right;" v-text="detalle.PorIva"></td>
                                        <td style="text-align:right;" v-text="FormatoMoneda((detalle.Precio * detalle.Cantidad),2)"> </td>
                                    </tr>
                                    
                                    
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="8" align="right"><strong>Total Iva:</strong></td>
                                        <td>$ {{FormatoMoneda(this.fillMovimiento.nVrIva ,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="8" align="right"><strong>Sub Total:</strong></td>
                                        <td>$ {{FormatoMoneda((this.fillMovimiento.nSubTotal),2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="8" align="right"><strong>Total:</strong></td>
                                        <td>$ {{FormatoMoneda((fillMovimiento.nTotal),2)}}</td>
                                    </tr>
                                </tbody>  
                                <tbody v-else>
                                    <tr>
                                        <td colspan="5">No hay articulos</td>
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

                    <div class="form-group row">
                        <div class="col-md-12">
                            <router-link class="btn btn-secondary" :to="'/pedidos/index'">
                                <i class="fas fa-arrow-left"></i> Cerrar
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal Error-->
        <div class="modal fade" :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alerta !!!</h5>
                        <button class="close" @click="AbrirModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="callout callout-danger" style="padding: 5px" v-for="(item, index) in arrMensajeError" :key="index" v-text="item"></div>
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
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            direccion:[],
            arrMensajeError:[],
            OpPedido:8,
            accionMovimiento:0,
            listPermisosFilterByRolUser:[],
            fillMovimiento:{
                nIdMovimiento: 0,
                nIdDireccion: 0,
                nIdTercero: 0,
                nNroDocumento:0,
                nIdDocumento:61,
                dFecha : '',
                dFecha1 : '',
                dFecha2 : '',
                cEstado:'',
                cSoporte:'',
                cNmCliente:'',
                cNmDireccion:'',
                cNmCondEntrega:'',
                cNmFormaPago:'',
                cNmAsesor:'',
                cComentarios:'',
                nVrIva:0,
                nSubTotal:0,
                nTotal:0,
            },

            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },

            fillDetallesMov:[],
            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 15,
            //Fin variables paginacion
        }
    },
    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.fillDetallesMov.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarMovimientosDetPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.fillDetallesMov.slice(inicio, fin);
        },
        pagesList() {
            let a = this.fillDetallesMov.length;
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

        /**
         * Obtenemos el listado de detalles
         */
        ListarMovimiento(IdMov){
            let url ="/movimiento/"+IdMov;
            axios.get(url).then(response=>{    
                this.inicializarPagination();
                if(response.data.movimiento.length){
                    let Datos = response.data.movimiento[0];
                    this.fillMovimiento.nIdMovimiento = Datos.IdMovimiento;
                    this.fillMovimiento.nNroDocumento = Datos.NroDocumento;
                    this.fillMovimiento.nIdDireccion = Datos.IdDireccion;
                    this.fillMovimiento.nIdTercero = Datos.IdTercero;
                    this.fillMovimiento.dFecha = Datos.Fecha;
                    this.fillMovimiento.dFecha1 = Datos.Fecha1;
                    this.fillMovimiento.dFecha2 = Datos.Fecha2;
                    this.fillMovimiento.cEstado = Datos.Estado;
                    this.fillMovimiento.cSoporte = Datos.Soporte;
                    this.fillMovimiento.cNmDireccion = Datos.direccion.NmDireccion;
                    this.fillMovimiento.cNmCondEntrega = Datos.Fecha;
                    this.fillMovimiento.cNmFormaPago = Datos.fpago.FormaPago;
                    this.fillMovimiento.cNmAsesor = Datos.asesor.Nombre;
                    this.fillMovimiento.cComentarios = Datos.Comentarios == '' ? 'SIN COMENTARIOS' : Datos.Comentarios;
                    this.fillMovimiento.nVrIva = Datos.VrIva;
                    this.fillMovimiento.nSubTotal = Datos.SubTotal;
                    this.fillMovimiento.nTotal = Datos.Total;
                    this.fillMovimiento.cNmCliente = Datos.tercero.NombreCorto;
                    this.fillDetallesMov = response.data.movimientos_det;
                    this.CargarDireccion(Datos.IdDireccion);
                }
                else{
                    this.listMovimientos = [];
                }
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        limpiarDatos(){
            this.fillMovimiento.nIdMovimiento = 0;
            this.fillMovimiento.nNroDocumento = 0;
            this.fillMovimiento.dFecha = '';
            this.fillMovimiento.dFecha1 = '';
            this.fillMovimiento.dFecha2 = '';
            this.fillMovimiento.cEstado = '';
            this.fillMovimiento.cSoporte = '';
            this.fillMovimiento.cNmDireccion = '';
            this.fillMovimiento.cNmCondEntrega = '';
            this.fillMovimiento.cNmFormaPago = '';
            this.fillMovimiento.cNmAsesor = '';
            this.fillMovimiento.cComentarios = '';
            this.fillMovimiento.nVrIva = '';
            this.fillMovimiento.nSubTotal = '';
            this.fillMovimiento.nTotal = '';
            this.fillMovimiento.cNmCliente = '';
            this.fillDetallesMov = [];
        },

        Autorizar(){
            let me = this;
            let url ="/movimiento/autorizar";
            axios.put(url,{
                params:{
                    'nIdMovimiento':me.fillMovimiento.nIdMovimiento
                }
            }).then(response=>{    
                let respuesta = response.data;
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1500
                });
                me.ListarMovimiento(me.fillMovimiento.nIdMovimiento);

            }).catch(error =>{
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        Editar(){
            this.accionMovimiento = !this.accionMovimiento;
        },

        ActualizarDatos(){
            let me = this;
            this.ValidarDatos();
            if(this.arrMensajeError.length <=0){
                axios.put('/movimiento/editar',{
                    params:{
                        'nIdMovimiento':this.fillMovimiento.nIdMovimiento,
                        'arraryDetallesMovimiento':this.fillDetallesMov
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: respuesta.msg,
                        showConfirmButton: false,
                        timer: 1300
                    });
                    me.ListarMovimiento(me.fillMovimiento.nIdMovimiento);
                    me.Editar();
                })
                .catch(function (error) {
                    console.log(error);
                    if (error.response.data.status == 401) {
                        this.$router.push({name: 'login'})
                        location.reload();
                        sessionStorage.clear();
                        this.fullscreenLoading = false;
                    }
                    if(error.response.data.status == 500){
                        Swal.fire({
                            icon :'danger',
                            type :'danger',
                            title :'',
                            text:response.data.error
                        })
                    }
                });
            }
            else{
                this.modalShow = true;
            }
        },

        ValidarDatos(){
            let i;
            for(i=0;i<this.fillDetallesMov.length;i++){
                let articulo = this.fillDetallesMov[i];
                if(articulo.Cantidad <=0){
                    this.arrMensajeError.push("La cantidad del cod "+articulo.Id_Item+" debe ser mayor a 0");
                }
                if(articulo.CantMinimaVenta > articulo.Cantidad && this.direccion[0].tipo.NoValidaCantMinVenta  == 0){
                    this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.CantMinimaVenta);
                }
                if(this.Is_Float(articulo.Cantidad)){
                    this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.item.listacostosdet.CantMinimaVenta+", debe ser igual o multiplos de esta");
                }
            }
            if(this.arrMensajeError.length==0){
                return true;
            }
            else{
                return false;
            }
        },

        Is_Float(num){
            return !isNaN(num) && Math.round(num) != num;
        },


        AbrirModal(){
            this.modalShow = !this.modalShow;
            if(this.modalShow){
            }
            else{
                this.arrMensajeError=[];
            }
        },

        eliminarDetalle(detalle){
           
            let me = this;
            let url ="/movimiento/EliminarDet";
            Swal.fire({
                title: 'Estas seguro(a) de eliminar '+detalle.item.Descripcion+'?',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: `Eliminar`,
                denyButtonText: `No Eliminar`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put(url,{
                        params:{
                            'nIdMovimientoDet':detalle.IdMovimientoDet
                        }
                    }).then(response=>{    
                        let respuesta = response.data;
                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: respuesta.msg,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        me.ListarMovimiento(detalle.IdMovimiento);

                    }).catch(error =>{
                        console.log(error)
                        if(error.response.status ==401){
                            me.$router.push({name: 'login'})
                            location.reload();
                            sessionStorage.clear();
                            this.fullscreenLoading = false;
                        }
                    })
                }
            })
        },

        NotificarPedido(){
            let url ="/movimiento/notificar";
            axios.put(url,{
                params:{
                    'nIdMovimiento':this.fillMovimiento.nIdMovimiento
                }
            }).then(response=>{    
                let respuesta = response.data;
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1800
                });
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
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
    },
    mounted() {
        this.ListarMovimiento(this.$attrs.id);
        this.listPermisosFilterByRolUser = sessionStorage.getItem('listPermisosFilterByRolUser');
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
        if(this.usuario.Tipo == 2){
            this.OpPedido = 61;
        }
        else{
            this.OpPedido = 8;
        }
        EventBus.$on('agregarDetalleMovimiento',data =>{
            console.log("Se recibio el evento detalle nuevo");
            let Descripcion = data[0].Descripcion;
            console.log(Descripcion)
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: "Se agrego con Exito "+Descripcion,
                showConfirmButton: false,
                timer: 1500
            });
            this.ListarMovimiento(this.$attrs.id);
        });
    },

}
</script>