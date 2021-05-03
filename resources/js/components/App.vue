<template>
    <div>
        <navbar :ruta="ruta" :usuario="authUser" :listPermisos ="listPermisosFilterByRolUser"></navbar>
        <sidebar :ruta="ruta" :usuario="authUser" :listPermisos ="listPermisosFilterByRolUser"></sidebar>
        <div class="content-wrapper">
            <transition name="slide" mode="out-in">
                <router-view></router-view>
            </transition>
        </div>
        <Footer></Footer>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
</template>
<script>
export default {
    props:['ruta','usuario','listPermisos'],
    data() {
        return {
            authUser:this.usuario,
            listPermisosFilterByRolUser:[],
        }
    },
    mounted() {
        EventBus.$on('verrifyAutenticathedUser',data =>{
            console.log("Evento refresca usuario ejecutado desde component App.vue")
            this.authUser = data;
        });
        //Con json.parse recuperamos un arreglo de json.
        //console.log(JSON.parse(sessionStorage.getItem('listPermisosFilterByRolUser')));
        this.listPermisosFilterByRolUser = sessionStorage.getItem('listPermisosFilterByRolUser');
        EventBus.$on('notififyRolPermisosByUser',data =>{
            console.log("Evento ejecutado desde component App.vue")
            this.listPermisosFilterByRolUser = data;
        });
    },
}
</script>