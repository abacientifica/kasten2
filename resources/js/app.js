/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
export const EventBus = new Vue();
window.EventBus = EventBus;
//Vue.component('paginador', require('./components/plantilla/paginador.vue'));
Vue.component('App', require('./components/App.vue').default);
Vue.component('sidebar', require('./components/plantilla/Sidebar.vue').default);
Vue.component('navbar', require('./components/plantilla/Navbar.vue').default);
Vue.component('Footer', require('./components/plantilla/Footer.vue').default);
Vue.component('Auth', require('./components/Auth.vue').default);
Vue.component('NuevoMovimiento', require('./components/plantilla/NuevoMovimiento.vue').default);
Vue.component('AgregarProductosPedido', require('./components/plantilla/AgregarProductosPedido.vue').default);
Vue.component('AgregarProductosMovimiento', require('./components/plantilla/AgregarProductosMovimiento.vue').default);
Vue.component('logacciones', require('./components/plantilla/acciones/logacciones.vue').default);
Vue.component('visualizar-archivo', require('./components/modulos/archivos/verdocumento.vue').default);
Vue.component('ayudas', require('./components/modulos/ayudas/listaAyudas.vue').default);
Vue.component('modal', require('./components/plantilla/modal/modal.vue').default);
Vue.component('homologar-plantillas', require('./components/plantilla/plantillasclientes/Homologar.vue').default);

//Cotizaciones
Vue.component('nuevacotizacion', require('./components/modulos/cotizaciones/create.vue').default);
Vue.component('editarcotizacion', require('./components/modulos/cotizaciones/editar.vue').default);
Vue.component('cot-homologar', require('./components/modulos/cotizaciones/opciones/homologar.vue').default);


import locale from 'element-ui/lib/locale/lang/es';
import router from './routes';

import ElementUI from 'element-ui';

window.Vue.use(ElementUI, { locale }); //Con esta declaracion lo utilizamos en todo el sitio

import 'element-ui/lib/theme-chalk/index.css';
import vSelect from "vue-select";

import Vuex, { mapActions, mapGetters } from 'vuex'
Vue.use(Vuex);

import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css' //Vuesax styles

Vue.use(Vuesax, {
    // options here
})

/**
 * SweetAlert2 - biblioteca para ventanas emergentes
 */
import Swal from 'sweetalert2'
window.Swal = Swal;

import Howler from 'howler'
window.Howler = Howler;

import moment from 'moment';
moment.locale('es');
window.moment = moment;

import VueSessionStorage from 'vue-sessionstorage'
Vue.use(VueSessionStorage)

import store from './store'

import servicesApp from '../../resources/js/ServicesApp'
const SERVICES_APP = new servicesApp();

const app = new Vue({
    el: '#app',
    router,
    store,
    data: {
        //Variables para el cierre de session automatico
        events: ['click', 'mousemove', 'mousedown', 'scroll', 'keypress', 'load'],
        warningTimer: null, //Muestra el mensaje de alerta de cierre de session
        logoutTimer: null, //El tiempo que va esperar si no confirma el cierre de session 
        warningZone: false, // se puede utilizar para mostrar un mensaje de alerta.
        usuario: [],
        permisos: [],
        listaPermisosByUser: [],
        listPermisosFilter: []
    },

    destroyed() {
        this.events.forEach(function(event) {
            window.removeEventListener(event, this.resetTimer);
        }, this);

        this.resetTimer();
    },

    methods: {


        //Inicio metodos para cierre de sesion automatico 
        setTimers: function() {
            //Iniciamos el contador
            this.warningTimer = setTimeout(this.warningMessage, 14 * 60 * 1000); // 14 minutos  60 * 1000
            this.warningZone = false;
        },

        warningMessage: function() {
            if (this.usuario != null) {
                let me = this;
                this.warningZone = true;
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Alerta',
                    text: "Hemos detectado inactividad, deseas cerrar la sesion ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'SI',
                    cancelButtonText: 'NO',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        me.logoutUser();
                        swalWithBootstrapButtons.fire(
                            '',
                            'sesion cerrada con exito.',
                            'success'
                        )
                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        me.resetTimer();
                    }
                })
                this.logoutTimer = setTimeout(this.logoutUser, 14 * 60 * 1000); //14 minutos 14 * 60* 1000
            }
        },

        logoutUser: function() {
            this.logout();
        },

        resetTimer: function() {
            clearTimeout(this.warningTimer);
            clearTimeout(this.logoutTimer);
            this.setTimers();
        },

        logout() {
            var url = '/authenticated/logout'
            axios.post(url).then(response => {
                this.$router.push({ name: 'login' })
                location.reload();
                sessionStorage.clear();
                localStorage.clear();
            })
        },

        RefrescarPermisos() {
            let usuario = JSON.parse(sessionStorage.getItem('authUser'));
            let url = "/permiso/ObtenerPermisosUsuario";
            axios.get(url, {
                params: {
                    'cUsuario': usuario.Usuario,
                }
            }).then((response) => {
                let Datos = response.data;
                //console.log({ Datos })
                this.listaPermisosByUser = Datos.permisos;
                this.filterPermisosByUsuario(Datos.permisos);
            }).catch(error => {
                console.log(error)
                if (error.response.status == 401) {
                    this.$router.push({ name: 'login' })
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        },


        filterPermisosByUsuario(ListPermisos) {
            let me = this;
            let listaPermisos = []
            ListPermisos.map(function(x, y) {
                if (x.slug != null) {
                    listaPermisos.push(x.slug);
                }
            })
            sessionStorage.setItem('listPermisosFilterByRolUser', JSON.stringify(listaPermisos));
            EventBus.$emit("permisosActualizados", JSON.stringify(listaPermisos));
            //EventBus.$emit("notififyRolPermisosByUser", sessionStorage.getItem('listPermisosFilterByRolUser'));
        },
        ...mapActions('Usuario', ['SET_USUARIO']),
        ...mapActions('PermisosUsuario', ['SET_PERMISOS']),
    },

    mounted() {
        //Capturamos cada evento para reiniciar el contador
        this.events.forEach(function(event) {
            window.addEventListener(event, this.resetTimer);
        }, this);
        const user = sessionStorage.getItem('authUser'),
            permisos = sessionStorage.getItem('listPermisosFilterByRolUser')
        this.usuario = user ? JSON.parse(user) : null;
        this.permisos = permisos ? JSON.parse(permisos) : null
        if (this.usuario) {
            this.SET_PERMISOS(this.permisos);
            this.SET_USUARIO(this.usuario)

            Echo.private(`logout.${this.usuario.Usuario}`).listen('LogoutUser', (e) => {
                console.log(e.usuario.mensaje)
                let mensaje = e.usuario.mensaje;
                Swal.fire({
                    position: 'top-center',
                    icon: 'warning',
                    title: 'Alerta !!',
                    text: mensaje,
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        this.logout();
                    }
                })
            });

            Echo.private(`cambiorol.${this.usuario.Usuario}`).listen('CambioRol', (e) => {
                this.RefrescarPermisos();
            });

        } else {
            if (this.$router.history.current.path !== '/login') {
                SERVICES_APP.validateSession(this);
            }
        }
        if (this.$router.history.current.path === '/login' && this.usuario) {
            this.$router.push({ name: 'home.index' })
        }
    },



});