@extends('emails.common')
@section('email-body')
    <h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 10px;">Job Pairs</h1>
    <p style="text-align: center;">Congratulations!</p>
    <p style="text-align: center;">Your mentorship session was just completed!</p>
    <p style="text-align: center;">Click on the link below to rate your mentor</p>
    <p style="text-align: center;">
        Echo variables:
        <a href="{{ url('rateMentee/' . $sessionId . '/' . $userId . '/' . $ratedId) }}">Click here</a>
    </p>
@endsection
