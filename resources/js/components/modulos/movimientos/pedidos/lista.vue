<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pedidos</h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pedidos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                <div class="container-fluid">
                    <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Pedidos</h3>
                    </div>
                    </div>
                    <div class="card-info">
                    <div class="card-body">
                        <div class="card-header">
                            <h3 class="card-title">Bandeja Resultados</h3>
                            <div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>ID</th>
                                            <th>Documento</th>
                                            <th>Consecutivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="documento in arrayDocumentos" :key="documento.IdDocumento">
                                            <td>
                                                <button type="button" @click="selDocumento(documento.IdDocumento,documento.Nombre)" class="btn btn-success btn-sm">
                                                <i class="fas fa-eye"></i>
                                                </button> &nbsp;
                                            </td>
                                            <td v-text="documento.IdDocumento"></td>
                                            <td v-text="documento.Nombre"></td>
                                            <td v-text="documento.Consecutivo"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            arrayDocumentos:[]
        }
    },
    methods: {
        ListarDocumentos(){
            let me = this;
            var url = '/documentos/ObtenerDocTp';
            axios.get(url,{
                params:{
                    'Tp':2
                }
            }).then(function (response) {
                var respuesta = response.data;
                me.arrayDocumentos = respuesta.documentos;
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                    this.$router.push({name: 'login'})
                    location.reload();
                    sessionStorage.clear();
                    this.fullscreenLoading = false;
                }
            });
        },
    },
    mounted() {
        this.ListarDocumentos();
    },
}
</script>