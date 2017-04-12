@extends('layouts.app')
@section('content')
    @include('mentorship_session.filters')
    <div id="mentorship_session_list">
        @include('mentorship_session.list')
    </div>
    @include('mentorship_session.modals.show', ['isCreatingNewSession' => false])
    @include('mentorship_session.modals.matching_modal_edit', ['isCreatingNewSession' => false])
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
        @include('mentorship_session.modals.delete')
    @endif
@endsection


@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init("#mentorship_session_list");
            var tabsHandler = new window.TabsHandler();
            tabsHandler.init("#mentorshipSessionShowModal");
        });
    </script>
@endsection
