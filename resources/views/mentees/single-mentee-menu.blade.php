@if($matchingMode)
    @if($loggedInUser->isMatcher() || $loggedInUser->userHasAccessToCRUDMentorshipSessions())
        <a href="javascript:void(0)" class="hidden menu-action">
            <div class="matchMentorBtn"
                 data-toggle="modal"
                 data-userName="{{$menteeViewModel->mentee->first_name . ' ' . $menteeViewModel->mentee->last_name}}"
                 data-menteeId="{{$menteeViewModel->mentee->id}}">
                <i class="matchingIcon ion-arrow-swap"></i>
            </div>
        </a>
    @endif
@else
    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
        <a href="javascript:void(0)"
           data-toggle="modal"
           data-userName="{{$menteeViewModel->mentee->first_name . ' ' . $menteeViewModel->mentee->last_name}}"
           data-menteeId="{{$menteeViewModel->mentee->id}}"
           class="deleteMenteeBtn hidden menu-action"><i class="deleteIcon ion-android-delete"></i></a>
        <a target="_blank" href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}" class="hidden secondItem menu-action"><i class="editIcon ion-edit"></i></a>
    {{--@elseif($loggedInUser->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees())--}}
        {{--<a href="javascript:void(0)" data-toggle="modal"--}}
           {{--data-menteeId="{{$menteeViewModel->mentee->id}}" data-original-status="{{$menteeViewModel->mentee->status_id}}"--}}
           {{--class="editMenteeStatusBtn hidden menu-action">--}}
            {{--<i class="editIcon ion-edit"></i>--}}
        {{--</a>--}}
    @elseif($loggedInUser->userHasAccessToEditMentorsAndMentees())
        <a target="_blank" href="{{route('showEditMenteeForm', $menteeViewModel->mentee->id)}}" class="hidden menu-action"><i class="editIcon ion-edit"></i></a>
    @endif
@endif
