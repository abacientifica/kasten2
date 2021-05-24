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
    props:['ruta','usuario'],
    data() {
        return {
            authUser:this.usuario,
            listPermisosFilterByRolUser:[],
        }
    },

    computed:{
        listPermisosusr (){
            let boolval = false;
            if(!this.listPermisosFilterByRolUser){
                this.logout();
                boolval =  true;
            }
            else{
                boolval = false;
            }
            return boolval
        }
    },

    methods: {
        logout(){
            var url = '/authenticated/logout'
            axios.post(url).then(response => {
                if (response.data.code == 204) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                }
            }).catch(error => {
                console.log("Error"+error)
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                }
            })
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
        //console.log(this.listPermisosFilterByRolUser)
        EventBus.$on('notififyRolPermisosByUser',data =>{
            console.log("Evento ejecutado desde component App.vue")
            this.listPermisosFilterByRolUser = data;
        });
    },
}
</script>