<template>
    <main class="main">
        <form action="#" method="post">
            <div class="input-group">
                <span class="input-group-append">
                <button type="button" class="btn btn-warning" @click.prevent="cerrarMensaje">Cerrar</button>
                </span>
                <input type="text" name="message" v-model="message" @keydown.enter.prevent="setRegistrarMensaje" @keydown="escribiendoMensaje" placeholder="Escriba un mensaje" class="form-control">
                <span class="input-group-append">
                <button type="button" class="btn btn-primary" @click.prevent="setRegistrarMensaje">Enviar</button>
                </span>
            </div>
        </form>
    </main>
</template>
<script>
export default {
    props:{
        contacto:{
            type:Object,
            default:null
        },
    },
    data() {
        return {
            message:''
        }
    },
    methods: {
        setRegistrarMensaje(){
            if(!this.message){
                return;
            }
            if(!this.contacto){
                return;
            }
            let url = "/chat/setRegistrarMensaje";
            axios.post(url,{
                'to':this.contacto.Usuario,
                'text': this.message
            }).then(response=>{
                this.$emit('mensaje',response.data.msg[0]);
                this.message = '';
            })
        },

        cerrarMensaje(){
            this.$emit('cerrarventana',true);
            this.message = '';
        },

        escribiendoMensaje(){
            let authUser = JSON.parse(sessionStorage.getItem('authUser'));
            Echo.private("escribiendo").whisper('typing',{
                usuario: authUser.Usuario,
                contacto : this.contacto.Usuario,
                typing :true
            })
        }
    },
}
</script>