<template>
    <div>
        <aside class="main-sidebar sidebar-collapse sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <!--<router-link to="/" class="brand-link">
            <img src="/img/kasten/LogoIcono.png" alt="Logo Aba" class="brand-image img-circle elevation-3" style="opacity: .8">
            <img src="/img/kasten/LogoKastenPositivo.png" alt="AdminLTE Docs Logo Large" class="brand-image-xs logo-xl" style="left: 12px">
        </router-link>-->
        <router-link to="/" class="brand-link logo-switch">
            <img src="/img/kasten/LogoIcono.png" alt="AdminLTE Docs Logo Small" class="brand-image-xl logo-xs">
            <img src="/img/kasten/LogoKastenLarge.png" alt="AdminLTE Docs Logo Large" class="brand-image-xs logo-xl" style="left: 12px">
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="/img/kasten/logo_aba_vertical.png" class="img-circle elevation-2 img-navbar-aba" style="height: 2.1rem" alt="User Image"> 
                <!--<template v-if="usuario.imagen ==null">
                    <img src="/img/avatar.png" class="img-circle elevation-2" style="height: 2.1rem" alt="User Image"> 
                </template>
                <template v-else>src="/img/kasten/logo_aba_vertical.png"
                    <img :src="usuario.imagen" class="img-circle elevation-2" style="height: 2.1rem" alt="User Image"> 
                </template>-->
            </div>
            <div class="info">
                <router-link :to="'/'" :usuario="usuario" class="d-block" >
                    Aba científica
                </router-link>
            </div>

        </div>
        <!--<div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a class="d-block logout-form" @click.prevent="logout()" href="#" v-loading.fullscreen.lock="fullscreenloading">
                    <i class="fas fa-sign-out-alt"></i>Cerrar Sesión
                </a>
            </div>
        </div>-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
            <li class="nav-item has-treeview">
                <router-link :to="'/'" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Home</p>
                </router-link>
            </li>
            <template v-if="listPermisos.includes('pedidos.index')">
                <li class="nav-header">MOVIMIENTOS</li>
                <template v-if="listPermisos.includes('pedidos.index')">
                    <li class="nav-item">
                        <router-link class="nav-link" :to="'/pedidos/index'">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p> Pedidos</p>
                        </router-link>
                    </li>
                </template>
            </template>
            <template v-if="listPermisos.includes('usuarios.index','roles.index','permisos.index')">
                <li class="nav-header">ADMINISTRACIÓN</li>
                <template v-if="listPermisos.includes('usuarios.index')">
                    <li class="nav-item">
                        <router-link :to="'/usuarios'" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </router-link>
                    </li>
                </template>
                <template v-if="listPermisos.includes('roles.index')">
                    <li class="nav-item">
                        <router-link :to="'/roles'" class="nav-link">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>Roles</p>
                        </router-link>
                    </li>
                </template>
                
                <template v-if="listPermisos.includes('permisos.index')">
                    <li class="nav-item">
                        <router-link :to="'/permisos'" class="nav-link">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Permisos</p>
                        </router-link>
                    </li>
                </template>

                <template v-if="listPermisos.includes('configuraciondoc.index') || listPermisos.includes('administrador.sistema')">
                    <li class="nav-item">
                        <router-link :to="'/configuracion/documentos'" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Config. Documentos</p>
                        </router-link>
                    </li>
                </template>
            </template>
            <template v-if="listPermisos.includes('reporte.ventas.index') || listPermisos.includes('administrador.sistema') ">
                <li class="nav-header">REPORTES</li>
                <li class="nav-item">
                    <template v-if="listPermisos.includes('reporte.ventas.index') || listPermisos.includes('administrador.sistema')">
                        <router-link :to="'/reportes/ventas'" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reporte Ventas Grafico</p>
                        </router-link>
                        <router-link :to="'/reportes/ventas/grilla'" class="nav-link">
                            <i class="nav-icon fas fa-table"></i>
                            <p>Reporte Ventas Tabla</p>
                        </router-link>
                    </template>
                </li>
            </template>
            <template v-if="listPermisos.includes('chat.index') || listPermisos.includes('administrador.sistema') ">
                <li class="nav-header">CHAT INTERNO</li>
                <router-link :to="'/chat'" class="nav-link">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>Chat</p>
                </router-link>
            </template>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    </div>
</template>
<script>
export default {
    props: ['ruta', 'usuario','listPermisos' ],
    data() {
        return {
            fullscreenloading:false,
            rutas:''
        }
    },
    methods: {
        logout(){
            this.fullscreenLoading = true;
            var url = '/authenticated/logout'
            axios.post(url).then(response => {
                if (response.data.code == 204) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            })
        }
    },
    mounted() {
        this.rutas = this.ruta
    },
}
</script>