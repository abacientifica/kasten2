<template>
    <div>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Roles</h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Roles</li>
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
                <router-link class="btn btn-info btn-sm" :to="'/roles/crear'">
                    <i class="fas fa-plus-square"></i> Nuevo Rol
                </router-link>
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
                                <input type="text" class="form-control" v-model="fillRol.cNombre" @keyup.enter="getListRoles()"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label">Url Amigable</label>
                              <div class="col-md-9">
                                <input type="text" class="form-control" v-model="fillRol.cUrl" @keyup.enter="getListRoles()"/>
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
                        <button class="btn btn-flat btn-info" @click.prevent="getListRoles()" v-loading.fullscreen.lock="fullscreenLoading">
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
                        <template v-if="listarRolesPaginate.length <= 0">
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
                            <tr v-for="(rol) in listarRolesPaginate" :key="rol.id">
                              <td v-text="rol.nombre"></td>
                              <td v-text="rol.slug"></td>
                              <td>
                                <button class="btn btn-primary btn-sm"  @click="abrirModal(3,rol)">
                                  <i class="fas fa-eye"> </i> Ver
                                </button>
                                <router-link class="btn btn-info btn-sm" :to="'/roles/editar/'+rol.id">
                                    <i class="fas fa-pencil-alt"></i> Editar
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
        <div class="modal fade" :class="{ show: showModal }" :style="showModal ? mostrarModal : ocultarModal" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="callout callout-danger" v-for="(errorm, index) in mensajeError" :key="index" v-text="errorm" style="padding: 5px"></div>
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title" v-text="tituloModal"></h5>
                        <button type="button" class="close" @click.prevent="cerrarModal()" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre:</label>
                                <div class="col-md-9">
                                  <input type="text"  class="form-control" v-model="fillRol.cNombre" placeholder="Nombre Rol" :disabled="accionModal==3?true:false"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input" >Url Amigable:</label>
                                <div class="col-md-9">
                                  <input type="text" class="form-control" v-model="fillRol.cUrl"  placeholder="Url" :disabled="accionModal==3?true:false"/>
                                </div>
                            </div>
                        </form>
                      <h3>Lista Permisos</h3>
                      <div class="card-body table-responsive">
                        <template v-if="permisosRol.length">
                            <div class="scrollTable">
                                <table class="table table-hover table-head-fixed text-nowrap projects">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Url Amigable</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in permisosRol" :key="index">
                                            <td v-text="item.nombre"></td>
                                            <td v-text="item.slug"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        <template v-else>
                            <div class="callout callout-info">
                                <h5>No se encontraron resultados...</h5>
                            </div>
                        </template>
                    </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" @click.prevent="cerrarModal()"> Cerrar</button>
                      <button type="button" v-if="accionModal == 0" class="btn btn-success" v-loading.fullscreen.lock="fullscreenLoading" @click.prevent="registrarRol()">
                        Crear
                      </button>
                      <button type="button" v-if="accionModal == 1" class="btn btn-primary" @click.prevent="ValidarActualizacion()">
                        Actualizar
                      </button>
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
          fillRol: {
            cNombre: "",
            cUrl: "",
          },
          listRoles: [],
          pageNumber: 0,
          perPage: 5,
          tituloModal: "",
          accionModal: 0,
          permisosRol:[],

          fillCrearRol: {
            nId:0,
            cNombre: "",
            cUrl: "",
          },
          mostrarModal: {
              display: "block",
              background: "#0000006b",
          },
          ocultarModal: {
              display: "none",
          },
          error: 0,
          mensajeError: [],
          showModal: false,
          Form: new FormData(),
          fullscreenLoading: false,
      };
  },

  computed: {
    //Obtener el numero de las paginas
    pageCount() {
        let a = this.listRoles.length;
        let b = this.perPage;
        return Math.ceil(a / b);
    },
    //Obtener Registros paginados
    listarRolesPaginate() {
        //0 * 5 =0 inicio
        //1 + 5 = 5 fin
        //0 - (5-1) slice desde hasta

        //1 * 5 = 5 inicio
        //5 + 5 = 10 fin
        //5 - (10-1) slice desde hasta

        let inicio = this.pageNumber * this.perPage;
        let fin = inicio + this.perPage;
        return this.listRoles.slice(inicio, fin);
    },
    pagesList() {
        let a = this.listRoles.length;
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
      this.fillRol.cNombre = "";
      this.fillRol.cUrl ='';
    },

    LimpiarRoles() {
      this.listRoles = [];
    },

    getListRoles() {
      this.fullscreenLoading = true;
      let url = "/roles/lista";
      axios
        .get(url, {
          params: {
            'cNombre': this.fillRol.cNombre,
            'cUrl': this.fillRol.cUrl,
          },
        })
        .then((response) => {
          if(response.data.roles.length >0){
              this.inicializarPagination();
              let RolesResponse = response.data.roles;
              this.listRoles = RolesResponse;
              
          }
          this.fullscreenLoading = false;
        });
        
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

    validarRegistro() {
      this.error = true;
      this.mensajeError = [];
      let Datos = this.fillCrearRol;

      if (!Datos.cNombre) {
        this.mensajeError.push("El campo primer nombre es obligatorio");
      }

      if (!Datos.cUrl) {
        this.mensajeError.push("La url es obligatorio");
      }
      return this.error;
    },

    abrirModal(op,data=[]) {
      this.showModal = true;
      if (op == 3) {
        this.tituloModal = "Rol  "+data.nombre;
        this.fillRol.cNombre = data.nombre;
        this.fillRol.cUrl = data.slug;
        this.accionModal=3;
        this.getListarPermisosByRol(data.id);
      }

      this.modal = 1;
    },

    cerrarModal() {
      this.modal = 0;
      this.showModal = false;
      this.mensajeError = [];
      this.tituloModal = "";
      this.permisosRol =[];

    },


    registrarRol() {
      if (!this.validarRegistro()) {
          this.guardarRol();
      }
    },

    guardarRol(IdImg) {
      let url = "/administracion/roles/registrar";
      axios
        .post(url, {
          cNombre: this.fillCrearRol.cNombre,
          cUrl: this.fillCrearRol.cUrl,
        })
        .then((response) => {
          this.getListRoles();
        });
    },

    getListarPermisosByRol(IdRol){
        var ruta = '/roles/getListarPermisosByRol'
        axios.get(ruta, {
            params: {
              'nIdRol'   :   IdRol,
              'bEdit'   :   false
            }
        }).then( response => {
            this.permisosRol = response.data.permisosbyrol;
        }).catch(error => {
        })
    },

    
    ActualizarRol() {
      let url = "/administracion/usuario/actualizar";
      axios.put(url, {
        nId : this.fillCrearRol.nId,
        cNombre: this.fillCrearRol.cNombre,
        cUrl: this.fillCrearRol.cUrl,
      })
      .then((response) => {
        this.cerrarModal();
        this.getListRoles();
      })
    },

    

    
  },

  mounted() {
    this.getListRoles();
  },
};
</script>
<style>
.scrollTable{
  max-height: 300px !important;
  overflow: auto !important;
}
</style>