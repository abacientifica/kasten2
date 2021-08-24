<template>
    <div>
        <el-collapse accordion>
            <el-collapse-item  v-for="(item,index) in ListAyudasItems" :key="item.id" :name="index">
                <template  slot="title">
                    <strong>{{item.TituloAyuda}}</strong>
                </template>
                <div v-html="item.Descripcion"></div>
                <div>Ver manual: <visualizar-archivo  v-if="item.Imagen !=null" :archivo="item.Imagen"  :titulo="item.TituloAyuda" :descripcion="item.Descripcion" :ver="true" ></visualizar-archivo></div>
            </el-collapse-item>
        </el-collapse>
    </div>
</template>
<script>
export default {
    props:['iddoc','url'],
    data() {
        return {
            ListAyudasItems:[]
        }
    },

    methods: {
        cargarAyudas(){
            let me = this;
            var url = '/ayuda/ObtenerAyudasKasten';
            axios.get(url,{ params:{
               'iddoc': this.iddoc,
               'url':this.url
            }
            }).then(function (response) {
                var respuesta = response.data;
                me.ListAyudasItems = respuesta.ayudas.length >0 ? respuesta.ayudas : [];
            })
            .catch(function (error) {
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        }
    },

    mounted() {
        this.cargarAyudas();
    },
}
</script>