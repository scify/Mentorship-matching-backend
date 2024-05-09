<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script> {{-- Vendor libraries like jQuery, bootstrap --}}
<script src="{{ mix('js/libs.js') }}"></script> {{-- Other Libraries --}}
<script src="{{ mix('js/app.js') }}"></script> {{-- our application common code --}}
<script src="{{mix('js/controllers.js')}}"></script>
<script>
    $(document).ready(function () {
        // display properly the pickers and their labels
        $("input.bootstrap-daterangepicker-basic").addClass("valid");
        (new SearchController()).init();
    });
</script>

<section>
    @yield('additionalFooter')
</section>
