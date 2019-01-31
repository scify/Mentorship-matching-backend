@extends('layouts.auth')

@section('content')
    <div class="bg-login printable">
        <div class="login-screen">
            <div class="panel-login blur-content">
                <div class="panel-heading"><img src="{{asset('/assets/img/jobpairs_logo.png')}}" height="120" alt="">
                </div><!--.panel-heading-->

                <div id="pane-login" class="panel-body active">
                    <h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 20px;">Job
                        Pairs</h1>
                    <p style="text-align: center; color: #F5F5F5; margin-bottom: 20px;">{!! $title !!}</p>
                    <div style="display: table; width: 100%">
                        <a href="{{ $accept_confirmation_url }}"
                           style="display: block; background-color: {{ $accept_confirmation_button_bg_color }}; text-align: center; color: white; padding: 10px; text-decoration: none; margin-left: auto; margin-right: auto;">
                            {{ $accept_confirmation_button_text }}
                        </a>
                        <p style="text-align: center; color: #F5F5F5; margin-bottom: 20px; margin-top: 20px">{!! $decline_title !!}</p>
                        <a href="{{ $decline_confirmation_url }}"
                           style="display: block; background-color: {{ $decline_confirmation_button_bg_color }}; text-align: center; color: white; padding: 10px; text-decoration: none; margin-left: auto; margin-right: auto;">
                            {{ $decline_confirmation_button_text }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-blur dark active">
            <div class="overlay"></div><!--.overlay-->
        </div><!--.bg-blur-->
    </div>
@endsection