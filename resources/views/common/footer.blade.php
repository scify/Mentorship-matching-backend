@vite('resources/assets/js/app.js') {{-- jQuery, Bootstrap, vendor libraries, app code and page controllers --}}
<script type="module">
    $(document).ready(function () {
        // display properly the pickers and their labels
        $("input.bootstrap-daterangepicker-basic").addClass("valid");
        (new SearchController()).init();
    });
</script>

<section>
    @yield('additionalFooter')
</section>
