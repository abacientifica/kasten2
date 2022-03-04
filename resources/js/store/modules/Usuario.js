export default {
    namespaced: true,
    state: {
        usuario: ''
    },
    actions: {
        async SET_USUARIO({ commit }, usuarioAuth) {
            commit('setUsuario', usuarioAuth);
        }
    },

    getters: {
        getUsuario: (state) => {
            return state.usuario;
        }
    },

    mutations: {
        setUsuario(state, user) {
            state.usuario = user
        },
    },
}