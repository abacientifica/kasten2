<template>
  <div class="main">
      <ol class="breadcrumb">
      </ol>
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
              Reporte Ventas
          </div>
          <div class="card-body">
            <div class="col-md-12">
              <div class="form-group row border">
                <div class="table-responsive col-md-12" v-if="arrDatos.length">
                  <ag-grid-vue
                      class="ag-theme-alpine"
                      @gridReady="onGridReady"
                      :style="{width,height }"
                      :localeText="localText"
                      :columnDefs="arrColumnas"
                      :rowData="arrDatos"
                      :gridOptions="gridOptions"
                      :autoGroupColumnDef="false"
                      :pagination="true"
                      :sideBar="sidebar"
                      :tooltipShowDelay="tooltipShowDelay">
                  </ag-grid-vue>
                </div>
                <div class="col-md-12" v-else>
                  <vs-alert progress="70" type="danger">
                    <template #title class="aling-center">
                      {{message}}
                    </template>
                  </vs-alert>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>
<script>
import servicesVentas from '../helpers/getVentasOpciones'
import serviceApp from "../../../../ServicesApp";
import { AgGridVue } from "ag-grid-vue";
const SERVICIOS_REST = new serviceApp();
const COLUMNS_CALC_FOOTER = ['IdMovimiento', 'SubTotal','TotalIva','Total']
export default {
  components:{
    AgGridVue
  },
  
  data() {
    return {
      arrDatos:[],
      arrColumnas:[],
      width:'100%',
      height:(window.innerHeight - 150)+'px',
      localText:servicesVentas.idioma(),
      idDocumento:94,
      sidebar:servicesVentas.sidebar(),
      tooltipShowDelay:null,
      gridApi:null,
      message:'Hola, estamos cargando los datos y esto puede tardar unos segundos .',
      gridOptions :{
        onFilterChanged:this.onFilterChanged,
      }
    }
  },

  beforeMount(){
    this.getVentas();
  },

  methods:{
    async getVentas (){
      const loader = servicesVentas.loader(this,'Cargando ventas un momento por favor...');
      let { columnas, datos, status, statusText} = await servicesVentas.ventasGrilla()
      if(!SERVICIOS_REST.validarStatus(this,status,statusText)){ loader.close(); this.message = "Ocurrio un error al cargar los datos."; return  }
      this.arrColumnas = servicesVentas.configGrilla(columnas)
      this.arrDatos = datos
      loader.close();
    },

    onGridReady(params){
      this.gridApi = params;
      this.gridApi.api.closeToolPanel();
      this.gridColumnApi = params.columnApi;
      let pinnedButtomData = SERVICIOS_REST.generatePinnedButtomData(this,this.gridColumnApi,COLUMNS_CALC_FOOTER);
      this.gridApi.api.setPinnedBottomRowData([pinnedButtomData]);
    },

    onFilterChanged(params){
      let pinnedButtomData = SERVICIOS_REST.generatePinnedButtomData(this,this.gridColumnApi,COLUMNS_CALC_FOOTER);
      this.gridApi.api.setPinnedBottomRowData([pinnedButtomData]);
    }
  },

  mounted() {
 
  },
}
</script> 
<style scope>
.ag-theme-alpine .ag-ltr .ag-cell {
  border-right: 1px solid #e0e0e0;
}

.ag-theme-alpine .ag-icon-desc:before , .ag-theme-alpine .ag-icon-asc:before, .ag-theme-alpine .ag-icon-menu:before{
  color:white;
}

.btn-search{
  float:right;
}

.card-header{
  padding: 0.50rem 1rem;
}

.el-button btn-search el-button--default{
  border: 2px solid black;
}
</style>