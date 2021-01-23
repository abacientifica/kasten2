import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);
import Home from './components/modulos/home/index.vue';
import Usuarios from './components/modulos/usuarios/index.vue';
import UsuariosPerfil from './components/modulos/usuarios/profile.vue';
import Roles from './components/modulos/roles/index.vue';
import RolesCrear from './components/modulos/roles/create.vue';
import RolesEditar from './components/modulos/roles/edit.vue';
import Permisos from './components/modulos/permisos/index.vue';
import PermisosCrear from './components/modulos/permisos/create.vue';
import PermisosEditar from './components/modulos/permisos/edit.vue';
export default new Router({
    routes: [{
            path: '/',
            component: Home,
            name: 'home.index',
        },
        {
            path: '/usuarios',
            component: Usuarios,
            name: 'usuarios.index',
            props: true,
        },
        {
            path: '/usuario/permisos',
            component: Permisos,
            name: 'usuario.permisos',
        },
        {
            path: '/usuario/perfil/:id',
            component: UsuariosPerfil,
            name: 'usuario.perfil',
            props: true,
        },
        {
            path: '/roles',
            component: Roles,
            name: 'roles.index',
        },
        {
            path: '/roles/crear',
            component: RolesCrear,
            name: 'roles.crear',
        },
        {
            path: '/roles/editar/:id',
            component: RolesEditar,
            name: 'roles.editar',
            props: true,
        },
        {
            path: '/permisos',
            component: Permisos,
            name: 'permisos.index',
        },
        {
            path: '/permiso/crear',
            component: PermisosCrear,
            name: 'permiso.crear',
        },
        {
            path: '/permiso/editar/:id',
            component: PermisosEditar,
            name: 'permiso.editar',
            props: true,
        },

    ],
    mode: 'history',
    linkActiveClass: 'active'
})