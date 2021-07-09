<template>
    <div>
        <div class="content-header margen-ruta">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Test Sistemas</h1>
                </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                        <li class="breadcrumb-item active">Test</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio Contenido-->
        <div class="content container-fluid">
            <div class="card">
                <div class="card-header">Test Grid</div>
                <div class="card-body">
                    <ag-grid-vue
                        style="width: 100%; height: 400px;"
                        class="ag-theme-alpine"
                        id="myGrid"
                        :gridOptions="gridOptions"
                        @grid-ready="onGridReady"
                        :columnDefs="columnDefs"
                        :defaultColDef="defaultColDef"
                        :rowData="rowData"
                        :modules="modules"></ag-grid-vue>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import "ag-grid-community/dist/styles/ag-grid.css";
import "ag-grid-community/dist/styles/ag-theme-alpine.css";
import 'ag-grid-enterprise';
import { AllCommunityModules } from 'ag-grid-community';
import { AgGridVue } from "ag-grid-vue";
export default {
    components: {
        AgGridVue
    },
    data() {
        return {
            gridOptions: null,
            gridApi: null,
            columnApi: null,
            columnDefs: null,
            defaultColDef: null,
            rowData: null,
            modules: AllCommunityModules,
            components: null,
        }
    },
    beforeMount() {
        let me = this;
        this.gridOptions = {};
        this.columnDefs = [
        {
            headerName: 'Editable A',
            field: 'a',
            editable: true,
            valueParser: numberValueParser,
        },
        {
            headerName: 'Editable B',
            field: 'b',
            editable: true,
            valueParser: numberValueParser,
        },
        {
            headerName: 'Editable C',
            field: 'c',
            //editable: true,
            //valueParser: numberValueParser,
            cellRenderer: function(params){
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML =
                    '<span @click = "Prueba" style="border-bottom: 1px solid grey; border-left: 1px solid grey; padding: 2px;">' +
                         params.value +
                    '</span>';
                    return tempDiv;
                
            },
            onCellClicked : function(params){
                console.log(me.gridApi)
            }
        },
        {
            headerName: 'API D',
            field: 'd',
            minWidth: 140,
            valueParser: numberValueParser,
            cellRenderer: 'agAnimateShowChangeCellRenderer',
        },
        {
            headerName: 'API E',
            field: 'e',
            minWidth: 140,
            valueParser: numberValueParser,
            cellRenderer: 'agAnimateShowChangeCellRenderer',
        },
        {
            headerName: 'Total',
            minWidth: 140,
            valueGetter: 'data.a + data.b + data.c + data.d + data.e',
            cellRenderer: 'agAnimateShowChangeCellRenderer',
        },
        {
            headerName: 'Average',
            minWidth: 140,
            valueGetter: '(data.a + data.b + data.c + data.d + data.e) / 5',
            cellRenderer: 'agAnimateSlideCellRenderer',
        },
        ];
        

        this.defaultColDef = {
        flex: 1,
        minWidth: 120,
        resizable: true,
        cellClass: 'align-right',
            valueFormatter: (params) => {
                return formatNumber(params.value);
            },
        };
        this.rowData = createRowData();
        this.components = { simpleCellRenderer: getSimpleCellRenderer() };
    },
    mounted() {
        this.gridApi = this.gridOptions.api;
        this.gridColumnApi = this.gridOptions.columnApi;
    },
    methods: {
        onUpdateSomeValues() {
        var rowCount = this.gridApi.getDisplayedRowCount();
        for (var i = 0; i < 10; i++) {
            var row = Math.floor(Math.random() * rowCount);
            var rowNode = this.gridApi.getDisplayedRowAtIndex(row);
            rowNode.setDataValue('d', Math.floor(Math.random() * 10000));
            rowNode.setDataValue('e', Math.floor(Math.random() * 10000));
        }
        },
        onGridReady(params) {},

        Probar: function(){
            alert("Entrpo")
        }
    },
}
window.createRowData = function createRowData() {
  var rowData = [];
  for (var i = 0; i < 40; i++) {
    rowData.push({
      a: Math.floor(((i + 323) * 25435) % 10000),
      b: Math.floor(((i + 323) * 23221) % 10000),
      c: Math.floor(((i + 323) * 468276) % 10000),
      d: 0,
      e: 0,
    });
  }
  return rowData;
};

window.numberValueParser = function numberValueParser(params) {
    console.log(params)
  return Number(params.newValue);
};

window.formatNumber = function formatNumber(number) {
  return Math.floor(number)
    .toString()
    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
};

window.getSimpleCellRenderer = function getSimpleCellRenderer() {
    function SimpleCellRenderer() {}
    SimpleCellRenderer.prototype.init = function (params) {
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML =
            '<span style="border-bottom: 1px solid grey; border-left: 1px solid grey; padding: 2px;">' +
            params.value +
            '</span>';
        this.eGui = tempDiv.firstChild;
    };
    SimpleCellRenderer.prototype.getGui = function () {
        return this.eGui;
    };
    return SimpleCellRenderer;
};
</script>