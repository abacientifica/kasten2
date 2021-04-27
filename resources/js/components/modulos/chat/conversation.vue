<template>
        <div class="direct-chat-messages" ref="bajar">
            <template v-if="contacto" >
                    <!-- Message. Default to the left -->
                    <template v-if="mensajes.length >0">
                        <div class="direct-chat-msg" v-for="(item,index) in mensajes" :key="index" :class="(item.from == contacto.Usuario) ? '' : 'right'">
                            <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name " :class="(item.to != contacto.Usuario) ? 'float-left' : 'float-right'" >{{item.remitente.Nombres}}</span>
                            <span class="direct-chat-timestamp " :class="(item.to != contacto.Usuario) ? 'float-left' : 'float-right'" v-text=" ' '+item.created_at"></span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img  v-if="item.from != contacto.Usuario" :src="item.remitente.imagen"  class="direct-chat-img"  alt="message user image">
                            <img  v-else :src="contacto.imagen"  class="direct-chat-img"  alt="message user image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" v-text="item.text">
                            </div>
                            <!-- /.direct-chat-text -->
                        </div>
                    </template>
                    <template v-else>
                        <div  class="direct-chat-msg" >
                            <vs-alert>
                            <template #title>
                                Hola
                            </template>
                                No tienes mensajes con {{contacto.Nombres}}
                            </vs-alert>
                        </div>
                    </template>
                    
                
            </template>
            <template v-else>
                <div class="alert alert-primary">
                    Debes seleccionar un usuario para iniciar una conversacion
                </div>
            </template>
        </div>
</template>
<script>
export default {
    props:{
        mensajes:{
            type:Array,
            default:[]
        },
        contacto:{
            type:Object,
            default:null
        }
    },

    watch:{
        //SI ocurrio un cambio en los mensajes que ejecute el scroll
        mensajes(){
            this.scrollToButtom();
        },
        contacto(){
            this.scrollToButtom();
        }
    },

    methods: {
        scrollToButtom(){
            setTimeout(()=>{
                this.$refs.bajar.scrollTop = this.$refs.bajar.scrollHeight - this.$refs.bajar.clientHeight;
            },100);
        }
    },
}
</script>