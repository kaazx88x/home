

var gulp = require('gulp');
var replace = require('gulp-replace');
var elixir = require('laravel-elixir');

/* elixir assets base path */
// elixir.config.assetsPath = 'public/web/';

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */



// require('laravel-elixir-replace');

// var replacements = [
//      ['<!-- CSS Development Starts -->', '{{--<!-- CSS Development Starts -->'],
//      ['<!-- CSS Development Ends -->', '<!-- CSS Development Ends -->--}}'],
//      ['<!-- JS Development Starts -->', '{{--<!-- JS Development Starts -->'],
//      ['<!-- JS Development Ends -->', '<!-- JS Development Ends -->--}}'],
// ];

// elixir(function(mix) {
//     mix.replace('resources/views/layout/master.blade.php', replacements);
// });

gulp.task('dev', function() {
    gulp.src('resources/views/layouts/master.blade.php')
        .pipe(replace(/\{\{-- <!-- (.*)Development Starts \(Disabled\) -->/g, '<!-- $1Development Starts (Enabled) -->'))
        .pipe(replace(/<!-- (.*)Development Ends \(Disabled\) --> --\}\}/g, '<!-- $1Development Ends (Enabled) -->'))
        .pipe(replace(/<!-- (.*)Production Starts \(Enabled\) -->/g, '{{-- <!-- $1Production Starts (Disabled) -->'))
        .pipe(replace(/<!-- (.*)Production Ends \(Enabled\) -->/g, '<!-- $1Production Ends (Disabled) --> --}}'))
        .pipe(gulp.dest('resources/views/layouts'));
});

gulp.task('prod', function() {
    gulp.src('resources/views/layouts/master.blade.php')
        .pipe(replace(/<!-- (.*)Development Starts \(Enabled\) -->/g, '{{-- <!-- $1Development Starts (Disabled) -->'))
        .pipe(replace(/<!-- (.*)Development Ends \(Enabled\) -->/g, '<!-- $1Development Ends (Disabled) --> --}}'))
        .pipe(replace(/\{\{-- <!-- (.*)Production Starts \(Disabled\) -->/g, '<!-- $1Production Starts (Enabled) -->'))
        .pipe(replace(/<!-- (.*)Production Ends \(Disabled\) --> --\}\}/g, '<!-- $1Production Ends (Enabled) -->'))
        .pipe(gulp.dest('resources/views/layouts'));
});

elixir(function(mix) {

    // styles
    mix.styles([
        'lib/bootstrap/css/bootstrap.min.css',
        'lib/font-awesome/css/font-awesome.min.css',
        'css/reset.css',
        'css/animate.css',
        'lib/jquery-ui/jquery-ui.min.css',
        'lib/select2/css/select2.min.css',
        'lib/owl.carousel/owl.carousel.css',
        'lib/fancyBox/jquery.fancybox.css',
        'lib/sweetalert/sweetalert.css',
        'lib/custombox/css/custombox.min.css',
        'lib/flag-icon/css/flag-icon.min.css',
        'css/responsive.css',
        'css/style.css',
        'css/custom.css',
    ],
    'public/web/build/css/all.css',
    'public/web');

    // scripts
    mix.scripts([
        'lib/jquery/jquery-1.11.2.min.js',
        'lib/bootstrap/js/bootstrap.min.js',
        'lib/select2/js/select2.min.js',
        'lib/jquery.bxslider/jquery.bxslider.min.js',
        'lib/owl.carousel/owl.carousel.min.js',
        'lib/owl.carousel/owl.carousel.min.js',
        'lib/jquery.elevatezoom.js',
        'lib/sweetalert/sweetalert.min.js',
        'lib/custombox/js/custombox.min.js',
        'lib/custombox/js/legacy.min.js',
        'lib/jquery-ui/jquery-ui.min.js',
        'lib/countdown/jquery.plugin.js',
        'lib/countdown/jquery.countdown.js',
        'lib/fancyBox/jquery.fancybox.js',
        'js/jquery.actual.min.js',
        'js/theme-script.js',
    ],
    'public/web/build/js/all.js',
    'public/web');

    mix.version(['web/build/css/all.css' , 'web/build/js/all.js']);

    // replacements
    mix.task('prod');
});
