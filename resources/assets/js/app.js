import './jquery-global';
import _ from 'lodash';
import 'jquery-validation/dist/jquery.validate.min.js';
import 'jquery-ui-dist/jquery-ui.min.js';
import 'icheck/icheck.min.js';
import 'chosen-js/chosen.jquery.js';
import 'select2/dist/js/select2.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'velocity-animate/velocity.min.js';
import 'moment';
import 'toastr';
import 'scrollmonitor/dist/module/index.js';
import 'textarea-autosize';
import 'bootstrap-select/dist/js/bootstrap-select.min.js';
import 'fastclick/lib/fastclick.js';
import 'jasny-bootstrap/dist/js/jasny-bootstrap.min.js';
import 'sweetalert/dist/sweetalert.min.js';
import 'datatables/media/js/jquery.dataTables.min.js';
import '../pleasure-admin-panel/js/sliders.js';
import { Layout } from '../pleasure-admin-panel/js/layout.js';
import { Pleasure } from '../pleasure-admin-panel/js/pleasure.js';
import 'bootstrap-daterangepicker/daterangepicker.js';
import { CustomFormsPickers } from '../pleasure-admin-panel/js/custom-forms-pickers.js';
import './FormController.js';
import './AvailabilityStatusChangeViewHandler.js';
import 'ion-rangeslider/js/ion.rangeSlider.min.js';
import './MentorsAndMenteesListsCssCorrector.js';
import './TabsHandler.js';
import './UniversityHandler.js';
import './ResidenceHandler.js';
import './ReferenceHandler.js';
import Popper from 'popper.js';
import * as Sentry from '@sentry/browser';
import './controllers/CompaniesListController.js';
import './controllers/MatchingController.js';
import './controllers/MenteesListController.js';
import './controllers/MentorshipSessionsListController.js';
import './controllers/MentorsListController.js';
import './controllers/RatingController.js';
import './controllers/SearchController.js';
import './controllers/UserFormController.js';
import './controllers/UserProfileController.js';
import './controllers/UsersListController.js';

window._ = _;
window.Popper = Popper;

if (import.meta.env.VITE_SENTRY_DSN_PUBLIC) {
    Sentry.init({
        dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
    });
}

$(document).ready(function () {
    console.log('Document ready');
    Pleasure.init();
    Layout.init();
    // initialize pickers
    CustomFormsPickers.init();
    $("[id^=tooltip-]").tooltip();
    setTimeout(function () {
        /*Close any flash message after some time*/
        $(".alert-dismissable").fadeTo(4000, 500).slideUp(500, function () {
            $(".alert-dismissable").alert('close');
        });
    }, 5000);

    // initialize iCheck
    $("input[type='checkbox'], input[type='radio']").iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass: 'iradio_flat-orange'
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
