<template>
    <div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Permiso</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Permisos</li>
                </ol>
            </div>
            <!-- /.col -->
            </div>
            <!-- Final /.row -->
        </div>
    </div>
    <div class="content container-fluid">
        <div class="card">
            <div class="card-header">
            <div class="card-tools">
                <template v-if="listPermisosFilterByRolUser.includes('permiso.crear') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                    <router-link class="btn btn-info btn-sm" :to="'/permiso/crear'">
                        <i class="fas fa-plus-square"></i> Nuevo Permiso
                    </router-link>
                </template>
            </div>
            </div>
            <div class="card-body">
            <div class="container-fluid">
                <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Criterios de Busqueda</h3>
                </div>
                <div class="card-info">
                    <div class="card-body">
                    <form role="form">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                            <label class="col-md-3 col-form-label">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" v-model="fillBusqPermiso.cNombre" @keyup.enter="getListarPermisos()"/>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                            <label class="col-md-3 col-form-label">Url Amigable</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" v-model="fillBusqPermiso.cUrl" @keyup.enter="getListarPermisos()"/>
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
                        <button class="btn btn-flat btn-info" @click.prevent="getListarPermisos()" v-loading.fullscreen.lock="fullscreenLoading">
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
                        <template v-if="listPermisos.length <= 0">
                        <div class="callout callout-info">
                            <h5>Sin Resultados</h5>
                        </div>
                        </template>
                        <table
                        class="table table-hover table-head-fixed text-nowrap projects"
                        v-else
                        >
                        <thead>
                            <tr>
                            <th>Nombre</th>
                            <th>Url Amigable</th>
                            <th>Operaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(permiso) in listarPermisoPaginate" :key="permiso.id">
                            <td v-text="permiso.nombre"></td>
                            <td v-text="permiso.slug"></td>
                            <td>
                                <template v-if="listPermisosFilterByRolUser.includes('permiso.editar') || listPermisosFilterByRolUser.includes('administrador.sistema')">
                                    <router-link class="btn btn-info btn-sm" :to="'/permiso/editar/'+permiso.id">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </router-link>
                                </template>
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
            modal: 0,
            fillBusqPermiso: {
                cNombre: "",
                cUrl: "",
            },
            pageNumber: 0,
            perPage: 5,
            listPermisos:[],
            fullscreenLoading:false,
            listPermisosFilterByRolUser:[],
        };
  },

    computed: {
        //Obtener el numero de las paginas
        pageCount() {
            let a = this.listPermisos.length;
            let b = this.perPage;
            return Math.ceil(a / b);
        },
        //Obtener Registros paginados
        listarPermisoPaginate() {
        //0 * 5 =0 inicio
        //1 + 5 = 5 fin
        //0 - (5-1) slice desde hasta

        //1 * 5 = 5 inicio
        //5 + 5 = 10 fin
        //5 - (10-1) slice desde hasta

            let inicio = this.pageNumber * this.perPage;
            let fin = inicio + this.perPage;
            return this.listPermisos.slice(inicio, fin);
        },
        pagesList() {
            let a = this.listPermisos.length;
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
        LimpiarFiltro() {
            this.fillBusqPermiso.cNombre = "";
            this.fillBusqPermiso.cUrl ='';
        },

        LimpiarRoles() {
            this.listPermisos = [];
        },

        getListarPermisos() {
            this.fullscreenLoading = true;
            let url = "/permisos/lista";
            axios
                .get(url, {
                params: {
                    'cNombre': this.fillBusqPermiso.cNombre,
                    'cUrl': this.fillBusqPermiso.cUrl,
                },
                })
                .then((response) => {
                    this.inicializarPagination();
                    let permisos = response.data.permisos;
                    this.listPermisos = permisos;
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

        nextPage() {
            this.pageNumber++;
        },

        pagePrev() {
            this.pageNumber--;
        },

        selectPage(page) {
            this.pageNumber = page;
        },

        inicializarPagination() {
            this.pageNumber = 0;
        },
    },

    mounted() {
        this.getListarPermisos();
        this.listPermisosFilterByRolUser = sessionStorage.getItem('listPermisosFilterByRolUser');
    },
}
</script>
<style>
.scrollTable{
  max-height: 300px !important;
  overflow: auto !important;
}
</style>