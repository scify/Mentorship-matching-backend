<!DOCTYPE html>
<html>
<!-- Header -->
@include('common.header.header')
@if(Auth::check())
    @include('common.header.navbarVertical')
@endif
@include('common.header.navbarHorizontal')
<body class="page-header-fixed" data-url="{!! URL::to('/') !!}">
<div class="content-wrapper">
    <section class="content-header">
        <h3 style="float: left;"> {{isset($page_title) ? $page_title:''}} <span id="feedback-header"></span></h3>
        <div class="row example-row">
            <div class="col-md-9" style="float: right; padding-top: 10px;">
                <div class="loading-bar indeterminate margin-top-10" id="globalLoader" hidden></div>
            </div><!--.col-->
        </div><!--.row-->
    </section>

    <!-- Main content -->
    <section class="content" style="padding: 0px !important;">
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
<!-- Footer -->
@if(Auth::check())
    @include('common.sidebar', ['user' => \Illuminate\Support\Facades\Auth::user()])
@endif
@include('common.footer')
</body>
</html>