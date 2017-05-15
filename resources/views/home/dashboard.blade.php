@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($loggedInUser->isAccountManager())
                <div id="pending_mentorship_sessions">
                    <div class="note note-warning note-left-striped">
                        <h4>PENDING MENTORSHIP SESSIONS</h4>
                        @if($mentorshipSessionViewModelsForAccManager->count() == 0)
                            No pending Mentorship sessions.
                        @else
                            @include('mentorship_session.list', ['mentorshipSessionViewModels' => $mentorshipSessionViewModelsForAccManager])
                        @endif
                    </div>
                </div>
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
                    You manage {{ $mentorshipSessionsNumForAccManager }} mentorship sessions. <a href="{{ route('showMentorshipSessionsForAccountManager') }}"> Click here </a> to view them.
                </div>
            </div>
            @endif
        </div>
        @if($loggedInUser->isMatcher())
            <div class="col-md-6">
                <div id="availableMentorsList">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4>MENTORS AVAILABLE FOR MATCHING</h4>
                            </div>
                        </div><!--.panel-heading-->
                        <div class="panel-body bg-color-light-grey">
                            @if($mentorViewModels->count() == 0)
                                No mentors available.
                            @else
                                @include('mentors.list', ['mentorViewModels' => $mentorViewModels])
                                @include('mentors.modals', ['statuses' => $mentorStatuses])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="availableMenteesList">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4>MENTEES AVAILABLE FOR MATCHING</h4>
                            </div>
                        </div><!--.panel-heading-->
                        <div class="panel-body bg-color-light-grey">
                            @if($mentorViewModels->count() == 0)
                                No mentees available.
                            @else
                                @include('mentees.list', ['menteeViewModels' => $menteeViewModels])
                                @include('mentees.modals', ['statuses' => $menteeStatuses])
                            @endif
                        </div>
                    </div>
                </div>
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
