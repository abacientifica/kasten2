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
                <img src="/img/kasten/icono_trasparente.png" class="img-circle elevation-2 " style="height: 2.1rem" alt="User Image"> 
                <!--<template v-if="usuario.imagen ==null">
                    <img src="/img/avatar.png" class="img-circle elevation-2" style="height: 2.1rem" alt="User Image"> 
                </template>
                <template v-else>src="/img/kasten/logo_aba_vertical.png"
                    <img :src="usuario.imagen" class="img-circle elevation-2" style="height: 2.1rem" alt="User Image"> 
                </template>-->
            </div>
            <div class="info">
                <router-link :to="'/'" :usuario="usuario" class="d-block" @click="DocSel =  null" >
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
            <li class="nav-item has-treeview" @click="DocSel =  null">
                <router-link :to="'/'" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Home</p>
                </router-link>
            </li>
            <!--<template v-if="listPermisos.includes('pedidos.index')">
                <li class="nav-header">MOVIMIENTOS</li>
                <template v-if="listPermisos.includes('pedidos.index')">
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link class="nav-link" :to="'/pedidos/index'">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p> Pedidos</p>
                        </router-link>
                    </li>
                </template>
            </template>-->
            <li class="nav-header">MOVIMIENTOS</li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link" v-if="NumDocs >0">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                    Documentos
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">{{NumDocs}}</span>
                </p>
                </a>
                <ul class="nav nav-treeview">
                <li  v-for="item in tiposDocs" :key="item.IdTpDocumento" class="nav-item" @click="DocSel = item.IdTpDocumento">
                    <router-link  class="nav-link" :to="'/tpdocumento/lista/'+item.IdTpDocumento"   :class="DocSel ==  item.IdTpDocumento? 'nav-link active': ''">
                        <i class="far fa-circle nav-icon"></i>
                        <p v-text="item.NmTpDocumento"></p>
                    </router-link>
                </li>
                </ul>
            </li>

            <template v-if="listPermisos.includes('usuarios.index','roles.index','permisos.index')">
                <li class="nav-header">ADMINISTRACIÓN</li>
                <template v-if="listPermisos.includes('usuarios.index')">
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link :to="'/usuarios'" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </router-link>
                    </li>
                </template>
                <template v-if="listPermisos.includes('roles.index')">
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link :to="'/roles'" class="nav-link">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>Roles</p>
                        </router-link>
                    </li>
                </template>
                
                <template v-if="listPermisos.includes('permisos.index')">
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link :to="'/permisos'" class="nav-link">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Permisos</p>
                        </router-link>
                    </li>
                </template>

                <template v-if="listPermisos.includes('configuraciondoc.index') || listPermisos.includes('administrador.sistema')">
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link :to="'/configuracion/documentos'" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Config. Documentos</p>
                        </router-link>
                    </li>
                    <li class="nav-item" @click="DocSel =  null">
                        <router-link :to="'/ayudas/index'" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Config. Ayudas</p>
                        </router-link>
                    </li>
                </template>
            </template>

            <template v-if="listPermisos.includes('reporte.ventas.index') || listPermisos.includes('administrador.sistema')  || 1 ">
            <li class="nav-header">REPORTES</li>
                <li class="nav-item" @click="DocSel =  null" v-if="listPermisos.includes('reporte.ventas.grafico') || listPermisos.includes('administrador.sistema') || 1">
                    <router-link :to="'/reportes/ventas'" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reporte Ventas Grafico</p>
                    </router-link>
                </li>
                <li class="nav-item" @click="DocSel =  null" v-if="listPermisos.includes('reporte.ventas.grilla') || listPermisos.includes('administrador.sistema')">
                    <router-link :to="'/reportes/grilla'" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>Reporte Ventas Tabla</p>
                    </router-link>
                </li>
            </template>

            <li class="nav-header" v-if="listPermisos.includes('administrador.sistema')" >UTILIDADES</li>
            <li class="nav-item has-treeview" v-if="listPermisos.includes('administrador.sistema')" >
                <a href="#" class="nav-link" >
                <i class="nav-icon fas fa-copy"></i>
                <p>
                    Utilidades
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">1</span>
                </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link  v-if="listPermisos.includes('administrador.sistema')"  class="nav-link" :to="'/utilidades/inventario'">
                            <i class="far fa-circle nav-icon"></i>
                            <p v-text="'Inventario'"></p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <template v-if="listPermisos.includes('chat.index') || listPermisos.includes('administrador.sistema') ">
                <li class="nav-header">CHAT INTERNO</li>
                <router-link :to="'/chat'" class="nav-link" @click="DocSel =  null">
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
    props: ['ruta', 'usuario','listPermisos'],
    data() {
        return {
            fullscreenloading:false,
            rutas:'',
            tiposDocs:[],
            NumDocs:0,
            DocSel:null
        }
    },
    methods: {
        logout(){
            var url = '/authenticated/logout'
            axios.post(url).then(response => {
                this.$router.push({name: 'login'})
                location.reload();
                sessionStorage.clear();
            })
        },

        getTiposDocumentos(){
            let me = this;
            var url = '/documentos/tipos/lista'
            this.tiposDocs = [];
            me.NumDocs =0;
            axios.get(url).then(response => {
                let respuesta = response.data;
                //me.tiposDocs = respuesta.tpdocumentos;
                respuesta.tpdocumentos.map(function(e){
                    if((e.DirGeneralK2 &&  me.listPermisos.includes(e.DirGeneralK2)) || (e.DirGeneralK2 && me.listPermisos.includes('administrador.sistema'))){
                        me.tiposDocs.push(e);
                        me.NumDocs++;
                    }
                })
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
        this.getTiposDocumentos();
        EventBus.$on('actualizarTpDocs',data =>{
            this.getTiposDocumentos();
        });
    },
}
</script>