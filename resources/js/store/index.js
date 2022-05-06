import Vue from "vue"
import Vuex from 'vuex'
import PermisosUsuario from './modules/PermisosUsuario'
import Usuario from './modules/Usuario'
import OpcionesModalCot from './modules/cotizaciones/homologar'
Vue.use(Vuex);
export default new Vuex.Store({

    modules: {
        PermisosUsuario,
        Usuario,
        OpcionesModalCot
    },

    state: {
        prueba: ''
    },
})