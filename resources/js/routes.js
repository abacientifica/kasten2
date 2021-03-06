import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);
import Home from './components/modulos/home/index.vue';
import Usuarios from './components/modulos/usuarios/index.vue';
import Login from './components/modulos/authenticated/login.vue';
import UsuariosPerfil from './components/modulos/usuarios/profile.vue';
import UsuarioPermiso from './components/modulos/usuarios/permission.vue';
import Roles from './components/modulos/roles/index.vue';
import RolesCrear from './components/modulos/roles/create.vue';
import RolesEditar from './components/modulos/roles/edit.vue';
import Permisos from './components/modulos/permisos/index.vue';
import PermisosCrear from './components/modulos/permisos/create.vue';
import PermisosEditar from './components/modulos/permisos/edit.vue';
import PedidosDocumentos from './components/modulos/movimientos/pedidos/lista.vue';
import PedidosIndex from './components/modulos/movimientos/pedidos/index.vue';
import PedidosVer from './components/modulos/movimientos/pedidos/ver.vue';
import PedidosCrear from './components/modulos/movimientos/pedidos/create.vue';
import RepVentas from './components/modulos/reportes/ventas.vue';
import RepVentasGrilla from './components/modulos/reportes/ventasgeneral.vue';
import Chat from './components/modulos/chat/chat.vue';
import EditarCamposDocumentos from './components/modulos/configuraciones/vercamposdocumentos.vue';
import ListaDocTpDocumentos from './components/modulos/tiposdocumentos/index.vue';

//Plantillas Clientes
import PlantillasClientes from './components/modulos/plantillas/index.vue';
import PlantillasClientesCrear from './components/modulos/plantillas/create.vue';
import PlantillasClientesVer from './components/modulos/plantillas/ver.vue';

//rutas configuracion documentos
import ConfigurarDocumentos from './components/modulos/configuraciones/configdocumentos.vue';

//Ayudas Kasten
import AyudasKasten from './components/modulos/ayudas/index.vue';
import Ayudas from './components/modulos/ayudas/ayudas.vue';
import AyudasItems from './components/modulos/ayudas/ayudasitem.vue';

//Pagina 404
import Pagina404 from './components/plantilla/404.vue';
import TestSistemas from './components/modulos/test/index.vue';

function verificarAcceso(to, from, next) {
    let authUser = JSON.parse(localStorage.getItem('authUser'));
    if (authUser && validUsuario()) {
        let listaPermisosByUser = JSON.parse(localStorage.getItem('listPermisosFilterByRolUser'));
        if (listaPermisosByUser.includes(to.name) || listaPermisosByUser.includes('administrador.sistema') || to.name == 'home.index') {
            next();
        } else {
            let listRolPermisosByUsuarioFilter = [];
            listaPermisosByUser.map(function(x) {
                if (x.includes('index')) {
                    listRolPermisosByUsuarioFilter.push(x);
                }
            })
            if (to.name == 'home.index') {
                next({ name: listRolPermisosByUsuarioFilter[0] })
            } else {
                next(from.path);
            }
        }
        next();
    } else {
        next('/login')
    }
}

function validUsuario() {
    let user = true;
    axios.get('/authenticated/getRefrescarUsaurioAutentificado').then(function(response) {
        if (response.data.length > 0) {
            user = true;
        }
    }).catch((error) => {
        let msgerror = error.message.split(" ");
        let coderror = msgerror.find(error => error == '401') ? msgerror.find(error => error == '401') : msgerror.find(error => error == '419');
        if (coderror == 401 || coderror == 419) {
            sessionStorage.clear();
            localStorage.clear();
        }
        user = false;
    });
    return user;
}

export default new Router({
    routes: [{
            path: '/login',
            component: Login,
            name: 'login',
            beforeEnter: (to, from, next) => {
                let authUser = JSON.parse(sessionStorage.getItem('authUser'));
                if (authUser) {
                    next({ name: 'home.index' });
                } else {
                    next();
                }
            }
        },

        {
            path: '/',
            component: Home,
            name: 'home.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/test',
            component: TestSistemas,
            name: 'test.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuarios',
            component: Usuarios,
            name: 'usuarios.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/permisos',
            component: UsuarioPermiso,
            name: 'usuario.permisos',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/permiso/:id',
            component: UsuarioPermiso,
            name: 'usuario.permiso',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/perfil/:id',
            component: UsuariosPerfil,
            name: 'usuario.perfil',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },
        {
            path: '/roles',
            component: Roles,
            name: 'roles.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/roles/crear',
            component: RolesCrear,
            name: 'roles.crear',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/roles/editar/:id',
            component: RolesEditar,
            name: 'roles.editar',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permisos',
            component: Permisos,
            name: 'permisos.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permiso/crear',
            component: PermisosCrear,
            name: 'permiso.crear',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permiso/editar/:id',
            component: PermisosEditar,
            name: 'permiso.editar',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        /** RUTAS MOVIMIENTOS **/
        {
            path: '/tpdocumento/lista/:IdTp',
            component: ListaDocTpDocumentos,
            name: 'tpdocumentos.documento',
            props: true,
        },

        {
            path: '/pedidos/documentos/:tp',
            component: PedidosDocumentos,
            name: 'pedidos.documentos',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/index',
            component: PedidosIndex,
            name: 'pedidos.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/ver/:iddoc/:id',
            component: PedidosVer,
            name: 'pedidos.ver',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/crear/:iddoc',
            component: PedidosCrear,
            name: 'pedidos.crear',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        //PLANTILLAS CLIENTES

        {
            path: '/plantillas/clientes/index',
            component: PlantillasClientes,
            name: 'plantillas_clientes.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/plantillas/clientes/crear',
            component: PlantillasClientesCrear,
            name: 'plantillas_clientes.crear',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/plantillas/clientes/ver/:id',
            component: PlantillasClientesVer,
            name: 'plantillas_clientes.ver',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        //CONFIGURACION DOCUMENTOS
        {
            path: '/configuracion/documentos',
            component: ConfigurarDocumentos,
            name: 'configuraciondoc.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/configuracion/documentos/edit/:iddoc',
            component: EditarCamposDocumentos,
            name: 'camposdocumentos.index',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        //REPORTES

        {
            path: '*',
            component: Pagina404,
            name: '404.index'
        },

        {
            path: '/reportes/ventas',
            component: RepVentas,
            name: 'reporte.ventas.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/reportes/ventas/grilla',
            component: RepVentasGrilla,
            name: 'reporte.ventas.grilla.index',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        /**Ruta del chat */
        {
            path: '/chat',
            component: Chat,
            name: 'chat.index',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        //Ayudas Kasten
        /**Ruta del chat */
        {
            path: '/ayudas/index',
            component: AyudasKasten,
            name: 'ayudas.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/ayudas/kasten/:id',
            component: Ayudas,
            name: 'ayudas.kasten',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        {
            path: '/ayuda/detalles/:id',
            component: AyudasItems,
            name: 'ayuda.detalles',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },
    ],
    mode: 'history',
    linkActiveClass: 'active'
})