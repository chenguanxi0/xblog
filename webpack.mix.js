const {mix} = require('laravel-mix');

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

let js = [
    'resources/assets/js/jquery.js',
    'resources/assets/js/bootstrap.js',
    'resources/assets/js/hightlight.js',
    'resources/assets/js/marked.js',
    'resources/assets/js/autosize.min.js',
    'resources/assets/js/imgLiquid-min.js',
    'resources/assets/js/codemirror-4.inline-attachment.js',
    'resources/assets/js/sweetalert.min.js',
    'resources/assets/js/app.js'
];

mix.js(js, 'public/js/app.js').version();
mix.sass('resources/assets/sass/home.scss', 'public/css/home.css').version();
mix.sass('resources/assets/sass/app.scss', 'public/css/app.css').version();
