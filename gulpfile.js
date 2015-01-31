var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

// files to version (cache bust)
//var version = [
// 'css/all.css',
// 'js/all.js'
//];

elixir(function(mix) {
    mix.less('app.less', 'public/css'),
    mix.styles([
        'vendor/components/font-awesome/css/font-awesome.css',
        'vendor/kartik-v/bootstrap-fileinput/css/fileinput.css',
        'public/css/app.css'
    ], './'),
    mix.scripts([
        'vendor/kartik-v/bootstrap-fileinput/js/fileinput.js',
        'resources/assets/js/app.js'
    ], './')
    mix.version(["css/all.css", "js/all.js"]);
    mix.copy('vendor/components/font-awesome/fonts/', 'public/build/fonts/');
});