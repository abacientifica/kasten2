<template>
	<div>

    <button class="btn btn-primary btn-sm fas fa-file" @click="modalShow=true"> </button>

    <!-- Modal -->
    <div class="modal fade  " :class="{ show: modalShow }" :style=" modalShow ? mostrarModal : ocultarModal" tabindex="-1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <vs-alert>
                            <button type="button" class="close" @click="modalShow = false" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>{{titulo}}</strong>
                        </vs-alert>
                        
                    </div>
                    <div class="modal-body">
                        <div class="card-tools" style="display:flex">
                            Pagina :
                            <select class="form-control col-md-1" v-model.number="page">
                                <option v-for="index in numPages" :key="index.index" v-text="index"></option>
                            </select>  De : {{numPages}}
                            <button class="btn btn-info btn-sm" @click="rotate += 90">&#x27F3;</button>
                            <button class="btn btn-info btn-sm" @click="rotate -= 90">&#x27F2;</button>
                            <button class="btn btn-success btn-sm" @click="$refs.pdf.print()"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                        <div style="width: 100%; overflow: auto;height: 600px;">
                            <div v-if="loadedRatio > 0 && loadedRatio < 1" style="background-color: green; color: white; text-align: center" :style="{ width: loadedRatio * 100 + '%' }">{{ Math.floor(loadedRatio * 100) }}%</div>
                            <pdf v-if="show" ref="pdf" style="border: 1px solid #333366" :src="src" :page="page" :rotate="rotate" @password="password" @progress="loadedRatio = $event" @error="error" @num-pages="numPages = $event" @link-clicked="page = $event"></pdf>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="modalShow = false">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import pdf from 'vue-pdf'

export default {
    props:["archivo","titulo","descripcion"],
	components: {
		pdf: pdf
	},
	data () {
		return {
			show: true,
			src:this.archivo,
			loadedRatio: 0,
			page: 1,
			numPages: 0,
            rotate: 0,
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
	methods: {
		password: function(updatePassword, reason) {

			updatePassword(prompt('password is "test"'));
		},
		error: function(err) {

			console.log(err);
		}
    },
}
</script>