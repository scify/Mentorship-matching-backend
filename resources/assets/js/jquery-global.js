// Every entry imports this first. ESM evaluates imports in source order, so
// jQuery is on window before any jQuery plugin file (Bootstrap 3, icheck,
// chosen, jasny, bootstrap-select, toastr, daterangepicker) executes.
import $ from 'jquery';

window.$ = window.jQuery = $;
