<template>
    <div>
        <span>{{params.value}} </span>
        <button v-if="params.data && params.data.EnNovedad" @click.prevent="listarNovedades(params.data.ItemAba)" class="btn btn-danger btn-sm"><i class="fas fa-bell"></i></button>
    </div>
</template>
<script>
export default {
    props:['Id_Item'],
    data () {
        return {
            dialogTableVisible:false,
            Novedades:[],
            params:[]
        };
    },
    beforeMount() {
    },
    mounted() {
    },
    methods: {
        listarNovedades(Item){
            let url ="/novedades/lista/"+Item;
            let me = this;
            axios.get(url).then(response=>{  
                let respuesta = response.data;
                EventBus.$emit('arrNovedades',respuesta.novedades);
            });
            
        }
    }
    

}
</script>