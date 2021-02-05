<template>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <template>
                    <div v-if="ImagenProfile!=null">
                      <img class="profile-user-img img-fluid img-circle"
                       :src="ImagenProfile"
                       alt="prueba">
                    </div>
                    <div v-else>
                      <img class="profile-user-img img-fluid img-circle"
                       src="/img/avatar.png"
                       alt="User profile picture">
                    </div>
                  </template>
                  
                </div>

                <h3 class="profile-username text-center" v-text="nombreCompleto"></h3>

                <p class="text-muted text-center" v-text="'Rol '+ NmRolActual"></p><hr>
                <p class="text-muted text-center" v-text="'Cargo '+fillUsuario.cCargo"></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Sobre Mi</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Nombre Completo</strong>

                <p class="text-muted" v-text="nombreCompleto">
                
                </p>

                <hr>

                <strong><i class="fas fa-envelope-open-text"></i> Correo </strong>

                <p class="text-muted" v-text="this.fillUsuario.cCorreo"></p>

                <hr>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Configuración</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="settings">
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombres:</label>
                            <div class="col-md-9">
                              <input type="text"  class="form-control" v-model="fillUsuario.cNombres" placeholder="Primer Nombre "/>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input" >Apellidos:</label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" v-model="fillUsuario.cApellidos"  placeholder="Segundo Nombre"/>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Identificación:</label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" v-model="fillUsuario.nIdtercero" placeholder="Identidad"/>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombre Usuario:</label>
                            <div class="col-md-9">
                              <input type="text" class="form-control" v-model="fillUsuario.cUsuario" placeholder="Usuario" disabled/>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Email :</label>
                            <div class="col-md-9">
                              <input  type="email" v-model="fillUsuario.cCorreo" class="form-control" placeholder="Email"/>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Contraseña:</label>
                            <div class="col-md-9">
                              <el-input placeholder="Ingrese Contraseña" v-model="fillUsuario.cContrasena"  show-password></el-input>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Foto :</label>
                            <div class="col-md-9">
                              <input type="file" id="btn-file" class="form-control" @change="getFile"/>
                            </div>
                          </div>
                          <div class="form-group row">
                            <button type="button" class="btn btn-primary" @click.prevent="ValidarDatos()">
                              Actualizar
                            </button>
                          </div>
                        </form>
                      </div>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    <!-- /.content -->
</template>
<script>
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            fillUsuario: {
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
          ImagenProfile:'',
          error: 0,
          mensajeError: [],
          Form: new FormData(),
          cImagenActual : '',
          NmRolActual :'',
        }
    },
    computed:{
      nombreCompleto:function(){
        return this.fillUsuario.cNombres+' '+this.fillUsuario.cApellidos
      }
    },
    methods: {
        obtenerUsuario(id){
          let url = "/usuarios/getUsuario/"+id;
          axios.get(url).then((response) => {
              let User = response.data.usuario;
              this.fillUsuario.cUsuario = User.Usuario;
              this.fillUsuario.cNombres = User.Nombres;
              this.fillUsuario.cApellidos = User.Apellidos;
              this.fillUsuario.nIdtercero = User.IdTercero;
              this.fillUsuario.cCorreo = User.Email;
              this.fillUsuario.cImagen = User.Imagen;
              this.fillUsuario.cCargo = User.Cargo;
              this.fillUsuario.nIdRol = User.IdRol;
              this.ImagenProfile = User.imagen;
              this.NmRolActual = response.data.rol;
          }).catch(error =>{
              if(error.response.status ==401){
                  this.$router.push({name: 'login'})
                  location.reload();
                  sessionStorage.clear();
                  this.fullscreenLoading = false;
              }
          });
        },

        ObtenerImagenActual(ImagenAct){
           ImagenAct = ImagenAct.split('/');
           this.cImagenActual = (ImagenAct[ImagenAct.length - 1]);
        },
        
        getFile(e) {
            this.fillUsuario.cImagen = e.target.files[0];
            this.CargarImagen(this.fillUsuario.cImagen);
        },

        /**
         * Obtiene la imagen del input file 
         */
        CargarImagen(file){
            //FileReader me permite leer archivos
            let leerImagen = new FileReader();

            //El evento onload se dispara despues de ejecutar readAsDataURL
            leerImagen.onload = (e)=>{
                this.fillUsuario.cImagen = e.target.result;
                this.ImagenProfile = e.target.result;
            }
            leerImagen.readAsDataURL(file);
        },

        ValidarDatos(){
            if(this.validarRegistro()){
                this.ActualizarUsuario();
            }
        },

        ActualizarUsuario() {
            let url = "/usuarios/editar";
            axios.put(url, {
                'fillCrearUsuario' : this.fillUsuario,
            })
            .then((response) => {
                this.getRefrescarUsuarioAutentificado();
                Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Usuario Actualizado',
                showConfirmButton: false,
                timer: 1500
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

        validarRegistro() {
          this.error = true;
          this.mensajeError = [];
          let Datos = this.fillUsuario;

          if (!Datos.cNombres) {
            this.mensajeError.push("El campo Nombres es obligatorio");
          }

          if (!Datos.cApellidos) {
            this.mensajeError.push("El apellido es obligatorio");
          }

          if (!Datos.cUsuario) {
            this.mensajeError.push("El usuario es obligatorio");
          }

          if (!Datos.cCorreo) {
            this.mensajeError.push("El correo es obligatorio");
          }

          if (this.mensajeError.length) {
            this.error = false;
          }
          return this.error;
        },

        getRefrescarUsuarioAutentificado(){
            let url = "/authenticated/getRefrescarUsaurioAutentificado";
            axios.get(url).then((response)=>{
                EventBus.$emit('verrifyAutenticathedUser',response.data)
            }).catch(error =>{
                if(error.response.status ==401){
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },

    },
    mounted() {
        this.obtenerUsuario(this.$attrs.id);
    },
}
</script>