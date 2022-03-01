const { default: Axios } = require("axios")

import axios from 'axios'
import "ag-grid-community/dist/styles/ag-grid.css";
import "ag-grid-community/dist/styles/ag-theme-alpine.css";
import 'ag-grid-enterprise';
import servicesAppKasten from '../../../../ServicesApp'
const servicesApp = new servicesAppKasten();

function clousureServices() {

    function formatoMoneda(value) {
        let amount = value;
        let decimals = 2;
        amount = (amount == null) ? 0 : amount;
        var sign = (amount.toString().substring(0, 1) == "-");

        amount += ''; // por si pasan un numero en vez de un string
        amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

        decimals = decimals || 0; // por si la variable no fue fue pasada

        // si no es un numero o es igual a cero retorno el mismo cero
        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);

        // si es mayor o menor que cero retorno el valor formateado como numero
        amount = '' + amount.toFixed(decimals);

        var amount_parts = amount.split('.'),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

        return sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
    }

    return {
        ventasGrilla: async() => {
            const response = await axios.get('/ventasGrilla').then((resp) => {
                let { columnas, datos, status, statusText } = resp.data;
                return { columnas, datos, status, statusText }
            }).catch(err => {
                let { statusText, status } = err.response
                return { statusText, status }
            })
            return response
        },

        loader: (me, msg = 'Cargando...') => {
            return me.$vs.loading({
                type: 'square',
                background: '#babaea',
                color: '#fff',
                text: msg
            });
        },

        configGrilla: (columns) => {
            let newConfig = [],
                permisos = localStorage.getItem('listPermisosFilterByRolUser')
            columns.map((column) => {
                if (column.PermisoVer && permisos.includes(column.PermisoVer) || !column.PermisoVer) {
                    newConfig.push({
                        headerClass: 'bg-info',
                        headerName: column.alias,
                        pinned: column.pinned,
                        resizable: true,
                        field: column.IdCampo,
                        sortable: true,
                        filter: column.filtro == 'true' ? true : column.filtro,
                        hide: column.visible == 'false' ? true : false,
                        valueFormatter: params => (column.FormatoCelda === 'FormatoMoneda') ? formatoMoneda(params.value) : params.value,
                        width: 147,
                        cellStyle: (params) => {
                            if (params.node.rowPinned) {
                                return { 'font-style': 'italic', 'background-color': '#87a1ea', 'font-weight': 'bold', 'text-align': 'right' };
                            }
                            if (column.Float) {
                                return { 'text-align': `${column.Float}` }
                            }
                        }
                    })
                }
            })
            return newConfig
        },

        idioma: () => {
            return servicesApp.traducirTextosAggrid();
        },

        sidebar: () => {
            return {
                toolPanels: [{
                        id: 'columns',
                        labelDefault: 'Columns',
                        labelKey: 'columns',
                        iconKey: 'columns',
                        toolPanel: 'agColumnsToolPanel',
                        toolPanelParams: {
                            suppressPivotMode: true,
                            suppressRowGroups: true,
                            suppressValues: true,
                        }
                    },
                    {
                        id: 'filters',
                        labelDefault: 'Filters',
                        labelKey: 'filters',
                        iconKey: 'filter',
                        toolPanel: 'agFiltersToolPanel',
                    },
                ],
                position: 'left',
                defaultToolPanel: 'filters',
                hiddenByDefault: false,
            }
        },

        inputOptionsDate: () => {
            return {

                pickerOptions: {
                    shortcuts: [{
                        text: 'Ult. Semana',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            console.log(start)
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: 'Ult. Mes',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: 'Ult. 3 Meses',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
            }
        },
    }
}

export default clousureServices()