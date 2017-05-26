@extends('emails.common')
@section('email-body')
<h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 10px;">Job Pairs</h1>
    <p style="text-align: center;">Congratulations!</p>
    <p style="text-align: center;">Your mentorship session was just completed!</p>
    <p style="text-align: center;">Click on the link below to make your profile available for a new session</p>
    <p style="text-align: center;">
        Test email from {{ $email }}
    </p>
@endsection
