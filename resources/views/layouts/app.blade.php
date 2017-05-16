<!DOCTYPE html>
<html>
<!-- Header -->
@if( !isset($publicForm))
    @php($publicForm = false)
@endif
@include('common.header.header')
@if(Auth::check() && !$publicForm)
    @include('common.header.navbarVertical', ['user' => \Illuminate\Support\Facades\Auth::user()])
@endif
{{--@include('common.header.navbarHorizontal')--}}
<body class="page-header-fixed {{ $publicForm == true ? 'publicForm':'' }}" data-url="{!! URL::to('/') !!}">
<div class="content">
    <!-- Main content -->

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
        @if (count($errors) > 0 && !$publicForm)
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @foreach ($errors->all() as $error)
                    <h4><i class="icon fa fa-ban"></i> {{ $error }}</h4>
                @endforeach
            </div>
        @endif

        @if(isset($pageTitle))
            <div class="page-header full-content margin-top-0">
                <div class="row">
                    <div class="col-sm-6">
                        <h1> {{$pageTitle}} <small> {{ isset($pageSubTitle) ? $pageSubTitle : ''}} </small></h1>
                    </div><!--.col-->
                    <div class="col-sm-6">
                        <ol class="breadcrumb">
                            <li><a href="{{route('home')}}" class="active"><i class="ion-home"></i></a></li>
                            <li><a href="#">{{$pageTitle}}</a></li>
                        </ol>
                    </div><!--.col-->
                </div><!--.row-->
            </div><!--.page-header-->
        @endif

        @yield('content')

</div>
<!-- Footer -->
@if(Auth::check() && !$publicForm)
    @include('common.sidebar', ['user' => \Illuminate\Support\Facades\Auth::user()])
@endif
@include('common.footer')
</body>
</html>
