/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.component('App', require('./components/App.vue').default);
Vue.component('sidebar', require('./components/plantilla/Sidebar.vue').default);
Vue.component('navbar', require('./components/plantilla/Navbar.vue').default);
Vue.component('Footer', require('./components/plantilla/Footer.vue').default);
import router from './routes.js';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
window.Vue.use(ElementUI); //Con esta declaracion lo utilizamos en todo el sitio
const app = new Vue({
    el: '#app',
    router
});