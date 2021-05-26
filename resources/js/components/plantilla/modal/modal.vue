<template>
    <div>
    
    <a @click="outerVisible = true" class="btn btn-secondary btn-sm fas fa-question-circle margin-left-1"></a>
    <el-dialog :title="titulo" :visible.sync="outerVisible">
        <ayudas :iddoc="iddoc" :url="slug" ></ayudas>
        <div slot="footer" class="dialog-footer">
        <el-button @click="outerVisible = false">Cerrar</el-button>
        </div>
    </el-dialog>
    </div>
</template>
<script>
export default {
    props:['titulo','iddoc','url'],
    data() {
        return {
            opcion:'',
            doc: '',
            slug:'',
            outerVisible: false,
            innerVisible: false,

            modalShow: false,
            mostrarModal: {
                display: 'block',
                background: '#0000006b',
            },
            ocultarModal: {
                display: 'none',
            },
        }
    },
}
</script>