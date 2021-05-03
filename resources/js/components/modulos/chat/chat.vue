<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-info">
                Mensajeria Instantanea
            </div>
            <div class="car-body">
                <hr>
                <div class="col-md-6">
                    <div class="card direct-chat direct-chat-primary" :class=" openToCloseContact ? 'direct-chat-contacts-open': '' " >
                        <div class="card-header ui-sortable-handle" style="cursor: move;">
                            <h3 class="card-title">Chat Directo</h3>

                            <div class="card-tools">
                            <span data-toggle="tooltip" :title="TotalMensajesNoLeidos+ ' New Messages'" class="badge badge-primary">{{TotalMensajesNoLeidos}}</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" v-on:click.prevent ="abrirContactos()" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle" >
                                <i class="fas fa-comments"></i>
                            </button>
                            </div>
                        </div>
                    <!-- /.card-header -->
                        <div class="card-body">
                            
                            <!-- Conversations are loaded here -->
                            <Conversation :mensajes="listMensajes" :contacto="selectContact" ></Conversation>
                            <!--/.direct-chat-messages-->

                            <!-- Contacts are loaded here -->
                            <ContactList :contactos="listContactos" @contacto="getListarConversaciones" :typing="userTyping"></ContactList>
                            <!-- /.direct-chat-pane -->
                        </div>
                        
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <SendMessage v-if="selectContact" @mensaje="setNuevoMensaje" :contacto="selectContact" @cerrarventana="abrirContactos"></SendMessage>
                        </div>
                        <!-- /.card-footer-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
</template>
<script>
import Conversation from "./conversation";
import ContactList from "./contactlist";
import SendMessage from "./sendmessage";
export default {
    components:{Conversation,ContactList,SendMessage},
    data() {
        return {
            listContactos:[],
            listMensajes:[],
            selectContact:null,
            openToCloseContact:true,
            userTyping:{
                usuario:'',
                state:false
            },
        }
    },
    computed:{
        TotalMensajesNoLeidos(){
            let TotalMsg =0;
            for(let i =0 ; i< this.listContactos.length; i++){
                TotalMsg = TotalMsg + this.listContactos[i].ExtensionTel;
            }
            return TotalMsg;
        }
    },
    methods: {
        abrirContactos(){
            this.openToCloseContact = !this.openToCloseContact;
        },

        getListarContactos(){
            let url = "/chat/getListarContactos";
            axios.get(url).then(response=>{
                this.listContactos = response.data.contactos;
            })
        },

        setNuevoMensaje(rpta){
            this.listMensajes.push(rpta);
        },

        getListarConversaciones(contacto){
            this.abrirContactos();
            let url = "/chat/getListarConversaciones";
            axios.get(url,{
                params:{'nIdContacto':contacto.Usuario}
            }).then(response=>{
                this.listMensajes = response.data.conversacion;
                this.selectContact = contacto
            })
            this.getListarContactos();
        },
        setMensajeEntrante(mensaje){
            //console.log(this.selectContact.Usuario)
            //console.log(mensaje)
            if(this.selectContact && this.selectContact.Usuario == mensaje.from){
                this.setNuevoMensaje(mensaje);
            }
            const noti = this.$vs.notification({
                
                progress: 'auto',
                boder:'bx bx-border-radius b-r',
                duration: 4000,
                color:'rgb(59,222,200)',
                position : 'top-right',
                title: mensaje.from,
                text: mensaje.text
            });
            this.getListarContactos();
        },
    },
    mounted() {
        let authUser = JSON.parse(sessionStorage.getItem('authUser'));
        Echo.private(`mensaje.${authUser.Usuario}`).listen('NuevoMensaje', (e) => {
            //console.log(e)
            this.setMensajeEntrante(e.mensaje);
        });

        Echo.private("escribiendo").listenForWhisper('typing',(e) => {
            if(authUser.Usuario == e.contacto){
                console.log(e);
                this.userTyping.state = true;
                this.userTyping.usuario = e.usuario;
                //Cambia a false cuando el usuario deja de escribir
                setTimeout(()=>{
                    this.userTyping.state = false;
                },2000)
            }
        });
        this.getListarContactos();
    },
}
</script>