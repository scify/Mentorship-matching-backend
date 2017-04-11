@extends('emails.common')
@section('email-body')
    <h1 style="text-align: center; font-size: 26px; margin-top: 15px; margin-bottom: 10px;">Job Pairs</h1>
    <p style="text-align: center;">You have been invited to manage a new mentorship session!</p>
    <p style="text-align: center;">Choose whether to accept or reject the invitation:</p>
    <div style="display: table; width: 100%">
        <p style="text-align: center; display: table-cell; width: 50%;">
            <a href="{{ route('acceptToManageMentorshipSession', ['id' => $id, 'email' => $email, 'mentorshipSessionId' => $mentorshipSessionId]) }}" style="display: block; background-color: #5cb85c; color: white; padding: 10px; width: 150px; text-decoration: none; margin-left: auto; margin-right: auto;">
                Accept
            </a>
        </p>
        <p style="text-align: center; display: table-cell; width: 50%;">
            <a href="{{ route('rejectToManageMentorshipSession', ['id' => $id, 'email' => $email, 'mentorshipSessionId' => $mentorshipSessionId]) }}" style="display: block; background-color: #d9534f; color: white; padding: 10px; width: 150px; text-decoration: none; margin-left: auto; margin-right: auto;">
                Reject
            </a>
        </p>
    </div>
@endsection
