<template id="paginators">
    <div  class="mb-6">
        <vue-ads-pagination 
            :total-items="totalData"
            :max-visible-pages="3"
            :itemsPerPage="itemPorPagina"
            :loading="loading"
            :page="page"
        >
            <template
                slot="buttons"
                slot-scope="props"
            >
                <vue-ads-page-button
                    v-for="(button, key) in props.buttons"
                    :key="key"
                    v-bind="button"
                    :class="{'bg-yellow-dark': button.active}"
                    @page-change="pageChange(button.page)"
                    @range-change="rageChange(button.start, button.end)"
                />
            </template>
        </vue-ads-pagination>
    </div>
</template>
<script>
//import 'vue-ads-pagination/dist/vue-ads-pagination.css';
import VueAdsPagination, { VueAdsPageButton } from 'vue-ads-pagination';
export default {
    components: {
        VueAdsPagination,
        VueAdsPageButton,
    },

    props:{
        totalItems:{
            type:Number,
            required:true,
            default:0
        },
        itemPorPagina:{
            type:Number,
            required:true,
            default:0
        }
    },

    components: {
       VueAdsPagination,
       VueAdsPageButton,
    },
    
    data() {
        return {
            totalData : this.totalItems,
            totalPorPagina : 10,
            page: 0,
            start: 0,
            end: 0,
            loading: false,
        }
    },

    watch:{
        totalItems(newVal){
            this.totalData = newVal
        }
    },
    
    methods: {
    	pageChange (page) {
            this.page = page;
            //Emitimos el evento para que el padre lo escuche y cambie la pagina
            this.$emit('paginaSelect',page);
        },
        
        rageChange (start, end) {
        	this.start = start;
            this.end = end;
        }
    },

    mounted() {
        console.log(this.totalData)
    },
}
</script>
<style>


html {
    line-height: 1.15;
    -webkit-text-size-adjust: 100%
}

main {
    display: block
}

strong {
    font-weight: bolder
}

button,
input {
    font-family: inherit;
    font-size: 100%;
    line-height: 1.15;
    margin: 0;
    overflow: visible
}

button {
    text-transform: none
}

[type=button],
[type=reset],
[type=submit],
button {
    -webkit-appearance: button
}

[type=button]::-moz-focus-inner,
[type=reset]::-moz-focus-inner,
[type=submit]::-moz-focus-inner,
button::-moz-focus-inner {
    border-style: none;
    padding: 0
}

[type=button]:-moz-focusring,
[type=reset]:-moz-focusring,
[type=submit]:-moz-focusring,
button:-moz-focusring {
    outline: 1px dotted ButtonText
}

legend {
    color: inherit;
    display: table;
    max-width: 100%;
    white-space: normal
}

[type=checkbox],
[type=radio],
legend {
    box-sizing: border-box;
    padding: 0
}

[type=number]::-webkit-inner-spin-button,
[type=number]::-webkit-outer-spin-button {
    height: auto
}

[type=search] {
    -webkit-appearance: textfield;
    outline-offset: -2px
}

[type=search]::-webkit-search-decoration {
    -webkit-appearance: none
}

::-webkit-file-upload-button {
    -webkit-appearance: button;
    font: inherit
}

[hidden],
template {
    display: none
}

html {
    box-sizing: border-box;
    font-family: sans-serif
}

*,
:after,
:before {
    box-sizing: inherit
}

button {
    background: transparent;
    padding: 0
}

button:focus {
    outline: 1px dotted;
    outline: 5px auto -webkit-focus-ring-color
}

html {
    font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
    line-height: 1.5
}


button:not(:disabled), [type="button"]:not(:disabled), [type="reset"]:not(:disabled), [type="submit"]:not(:disabled) {
    cursor: pointer;
    border-width: 0;
    border-style: solid;
    border-color: #e2e8f0;
}

input::placeholder {
    color: #a0aec0
}

[role=button],
button {
    cursor: pointer
}

button,
input {
    padding: 0;
    line-height: inherit;
    color: inherit
}

canvas {
    display: block;
    vertical-align: middle
}

.vue-ads-bg-gray-100 {
    background-color: #f7fafc
}

.vue-ads-bg-gray-200 {
    background-color: #edf2f7
}

.vue-ads-bg-teal-500 {
    background-color: #38b2ac
}

.hover\:vue-ads-bg-gray-100:hover {
    background-color: #f7fafc
}

.vue-ads-cursor-default {
    cursor: default
}

.vue-ads-flex {
    display: flex
}

.vue-ads-justify-end {
    justify-content: flex-end
}

.vue-ads-flex-grow {
    flex-grow: 1
}

.vue-ads-leading-normal {
    line-height: 1.5
}

.vue-ads-leading-loose {
    line-height: 2
}

.vue-ads-m-2 {
    margin: .5rem
}

.vue-ads-ml-1 {
    margin-left: .25rem
}

.focus\:vue-ads-outline-none:focus,
.vue-ads-outline-none {
    outline: 0
}

.vue-ads-px-0 {
    padding-left: 0;
    padding-right: 0
}

.vue-ads-pr-2 {
    padding-right: .5rem
}

.vue-ads-text-white {
    color: #fff
}

.vue-ads-text-xs {
    font-size: .75rem
}

.vue-ads-w-6 {
    width: 1.5rem
}

</style>
