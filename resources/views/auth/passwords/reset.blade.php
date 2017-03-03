@extends('layouts.auth')

@section('content')
    <div class="bg-login printable">
        <div class="login-screen">
            <div class="panel-login blur-content">
                <div class="panel-heading"><img src="{{asset('/assets/img/jobpairs_logo.png')}}" height="120" alt="">
                </div><!--.panel-heading-->

                <div id="pane-forgot-password" class="panel-body active">
                    <form class="form-horizontal forgotPasswordForm noInputStyles" role="form" method="POST" action="{{ url('/password/reset') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <h2>Reset your password</h2>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input type="email" class="form-control" name="email" placeholder="Enter your email">
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input id="password" type="password" class="form-control" name="password" required placeholder="Enter your new password">
                                </div>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">                            
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Re-enter your new password">
                                </div>
                            </div>
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-buttons clearfix">                            
                            <button type="submit" class="btn btn-primary pull-right btn-ripple">Reset password</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
        <div class="bg-blur dark active">
            <div class="overlay"></div><!--.overlay-->
        </div><!--.bg-blur-->
    </div>
@endsection