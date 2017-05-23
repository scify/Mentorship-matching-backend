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

    mix.sass(['app.scss', 'profile_page.scss', 'mentor_mentee_item.scss', 'plugins_custom.scss', 'mentorship_session_item.scss',
            'reports.scss'
        ],
        'public/css/app.css'
    );
    mix.sass(['auth.scss'],  'public/css/auth.css');

    mix.styles([
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/sweetalert/dist/sweetalert.css',
        'node_modules/jasny-bootstrap/dist/css/jasny-bootstrap.min.css',
        'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
        'node_modules/datatables/media/css/jquery.dataTables.min.css',
        'node_modules/chosen-js/chosen.css',
        'node_modules/icheck/skins/flat/_all.css',
        'node_modules/bootstrap-daterangepicker/daterangepicker.css',
        'resources/assets/pleasure-admin-panel/css/admin1.css',
        'resources/assets/pleasure-admin-panel/css/elements.css',
        'resources/assets/pleasure-admin-panel/css/plugins.css',
        'node_modules/ion-rangeslider/css/ion.rangeSlider.css',
        'node_modules/ion-rangeslider/css/ion.rangeSlider.skinModern.css'

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
            'node_modules/icheck/icheck.min.js',
            'node_modules/chosen-js/chosen.jquery.js',
            'node_modules/select2/dist/js/select2.min.js',
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
            'resources/assets/pleasure-admin-panel/js/sliders.js',
            'resources/assets/pleasure-admin-panel/js/layout.js',
            'resources/assets/pleasure-admin-panel/js/pleasure.js',
            'node_modules/bootstrap-daterangepicker/daterangepicker.js',
            'resources/assets/pleasure-admin-panel/js/custom-forms-pickers.js',
            'resources/assets/js/FormController.js',
            'resources/assets/js/AvailabilityStatusChangeViewHandler.js',
            'node_modules/ion-rangeslider/js/ion.rangeSlider.min.js',
            'resources/assets/js/MentorsAndMenteesListsCssCorrector.js',
            'resources/assets/js/TabsHandler.js',
            'resources/assets/js/UniversityHandler.js'
        ],
        'public/js/app.js' , './')
        .scripts([
            'resources/assets/js/controllers/'
        ], 'public/js/controllers.js')
        .scripts([
            'resources/assets/js/AuthPage.js'
        ], 'public/js/auth.js')
        .scripts([
            'node_modules/iframe-resizer/js/iframeResizer.contentWindow.min.js'
        ], 'public/js/iframe-contentWindow.js', './')
        .scripts([
            'node_modules/iframe-resizer/js/iframeResizer.min.js'
        ], 'public/js/iframe.js', './');


    //the parameter is relative to the public directory
    mix.version(['css/app.css', 'css/auth.css', 'css/vendors.css', 'js/app.js', 'js/controllers.js']);
});
