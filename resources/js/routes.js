import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);
import Login from './components/modulos/authenticated/login.vue';

function verificarAcceso(to, from, next) {
    let authUser = JSON.parse(localStorage.getItem('authUser')) || null;
    if (authUser) {
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
            component: () =>
                import ( /*webpackChunkName:"Home"*/ './components/modulos/home/index.vue'),
            name: 'home.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/test',
            component: () =>
                import ( /*webpackChunkName:"Test"*/ './components/modulos/test/index.vue'),
            name: 'test.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuarios',
            component: () =>
                import ( /*webpackChunkName:"Usuarios"*/ './components/modulos/usuarios/index.vue'),
            name: 'usuarios.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/permisos',
            component: () =>
                import ( /*webpackChunkName:"UsuariosPermisos"*/ './components/modulos/usuarios/permission.vue'),
            name: 'usuario.permisos',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/permiso/:id',
            component: () =>
                import ( /*webpackChunkName:"UsuariosPermisosID"*/ './components/modulos/usuarios/permission.vue'),
            name: 'usuario.permiso',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/usuario/perfil/:id',
            component: () =>
                import ( /*webpackChunkName:"UsuarioPerfil"*/ './components/modulos/usuarios/profile.vue'),
            name: 'usuario.perfil',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },
        {
            path: '/roles',
            component: () =>
                import ( /*webpackChunkName:"Roles"*/ './components/modulos/roles/index.vue'),
            name: 'roles.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/roles/crear',
            component: () =>
                import ( /*webpackChunkName:"CrearRol"*/ './components/modulos/roles/create.vue'),
            name: 'roles.crear',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/roles/editar/:id',
            component: () =>
                import ( /*webpackChunkName:"EditarRol"*/ './components/modulos/roles/edit.vue'),
            name: 'roles.editar',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permisos',
            component: () =>
                import ( /*webpackChunkName:"Permisos"*/ './components/modulos/permisos/index.vue'),
            name: 'permisos.index',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permiso/crear',
            component: () =>
                import ( /*webpackChunkName:"CrearPermiso"*/ './components/modulos/permisos/create.vue'),
            name: 'permiso.crear',
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/permiso/editar/:id',
            component: () =>
                import ( /*webpackChunkName:"EditarPermiso"*/ './components/modulos/permisos/edit.vue'),
            name: 'permiso.editar',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        /** RUTAS MOVIMIENTOS **/
        {
            path: '/tpdocumento/lista/:IdTp',
            component: () =>
                import ( /*webpackChunkName:"ListaTpDocs"*/ './components/modulos/tiposdocumentos/index.vue'),
            name: 'tpdocumentos.documento',
            props: true,
        },

        {
            path: '/pedidos/documentos/:tp',
            component: () =>
                import ( /*webpackChunkName:"PedidosDocumentos"*/ './components/modulos/movimientos/pedidos/lista.vue'),
            name: 'pedidos.documentos',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/index',
            component: () =>
                import ( /*webpackChunkName:"PedidosIndex"*/ './components/modulos/movimientos/pedidos/index.vue'),
            name: 'pedidos.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/ver/:iddoc/:id',
            component: () =>
                import ( /*webpackChunkName:"PedidosVer"*/ './components/modulos/movimientos/pedidos/ver.vue'),
            name: 'pedidos.ver',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/pedidos/crear/:iddoc',
            component: () =>
                import ( /*webpackChunkName:"PedidosCrear"*/ './components/modulos/movimientos/pedidos/create.vue'),
            name: 'pedidos.crear',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        //PLANTILLAS CLIENTES

        {
            path: '/plantillas/clientes/index',
            component: () =>
                import ( /*webpackChunkName:"PlantillasClientes"*/ './components/modulos/plantillas/index.vue'),
            name: 'plantillas_clientes.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/plantillas/clientes/crear',
            component: () =>
                import ( /*webpackChunkName:"PlantillasClientesCrear"*/ './components/modulos/plantillas/create.vue'),
            name: 'plantillas_clientes.crear',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/plantillas/clientes/ver/:id',
            component: () =>
                import ( /*webpackChunkName:"PlantillasClientesVer"*/ './components/modulos/plantillas/ver.vue'),
            name: 'plantillas_clientes.ver',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        //RUTAS COTIZACIONES
        {
            path: '/cotizaciones/index',
            component: () =>
                import ( /*webpackChunkName:"Cotizaciones"*/ './components/modulos/cotizaciones/index.vue'),
            name: 'cotizaciones.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/cotizaciones/ver/:id',
            component: () =>
                import ( /*webpackChunkName:"CotizacionesVer"*/ './components/modulos/cotizaciones/ver.vue'),
            name: 'cotizaciones.ver',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/cotizaciones/crear',
            component: () =>
                import ( /*webpackChunkName:"CotizacionesCrear"*/ './components/modulos/cotizaciones/create.vue'),
            name: 'cotizaciones.crear',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },




        //CONFIGURACION DOCUMENTOS
        {
            path: '/configuracion/documentos',
            component: () =>
                import ( /*webpackChunkName:"ConfigurarDocumentos"*/ './components/modulos/configuraciones/configdocumentos.vue'),
            name: 'configuraciondoc.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/configuracion/documentos/edit/:iddoc',
            component: () =>
                import ( /*webpackChunkName:"EditarCamposDocumentos"*/ './components/modulos/configuraciones/vercamposdocumentos.vue'),
            name: 'camposdocumentos.index',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        //REPORTES

        {
            path: '*',
            component: () =>
                import ( /*webpackChunkName:"Pagina404"*/ './components/plantilla/404.vue'),
            name: '404.index'
        },

        {
            path: '/reportes/ventas',
            component: () =>
                import ( /*webpackChunkName:"RepVentas"*/ './components/modulos/reportes/pages/VentasGraficaPage.vue'),
            name: 'reporte.ventas.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },
        {
            path: '/reportes/grilla',
            component: () =>
                import ( /*webpackChunkName:"RepVentasGrilla"*/ './components/modulos/reportes/pages/VentasGeneralPage.vue'),
            name: 'reporte.ventas.grilla.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        /**Ruta del chat */
        {
            path: '/chat',
            component: () =>
                import ( /*webpackChunkName:"Chat"*/ './components/modulos/chat/chat.vue'),
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
            component: () =>
                import ( /*webpackChunkName:"AyudasKasten"*/ './components/modulos/ayudas/index.vue'),
            name: 'ayudas.index',
            props: true,
            beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }
        },

        {
            path: '/ayudas/kasten/:id',
            component: () =>
                import ( /*webpackChunkName:"Ayudas"*/ './components/modulos/ayudas/ayudas.vue'),
            name: 'ayudas.kasten',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        {
            path: '/ayuda/detalles/:id',
            component: () =>
                import ( /*webpackChunkName:"AyudasItems"*/ './components/modulos/ayudas/ayudasitem.vue'),
            name: 'ayuda.detalles',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        //UTILIDADES
        {
            path: '/utilidades/inventario',
            component: () =>
                import ( /*webpackChunkName:"Inventario"*/ './components/modulos/utilidades/inventario/index.vue'),
            name: 'utilidades.inventario',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

        {
            path: '/pruebas/testVue',
            component: () =>
                import ( /*webpackChunkName:"Pruebas"*/ './components/modulos/test/vuetest.vue'),
            name: 'pruebas.vue',
            props: true,
            /*beforeEnter: (to, from, next) => {
                verificarAcceso(to, from, next);
            }*/
        },

    ],
    mode: 'history',
    linkActiveClass: 'active'
})