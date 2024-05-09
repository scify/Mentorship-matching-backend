const {Pleasure} = require("../pleasure-admin-panel/js/pleasure");
const {Layout} = require("../pleasure-admin-panel/js/layout");
const {CustomFormsPickers} = require("../pleasure-admin-panel/js/custom-forms-pickers");
window._ = require('lodash');
let $ = require("jquery");
require('icheck');
require('bootstrap');
require('bootstrap-select');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import * as Sentry from "@sentry/browser";

if (process.env.MIX_SENTRY_DSN_PUBLIC) {
    Sentry.init({
        dsn: process.env.MIX_SENTRY_DSN_PUBLIC,
    });
}

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = $ = require('jquery');
} catch (e) {
    console.error(e);
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
