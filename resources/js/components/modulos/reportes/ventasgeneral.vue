<template>
    <div class="content container-fluid">
      <div class="card">
        <div class="card-header bg-info">
          Tabla Ventas
        </div>
        <div class="card-body">
          <button class="btn btn-primary" @click="getSelectedRows()">Filas Seleccionadas</button>
          <ag-grid-vue
              style="width: 100%; height: 700px;"
              class="ag-theme-alpine"
              :pagination="true"
              :paginationPageSize="paginationPageSize"
              :columnDefs="columnDefs"
              :rowData="rowData"
              rowSelection="multiple"
              @grid-ready="onGridReady"
              :sideBar="true">
          </ag-grid-vue>
        </div>
      </div>
    </div>
</template>
 
<script>
import "ag-grid-community/dist/styles/ag-grid.css";
import "ag-grid-community/dist/styles/ag-theme-alpine.css";
import 'ag-grid-enterprise';
import { AgGridVue } from "ag-grid-vue";

    export default {
        name: 'App',
        data() {
            return {
                columnDefs: null,
                rowData: null,
                autoGroupColumnDef: null,
                paginationPageSize:100,
                Columnas:[],
            }
        },
        components: {
            AgGridVue
        },
        methods: {
          onGridReady(params) {
              this.gridApi = params.api;
              this.columnApi = params.columnApi;
          },
          getSelectedRows() {
              const selectedNodes = this.gridApi.getSelectedNodes();
              const selectedData = selectedNodes.map( node => node.data );
              const selectedDataStringPresentation = selectedData.map( node => `${node.make} ${node.model}`).join(', ');
              alert(`Selected nodes: ${selectedDataStringPresentation}`);
          }
        },
        beforeMount() {
            let me = this;
            this.columnDefs = [];
            this.rowData = [];
            let Cols =[];
            let Datos =[];
            axios.get('/ventasGrilla').then(response=>{
                Cols =  response.data.columnas;
                Datos =  response.data.datos;
                Cols.map(function(x,y){
                  if(y<=0){
                    me.columnDefs.push({field : x.Field, sortable: true,filter:true , checkboxSelection:true});
                  }
                  else{
                    me.columnDefs.push({field : x.Field, sortable: true,filter:true });
                  }
                });

                Datos.map(function(x,y){
                  me.rowData.push(x);
                })
            });

            
               


            /*this.rowData = [
                { 'Id': 'Toyota', Descripcion: 'Celica', Precio: 35000 },
                { 'Id': 'Ford', Descripcion: 'Mondeo', Precio: 32000 },
                { 'Id': 'Porsche', Descripcion: 'Boxter', Precio: 72000 }
            ];*/
        },
        mounted() {
          
        },
    }
</script>