var elixir = require('laravel-elixir');
var debug  = require('gulp-debug');

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
        'bower_components/bootstrap-fileinput/css/fileinput.css',
        'bower_components/dropzone/dist/dropzone.css',
        'bower_components/blueimp-gallery/css/blueimp-gallery.css',
        'bower_components/blueimp-gallery/css/blueimp-gallery-indicator.css',
        'bower_components/blueimp-bootstrap-image-gallery/css/bootstrap-image-gallery.css',
        'bower_components/jcrop/css/jquery.Jcrop.css',
        'public/css/app.css'
    ], '', './'),
    mix.scripts([
        'bower_components/javascript-debug/ba-debug.js',
        'bower_components/jquery/dist/jquery.js',
        'resources/assets/js/jquery.pixel.js',
        'bower_components/bootstrap/js/dropdown.js',
        'bower_components/color-thief/src/color-thief.js',
        'bower_components/bootstrap-sweetalert/lib/sweet-alert.js',
        'bower_components/bootstrap-fileinput/js/fileinput.js',
        'bower_components/dropzone/dist/dropzone.js',
        'bower_components/blueimp-gallery/js/blueimp-gallery.js',
        'bower_components/blueimp-gallery/js/blueimp-gallery-indicator.js',
        'bower_components/blueimp-gallery/js/jquery.blueimp-gallery.js',
        'bower_components/blueimp-bootstrap-image-gallery/js/bootstrap-image-gallery.js',
        'bower_components/jcrop/js/jquery.Jcrop.js',
        'resources/assets/js/pixel.js',
        'resources/assets/js/pixel.*.js',
        'resources/assets/js/fileinput.js',
        'resources/assets/js/dropzone.js',
        'resources/assets/js/image-toolbars.js',
        'resources/assets/js/app.js'
    ], '', './')
    mix.version(["css/all.css", "js/all.js"]);
    mix.copy('bower_components/font-awesome/fonts/**', 'public/build/fonts/');
    mix.copy('bower_components/bootstrap/fonts/**', 'public/build/fonts/');
    mix.copy('bower_components/jcrop/css/Jcrop.gif', 'public/build/css/Jcrop.gif');
    mix.copy('resources/assets/images/', 'public/build/images/');
});
