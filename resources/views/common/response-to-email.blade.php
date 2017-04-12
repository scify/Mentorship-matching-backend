@extends('layouts.auth')

@section('content')
    <div class="bg-login printable">
        <div class="login-screen">
            <div class="panel-login blur-content">
                <div class="panel-heading"><img src="{{asset('/assets/img/jobpairs_logo.png')}}" height="120" alt="">
                </div><!--.panel-heading-->

                <div id="pane-login" class="panel-body active">
                    <h2>{{ $title }}</h2>
                    @if(isset($message_success))
                    <div class="alert alert-success">
                        <h4><i class="icon fa fa-check"> </i>
                            {{ $message_success }}
                        </h4>
                    </div>
                    @elseif(isset($message_failure))
                    <div class="alert alert-danger" style="display: block !important;">
                        <h4><i class="icon fa fa-ban"></i>
                            {{ $message_failure }}
                        </h4>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-blur dark active">
            <div class="overlay"></div><!--.overlay-->
        </div><!--.bg-blur-->
    </div>
@endsection
