<template>
    <div>
        <div ref="example-element">{{counter}}</div>
         <!--<h3>{{counter}}</h3>-->
    </div>
</template>
<script>
export default {
    data() {
        return {
            property: 'Example Property',
            counter:0,
            exampleDetroyproperty:'Esto representa una propiedad que perdera memoria si no se limpia'
        }
    },

    computed:{
        propertyComputed(){
            return this.property;
        }
    },

    //En este punto vue todavía no ha cargado el componente, esto quiere decir que no se puede acceder a ninguna opcion, ni metodo ni data del componente,
    //Se suele usar para hacer comprobaciones antes de ingresar a una ruta, por ejemplo comprobar si el usuario esta logueado antes de entrar a una ruta o redireccionar al login

    beforeCreate() {
        console.log("beforeCreate() se ejecuta justo al inicializar el componente los datos no se han echo reactivos no se han configurado todavía");
    },

    //En este ciclo de vida se cargan todas las opciones del componente como arrays de data llenado de propiedades, pero todavia no se puede acceder al DOM
    created() {
        console.log("created() En este punto this.property es reactivo y propertyComputed se actualizara");
        this.property = 'Example Property Update'

        /*setInterval(()=>{
            this.counter++;
        },1000)*/
    },

    //En este ciclo si se tendra acceso total al DOM, el computed se ejecuta exactamente cuando se termina de pintar la vista, por lo tanto desde aqui podemos realizar cambios en la vista.
    mounted(){
        console.log("mounted() En este punto  se ha creado el vm.$el y se ha reemplazado 'el' ");
        //console.log(this.$el.textContent)
    },

    //Se utiliza si se necesita obtener un nuevo estado de cualquier dato reactivo de su componente antes de que se renderice nuevamente
    beforeUpdate() {
        console.log('En este punto, virtual DOM no se ha vuelto a renderizar ni a parchear')
        console.log(this.counter)
    },

    //Se usa si se necesita acceder al DOM despues de cambiar una propiedad
    updated() {
        console.log('En este punto, virtual DOM se ha vuelto a renderizar y ha parchear')
        console.log(+this.$refs['example-element'].textContent !== this.counter)
    },

    //Se utiliza si se necesita limpiar eventos o subcripciones reactivas
    beforeDestroy() {
        console.log('En este punto los observadores,componentes segundarios y los detectores de eventos a un no se han eliminado');
        this.exampleDetroyproperty = null;
        delete this.exampleDetroyproperty;
    },

    destroyed() {
        console.log(this)
    },

    
}
</script>