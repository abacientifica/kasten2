export default {
    namespaced: true,
    state: {
        usuario: ''
    },
    actions: {
        async SET_USUARIO({ commit }, usuarioAuth) {
            if (!usuarioAuth) {
                const res = await axios.get('/authenticated/getRefrescarUsaurioAutentificado');
                sessionStorage.setItem('authUser', JSON.stringify(res.data));
                commit('setUsuario', res.data)
            } else {
                commit('setUsuario', usuarioAuth);
            }
        }
    },

    getters: {
        obtUsuario: (state) => {
            return state.usuario;
        }
    },

    mutations: {
        setUsuario(state, user) {
            state.usuario = user
        },
    },
}