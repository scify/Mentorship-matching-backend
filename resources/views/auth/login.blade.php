@extends('layouts.auth')

@section('content')
    <div class="bg-login printable">
        <div class="login-screen">
            <div class="panel-login blur-content">
                <div class="panel-heading"><img src="{{asset('/assets/img/jobpairs_logo.png')}}" height="120" alt="">
                </div><!--.panel-heading-->

                <div id="pane-login" class="panel-body active">

                    <form class="form-horizontal loginForm noInputStyles" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}
                        <h2>Login to BackOffice</h2>
                        <div class="centeredVertically">
                        <div class="form-group {{ $errors->has('email_address') ? ' has-error' : '' }}">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input type="email" class="form-control" name="email_address" placeholder="Enter your email">
                                </div>
                            </div>
                            @if ($errors->has('email_address'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email_address') }}</strong>
                                    </span>
                            @endif
                        </div><!--.form-group-->

                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="Enter your password" required>
                                </div>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div><!--.form-group-->

                        <div class="form-buttons clearfix">
                            <div class="icheckbox pull-left">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}>
                                    Remember Me
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary pull-right">
                                Login
                            </button>
                        </div><!--.form-buttons-->
                        </div>

                        <ul class="extra-links">
                            <li><a href="javascript:void(0)" class="show-pane-forgot-password">Forgot your password</a></li>
                        </ul>

                        {{--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
                            {{--<label for="password" class="col-md-4 control-label">Password</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="password" type="password" class="form-control" name="password" required>--}}

                                {{--@if ($errors->has('password'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('password') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<div class="icheckbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}>--}}
                                        {{--Remember Me--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}


                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-8 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Login--}}
                                {{--</button>--}}

                                {{--<a class="btn btn-link" href="{{ url('/password/reset') }}">--}}
                                    {{--Forgot Your Password?--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </form>
                </div>

                <div id="pane-forgot-password" class="panel-body">
                    <form class="form-horizontal forgotPasswordForm noInputStyles" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}
                        <h2>Forgot your password</h2>
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="inputer">
                                <div class="input-wrapper">
                                    <input class="form-control" placeholder="Enter your email address" type="email" name="email">
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div> 
                        <div class="form-buttons clearfix">
                            <button type="button" class="btn btn-white pull-left show-pane-login btn-ripple">Cancel<span class="ripple _2 animate" style="height: 0px; width: 0px; top: 271px; left: 589px;"></span></button>
                            <button type="submit" class="btn btn-primary pull-right btn-ripple">Send</button>
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
@section('additionalFooter')
    <script>
        $(document).ready(function () {
            $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_flat-orange',
                radioClass: 'iradio_flat-orange'
            });

            var authPage = new window.AuthPage();
            authPage.init();    
        });
    </script>
@endsection
