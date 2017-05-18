@extends('emails.common')
@section('email-body')
    <h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 10px;">Job Pairs</h1>
    <p style="text-align: center;">Test email</p>
    <p style="text-align: center;">You can login here:
        <a href="{{ url('/login') }}" style="color: #555;">{{ url("/login") }}</a>
    </p>
    <div style="display: table; margin-left: auto; margin-right: auto; margin-top: 40px; margin-bottom: 40px;">
        This is a test email.
    </div>
@endsection
