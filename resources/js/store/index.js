import Vue from "vue"
import Vuex from 'vuex'
import PermisosUsuario from './modules/PermisosUsuario'
import Usuario from './modules/Usuario'
Vue.use(Vuex);
export default new Vuex.Store({

    modules: {
        PermisosUsuario,
        Usuario,
    },

    state: {
        prueba: ''
    },
})