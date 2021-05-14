<template>
    <div>
        Editar Campos
    </div>
</template>
<script>
export default {
    data() {
        return {
            arrCamposTabla:[]
        }
    },
    methods: {
        CargarCampos(){
            axios.get('/documentos/campos/').then(function(response){
                console.log(response.data)
            }).catch({
                
            })
        }
    },
    mounted() {
        this.CargarCampos();
    },
}
</script>