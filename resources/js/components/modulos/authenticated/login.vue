<template>
    <div class="login-page">
        <div><!--class="row justify-content-center"-->
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
                            <input type="text"  name="usuario"  class="form-control"  v-model="fillLogin.cUsuario" placeholder="Usuario">
                            
                        </div>
                        <div class="form-group mb-4">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" id="password"  class="form-control" v-model="fillLogin.cContrasena" placeholder="Password" required>
                            
                        </div>
                        <div class="row">
                            <div class="col-6">
                            <button class="btn btn-primary px-4" @click.prevent="login()" :disabled="!fillLogin.cUsuario || !fillLogin.cContrasena ? true:false">Acceder</button><hr>
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
import servicesApp  from '../../../../js/ServicesApp'
const SERVICES_APP = new servicesApp();
import { mapActions, mapGetters } from 'vuex'
export default {
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            mensajeError:[],
            fillLogin:{
                cUsuario:'',
                cContrasena:''
            },
        }
    },
    methods: {
        async login(){
            sessionStorage.clear();
            localStorage.clear();
            const loader = SERVICES_APP.loader(this,`Hola estamos ${ this.fillLogin.cUsuario } cargando la información ...`)
            if(this.validarLogin()){
                return;
                loader.close();
            }
            const  { authUser , status, statusText} = await SERVICES_APP.login(this.fillLogin.cUsuario,this.fillLogin.cContrasena)
            const { permisos } = await SERVICES_APP.getListarRolPermisosByUser(authUser)
            if(!SERVICES_APP.validarStatusLogin(this,status,statusText)){ loader.close(); return  }

            this.$router.push({name:'home.index'});
            console.log({ authUser , permisos, status, statusText})
            loader.close();
            location.reload();
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

    },

    mounted() {
        
    },
}
</script>