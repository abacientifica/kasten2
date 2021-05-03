<template>
    <div>
        <!--Contenido Principal del Sitio-->
        <transition name="slide" mode="out-in">
            <router-view></router-view>
        </transition>
        <!---->
    </div>
</template>
<script>
export default {
    
}
</script>