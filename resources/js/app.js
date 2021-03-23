/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
export const EventBus = new Vue();
window.EventBus = EventBus;
Vue.component('App', require('./components/App.vue').default);
Vue.component('sidebar', require('./components/plantilla/Sidebar.vue').default);
Vue.component('navbar', require('./components/plantilla/Navbar.vue').default);
Vue.component('Footer', require('./components/plantilla/Footer.vue').default);
Vue.component('Auth', require('./components/Auth.vue').default);
Vue.component('NuevoMovimiento', require('./components/plantilla/NuevoMovimiento.vue').default);
Vue.component('AgregarProductosPedido', require('./components/plantilla/AgregarProductosPedido.vue').default);
Vue.component('AgregarProductosMovimiento', require('./components/plantilla/AgregarProductosMovimiento.vue').default);
import router from './routes';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import vSelect from "vue-select";

window.Vue.use(ElementUI); //Con esta declaracion lo utilizamos en todo el sitio

/**
 * SweetAlert2 - biblioteca para ventanas emergentes
 */
import Swal from 'sweetalert2'
window.Swal = Swal;

const app = new Vue({
    el: '#app',
    router,

    data: {
        //Variables para el cierre de session automatico
        events: ['click', 'mousemove', 'mousedown', 'scroll', 'keypress', 'load'],
        warningTimer: null, //Muestra el mensaje de alerta de cierre de session
        logoutTimer: null, //El tiempo que va esperar si no confirma el cierre de session 
        warningZone: false, // se puede utilizar para mostrar un mensaje de alerta.
        usuario: []
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
            this.fullscreenLoading = true;
            var url = '/authenticated/logout'
            axios.post(url).then(response => {
                if (response.data.code == 204) {
                    this.$router.push({ name: 'login' })
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    this.$router.push({ name: 'login' })
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        }
    },

    mounted() {
        //Capturamos cada evento para reiniciar el contador
        this.events.forEach(function(event) {
            window.addEventListener(event, this.resetTimer);
        }, this);
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
    },



});