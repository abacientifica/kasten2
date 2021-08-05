const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.styles([
        'resources/vendor/css/all.min.css',
        'resources/vendor/css/adminlte.css',
        'resources/vendor/css/transicion.css',
        'resources/vendor/css/styles-aggrid.scss',
    ], 'public/css/plantilla.css')
    .js('resources/js/app.js', 'public/js') //Este es el que inclue laravel que viene con boostap,Jquery,Vue
    .scripts([
        'resources/vendor/js/adminlte.js',
        'resources/vendor/js/demo.js',
    ], 'public/js/plantilla.js')
    .copy('resources/vendor/fontawesome/webfonts', 'public/webfonts')
    .copy('resources/vendor/img', 'public/img');