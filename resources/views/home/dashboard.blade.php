@extends('layouts.app')

@section('content')
    <div class="row dashboard">
        <div class="col-md-12">
            @if($loggedInUser->isAccountManager())
                @if($pendingMentorshipSessionViewModelsForAccManager->count() > 0)
                    <div id="pending_mentorship_sessions">
                        <div class="note note-warning note-left-striped">
                            <h4>RECENTLY ASSIGNED MENTORSHIP SESSIONS</h4>
                            @include('mentorship_session.list', ['mentorshipSessionViewModels' => $pendingMentorshipSessionViewModelsForAccManager])
                        </div>
                    </div>
                @endif
                @include('mentorship_session.modals.show')
                @include('mentorship_session.modals.matching_modal_edit', ['statuses' => $mentorshipSessionStatuses])
                @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                    @include('mentorship_session.modals.delete')
                @endif

            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>YOUR MENTORSHIP SESSIONS</h4>
                    </div>
                </div><!--.panel-heading-->
                <div class="panel-body">
                    @if($pendingMentorshipSessionViewModelsForAccManager->count() == 0)
                        You don't have any pending mentorship sessions assigned. You can view all the sessions you participate in <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> here. </a>
                    @else
                        You currently manage {{ $mentorshipSessionsNumForAccManager }} mentorship sessions. <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> Click here </a> to view them.
                    @endif
                </div>
            </div>
            @endif
        </div>
        @if($loggedInUser->isMatcher())
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>MENTORS AVAILABLE FOR MATCHING</h4>
                        </div>
                    </div><!--.panel-heading-->
                    <div class="panel-body">
                        There are {{ $availableMentorsViewModels->count() }} mentors available for matching in the platform. <a href="{{ route('showAllMentors') }}"> Click here </a> to view all mentors.
                    </div>
                </div>
            </div>
            {{--<div class="col-md-6">--}}
                {{--<div id="availableMentorsList">--}}
                    {{--<div class="panel">--}}
                        {{--<div class="panel-heading">--}}
                            {{--<div class="panel-title">--}}
                                {{--<h4>MENTORS AVAILABLE FOR MATCHING</h4>--}}
                            {{--</div>--}}
                        {{--</div><!--.panel-heading-->--}}
                        {{--<div class="panel-body bg-color-light-grey">--}}
                            {{--@if($mentorViewModels->count() == 0)--}}
                                {{--No mentors available.--}}
                            {{--@else--}}
                                {{--@include('mentors.list', ['mentorViewModels' => $mentorViewModels])--}}
                                {{--@include('mentors.modals', ['statuses' => $mentorStatuses])--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-6">--}}
                {{--<div id="availableMenteesList">--}}
                    {{--<div class="panel">--}}
                        {{--<div class="panel-heading">--}}
                            {{--<div class="panel-title">--}}
                                {{--<h4>MENTEES AVAILABLE FOR MATCHING</h4>--}}
                            {{--</div>--}}
                        {{--</div><!--.panel-heading-->--}}
                        {{--<div class="panel-body bg-color-light-grey">--}}
                            {{--@if($mentorViewModels->count() == 0)--}}
                                {{--No mentees available.--}}
                            {{--@else--}}
                                {{--@include('mentees.list', ['menteeViewModels' => $menteeViewModels])--}}
                                {{--@include('mentees.modals', ['statuses' => $menteeStatuses])--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        @endif
        @if($loggedInUser->isAdmin())
            <div class="col-md-12 margin-top-30">
                <h5>QUICK STATISTICS</h5>
                @include('reports.platform_reports')
            </div>
        @endif

    </div>

@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.TabsHandler();
            controller.init("#mentorshipSessionShowModal");

            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init("#pending_mentorship_sessions");

            var mentorsListController = new window.MentorsListController();
            mentorsListController.init();

            var menteesListController = new window.MenteesListController();
            menteesListController.init();

                    @if($loggedInUser->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init("#availableMentorsList");
            availabilityStatusChangeHandler.init("#availableMenteesList");
            @endif
        });
    </script>
@endsection
