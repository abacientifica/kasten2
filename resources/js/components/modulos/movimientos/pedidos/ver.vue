<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ver Pedido </h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
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
                    <b class="aling-left" v-text="fillMovimiento.cNmCliente"></b>
                    <div class="card-tools">
                        <div class="row">
                            <div class="btn-group">
                                <template v-if="listPermisosFilterByRolUser.includes('pedidos.crear')">
                                    <template v-if="listPermisosFilterByRolUser.includes('pedidos.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                            <router-link class="btn btn-info btn-sm" :to="'/pedidos/crear/'+this.OpPedido">
                                                <i class="fas fa-plus-square"></i> Nuevo Pedido
                                            </router-link>
                                    </template>
                                </template>
                                <router-link class="btn btn-info btn-sm" :to="'/pedidos/index'">
                                    <i class="fas fa-arrow-left"></i> Regresar
                                </router-link>
                                <modal :titulo="'Ayudas Pedidos'" :iddoc="fillMovimiento.nIdDocumento" :url="'pedidos.crear'"></modal>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 btn-group-justified"  style="display:flex" >
                        <button class="btn btn-success btn-margin-left" v-if="fillMovimiento.cEstado =='AUTORIZADA'" @click.prevent="NotificarPedido()"><i class="fas fa-bell"></i> Enviar Alerta Servicio Cliente</button>
                        <button class="btn btn-primary btn-margin-left" v-if="fillMovimiento.cEstado =='AUTORIZADA'" @click.prevent="setGenerarDocumento()"><i class="fas fa-print"></i> Imprimir</button>
                        <button class="btn btn-warning btn-margin-left" v-if="fillMovimiento.cEstado =='AUTORIZADA' && AptoAnular " @click.prevent="active=!active" ><i class="fas fa-ban"></i> Anular</button>
                        <button class="btn btn-success btn-margin-left" v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==0" @click.prevent="Autorizar()"><i class="fas fa-check"></i> Autorizar</button>
                        <button class="btn btn-primary btn-margin-left" v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==0" @click.prevent="Editar()"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-success btn-margin-left" v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==1" @click.prevent="ActualizarDatos()"><i class="fas fa-check"></i> Guardar Cambios</button>
                        <button class="btn btn-warning btn-margin-left" v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento==1" @click.prevent="Editar()"><i class="fas fa-times-circle"></i> Cancelar Edición</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-if="OcultarPanel" @click.prevent="OcultarMostrarPanel()"><i class="fas fa-eye"></i> Mostrar Encabezado</button>
                        <button class="btn btn-secondary btn-sm btn-margin-left" v-if="!OcultarPanel" @click.prevent="OcultarMostrarPanel()"><i class="fas fa-eye-slash"></i> Ocultar Encabezado</button>
                        <logacciones :IdMovimiento ="fillMovimiento.nIdMovimiento"></logacciones>
                    </div><hr>
                    <div class="form-group row border" v-if="!OcultarPanel">
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>IdMovimiento</label>
                                <!--label muestra el objeto o la columna en el select-->
                                <p class="p-encabezado" v-text="fillMovimiento.nIdMovimiento"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label for="" class='label-strong margen-label-encabezado'>Nro Documento</label>
                                <p class="p-encabezado" v-text="fillMovimiento.nNroDocumento"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha Creación</label>
                                <p class="p-encabezado">{{moment(fillMovimiento.dFecha).format('MMMM DD YYYY, h:mm:ss a')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Condición Entrega</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cNmCondEntrega"></p>
                            </div>
                        </div>

                        <div class="col-md-3 oculto">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha Minima Entrega </label>
                                <p class="p-encabezado" >{{moment(fillMovimiento.dFecha1).format('MMMM DD YYYY')}}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Fecha  Entrega </label>
                                <p class="p-encabezado">{{moment(fillMovimiento.dFecha2).format('MMMM DD YYYY')}}</p>
                            </div>
                        </div>

                        

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Estado</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cEstado"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Soporte</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cSoporte"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong'>Centro Costos / Dirección</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cNmDireccion"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Asesor</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cNmAsesor"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Asesor Serv. Cliente</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cNmAsesorServC"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Forma Pago</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cNmFormaPago"></p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group margen-form-item">
                                <label class='label-strong margen-label-encabezado'>Comentarios</label>
                                <p class="p-encabezado" v-text="fillMovimiento.cComentarios"></p>
                            </div>
                        </div>
                    </div>
                    <AgregarProductosMovimiento v-if="fillMovimiento.cEstado == 'DIGITADA' && accionMovimiento ==1" :IdTercero="fillMovimiento.nIdTercero" :IdDireccion="fillMovimiento.nIdDireccion" :IdMovimiento="fillMovimiento.nIdMovimiento" :arrayDetallesMovimientoAct="ListarMovimientosDetPaginate"></AgregarProductosMovimiento>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm" >
                                <thead class="bg-info">
                                    <tr>
                                        <th v-if="accionMovimiento==1">Opción</th>
                                        <th class="texto-centrado">Codigo Aba</th>
                                        <th class="texto-centrado">Descripción</th>
                                        <th class="texto-centrado">Referencia</th>
                                        <th class="texto-centrado">Marca</th>
                                        <th class="texto-centrado">UMV</th>
                                        <th class="texto-centrado">Invima</th>
                                        <th class="texto-centrado">Precio</th>
                                        <th class="texto-centrado">Cantidad</th>
                                        <th class="texto-centrado">Iva</th>
                                        <th class="texto-centrado">Sub total</th>
                                    </tr>
                                </thead>
                                <tbody v-if="ListarMovimientosDetPaginate.length">
                                    <tr v-for="(detalle) in ListarMovimientosDetPaginate" :key="detalle.id">
                                        <td v-if="accionMovimiento==1">
                                            <button type="button" @click="eliminarDetalle(detalle)" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </td>
                                        <td class="texto-derecha" v-text="detalle.Id_Item"></td>
                                        <td v-text="detalle.item.Descripcion"></td>
                                        <td v-text="detalle.item.listacostosdet.RefFabricante"></td>
                                        <td v-text="detalle.item.listacostosdet.marca.NmMarca"></td>
                                        <td v-text="detalle.item.UMM"></td>
                                        <td v-text="detalle.item.listacostosdet.RegInvima"></td>
                                        <td class="texto-derecha" v-text="FormatoMoneda(detalle.Precio,2)"></td>
                                        <td class="texto-derecha" v-text="detalle.Cantidad" v-if="accionMovimiento==0"></td>
                                        <td v-else><input type="number" v-model="detalle.Cantidad" class="form-control" :style="detalle.Cantidad <= 0 || detalle.Cantidad < 1  ||  Is_Float(detalle.Cantidad / detalle.item.listacostosdet.CantMinimaVenta) ? 'border: 2px solid red;':''"></td>
                                        <td class="texto-derecha" v-text="detalle.PorIva"></td>
                                        <td class="texto-derecha" v-text="FormatoMoneda((detalle.Precio * detalle.Cantidad),2)"> </td>
                                    </tr>
                                    
                                    
                                    <tr style="background-color: rgb(196 196 245);">
                                        <td :colspan="accionMovimiento == 1?'10':'9'" align="right"><strong>Sub Total:</strong></td>
                                        <td class="texto-derecha">${{FormatoMoneda((fillMovimiento.nTotal = calcularTotal),2)}}</td>
                                    </tr>

                                    <tr style="background-color: rgb(196 196 245);">
                                        <td :colspan="accionMovimiento == 1?'10':'9'" align="right"><strong>Total Iva:</strong></td>
                                        <td class="texto-derecha">${{FormatoMoneda(this.fillMovimiento.nVrIva ,2)}}</td>
                                    </tr>

                                    <tr style="background-color: rgb(196 196 245);">
                                        <td :colspan="accionMovimiento == 1?'10':'9'" align="right"><strong>Total:</strong></td>
                                        <td class="texto-derecha">${{FormatoMoneda((fillMovimiento.nTotal + this.fillMovimiento.nVrIva),2)}}</td>
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
                        <button class="btn btn-secondary" @click="AbrirModal" v-loading.fullscreen.lock="fullscreenLoading">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <div class="center">
            <vs-dialog v-model="active">
                <template #header>
                <h4 class="not-margin">
                    Hola {{usuario.Nombres}}
                </h4>
                </template>


                <div class="con-form">
                    <textarea class="form-control" v-model="msgAnulacion" placeholder="Describe la causa de la anulación...">
                    
                    </textarea>
                </div>
                <vs-button block warn :disabled="msgAnulacion!=''?false:true" @click.prevent="AnularMovimiento()">
                    Anular
                </vs-button>
            </vs-dialog>
            </div>
        </template>

    </div>
    
</template>
<script>
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            usuario:'',
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
                cNmAsesorServC:'',
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
            moment:moment,
            fullscreenLoading:false,
            active:false,
            msgAnulacion:'',
            AptoAnular:true,
            MovActual:this.$attrs.id,
            //Panel oculto
            OcultarPanel:false
        }
    },
    computed: {
        calcularTotal: function(){
            var resultado =0;
            var porIva = 0;
            if(this.accionMovimiento!=0){
                for(var i=0;i< this.ListarMovimientosDetPaginate.length;i++){
                    var objeto = this.ListarMovimientosDetPaginate[i];
                    porIva = porIva + (((objeto['Cantidad'] * objeto['Precio']) * objeto['PorIva'])/100);
                    resultado = resultado + (objeto['Cantidad'] * objeto['Precio']);
                }
            }
            else{
                var objeto = this.fillMovimiento;
                porIva = objeto['nVrIva'] ;
                resultado = objeto['nSubTotal'];
            }
            for(var i=0;i< this.ListarMovimientosDetPaginate.length;i++){
                var objeto = this.ListarMovimientosDetPaginate[i];
                if((objeto['Cantidad'] - objeto['CantAfectada']) != objeto['Cantidad']){
                    this.AptoAnular = false;
                    break;
                }
            }
            this.fillMovimiento.nVrIva = porIva;
            return resultado;
        },
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
        mensajeAnular(){
            if(!this.active){
                this.msgAnulacion ='';
            }
            return this.msgAnulacion;
        }

    },
    methods: {

        /**
         * Obtenemos el listado de detalles
         */
        ListarMovimiento(IdMov,IdDoc){
            let url ="/movimiento/"+IdDoc+'/'+this.MovActual;
            let me = this;
            const loader = this.loaderk();
            axios.get(url).then(response=>{    
                this.inicializarPagination();
                if(response.data.msg == "no_encontrado"){
                    loader.close();
                    me.$router.push('/*');
                    return;
                }
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
                    this.fillMovimiento.cNmDireccion = Datos.direccion.NmDireccion+' ('+Datos.direccion.Direccion+' )';
                    this.fillMovimiento.cNmCondEntrega = Datos.condentrega.NmCondicionEntrega;
                    this.fillMovimiento.cNmFormaPago = Datos.fpago.FormaPago;
                    this.fillMovimiento.cNmAsesor = Datos.asesor.Nombre;
                    this.fillMovimiento.cNmAsesorServC = Datos.tercero.asesorservcliente.Nombre+" "+Datos.tercero.asesorservcliente.Email;
                    this.fillMovimiento.cComentarios = Datos.Comentarios == '' ? 'SIN COMENTARIOS' : Datos.Comentarios;
                    this.fillMovimiento.nVrIva = Datos.VrIva;
                    this.fillMovimiento.nSubTotal = Datos.SubTotal;
                    this.fillMovimiento.nTotal = Datos.Total;
                    this.fillMovimiento.cNmCliente = Datos.tercero.NombreCorto;
                    this.fillDetallesMov = response.data.movimientos_det;
                    this.CargarDireccion(Datos.IdDireccion);
                    loader.close();
                }
                else{
                    loader.close();
                    this.listMovimientos = [];
                }
            }).catch(error =>{
                loader.close();
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
            const loader = this.loaderk();
            axios.put(url,{
                params:{
                    'nIdMovimiento':me.fillMovimiento.nIdMovimiento
                }
            }).then(response=>{    
                let respuesta = response.data;
                loader.close();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: respuesta.msg,
                    showConfirmButton: false,
                    timer: 1500
                });

                me.ListarMovimiento(me.fillMovimiento.nIdMovimiento,me.fillMovimiento.nIdDocumento);
                

            }).catch(error =>{
                console.log(error.response.data.line)
                if(error.response.status ==401){
                    me.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
                if(error.response.status == 500 || error.response.status == 501){
                    loader.close();
                    let message = error.response.data.message;
                    let linea = error.response.data.line;
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Ups.. Ocurrio un error al autorizar el movimiento, intenta nuevamente.",
                        text :message + 'linea : '+ linea ,
                        showConfirmButton: true
                    });
                }
            })
        },

        Editar(){
            this.accionMovimiento = !this.accionMovimiento;
        },

        ActualizarDatos(){
            let me = this;
            const loader = this.loaderk();
            !this.ValidarDatos() ? loader.close() : '';
            if(this.arrMensajeError.length <=0){
                axios.put('/movimiento/editar',{
                    params:{
                        'nIdMovimiento':this.fillMovimiento.nIdMovimiento,
                        'arraryDetallesMovimiento':this.fillDetallesMov
                    }
                }).then(function (response) {
                    var respuesta = response.data;
                    loader.close();
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: respuesta.msg,
                        showConfirmButton: false,
                        timer: 1300
                    });
                    me.ListarMovimiento(me.fillMovimiento.nIdMovimiento,me.fillMovimiento.nIdDocumento);
                    me.Editar();
                })
                .catch(function (error) {
                    console.log(error);
                    loader.close();
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

                if(articulo.CantMinimaVenta > articulo.Cantidad && this.direccion[0].tipo.NoValidaCantMinVenta  == 0 && articulo.listaprecios.NoValidaCantMinVenta ==0){
                    this.arrMensajeError.push("La cantidad minima de venta del cod "+articulo.Id_Item+" es "+articulo.CantMinimaVenta);
                }

                if(articulo.Cantidad >0  && this.Is_Float((articulo.Cantidad / articulo.item.listacostosdet.CantMinimaVenta))){
                    if(articulo.listaprecios.NoValidaCantMinVenta ==0){
                        this.arrMensajeError.push("La cantidad minima de venta es "+articulo.item.listacostosdet.CantMinimaVenta+", debe ser igual o multiplos de esta");
                    }
                    else{
                        this.arrMensajeError.push("La cantidad debe ser un numero entero.");
                    }
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
                        me.ListarMovimiento(detalle.IdMovimiento,me.fillMovimiento.nIdDocumento);

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
            
            amount = (amount == null) ? 0 : amount;
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

        setGenerarDocumento(){
            const loader = this.loaderk();
            var config = {
                responseType : 'blob',
            }

            axios.post('/movimiento/setGenerarDocumento',{
                'nIdMov':this.fillMovimiento.nIdMovimiento,
                'nIdDoc':this.fillMovimiento.nIdDocumento,
            },config).then(function (response) {
                
                var oMyBlob = new Blob([response.data], {type : 'application/pdf'}); // the blob
                var url = URL.createObjectURL(oMyBlob);
                window.open(url);
                loader.close();
                //console.log(url);
            })
            .catch(function (error) {
                loader.close();
                if (error.response.data.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
            });
        },

        AnularMovimiento(){
            const loader = this.loaderk();
            let me =this;
            axios.put('/movimiento/anularMovimiento',{
                'nIdMov':me.fillMovimiento.nIdMovimiento,
                'nIdDoc':me.fillMovimiento.nIdDocumento,
                'Comentarios': me.msgAnulacion,
            }).then(function (response) {
                me.active = false;
                me.ListarMovimiento(me.fillMovimiento.nIdMovimiento,me.fillMovimiento.nIdDocumento);
                Swal.fire({
                    position: 'top-center',
                    icon: response.data.status == 200 ? 'success' : 'danger',
                    title: response.data.msg,
                    showConfirmButton: false,
                    timer: 1500
                });
                loader.close();
                
            })
            .catch(function (error) {
                loader.close();
                console.log(error)
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    loader.close();
                }
            });
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
            });
        },

        OcultarMostrarPanel(){
            this.OcultarPanel = !  this.OcultarPanel;
            localStorage.setItem('panelOcultoPed', this.OcultarPanel);
        },
    },
    mounted() {
        this.ListarMovimiento(this.$attrs.id,this.$attrs.iddoc);
        this.listPermisosFilterByRolUser = localStorage.getItem('listPermisosFilterByRolUser');
        this.usuario = JSON.parse(localStorage.getItem('authUser'));
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
            this.ListarMovimiento(this.$attrs.id,this.$attrs.iddoc);
        });
        let PanelOculto = localStorage.getItem('panelOcultoPed');
        if(PanelOculto){
            this.OcultarPanel = PanelOculto
        }
    },
    
}
</script>