<template>
    <div>
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <template v-if="listPermisos.includes('home.index')">
                <li class="nav-item d-none d-sm-inline-block">
                    <router-link class="nav-link" :to="{name: 'home.index'}">Inicio</router-link>
                </li>
            </template>
            <template v-if="listPermisos.includes('pedido.index')">
                <li class="nav-item d-none d-sm-inline-block">
                    <router-link class="nav-link" :to="{name: 'pedido.index'}">Pedido</router-link>
                </li>
            </template>
            </ul>

            <!-- SEARCH FORM -->
            <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <el-autocomplete
                    class="inline-input"
                    v-model="cBusqueda"
                    :fetch-suggestions="querySearch"
                    placeholder="Buscar ..."
                    :trigger-on-focus="false"
                    size="small"
                    @select="handleSelect">
                    <i class="el-icon-search el-input__icon" slot="suffix" ></i>
                </el-autocomplete>
            </div>
            </form>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                    <img src="/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                        Brad Diesel
                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                        </h3>
                        <p class="text-sm">Call me whenever you can...</p>
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                    </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                    <img src="/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                        John Pierce
                        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                        </h3>
                        <p class="text-sm">I got your message bro</p>
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                    </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                    <img src="/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                        Nora Silvester
                        <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                        </h3>
                        <p class="text-sm">The subject goes here</p>
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                    </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
                </a>
            </li>
            </ul>
        </nav>
    </div>
</template>
<script>
export default {
    props:['ruta','usuario','listPermisos'],
    data() {
        return {
            links: [],
            cBusqueda: '',
            listaPermisosByUser:[],
            listPermisosFilterByRolUser:[],
            oUsuario:[]
        };
    },
    methods: {
        querySearch(queryString, cb) {
            var links = this.listPermisosFilterByRolUser;
            var results = queryString ? links.filter(this.createFilter(queryString)) : links;
            // call callback function to return suggestion objects
            cb(results);
        },
        createFilter(queryString) {
            return (link) => {
                return (link.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
            };
        },

        //Este metodo se ejecuta despues de seleccionar el dato en el buscador
        handleSelect(item) {
            console.log(item);
            if(this.$route.name != item.link){
                this.$router.push({name : item.link});
                this.cBusqueda ='';
            }
            else{
                this.cBusqueda ='';
            }
        },
        handleIconClick(ev) {
            console.log(ev);
        },

        getListarRolPermisosByUser(){
            let url = "/permiso/ObtenerPermisosUsuario";
            axios.get(url,{params:{
                'cUsuario':this.usuario.Usuario,
                'nIdRol':this.usuario.IdRol
            }}).then((response)=>{
                let Datos = response.data;
                this.listaPermisosByUser = Datos.permisos;
                this.filterListRolPermisosByUsuario();
            })
        },


        filterListRolPermisosByUsuario(){
            let me = this;
            me.listPermisosFilterByRolUser=[];
            me.listaPermisosByUser.map(function(x,y){
                //Aqui solamente incluimos los permisos que son de lista por ejemplo lista de pedidos, lista de usuarios.
                
                if(x.slug != null && x.slug.includes('index')){
                    me.listPermisosFilterByRolUser.push({
                        'value':x.nombre,
                        'link':x.slug
                    });
                }
            })
        },

    },
    mounted() {
        EventBus.$on('notififyRolPermisosByUser',data =>{
            this.getListarRolPermisosByUser();
        });
        this.getListarRolPermisosByUser();
    }
    
}
</script>