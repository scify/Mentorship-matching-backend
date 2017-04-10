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
                    <h4>PENDING MENTORSHIP SESSIONS:</h4>
                    @if($mentorshipSessionViewModelsForAccManager->count() == 0)
                        No pending Mentorship sessions.
                    @else
                        @include('mentorship_session.list', ['mentorshipSessionViewModels' => $mentorshipSessionViewModelsForAccManager])
                    @endif
                </div>
                </div>
            @endif
        </div>
    </div>
    @include('mentorship_session.modals.show')
    @include('mentorship_session.modals.matching_modal')
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
        @include('mentorship_session.modals.delete')
    @endif
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new window.TabsHandler();

            var mentorshipSessionsListController = new window.MentorshipSessionsListController();
            mentorshipSessionsListController.init("#pending_mentorship_sessions");
            controller.init("#mentorshipSessionShowModal");
        });
    </script>
@endsection
