<template>
    <div class="login-page">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-group mb-0">
                    <div class="card p-4">
                    <form class="form-horizontal was-validated" method="post">
                        <input type="hidden" name="_token" :value="csrf">
                        <div class="card-body">
                            <center>
                                 <img src="/img/kasten/LogoKastenPositivo.png" height="100px" alt="Logo aba" align="middle">
                            </center>
                        <div class="form-group mb-3">
                            <span class="input-group-addon"><i class="fas fa-user-tie"></i></span>
                            <input type="text"  name="usuario"  class="form-control" @keyup.enter="login()" v-model="fillLogin.cUsuario" placeholder="Usuario">
                            
                        </div>
                        <div class="form-group mb-4">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" id="password" @keyup.enter="login()" class="form-control" v-model="fillLogin.cContrasena" placeholder="Password" required>
                            
                        </div>
                        <div class="row">
                            <div class="col-6">
                            <button class="btn btn-primary px-4" @click.prevent="login()" v-loading.fullscreen.lock="fullscreenloading">Acceder</button><hr>
                            Recuerdame <input type="checkbox" name="recuerda" id="recuerda">
                            </div>
                        </div>
                        </div>
                    </form>
                    </div>
                    <div class="card text-white bg-info py-5 d-none d-md-block" style="width:44%">
                        <div class="card-body text-center">
                            <div>
                                <img src="/img/kasten/02_LogoKastenAlterno.png" style="height: 200px">
                                    <p>Desarrollado por  ABA Cientifica S.A.S todos los derechos reservados</p>
                                <img src="/img/kasten/icono_trasparente.png" style="height: 43px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            error:0,
            mensajeError:[],
            fillLogin:{
                cUsuario:'',
                cContrasena:''
            },
            fullscreenloading:false,
            listaPermisosByUser:[],
            listPermisosFilterByRolUser:[],
        }
    },
    methods: {
        login(){
            const loader = this.loaderk();
            if(this.validarLogin()){
                return;
                loader.close();
            }
            var url ="/authenticated/login";
            axios.post(url,{
                'cUsuario':this.fillLogin.cUsuario,
                'cContrasena':this.fillLogin.cContrasena
            }).then(response=>{
                if(response.data.status == 200){
                    this.getListarRolPermisosByUser(response.data.authUser);
                    //this.filterListRolPermisosByUsuario();
                    sessionStorage.setItem('authUser',JSON.stringify(response.data.authUser));
                    //this.loginSucces();
                    loader.close();
                }
            }).catch((error)=>{
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
                coderror =='' ? msgerror.find(error => error == '401') :msgerror.find(error => error == '419');
                if(coderror == 401 || coderror == 419){
                    sessionStorage.clear();
                    localStorage.clear();
                    location.reload();
                    this.$router.push({name: 'login'})
                }
            })
            loader.close();
        },

        validarLogin(){
            this.error =0;
            this.mensajeError = [];
            if(!this.fillLogin.cUsuario){
                this.mensajeError.push("Debes ingresar un email");
            }
            if(!this.fillLogin.cContrasena){
                this.mensajeError.push("Debes ingresar la contraseña");
            }
            if(this.mensajeError.length){
                this.error =1;
            }
            return this.error;
        },

        loginFailed(){
            this.error =0;
            this.mensajeError = [];
            this.mensajeError.push("Estas credenciales no coinciden con nuestros registros");
            this.fillLogin.cContrasena ='';
            if(this.mensajeError.length){
                this.error =1;
            }
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: "Estas credenciales no coinciden con nuestros registros,intenta de nuevo",
                showConfirmButton: false,
                timer: 1800
            });
            return this.error;
        },
        loginSucces(){
            const loader = this.loaderk();
            this.$router.push({name:'home.index'});
            location.reload();
            loader.close();
        },

        getListarRolPermisosByUser(authUser){
            let url = "/permiso/ObtenerPermisosUsuario";
            axios.get(url,{params:{
                'cUsuario':authUser.Usuario,
                'nIdRol':authUser.IdRol
            }}).then((response)=>{
                let Datos = response.data;
                this.listaPermisosByUser = Datos.permisos;
                this.filterListRolPermisosByUsuario(authUser);
            })
        },

        filterListRolPermisosByUsuario(authUser){
            let me = this;
            me.listaPermisosByUser.map(function(x,y){
                if(x.slug != null){
                    me.listPermisosFilterByRolUser.push(x.slug);
                }
            })
            //console.log(me.listPermisosFilterByRolUser)
            //json.STRINGIFY convierte una cadena de string en un json para dalr esu uso
            localStorage.setItem('listPermisosFilterByRolUser',JSON.stringify(this.listPermisosFilterByRolUser));
            localStorage.setItem('authUser',JSON.stringify(authUser));

            sessionStorage.setItem('listPermisosFilterByRolUser',JSON.stringify(this.listPermisosFilterByRolUser));
            sessionStorage.setItem('authUser',JSON.stringify(authUser));
            this.loginSucces();
        },

        loaderk() {
            return this.$vs.loading({
                type : 'square',
                background: '#babaea',
                color: '#fff',
                text:'Cargando...'
            });
        }

    },

    mounted() {
        
    },
}
</script>