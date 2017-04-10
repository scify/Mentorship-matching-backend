@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>NOTIFICATIONS</h4>
                    </div>
                </div><!--.panel-heading-->
                <div class="panel-body">
                    [todo: Usefull shortcuts and notifications can be displayed here]
                </div>
            </div>
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
            @endif
        </div>
        @if($loggedInUser->isMatcher())
            <div class="col-md-6">
                <div id="availableMentorsList">
                    <div class="note note-success note-top-striped">
                        <h4>MENTORS AVAILABLE FOR MATCHING</h4>
                        @if($mentorViewModels->count() == 0)
                            No mentors available.
                        @else
                            @include('mentors.list', ['mentorViewModels' => $mentorViewModels])
                            @include('mentors.modals', ['statuses' => $mentorStatuses])
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="availableMenteesList">
                    <div class="note note-success note-top-striped">
                        <h4>MENTEES AVAILABLE FOR MATCHING</h4>
                        @if($mentorViewModels->count() == 0)
                            No mentees available.
                        @else
                            @include('mentees.list', ['menteeViewModels' => $menteeViewModels])
                            @include('mentees.modals', ['statuses' => $menteeStatuses])
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
    @include('mentorship_session.modals.show')
    @include('mentorship_session.modals.matching_modal', ['statuses' => $mentorshipSessionStatuses])
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
        @include('mentorship_session.modals.delete')
    @endif
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
