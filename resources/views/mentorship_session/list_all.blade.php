@extends('layouts.app')
@section('content')
    @include('mentorship_session.list')
    @include('mentorship_session.modals.show')
    @include('mentorship_session.modals.matching_modal')
@endsection


@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init();
        });
    </script>
@endsection
