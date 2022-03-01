import { Result } from "element-ui";

export default class ServicesApp {

    ObtenerDatosGrillaAgGrid(datos) {
        let arrItems = [];
        datos.forEachNodeAfterFilter((node, index) => {
            arrItems.push(node);
        })
        return arrItems;
    }

    traducirTextosAggrid() {
        return {
            // for filter panel
            page: 'Pagina',
            more: 'Mas',
            to: 'a',
            of: 'de',
            next: 'Siguente',
            last: 'Último',
            first: 'Primero',
            previous: 'Anteror',
            loadingOoo: 'Cargando...',

            // for set filter
            selectAll: 'Seleccionar Todo',
            searchOoo: 'Buscar...',
            blanks: 'En blanco',

            // for number filter and text filter
            filterOoo: 'Filtrar',
            applyFilter: 'Aplicar Filtro...',
            equals: 'Igual',
            notEqual: 'No Igual',

            // for number filter
            lessThan: 'Menor que',
            greaterThan: 'Mayor que',
            lessThanOrEqual: 'Menor o igual que',
            greaterThanOrEqual: 'Mayor o igual que',
            inRange: 'En rango de',

            // for text filter
            contains: 'Contiene',
            notContains: 'No contiene',
            startsWith: 'Empieza con',
            endsWith: 'Termina con',

            // filter conditions
            andCondition: 'Y',
            orCondition: 'O',

            // the header of the default group column
            group: 'Grupo',

            // tool panel
            columns: 'Columnas',
            filters: 'Filtros',
            valueColumns: 'Valos de las Columnas',
            pivotMode: 'Modo Pivote',
            groups: 'Grupos',
            values: 'Valores',
            pivots: 'Pivotes',
            toolPanelButton: 'BotonDelPanelDeHerramientas',

            // other
            noRowsToShow: 'No hay filas para mostrar',

            // enterprise menu
            pinColumn: 'Columna Pin',
            valueAggregation: 'Agregar valor',
            autosizeThiscolumn: 'Autoajustar esta columna',
            autosizeAllColumns: 'Ajustar todas las columnas',
            groupBy: 'agrupar',
            ungroupBy: 'desagrupar',
            resetColumns: 'Reiniciar Columnas',
            expandAll: 'Expandir todo',
            collapseAll: 'Colapsar todo',
            toolPanel: 'Panel de Herramientas',
            export: 'Exportar',
            csvExport: 'Exportar a CSV',
            excelExport: 'Exportar a Excel (.xlsx)',
            excelXmlExport: 'Exportar a Excel (.xml)',


            // enterprise menu pinning
            pinLeft: 'Pin Izquierdo',
            pinRight: 'Pin Derecho',


            // enterprise menu aggregation and status bar
            sum: 'Suman',
            min: 'Minimo',
            max: 'Maximo',
            none: 'nada',
            count: 'contar',
            average: 'promedio',

            // standard menu
            copy: 'Copiar',
            copyWithHeaders: 'Copiar con cabeceras',
            paste: 'Pegar'
        }
    }

    ObtenerFiltrosListaCotizaciones() {
        let url = "/cotizaciones/filtros/usuario/"
        let Filtros = [];
        axios.post(url).then(response => {
            Filtros.push(response.data.filtros);
        });
        return Filtros;
    }

    ObtenerTpError(error) {
        let msgerror = error.message.split(" ");
        let coderror = msgerror.find(error => error == '401') ? msgerror.find(error => error == '401') : msgerror.find(error => error == '419');
        coderror == '' ? msgerror.find(error => error == '401') : msgerror.find(error => error == '419');
        return coderror;
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

    validarStatus(me, status, statusText) {
        if (status === 200) {
            return true
        } else if (status === 401 || status === 419) {
            me.$router.push({ name: 'login' })
            location.reload();
            localStorage.clear();
            sessionStorage.clear();
            return false
        } else if (status === 500) {
            this.alertMessage(me, `Ocurrio un error : ${status } ${statusText}`, 'danger')
            return false
        } else {
            return false
        }
    }

    alertMessage(me, message, tipo = '', time = 1800) {
        me.$vs.notification({
            duration: time,
            progress: 'auto',
            flat: true,
            color: tipo,
            position: 'top-center',
            title: null,
            text: message
        })
    }

    generatePinnedButtomData(me, apiColumns, arrColumnsCalculated) {
        let result = {};
        apiColumns.getAllGridColumns().forEach(item => {
            result[item.colId] = null;

        });
        return this.calculatedPinnedButtomData(result, me, arrColumnsCalculated);
    }

    calculatedPinnedButtomData(target, me, arrColumnsCalculated) {
        let columnWithAggregation = arrColumnsCalculated,
            count = 0;
        columnWithAggregation.forEach(element => {
            if (count > 0) {
                me.gridApi.api.forEachNodeAfterFilter(function(rowNode) {
                    target[element] += Number(typeof rowNode.data[element] == 'number' && rowNode.data[element].toFixed(2));
                });
                if (target[element]) {
                    target[element] = `${target[element].toFixed(2)}`;
                }
            } else {
                target[element] = 'Totales :'
            }
            count++
        })
        return target;
    }
}