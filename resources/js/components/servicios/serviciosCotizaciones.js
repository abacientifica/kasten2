export default class serviciosCotizaciones {

    async getCotizacion(id) {
        let url = '/cotizacion/' + id
        const datos = await axios.get(url);
        return datos;
    }

    configuracionColumna(objColumna, permisos, estado, me) {
        let col = objColumna;
        let visible = objColumna.visible == 'false' ? true : false;
        let configCol = null;
        let filtro = col.filtro == 'true' ? true : col.filtro;
        let editar = (col.edit === 'true' && estado == 'DIGITADA' && (this.validarPermiso(permisos, 'editardetallles') || (col.permiso && this.validarPermiso(permisos, col.permiso)))) ? true : false;
        const refsOpcion = {
            null: '',
            '1': 'SI',
            '0': 'NO'
        }

        const operacionesColumnas = (config) => {
            if (editar && col.tipoEdicion === 'select') {
                config = {...config,
                    cellEditor: 'select',
                    cellEditorParams: { values: [0, 1] },
                    refData: refsOpcion,
                }
            }
            if (col.tipoFiltro) {
                config.filter = col.tipoFiltro
            }
            return config
        }

        switch (col.columna) {
            case 'IdCotizacionDet':
                configCol = {
                    headerClass: 'bg-info',
                    headerName: col.alias,
                    pinned: 'left',
                    resizable: true,
                    field: col.columna,
                    cellRendererFramework: 'novedades',
                    headerCheckboxSelection: true,
                    checkboxSelection: true,
                    sortable: true,
                    filter: filtro,
                    tooltipField: col.columna,
                    width: 147,
                    hide: visible,
                    cellStyle: (params) => {
                        if (params.node.rowPinned) {
                            return { 'font-style': 'italic', 'background-color': '#87a1ea', 'font-weight': 'bold', 'text-align': 'right' };
                        }
                        if (col.float) {
                            return { 'float': col.float }
                        }
                    }
                };
                break;

            case 'Opciones':
                configCol = {
                    headerClass: 'bg-info',
                    headerName: '',
                    pinned: col.pinned,
                    resizable: true,
                    field: col.columna,
                    width: 50,
                    cellRenderer: function(params) {
                        let Homologado = params.data.NmListaCostos;
                        var tempDiv = document.createElement('div');
                        if (params.data.Autorizado != 1 && (params.data.EnlaceCot <= 0 || !params.data.EnlaceCot)) {
                            if (!params.node.rowPinned) {
                                if (Homologado) {
                                    tempDiv.innerHTML = '<span  class="btn btn-success btn-sm"><i class="fas fa-cog"></span>';
                                } else {
                                    tempDiv.innerHTML = '<span  class="btn btn-primary btn-sm"><i class="fas fa-cog"></span>';
                                }
                            }
                        }
                        return tempDiv;
                    },
                    onCellClicked: function(params) {
                        if (params.data.Autorizado != 1) {
                            me.accionesDetalle(params);
                        }
                    },
                    cellStyle: (params) => {
                        if (params.node.rowPinned) {
                            return { 'font-style': 'italic', 'background-color': '#87a1ea', 'font-weight': 'bold', 'text-align': 'right' };
                        }
                    }
                };
                break;

            default:
                configCol = {
                    headerClass: 'bg-info',
                    headerName: col.alias,
                    pinned: col.pinned,
                    resizable: true,
                    field: col.columna,
                    valueFormatter: (col.FormatoCelda == 'FormatoMoneda') ? this.FormatoMoneda() : '',
                    sortable: true,
                    filter: filtro,
                    hide: visible,
                    editable: editar,
                    tooltipField: col.columna,
                    width: 147,
                    cellStyle: (params) => {
                        if (params.node.rowPinned) {
                            return { 'font-style': 'italic', 'background-color': '#87a1ea', 'font-weight': 'bold', 'text-align': 'right' };
                        }
                        if (col.float) {
                            return { 'text-align': col.float }
                        }
                    }
                }
                break;
        }
        configCol = operacionesColumnas(configCol);
        return configCol;
    }

    validarPermiso(permisos, permiso) {
        if (permisos.includes('plantillas_clientes.' + permiso) || permisos.includes(permiso) || permisos.includes('administrador.sistema') || permisos.includes('plantillas_cliente_det.' + permiso)) {
            return true;
        } else {
            return false;
        }
    }

    cambioFiltros(me, params) {
        localStorage.removeItem('filtros_cot_det');
        let datosFiltro = JSON.stringify(params.api.getFilterModel());
        localStorage.setItem('filtros_cot_det', datosFiltro);
        this.guardarFiltros();
        me.itemsSeleccionados.length > 0 ? this.itemsSeleccionados = [] : '';
        //if (!localStorage.getItem('itemHmActual')) this.gridOptions.api.deselectAll();
    }

    cambioColumnas(estadoColumnas) {
        localStorage.removeItem('columnas_cot_det');
        let DatosFiltro = JSON.stringify(estadoColumnas);
        localStorage.setItem('columnas_cot_det', DatosFiltro);
        this.guardarFiltros();
    }

    guardarFiltros() {
        let url = "/cotizaciones/detalles/filtros";
        axios.post(url, {
            params: {
                'filtros': localStorage.getItem('filtros_cot_det'),
                'columnas': localStorage.getItem('columnas_cot_det'),
            }
        }).then(response => {
            let respuesta = response.data;
        }).catch(error => {
            console.log(error)
        })
    }

    FormatoMoneda(amount = 0, decimals = 2) {
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


}