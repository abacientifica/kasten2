<template>
    <main class="main">
        
        <div class="direct-chat-contacts">
            <ul class="contacts-list">
                <li><input class="form-control" type="search" v-model="filtro" placeholder="Search" aria-label="Search"></li>
                <li v-for="(contacto,index) in ListaContactos" :key="index" @click="seleccionarContacto(index,contacto)" :class="(index == selected) ? 'selected' : ''">
                    <a href="#">
                        <img v-if="contacto.imagen" class="contacts-list-img img-tamano" :src="contacto.imagen" >
                        <img v-else class="contacts-list-img" src="/img/avatar.png">
                        <div class="contacts-list-info">
                        <span class="contacts-list-name" v-text="contacto.Nombres + ' ' + contacto.Apellidos">
                           
                        </span>
                        <small class="direct-chat-timestamp float-right contact-notification">{{contacto.ExtensionTel}}</small>
                        <span v-if="typing.state==true && typing.usuario == contacto.Usuario" class="contacts-list-msg">Está escribiendo ...</span>
                        <span  v-else class="contacts-list-msg" v-text="contacto.UltMensaje"></span>
                        </div>
                        <!-- /.contacts-list-info -->
                    </a>
                </li>
            </ul>
            <!-- /.contacts-list -->
        </div>
    </main>
</template>
<script>
export default {
    props:{
        contactos:{
            type:Array,
            default:[]
        },
        typing:{
            type:Object,
        },
    },
    
    data() {
        return {
            selected:'',
            filtro:'',
            getContactos: [],
        }
    },

    computed:{
        ListaContactos (){
            return this.contactos.filter(contacto => {
                return (contacto.Nombres.toLowerCase().indexOf(this.filtro.toLowerCase()) != -1);
            })
        }
    },
    methods: {
        seleccionarContacto(index,contacto){
            this.selected = index;
            this.filtro = '';
            this.$emit('contacto',contacto);
            
        },

        probarfiltro(){
            let Contactos = [];
            let me = this;
            this.getContactos.map(function(x){
                let usuario = x.Usuario+" "+x.Nombres;
                if(usuario.includes(me.filtro)){
                    Contactos.push(x)
                }
            });
            //this.getContactos = this.getContactos.find(element => element.Nombres = this.filtro);
            this.getContactos = Contactos;
        }
    },
    mounted() {
    },
}
</script>
<style>
.selected{
    background: #88ccbd;
}
.contact-notification{
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    background: rgb(179, 218, 140);
}
.img-tamano{
    height: 40px;
}
</style>