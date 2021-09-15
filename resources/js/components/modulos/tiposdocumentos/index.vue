<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><div style="display:none" v-text="cambioDoc"></div></h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link :to="'/'">Home</router-link></li>
                        <li class="breadcrumb-item active">Documentos {{TpDocumento.NmTpDocumento}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i><label></label>
                    </div>
                    <template>
                        <div>
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-info">
                                    <tr>
                                        <th>ID</th>
                                        <th>Documento</th>
                                        <th>Consecutivo</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="documento in arrDocumentos" :key="documento.IdDocumento">
                                        <template v-if="listPermisos.includes(documento.Indexk2) || listPermisos.includes('administrador.sistema')">
                                            <td v-text="documento.IdDocumento"></td>
                                            <td v-text="documento.Nombre"></td>
                                            <td v-text="documento.Consecutivo"></td>
                                            <td>
                                                <router-link  :to="{name: documento.Indexk2}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </router-link>
                                            </td>
                                        </template>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
        </div>
    </div>
</template>
<script>
export default {

    data() {
        return {
            TpDocumento:[],
            arrDocumentos:[],
            listPermisos:[],
            IdTipo:this.$attrs.IdTp,
        }
    },

    computed:{
        cambioDoc: function(){
            this.$attrs.IdTp;
            this.IdTipo = this.$attrs.IdTp;
            this.ObtenerTpDocumento(this.$attrs.IdTp);
            return true;
        }
    },

    methods: {
        ObtenerTpDocumento(Tp){
            let url = `/documentos/tipo/${Tp}`;
            let me = this;
            me.arrDocumentos = [];
            axios.get(url).then((response)=>{
                let respuesta = response.data;
                me.TpDocumento = respuesta.tpdocumento;
                let Docs =  me.TpDocumento.documentos;
                me.arrDocumentos = Docs.filter(function(e){
                    if(e.Indexk2){
                        return e;
                    }
                })
                if(me.arrDocumentos.length == 1){
                    this.$router.push({name: me.arrDocumentos[0].Indexk2}).catch(error=>{});
                }
            }).catch((error)=>{

            })
        }
    },
    mounted() {
        this.listPermisos = JSON.parse(localStorage.getItem('listPermisosFilterByRolUser'));
        this.ObtenerTpDocumento(this.$attrs.IdTp);
        console.log(this.$attrs.IdTp)
    },
}
</script>