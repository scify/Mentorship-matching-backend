@extends('layouts.app')
@section('content')
    @include('mentorship_session.list')
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init();
            var tabsHandler = new window.TabsHandler();
            tabsHandler.init("#mentorshipSessionShowModal");
        });
    </script>
@endsection