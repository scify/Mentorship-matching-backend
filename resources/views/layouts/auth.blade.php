<!DOCTYPE html>
<html>
<!-- Header -->
@include('common.header.header')
<link rel="stylesheet" href="{{asset(elixir('css/auth.css'))}}">
<body class="page-header-fixed" data-url="{!! URL::to('/') !!}">
<div class="content-wrapper">
    <!-- Main content -->
    <section class="">
        @if(session('flash_message_success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> {{ session('flash_message_success') }}</h4>
            </div>
        @endif

        @if(session('flash_message_failure'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> {{ session('flash_message_failure') }}</h4>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @foreach ($errors->all() as $error)
                    <h4><i class="icon fa fa-ban"></i> {{ $error }}</h4>
                @endforeach
            </div>
        @endif

        @yield('content')

    </section>
</div>
@include('common.footer')
</body>
</html>