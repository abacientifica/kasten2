export default {
    namespaced: true,
    state: {
        permisos: ''
    },
    actions: {
        async SET_PERMISOS({ commit }, user) {
            if (user != null) {
                let permisosActuales = JSON.parse(localStorage.getItem('listPermisosFilterByRolUser'));
                const res = await axios.get('/permiso/ObtenerPermisosUsuario', {
                    params: {
                        'cUsuario': user
                    }
                });
                if (permisosActuales) {
                    let listaPermisosByUser = [];
                    res.data.permisos.map(function(x, y) {
                        if (x.slug != null) {
                            listaPermisosByUser.push(x.slug);
                        }
                    })
                    if (listaPermisosByUser.length != permisosActuales.length) {
                        sessionStorage.setItem('listPermisosFilterByRolUser', JSON.stringify(listaPermisosByUser));
                        localStorage.setItem('listPermisosFilterByRolUser', JSON.stringify(listaPermisosByUser));
                        commit('setPermisos', listaPermisosByUser)
                    } else {
                        commit('setPermisos', permisosActuales)
                    }
                } else if (state.permisos == '') {
                    commit('setPermisos', permisosActuales)
                }
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