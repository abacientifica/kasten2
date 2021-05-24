<template>
    <div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
            <i class="far fa-file"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <vs-alert color="sucess">{{descripcion}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </vs-alert>
            </div>
            <div class="modal-body">
                
                <div class="col-md-12" style="display:flex">
                    Pagina :
                    <select v-model.number="page" class="form-control col-md-1">
                        <option v-for="index in numPages" :key="index.i" >{{index}}</option>
                    </select> De : {{numPages}}
                    <button @click="rotate += 90" class="btn btn-info btn-sm">&#x27F3;</button>
                    <button @click="rotate -= 90" class="btn btn-info btn-sm">&#x27F2;</button>
                    <button @click="$refs.pdf.print()" class="btn btn-success btn-sm">Imprimir</button>
                </div>
                
                <div>
                    <div v-if="loadedRatio > 0 && loadedRatio < 1" style="background-color: green; color: white; text-align: center;" :style="{ width: loadedRatio * 100 + '%' }">{{ Math.floor(loadedRatio * 100) }}%</div>
                    <div class='pdf-viewer-wrapper' v-dragscroll :class='{"zoom-active": zoom > 100 }'>
                        <pdf v-if="show" v-model="verdoc" ref="pdf" style="border: 1px solid #7d6ef9;width:100%;height: 500px;overflow: auto;" :src="src" :page="page" :rotate="rotate" @password="password" @progress="loadedRatio = $event" @error="error" @num-pages="numPages = $event" @link-clicked="page = $event"></pdf>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
        </div>
    </div>
</template>
<script>
import pdf from 'vue-pdf'
import { dragscroll } from 'vue-dragscroll'
export default {
    props:["archivo","descripcion","ver"],
    directives: { dragscroll },
	components: {
		pdf: pdf
    },
    computed:{
        verdoc(op=true){
            if(op){
                if(this.ver){
                    this.pdfList.push(this.archivo);
                    this.src =this.archivo;
                }
                else{
                    this.pdfList=[];
                    this.src ='';
                }
            }
            else{
                this.pdfList=[];
                this.src ='';
            }
            return true;
        },

        cerrardoc(){
            this.pdfList=[];
            this.src ='';
            return true;
        }
    },
	data () {
		return {
			show: true,
			pdfList: [],
			src:'',
			loadedRatio: 0,
			page: 1,
			numPages: 0,
            rotate: 0,
            zoom: 100
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
    mounted() {
        /*if(this.ver != false){
            this.pdfList.push(this.archivo);
            this.src =this.archivo;
        }*/
    },
}
/*import pdf  from 'vue-pdf'

export default {
    props: ['archivo'],
    //loadingTask = pdf.createLoadingTask(this.archivo),
    data() {
        return {
            src : pdf.createLoadingTask(this.archivo),
            numPages :0
        }
    },
    components:{
        pdf
    },

    mounted() {
        this.src.promise.then(pdf => {
			this.numPages = pdf.numPages;
		});
    },
}*/

</script>
<style lang='scss'>
.pdf-viewer-wrapper {
  
  max-height: 68vh;
  &.zoom-active {
      cursor: grab;
   }
}
</style>