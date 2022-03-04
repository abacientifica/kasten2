export default {
    namespaced: true,
    state: {
        permisos: ''
    },
    actions: {
        async SET_PERMISOS({ commit }, permisos) {
            if (permisos) {
                commit('setPermisos', permisos)
            } else {
                commit('setPermisos', null)
            }
        }
    },

    getters: {
        obtPermisos: (state) => {
            return state.permisos;
        }
    },

    mutations: {
        setPermisos(state, permisos) {
            state.permisos = permisos
        },
    },
}