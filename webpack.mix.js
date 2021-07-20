const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ])
   .autoload({
        jquery: ['$', 'window.jQuery']
    })
    .js('resources/js/common.js', 'public/js')
    .scripts([
        'resources/js/hls.js',
        'resources/js/video.js',
     ], 'public/js/all.js')
    .sass('resources/sass/fonts.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .styles([
        'resources/css/reset.css',
        'resources/css/video-js.css',
        'resources/css/vue.css'
    ], 'public/css/all.css');
