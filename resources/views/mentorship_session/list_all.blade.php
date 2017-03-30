@extends('layouts.app')
@section('content')
    @include('mentorship_session.list')
    @include('mentorship_session.modals.show')
@endsection


@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init();
        });
    </script>
@endsection
