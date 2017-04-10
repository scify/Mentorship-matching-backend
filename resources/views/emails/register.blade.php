@extends('emails.common')
@section('email-body')
    <h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 10px;">Job Pairs</h1>
    <p style="text-align: center;">Your account at Job Pairs BackOffice has been created</p>
    <p style="text-align: center;">You can login here:
        <a href="{{ url('/login') }}">{{ url("/login") }}</a>
    </p>
    <div style="display: table; margin-left: auto; margin-right: auto; margin-top: 40px; margin-bottom: 40px;">
        <div style="display: table-row;">
            <p style="text-align: right; font-size: 16px; font-weight: bold; display: table-cell; padding-right: 10px;">email:</p>
            <p style="display: table-cell; color: #555;">{{ $email }}</p>
        </div>
        <div style="display: table-row;">
            <p style="text-align: right; font-size: 16px; font-weight: bold; display: table-cell; padding-right: 10px;">password:</p>
            <p style="display: table-row; color: #555;">{{ $password }}</p>
        </div>
    </div>
@endsection
