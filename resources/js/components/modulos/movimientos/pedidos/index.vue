<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pedidos</h1>
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
                <div class="card-header">
                    <template v-if="listPermisosFilterByRolUser.includes('pedidos.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                        <div class="card-tools">
                            <div class="row">
                                <router-link class="btn btn-info btn-sm" :to="'/pedidos/crear/'+this.OpPedido" style="margin-right: 1rem">
                                <i class="fas fa-plus-square"></i> Nuevo Pedido
                                </router-link>
                                <modal :titulo="'Ayudas Pedidos'" :iddoc="this.OpPedido" :url="'pedidos.crear'"></modal>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card card-info">
                        <div class="card-header bg-head ">
                            <h3 class="card-title">Criterios de Busqueda</h3>
                        </div>
                        <div class="card-info">
                            <div class="card-body">
                            <form role="form">
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Nro. Documento</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" v-model="fillMovimiento.nNroDocumento" @keyup.enter="ListarMovimientos()"/>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Estado</label>
                                    <div class="col-md-9">
                                        <el-select v-model="fillMovimiento.cEstado" placeholder="Seleccione un estado" clearable @keyup.enter="ListarMovimientos()">
                                        <el-option v-for="item in listEstados" :key="item.value" :label="item.label" :value="item.value">
                                        </el-option>
                                        </el-select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Rago Fecha</label>
                                    <div class="col-md-9">
                                        <el-date-picker
                                            v-model="fillMovimiento.oRangoFechas"
                                            class="form-control"
                                            type="daterange"
                                            align="right"
                                            unlink-panels
                                            range-separator="A"
                                            start-placeholder="Desde"
                                            end-placeholder="Hasta"
                                            :picker-options="pickerOptions"
                                            format="yyyy-MM-dd"
                                            value-format="yyyy-MM-dd">
                                        </el-date-picker>
                                    </div>
                                    </div>
                                </div>
                                
                                </div>
                            </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                            <div class="col-md4 offset-4">
                                <button class="btn btn-flat btn-info" @click.prevent="ListarMovimientos()" v-loading.fullscreen.lock="fullscreenLoading">
                                Buscar
                                </button>
                                <button class="btn btn-flat btn-default" @click.prevent="LimpiarFiltro()">
                                Limpiar
                                </button>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="card-info">
                        <div class="card-body">
                            <div class="card-header">
                            <h3 class="card-title">Bandeja Resultados</h3>
                            <div class="card-body table-responsive">
                                <template v-if="ListarMovimientosPaginate.length <= 0">
                                <div class="callout callout-info">
                                    <h5>Sin Resultados</h5>
                                </div>
                                </template>
                                <table class="table table-hover table-bordered table-striped table-sm" v-else>
                                <thead class="bg-info">
                                    <tr>
                                    <th class="texto-centrado">Nro</th>
                                    <th class="texto-centrado">Orden Compra</th>
                                    <th class="texto-centrado">Dirección</th>
                                    <th class="texto-centrado">Fecha Pedido</th>
                                    <th class="texto-centrado">Fecha Entrega</th>
                                    <th class="texto-centrado">Total</th>
                                    <th class="texto-centrado">Estado</th>
                                    <th class="texto-centrado">Comentarios</th>
                                    <th class="texto-centrado">Opcion</th>
                                    </tr>
                                </thead>
                                <tbody v-if="ListarMovimientosPaginate.length >0">
                                    <tr v-for="(mov) in ListarMovimientosPaginate" :key="mov.IdMovimiento">
                                        <td class="texto-derecha" v-text="mov.NroDocumento"></td>
                                        <td v-text="mov.Soporte"></td>
                                        <td v-text="mov.direccion.NmDireccion"></td>
                                        <td>{{moment(mov.FhAutoriza).format('MMMM DD YYYY, h:mm:ss a')}}</td>
                                        <td>{{moment(mov.Fecha2).format('MMMM DD YYYY')}}</td>
                                        <td class="texto-derecha" v-text="FormatoMoneda(mov.Total >0 ? mov.Total:0,2)"></td>
                                        <td v-text="mov.Estado"></td>
                                        <td v-text="mov.Comentarios"></td>
                                        <td>
                                            <router-link  :to="'/pedidos/ver/'+mov.IdDocumento+'/'+mov.IdMovimiento" class="btn btn-info btn-sm" v-if="listPermisosFilterByRolUser.includes('pedidos.ver') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
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
                        </div>
                        </div>
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
            Form: new FormData(),
            //Al cambiar a true muestra el loading
            fullscreenLoading:false,
            listPermisosFilterByRolUser:[],
            moment:moment,
            //Objeto usuario para realizar consultas de usuarios.
            fillMovimiento:{
                nNroDocumento:'',
                nIdMovimiento:'',
                nIdTercero:'',
                cEstado:'DIGITADA',
                nIdDireccion:'',
                cFechaDesde:'',
                cFechaHasta:'',
                oRangoFechas:''
            },
            OpPedido:8,
            ImagenPerfil:'',
            //Con este objeto enviados los datos del usuarios nuevo o el que vamos a actualizar
            fillCrearUsuario:{
                cUsuario : '',
                nIdtercero: '',
                cContrasena : '',
                cNombres : '',
                cApellidos : '',
                cCargo:'',
                cCorreo:'',
                nIdRol:'',
                cImagen:'',
            },
            //Contiene la lista de usuarios recuperada
            listMovimientos: [],
            //LLena el combo de estados en el filtro
            listEstados: [
                { value: 'DIGITADA', label: "DIGITADA" },
                { value: 'AUTORIZADA', label: "AUTORIZADA" },
                { value: 'CERRADA', label: "CERRADA" },
                { value: 'ANULADA', label: "ANULADA" },
            ],
            listRoles:[],

            //Inicio de variables de paginacion
            pageNumber: 0,
            perPage: 15,
            //Fin variables paginacion
            tituloModal: "",
            accionModal: 0,
            showModal:false,
            mensajeError:[],
            mostrarModal: {
                display: "block",
                background: "#0000006b",
            },
            ocultarModal: {
                display: "none",
            },
            usuario:[],
            moment:moment,
            pickerOptions: {
                shortcuts: [{
                    text: 'Ult. Semana',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        console.log(start)
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: 'Ult. Mes',
                        onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: 'Ult. 3 Meses',
                        onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            },
        value1: '',
        value2: ''
        }
    },
    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.listMovimientos.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados el valor de 5 se puede cambiar por el deseado
        ListarMovimientosPaginate() {
            //0 * 5 =0 inicio
            //1 + 5 = 5 fin
            //0 - (5-1) slice desde hasta

            //1 * 5 = 5 inicio
            //5 + 5 = 10 fin
            //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.listMovimientos.slice(inicio, fin);
        },
        pagesList() {
            let a = this.listMovimientos.length;
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
         * Obtenemos el listado de usuarios
         */
        ListarMovimientos(IdTercero= this.fillMovimiento.nIdtercero){
            this.fullscreenLoading = true;
            let url ="/movimientos/lista";
            axios.get(url,{params:{
                'nNroDocumento' : this.fillMovimiento.nNroDocumento,
                'nIdMovimiento' : this.fillMovimiento.nIdMovimiento,
                'nIdTercero' : IdTercero,
                'cEstado' : this.fillMovimiento.cEstado,
                'nIdDocumento' : 61,
                'nIdDireccion': this.fillMovimiento.nIdDireccion,
                'cFechaDesde' : this.fillMovimiento.cFechaDesde,
                'cFechaHasta' : this.fillMovimiento.cFechaHasta,
                'oRangoFechas' : this.fillMovimiento.oRangoFechas
            }}).then(response=>{    
                this.inicializarPagination();
                if(response.data.movimientos.length){
                    this.listMovimientos = response.data.movimientos;
                }
                else{
                    this.listMovimientos = [];
                }
                this.fullscreenLoading = false;
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        LimpiarFiltro(){
            this.fillMovimiento.cNombre ='';
            this.fillMovimiento.cUsuario ='';
            this.fillMovimiento.cCorreo ='';
            this.fillMovimiento.cEstado =1;
            this.ListarMovimientos();
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

        ValidarDatos(){
            if(this.validarRegistro()){
                this.ActualizarUsuario();
            }
        },

        /**
         * Valida los campos del formulario editar o registrar
         */
        validarRegistro() {
            this.error = true;
            this.mensajeError = [];
            let Datos = this.fillCrearUsuario;

            if (!Datos.cUsuario) {
                this.mensajeError.push("El usuario es obligatorio");
            }

            if (!Datos.cNombres) {
                this.mensajeError.push("Los nombres son obligatorios");
            }

            if (!Datos.cApellidos) {
                this.mensajeError.push("Los apellidos son obligatorios");
            }

            if (!Datos.cCorreo) {
                this.mensajeError.push("El correo es obligatorio");
            }

            if (!Datos.cContrasena && this.accionModal ==0) {
                this.mensajeError.push("La contraseña es obligatorio");
            }

            if (!Datos.nIdRol) {
                this.mensajeError.push("El rol es obligatorio");
            }

            if (!Datos.cCargo) {
                this.mensajeError.push("El cargo es obligatorio");
            }

            if (this.mensajeError.length) {
                this.error = false;
            }
            return this.error;
        },

        /**
         * Actualiza el usuario
         */
        ActualizarUsuario() {
            let url = "/usuarios/editar";
            axios.put(url, {
                'fillCrearUsuario' : this.fillCrearUsuario,
            })
            .then((response) => {
                this.ListarMovimientos();
                this.cerrarModal();
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

        /**
         * op 1 = usuarios nuevo,2 =editar usuario
         */
        abrirModal(op,data=[]) {
            this.showModal = true;
            if(op = 1){
                this.accionModal = 0;
                this.tituloModal ="Crear Nuevo Usuario";
            }
            if(op = 2){
                this.accionModal = 1;
                this.getListRoles();
                this.tituloModal ="Actualizar Usuario "+ data.Nombres;
                this.fillCrearUsuario.cUsuario = data.Usuario;
                this.fillCrearUsuario.nIdtercero = data.IdTercero;
                this.fillCrearUsuario.cContrasena = data.Contrasena;
                this.fillCrearUsuario.cNombres = data.Nombres;
                this.fillCrearUsuario.cApellidos = data.Apellidos;
                this.fillCrearUsuario.cCargo = data.Cargo;
                this.fillCrearUsuario.cCorreo = data.Email;
                this.fillCrearUsuario.nIdRol = data.IdRol ==0 ? '': data.IdRol;
                this.fillCrearUsuario.cImagen = '';
                this.ImagenPerfil = data.imagen;
            }
        },

        cerrarModal(){
            this.showModal = false;
            this.accionModal = 0;
            this.limpiarFormulario();
        },

        limpiarFormulario(){
            this.fillCrearUsuario.cUsuario = '';
            this.fillCrearUsuario.nIdtercero = '';
            this.fillCrearUsuario.cContrasena = '';
            this.fillCrearUsuario.cNombres = '';
            this.fillCrearUsuario.cApellidos = '';
            this.fillCrearUsuario.cCargo = '';
            this.fillCrearUsuario.cCorreo = '';
            this.fillCrearUsuario.nIdRol = '';
            this.fillCrearUsuario.cImagen = '';
        },

        /**
         * Carga las propiedades del input file
         */
        getFile(e) {
            this.fillCrearUsuario.cImagen = e.target.files[0];
            this.CargarImagen(this.fillCrearUsuario.cImagen);
        },

        /**
         * Obtiene la imagen del input file 
         */
        CargarImagen(file){
            //FileReader me permite leer archivos
            let leerImagen = new FileReader();

            //El evento onload se dispara despues de ejecutar readAsDataURL
            leerImagen.onload = (e)=>{
                this.ImagenPerfil = e.target.result;
                this.fillCrearUsuario.cImagen = this.ImagenPerfil;
            }
            leerImagen.readAsDataURL(file);
        },

        /**
         * Carga los roles del combo rol usuario
         */
        getListRoles() {
            let url = "/roles/lista";
            axios .get(url).then((response) => {
                if(response.data.roles.length >0){
                    this.listRoles = response.data.roles;
                }
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
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
    },
    mounted() {
       
        this.listPermisosFilterByRolUser = sessionStorage.getItem('listPermisosFilterByRolUser');
        this.listPermisosFilterByRolUser == null ? [] : this.listPermisosFilterByRolUser;
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
        if(this.usuario.Tipo == 2){
            this.OpPedido = 61;
            this.fillMovimiento.nIdtercero = this.usuario.IdTercero;
            if(this.usuario.IdDireccion >0){
                this.fillMovimiento.nIdDireccion = this.usuario.IdDireccion;
            }
        }
        else{
            this.OpPedido = 8;
        }
        this.ListarMovimientos(this.fillMovimiento.nIdtercero);
    },

}
</script>