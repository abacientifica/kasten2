<template>
    <div>
        <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #c7b299;">
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
            

            <ul class="nav navbar-nav ml-auto">
                <div class="menu-rigth" >
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <img :src="usuario.imagen" class="img-avatar img-navbar-aba" >
                            <span class="d-md-down-none" >{{usuario.Nombres}}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header text-center">
                                <strong>Cuenta</strong>
                            </div>
                            
                            <router-link :to="{name:'usuario.perfil',params:{id:usuario.Usuario}}" :usuario="usuario" class="dropdown-item">
                                <i class="fas fa-wrench"></i>Configuración
                            </router-link>
                            <a class="dropdown-item" href="#"  @click.prevent="logout()">
                                <i class="fas fa-lock"></i> Cerrar sesión
                            </a>

                        </div>
                    </li>
                </div>
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
        
    },

    },
    mounted() {
        EventBus.$on('notififyRolPermisosByUser',data =>{
            this.getListarRolPermisosByUser();
        });
        this.getListarRolPermisosByUser();
        if(JSON.parse(sessionStorage.getItem('listPermisosFilterByRolUser'))== null){
            sessionStorage.setItem('authUser',JSON.stringify(this.usuario));
        }
    }
    
}
</script>