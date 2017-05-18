<li class="has-action-left singleItem col-md-12 matchMentorItem">

    @include('mentorship_session.single-mentorship-session-menu')

    <a href="javascript:void(0)" class="visible no-slide-left"
       data-matcherFullName="{{ $mentorshipSessionViewModel->matcher->first_name . " " . $mentorshipSessionViewModel->matcher->last_name }}"
       data-matcherId="{{ $mentorshipSessionViewModel->matcher->id }}" data-accountManagerId="{{ $mentorshipSessionViewModel->accountManager->id }}"
       data-sessionId="{{ $mentorshipSessionViewModel->mentorshipSession->id }}" data-mentorId="{{ $mentorshipSessionViewModel->mentorViewModel->mentor->id }}"
       data-menteeId="{{ $mentorshipSessionViewModel->menteeViewModel->mentee->id }}" data-sessionStatusId="{{ $mentorshipSessionViewModel->mentorshipSession->status_id }}"
       data-generalComment="{{ $mentorshipSessionViewModel->mentorshipSession->general_comment }}"
       data-actionRequired='{{ $mentorshipSessionViewModel->status->action_required }}'>
        <div class="row mentorshipSessionInfo">
            @if($mentorshipSessionViewModel->status->action_required != null)
                <p class="actionRequiredMessage">Action required. <br> If you are the account manager, edit this session to continue.</p>
            @endif
            <div class="col-md-8 centeredVertically">
                <div class="col-md-5 col-xs-5 text-align-right">
                    <div class="row">
                        <div class="col-md-9 col-xs-9 mentorView">
                            @include('mentorship_session.modals.mentor_view', ['mentorViewModel' => $mentorshipSessionViewModel->mentorViewModel])
                            <h6>Mentor</h6>
                        </div>
                        <div class="col-md-3 col-xs-3">
                            <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentor_default.png") }}" alt="Mentor profile image">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-xs-2 text-center matchingIconContainer">
                    <i class="matchingIcon ion-arrow-swap"></i>
                </div>
                <div class="col-md-5 col-xs-5 text-align-left">
                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <img class="matchingImg face-radius" src="{{ asset("/assets/img/mentee_default.png") }}" alt="Mentor profile image">
                        </div>
                        <div class="col-md-9 col-xs-9">
                            @include('mentorship_session.modals.mentee_view', ['menteeViewModel' => $mentorshipSessionViewModel->menteeViewModel])
                            <h6>Mentee</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottomInfo">
                <div class="col-md-12">
                <span class="caption">Account Manager: <span id="accountManagerName">{{ $mentorshipSessionViewModel->accountManager->first_name . " " .
                    $mentorshipSessionViewModel->accountManager->last_name }}</span></span>
                </div>
                <div class="col-md-12">
                <span class="caption margin-top-5">
                    @if($mentorshipSessionViewModel->status != null)
                        <span id="sessionStatus" class="{{$mentorshipSessionViewModel->status->title}}">{{$mentorshipSessionViewModel->status->description}}</span>
                    @endif
                    @if($mentorshipSessionViewModel->status != null && $mentorshipSessionViewModel->mentorshipSession->created_at != null)
                        |
                    @endif
                    @if($mentorshipSessionViewModel->mentorshipSession->created_at != null)
                        created: <span id="createdAt">{{ $mentorshipSessionViewModel->createdAtDiffForHumans }}</span>
                    @endif
                    @if(($mentorshipSessionViewModel->status != null || $mentorshipSessionViewModel->mentorshipSession->created_at != null) &&
                        $mentorshipSessionViewModel->mentorshipSession->updated_at != null
                    )
                        |
                    @endif
                    @if($mentorshipSessionViewModel->mentorshipSession->updated_at != null)
                        updated: <span id="updatedAt">{{ $mentorshipSessionViewModel->updatedAtDiffForHumans }}</span>
                    @endif
                </span>
                </div>
            </div>
        </div>
    </a>
</li>
