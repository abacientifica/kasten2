<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                
            </div>
            <div class="car-body">
                <div class="row">
                    <div class="col-md-6" v-if="usuario.Tipo != 2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>Pedidos </h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ingresos">                                                
                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Pedidos de los últimos meses.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>{{TituloGrafica}}</h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ventas">                                                
                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>{{TituloGrafica}} de los últimos meses.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <vs-alert progress="70">
                    <template #title class="aling-center">
                    Hola {{usuario.Nombres}} !!!
                    </template>
                    Kasten es un software ERP de entorno web desarrollado para el manejo de Aba  científica, cualquier uso no autorizado de este programa será sancionado como la ley lo estipula para los derechos de autor, para mayor información comuníquese al (4) 444 6303 Ext 220 o al email sistemas@aba.com.co
                    <br>Bienvenido y esperamos que sea de su mayor utilidad.
                </vs-alert>
            </div>
        </div>
    </div>
    </main>
</template>
<script>
import Chart from 'chart.js';
export default {
    
    data() {
        return {
            TituloGrafica : "Ventas",
            varIngreso:null,
            charIngreso:null,
            ingresos:[],
            varTotalIngreso:[],
            varMesIngreso:[], 

            //Variables ventas
            varVenta:null,
            charVenta:null,
            ventas:[],
            varTotalVenta:[],
            varMesVenta:[],
            fullscreenLoading:false,
            usuario:[],
            moment:moment
        }
    },
    methods: {
        getPedidos(){
            let me=this;
            var url= '/dashboard';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.ingresos = respuesta.pedidos;
                //cargamos los datos del chart
                me.loadIngresos();
               
            })
            .catch(function (error) {
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401');
                console.log(msgerror)
                if(coderror == 401 || coderror == 419){
                    sessionStorage.clear();
                    localStorage.clear();
                    location.reload();
                    this.$router.push({name: 'login'})
                    
                }
            });
        },

        getVentas(){
            let me=this;
            var url= '/dashboard';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.ventas = respuesta.ventas;
                //cargamos los datos del chart
                me.loadVentas();
            })
            .catch(function (error) {
                let msgerror = error.message.split(" ");
                let coderror = msgerror.find(error => error == '401') ?  msgerror.find(error => error == '401') :  msgerror.find(error => error == '419');
                coderror =='' ? msgerror.find(error => error == '401') :coderror;
                if(coderror == 401 || coderror == 419){
                    sessionStorage.clear();
                    localStorage.clear();
                    location.reload();
                    this.$router.push({name: 'login'})
                    
                }
            });
        },

        loadVentas(){
            let me=this;
            me.ventas.map(function(x){
                var fecha = new Date();
                var ano = fecha. getFullYear();
                let mes = moment(ano+'-'+x.mes+'-01').format('MMMM');
                me.varMesVenta.push(mes+'('+x.anio+')');
                me.varTotalVenta.push(x.total);
            });
            me.varVenta=document.getElementById('ventas').getContext('2d');

            me.charVenta = new Chart(me.varVenta, {
                type: 'bar',
                data: {
                    labels: me.varMesVenta,
                    datasets: [{
                        label: 'Facturación (Valores antes de IVA)',
                        data: me.varTotalVenta,
                        backgroundColor: '#EBE4DB',
                        borderColor: '#D9CBBA',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                callback: function(value, index, values) {
                                    if(parseInt(value) >= 1000){
                                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return '$' + value;
                                    }
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var valor = tooltipItem.yLabel
                                valor = new Intl.NumberFormat('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                }).format(valor)

                                return data.datasets[tooltipItem.datasetIndex].label+": "+ valor;
                            }
                        }
                    }
                }
            });
        },

        loadIngresos(){
            let me=this;
            this.isLoading = true;
            me.ingresos.map(function(x){
                var fecha = new Date();
                var ano = fecha. getFullYear();
                let mes = moment(ano+'-'+x.mes+'-01').format('MMMM');
                me.varMesIngreso.push(mes);
                me.varTotalIngreso.push(x.total);
            });
            me.varIngreso=document.getElementById('ingresos').getContext('2d');

            me.charIngreso = new Chart(me.varIngreso, {
                type: 'bar',
                data: {
                    labels: me.varMesIngreso,
                    datasets: [{
                        label: 'Pedidos',
                        data: me.varTotalIngreso,
                        backgroundColor: '#EBE4DB',
                        borderColor: '#D9CBBA',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                callback: function(value, index, values) {
                                    if(parseInt(value) >= 20000){
                                        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return '$' + parseFloat(value).toFixed(2);
                                    } 
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var valor = tooltipItem.yLabel
                                valor = new Intl.NumberFormat('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                }).format(valor)

                                return data.datasets[tooltipItem.datasetIndex].label+": "+ valor;
                            }
                        }
                    }
                }
            });
            this.isLoading = false;
        },
    },

    FormatoMoneda : function(amount=0, decimals) {
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

        return  sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
    },

    mounted() {
        this.usuario = JSON.parse(sessionStorage.getItem('authUser'));
        if(this.usuario.Tipo == 2){
            this.TituloGrafica = 'Compras';
        }
        this.isLoading = true;
        this.getPedidos();
        this.getVentas();
        this.isLoading = false;
    },
}
</script>