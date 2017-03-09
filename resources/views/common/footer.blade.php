<script src="{{asset(elixir('js/app.js'))}}"></script>
<script src="{{asset(elixir('js/controllers.js'))}}"></script>
<script>
    $(document).ready(function () {
        Pleasure.init();
        Layout.init();

        // initialize iCheck
        $("input[type='checkbox'], input[type='radio']").iCheck({
            checkboxClass: 'icheckbox_flat-orange',
            radioClass: 'iradio_flat-orange'
        });

        setTimeout(function(){
            /*Close any flash message after some time*/
            $(".alert-dismissable").fadeTo(4000, 500).slideUp(500, function(){
                $(".alert-dismissable").alert('close');
            });
        }, 4000);

        (new SearchController()).init();
    });
</script>

<section>
    @yield('additionalFooter')
</section>
