const mix = require('laravel-mix');
mix.disableSuccessNotifications();


mix.sass('resources/assets/sass/app.scss', 'public/css/app.css');
mix.sass('resources/assets/sass/auth.scss', 'public/css/auth.css');

mix.styles([
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/jasny-bootstrap/dist/css/jasny-bootstrap.min.css',
    'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
    'node_modules/datatables/media/css/jquery.dataTables.min.css',
    'node_modules/chosen-js/chosen.css',
    'node_modules/icheck/skins/flat/_all.css',
    'node_modules/bootstrap-daterangepicker/daterangepicker.css',
    'resources/assets/pleasure-admin-panel/css/admin1.css',
    'resources/assets/pleasure-admin-panel/css/elements.css',
    'resources/assets/pleasure-admin-panel/css/plugins.css',
    'node_modules/ion-rangeslider/css/ion.rangeSlider.min.css'

], 'public/css/vendors.css')
    .sourceMaps()
    .webpackConfig({
        devtool: 'source-map'
    }).version();

const directories = {
    'resources/assets/pleasure-admin-panel/fonts': 'public/build/fonts',
    'resources/assets/pleasure-admin-panel/fontawesome': 'public/build/fontawesome',
    'resources/assets/pleasure-admin-panel/ionicons': 'public/build/ionicons'
};

const files = {
    'node_modules/icheck/skins/flat/*.png': 'public/build/css',
    'node_modules/chosen-js/*.png': 'public/build/css',
    'resources/assets/pleasure-admin-panel/img/*.png': 'public/build/css'
};

for (const directory in directories) {
    mix.copy(directory, directories[directory]);
}

for (const file in files) {
    mix.copy(file, files[file]);
}

mix.js([
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
        'node_modules/scrollmonitor/dist/module/index.js',
        'node_modules/textarea-autosize/dist/textarea-autosize.js',
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
        'resources/assets/js/UniversityHandler.js',
        'resources/assets/js/ResidenceHandler.js',
        'resources/assets/js/ReferenceHandler.js'
    ],
    'public/js/libs.js')
    .js('resources/assets/js/app.js', 'public/js')
    .js([
        'resources/assets/js/controllers/CompaniesListController.js',
        'resources/assets/js/controllers/MatchingController.js',
        'resources/assets/js/controllers/MenteesListController.js',
        'resources/assets/js/controllers/MentorshipSessionsListController.js',
        'resources/assets/js/controllers/MentorsListController.js',
        'resources/assets/js/controllers/RatingController.js',
        'resources/assets/js/controllers/SearchController.js',
        'resources/assets/js/controllers/UserFormController.js',
        'resources/assets/js/controllers/UserProfileController.js',
        'resources/assets/js/controllers/UsersListController.js',
    ], 'public/js/controllers.js')
    .js([
        'resources/assets/js/AuthPage.js'
    ], 'public/js/auth.js')
    .js([
        'node_modules/iframe-resizer/js/iframeResizer.contentWindow.min.js'
    ], 'public/js/iframe-contentWindow.js')
    .js([
        'node_modules/iframe-resizer/js/iframeResizer.min.js'
    ], 'public/js/iframe.js')
    .extract([
        'jquery', 'bootstrap', 'bootstrap-select', 'moment'
    ])
    .sourceMaps()
    .webpackConfig({
        devtool: 'source-map'
    })
    .version();


mix.autoload({
    'jquery': ['$', 'window.jQuery', 'jQuery']
});
