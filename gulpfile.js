// console.log(process.argv[3]);
//
// if (process.argv[3] == '--local' || process.argv[2] == '--local') {
//     process.env.DISABLE_NOTIFIER = true;
// }
process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');

// Include gulp
var gulp = require('gulp');

elixir(function(mix) {

    mix.sass('app.scss',  'public/css/app.css');

    mix.styles([

        'node_modules/sweetalert/dist/sweetalert.css',
        'node_modules/jasny-bootstrap/dist/css/jasny-bootstrap.min.css',
        'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
        'node_modules/datatables/media/css/jquery.dataTables.min.css',
        'node_modules/chosen-js/chosen.css',
        'node_modules/icheck/skins/flat/_all.css',
        'resources/assets/pleasure-admin-panel/css/admin1.css',
        'resources/assets/pleasure-admin-panel/css/elements.css',
        'resources/assets/pleasure-admin-panel/css/plugins.css'

    ], 'public/css/vendors.css', './');

    var directories = {
        'resources/assets/pleasure-admin-panel/fonts': 'public/build/fonts',
        'resources/assets/pleasure-admin-panel/fontawesome': 'public/build/fontawesome',
        'resources/assets/pleasure-admin-panel/ionicons': 'public/build/ionicons'
    };

    var files = {
        'node_modules/icheck/skins/flat/*.png': 'public/build/css',
        'node_modules/chosen-js/*.png': 'public/build/css',
        'resources/assets/pleasure-admin-panel/img/*.png' : 'public/build/css'
    };

    for (var directory in directories) {
        mix.copy(directory, directories[directory]);
    }

    for (var file in files) {
        mix.copy(file, files[file]);
    }

    mix.scripts([
            'node_modules/npm-modernizr/modernizr.js',
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/jquery-validation/dist/jquery.validate.min.js',
            'node_modules/jquery-ui-dist/jquery-ui.min.js',
            'node_modules/chosen-js/chosen.jquery.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/velocity-animate/velocity.min.js',
            'node_modules/moment/moment.js',
            'node_modules/toastr/toastr.js',
            'node_modules/scrollmonitor/scrollMonitor.js',
            'node_modules/textarea-autosize/dist/jquery.textarea_autosize.min.js',
            'node_modules/bootstrap-select/dist/js/bootstrap-select.min.js',
            'node_modules/fastclick/lib/fastclick.js',
            'node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js',
            'node_modules/sweetalert/dist/sweetalert.min.js',
            'node_modules/datatables/media/js/jquery.dataTables.min.js',
            'node_modules/icheck/icheck.min.js',
            'resources/assets/pleasure-admin-panel/js/sliders.js',
            'resources/assets/pleasure-admin-panel/js/layout.js',
            'resources/assets/pleasure-admin-panel/js/pleasure.js'
        ],
        'public/js/app.js' , './')
        .scripts([
            'resources/assets/js/controllers/'
        ], 'public/js/controllers.js');


    //the parameter is relative to the public directory
    mix.version(['css/app.css', 'css/vendors.css', 'js/app.js', 'js/controllers.js']);
});