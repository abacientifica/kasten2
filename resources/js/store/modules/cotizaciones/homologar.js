export default {
    namespaced: true,
    state: {
        modalHomologarCotDet: false,
        itemSel: null,
        itemsActual: null
    },
    actions: {
        abrirModal({ commit }, ...params) {
            commit('SET_MODAL_COT', [true, params])
        },

        cerrarModal({ commit }) {
            commit('SET_MODAL_COT', [false])
        }
    },
    mutations: {
        SET_MODAL_COT(state, params) {
            const [opcion, arrDetalles] = params;
            if (opcion) {
                const [item, detalles] = arrDetalles[0];
                state.itemSel = item;
                state.itemsActual = detalles;
            } else {
                state.itemSel = null;
                state.itemsActual = null;
            }
            state.modalHomologarCotDet = opcion;
        }
    },
    getters: {
        opcionDialog(state) {
            return state;
        }
    }
}